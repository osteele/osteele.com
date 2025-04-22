#!/usr/bin/env -S uv --quiet run --script
# /// script
# requires-python = ">=3.12"
# dependencies = [
#     "rdflib",
#     "boto3",
#     "playwright",
#     "typer",
#     "pillow",
#     "tqdm",
# ]
# ///
"""
WebApp Screenshot Manager

This script manages screenshots for web apps listed in projects.ttl. It can:
- List all web app projects
- Identify web apps without thumbnails
- Generate screenshots using Playwright
- Upload screenshots to Cloudfront R2 bucket
- Update the TTL file with the thumbnail URLs

Example usage:
# List all web app projects
add_project_thumbnail.py list

# List web apps that don't have thumbnails
add_project_thumbnail.py missing

# Generate and upload a screenshot for a specific web app
add_project_thumbnail.py capture "Project Name"

# Update a project with an existing image URL
add_project_thumbnail.py update "Project Name" --url https://example.com/image.webp

# Update a project with a local image file (will upload it)
add_project_thumbnail.py update "Project Name" --file path/to/image.png
"""

import os
import re
import subprocess
import sys
import tempfile
import time
from datetime import datetime
from pathlib import Path
from typing import Any, Dict, List, Optional, Set, Tuple
from urllib.parse import urlparse

import boto3
import typer
from PIL import Image
from playwright.sync_api import sync_playwright
from rdflib import Graph, Literal, Namespace, URIRef
from rdflib.term import Node
from tqdm import tqdm

# Constants
TTL_FILE_PATH = Path("src/data/projects.ttl")
SCREENSHOT_BUCKET = "web-images"  # Cloudflare R2 bucket name
SCREENSHOT_PREFIX = "portfolio/website/thumbnails/"  # Prefix with trailing slash
THUMBNAIL_DOMAIN = "images.osteele.com"  # Domain where images are served
THUMBNAIL_WIDTH = 800  # Default thumbnail width
THUMBNAIL_HEIGHT = 600  # Default thumbnail height

# Namespaces
SCHEMA = Namespace("http://schema.org/")
OS = Namespace("http://osteele.com/ns/")
DC = Namespace("http://purl.org/dc/terms/")
DOAP = Namespace("http://usefulinc.com/ns/doap#")

# Create the Typer app
app = typer.Typer(help="Manage screenshots for web app projects")


def parse_ttl_file(file_path: Path) -> Graph:
    """Parse the TTL file into an RDF graph."""
    if not file_path.exists():
        typer.echo(f"Error: TTL file not found at {file_path}")
        sys.exit(1)

    graph = Graph()
    graph.parse(file_path, format="turtle")
    return graph


def get_webapp_projects(graph: Graph) -> List[Dict[str, Any]]:
    """Extract web app projects from the RDF graph."""
    projects = []

    # Query for all projects
    for subject in graph.subjects(URIRef(f"{DC}title"), None):
        # Check if this is a webapp project
        categories = set(
            str(obj) for obj in graph.objects(subject, URIRef(f"{OS}category"))
        )

        if any(cat in ["webapp", "web-app", "web-apps"] for cat in categories):
            # This is a web app, get its details
            title = (
                str(graph.value(subject, URIRef(f"{DC}title")))
                if graph.value(subject, URIRef(f"{DC}title"))
                else None
            )
            website = (
                str(graph.value(subject, URIRef(f"{SCHEMA}url")))
                if graph.value(subject, URIRef(f"{SCHEMA}url"))
                else None
            )
            thumbnail = (
                str(graph.value(subject, URIRef(f"{SCHEMA}thumbnail")))
                if graph.value(subject, URIRef(f"{SCHEMA}thumbnail"))
                else None
            )
            repo = (
                str(graph.value(subject, URIRef(f"{DOAP}repository")))
                if graph.value(subject, URIRef(f"{DOAP}repository"))
                else None
            )

            if title:  # Only add if we have at least a title
                projects.append(
                    {
                        "subject": subject,
                        "title": title,
                        "website": website,
                        "repo": repo,
                        "thumbnail": thumbnail,
                    }
                )

    # Sort by title
    projects.sort(key=lambda p: p["title"])
    return projects


def get_s3_client():
    """Get an S3 client configured for R2."""
    # Check for environment variables
    account_id = os.environ.get("R2_ACCOUNT_ID")
    access_key = os.environ.get("R2_ACCESS_KEY")
    secret_key = os.environ.get("R2_SECRET_KEY")

    if not all([account_id, access_key, secret_key]):
        typer.echo("Error: R2 credentials not found in environment variables.")
        typer.echo("Please set R2_ACCOUNT_ID, R2_ACCESS_KEY, and R2_SECRET_KEY.")
        sys.exit(1)

    endpoint_url = f"https://{account_id}.r2.cloudflarestorage.com"
    return boto3.client(
        "s3",
        endpoint_url=endpoint_url,
        aws_access_key_id=access_key,
        aws_secret_access_key=secret_key,
    )


def ensure_playwright_browsers():
    """Ensure Playwright browsers are installed."""
    try:
        with sync_playwright() as p:
            try:
                # Try to launch browser to check if installed
                browser = p.chromium.launch(headless=True)
                browser.close()
                return True
            except Exception as e:
                if "Executable doesn't exist" in str(e):
                    typer.echo(
                        "Playwright browsers are not installed. Installing now..."
                    )
                    result = subprocess.run(
                        [sys.executable, "-m", "playwright", "install", "chromium"],
                        capture_output=True,
                        text=True,
                    )
                    if result.returncode == 0:
                        typer.echo("Playwright browsers installed successfully!")
                        return True
                    else:
                        typer.echo(
                            f"Failed to install Playwright browsers: {result.stderr}"
                        )
                        return False
                else:
                    typer.echo(f"Error checking Playwright browsers: {str(e)}")
                    return False
    except Exception as e:
        typer.echo(f"Error initializing Playwright: {str(e)}")
        return False


def take_screenshot(
    url: str,
    output_path: Path,
    width: int = THUMBNAIL_WIDTH,
    height: int = THUMBNAIL_HEIGHT,
) -> Path:
    """Take a screenshot of a website using Playwright."""
    typer.echo(f"Taking screenshot of {url}")

    # Make sure browsers are installed
    if not ensure_playwright_browsers():
        raise Exception("Failed to ensure Playwright browsers are installed")

    # Validate URL
    if not url.startswith(("http://", "https://")):
        url = f"https://{url}"

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={"width": width, "height": height})

        try:
            typer.echo("Navigating to page...")
            page.goto(url, wait_until="networkidle", timeout=60000)

            # Wait a bit for any animations or delayed content
            page.wait_for_timeout(3000)

            typer.echo("Taking screenshot...")
            page.screenshot(path=output_path)

            # Convert to WebP if not already
            if output_path.suffix.lower() != ".webp":
                webp_path = output_path.with_suffix(".webp")
                Image.open(output_path).save(webp_path, format="WEBP", quality=85)
                output_path = webp_path

            typer.echo(f"Screenshot saved to {output_path}")
            browser.close()
            return output_path

        except Exception as e:
            browser.close()
            typer.echo(f"Error taking screenshot: {str(e)}")
            raise


def upload_to_r2(file_path: Path, project_name: str) -> str:
    """Upload a screenshot to R2 bucket and return the URL."""
    s3_client = get_s3_client()

    # Create a safe filename based on the project name
    safe_name = re.sub(r"[^a-zA-Z0-9]", "-", project_name.lower())
    timestamp = int(time.time())
    key = f"{SCREENSHOT_PREFIX}{safe_name}-{timestamp}.webp"

    # Upload the file
    typer.echo(f"Uploading to R2 bucket {SCREENSHOT_BUCKET}/{key}...")
    s3_client.upload_file(
        str(file_path),
        SCREENSHOT_BUCKET,
        key,
        ExtraArgs={"ContentType": "image/webp", "ACL": "public-read"},
    )

    # Return the public URL
    return f"https://{THUMBNAIL_DOMAIN}/{key}"


def update_ttl_file(graph: Graph, subject: Node, thumbnail_url: str) -> bool:
    """Update the TTL file with the thumbnail URL in a targeted way that preserves formatting."""
    # First, let's identify the project entry in the original file
    with open(TTL_FILE_PATH, "r", encoding="utf-8") as f:
        ttl_content = f.read()

    # Find all project blocks
    projects = []
    current_project = []
    for line in ttl_content.split("\n"):
        if line.strip().startswith("os:") and "a doap:Project" in line:
            if current_project:
                projects.append("\n".join(current_project))
            current_project = [line]
        elif current_project:
            current_project.append(line)
    if current_project:
        projects.append("\n".join(current_project))

    # Find the project we want to update
    subject_id = str(subject).split("/")[-1]  # Extract the ID part from the URI
    project_content = None
    for project in projects:
        if project.startswith(f"os:{subject_id}"):
            project_content = project
            break

    if not project_content:
        typer.echo(f"Error: Couldn't find project block for {subject_id} in TTL file.")
        return False

    # Check if there's already a thumbnail line in the project
    thumbnail_pattern = re.compile(r'schema:thumbnail\s+"([^"]+)"\s*;')
    thumbnail_match = thumbnail_pattern.search(project_content)

    if thumbnail_match:
        # Replace existing thumbnail
        updated_content = thumbnail_pattern.sub(
            f'schema:thumbnail "{thumbnail_url}" ;', project_content
        )
    else:
        # Add thumbnail after the last property
        # Find the first line with a period (end of project definition)
        lines = project_content.split("\n")
        period_line_index = next(
            (i for i, line in enumerate(lines) if line.strip().endswith(".")), -1
        )

        if period_line_index > 0:
            # Replace period with semicolon and add thumbnail
            lines[period_line_index] = lines[period_line_index].rstrip(".") + " ;"
            lines.insert(
                period_line_index + 1, f'    schema:thumbnail "{thumbnail_url}" .'
            )
            updated_content = "\n".join(lines)
        else:
            # Just append to the end if we can't find a line ending with a period
            updated_content = (
                project_content.rstrip()
                + f'\n    schema:thumbnail "{thumbnail_url}" .\n'
            )

    # Replace the project content in the full file
    new_ttl_content = ttl_content.replace(project_content, updated_content)

    # Write the updates back to the file
    with open(TTL_FILE_PATH, "w", encoding="utf-8") as f:
        f.write(new_ttl_content)

    return True


def find_project_by_name(
    projects: List[Dict[str, Any]], name: str
) -> Optional[Dict[str, Any]]:
    """Find a project by name (case-insensitive)."""
    for project in projects:
        if project["title"].lower() == name.lower():
            return project
    return None


@app.command()
def list():
    """List all web app projects."""
    graph = parse_ttl_file(TTL_FILE_PATH)
    webapps = get_webapp_projects(graph)

    typer.echo(f"Found {len(webapps)} web app projects:")
    for i, project in enumerate(webapps, 1):
        website = f" ({project['website']})" if project["website"] else ""
        thumbnail = " [HAS THUMBNAIL]" if project["thumbnail"] else ""
        typer.echo(f"{i}. {project['title']}{website}{thumbnail}")


@app.command()
def missing():
    """List web apps that don't have thumbnails."""
    graph = parse_ttl_file(TTL_FILE_PATH)
    webapps = get_webapp_projects(graph)

    missing_thumbnails = [p for p in webapps if not p["thumbnail"]]

    typer.echo(f"Found {len(missing_thumbnails)} web app projects without thumbnails:")
    for i, project in enumerate(missing_thumbnails, 1):
        website = f" ({project['website']})" if project["website"] else " [NO WEBSITE]"
        typer.echo(f"{i}. {project['title']}{website}")


@app.command()
def capture(
    project_name: str,
    url: Optional[str] = None,
    width: int = THUMBNAIL_WIDTH,
    height: int = THUMBNAIL_HEIGHT,
):
    """Generate a screenshot for a project, upload it, and update the TTL file."""
    graph = parse_ttl_file(TTL_FILE_PATH)
    webapps = get_webapp_projects(graph)

    # Find the project
    project = find_project_by_name(webapps, project_name)
    if not project:
        typer.echo(f"Error: Project '{project_name}' not found or is not a web app.")
        sys.exit(1)

    # Determine the URL to screenshot
    screenshot_url = url or project["website"]
    if not screenshot_url:
        typer.echo(
            f"Error: No URL specified and project '{project_name}' has no website URL."
        )
        typer.echo("Please provide a URL with --url.")
        sys.exit(1)

    # Create a temporary file for the screenshot
    with tempfile.NamedTemporaryFile(suffix=".png", delete=False) as tmp:
        tmp_path = Path(tmp.name)

    try:
        # Take the screenshot
        screenshot_path = take_screenshot(screenshot_url, tmp_path, width, height)

        # Upload to R2
        thumbnail_url = upload_to_r2(screenshot_path, project_name)

        # Update the TTL file
        if update_ttl_file(graph, project["subject"], thumbnail_url):
            typer.echo(
                f"Successfully updated '{project_name}' with thumbnail: {thumbnail_url}"
            )
        else:
            typer.echo(f"Failed to update TTL file for '{project_name}'.")

    finally:
        # Clean up temporary file
        if tmp_path.exists():
            tmp_path.unlink()


@app.command()
def update(project_name: str, url: Optional[str] = None, file: Optional[Path] = None):
    """Update a project with an existing image URL or file."""
    if not url and not file:
        typer.echo("Error: Either --url or --file must be specified.")
        sys.exit(1)

    graph = parse_ttl_file(TTL_FILE_PATH)
    webapps = get_webapp_projects(graph)

    # Find the project
    project = find_project_by_name(webapps, project_name)
    if not project:
        typer.echo(f"Error: Project '{project_name}' not found or is not a web app.")
        sys.exit(1)

    thumbnail_url = url

    # If a file is provided, upload it to R2
    if file:
        if not file.exists():
            typer.echo(f"Error: File '{file}' not found.")
            sys.exit(1)

        webp_path = None  # Initialize to avoid reference errors
        try:
            # Convert to WebP if not already
            if file.suffix.lower() != ".webp":
                with tempfile.NamedTemporaryFile(suffix=".webp", delete=False) as tmp:
                    webp_path = Path(tmp.name)
                    Image.open(file).save(webp_path, format="WEBP", quality=85)
                    file = webp_path

            # Upload to R2
            thumbnail_url = upload_to_r2(file, project_name)

        finally:
            # Clean up temporary file if created
            if webp_path and webp_path.exists():
                webp_path.unlink()

    # Update the TTL file
    if update_ttl_file(graph, project["subject"], thumbnail_url):
        typer.echo(
            f"Successfully updated '{project_name}' with thumbnail: {thumbnail_url}"
        )
    else:
        typer.echo(f"Failed to update TTL file for '{project_name}'.")


@app.command()
def batch_capture(
    width: int = THUMBNAIL_WIDTH, height: int = THUMBNAIL_HEIGHT, max_count: int = 0
):
    """Generate screenshots for all web apps without thumbnails."""
    # Make sure browsers are installed before starting batch operation
    if not ensure_playwright_browsers():
        typer.echo("Failed to ensure Playwright browsers are installed")
        sys.exit(1)

    graph = parse_ttl_file(TTL_FILE_PATH)
    webapps = get_webapp_projects(graph)

    missing_thumbnails = [p for p in webapps if not p["thumbnail"] and p["website"]]

    if max_count > 0 and len(missing_thumbnails) > max_count:
        missing_thumbnails = missing_thumbnails[:max_count]

    typer.echo(
        f"Found {len(missing_thumbnails)} web app projects without thumbnails that have websites."
    )

    for project in tqdm(missing_thumbnails, desc="Capturing screenshots"):
        typer.echo(f"\nProcessing: {project['title']}")

        # Create a temporary file for the screenshot
        with tempfile.NamedTemporaryFile(suffix=".png", delete=False) as tmp:
            tmp_path = Path(tmp.name)

        try:
            # Take the screenshot
            screenshot_path = take_screenshot(
                project["website"], tmp_path, width, height
            )

            # Upload to R2
            thumbnail_url = upload_to_r2(screenshot_path, project["title"])

            # Update the TTL file
            if update_ttl_file(graph, project["subject"], thumbnail_url):
                typer.echo(
                    f"Successfully updated '{project['title']}' with thumbnail: {thumbnail_url}"
                )
            else:
                typer.echo(f"Failed to update TTL file for '{project['title']}'.")

            # Wait a bit between projects to avoid overwhelming the sites
            time.sleep(2)

        except Exception as e:
            typer.echo(f"Error processing {project['title']}: {str(e)}")

        finally:
            # Clean up temporary file
            if tmp_path.exists():
                tmp_path.unlink()


if __name__ == "__main__":
    app()
