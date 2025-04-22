#!/usr/bin/env -S uv --quiet run --script
# /// script
# requires-python = ">=3.12"
# dependencies = [
#     "typer",
#     "requests",
# ]
# ///

"""
Update Projects Utility

This script provides project update utilities for the site. It can update
creation and modification dates, as well as website URLs in the TTL file based on GitHub data.

Example usage:
# Update a specific project's dates by name
update_projects.py dates "Liquid Template Engine"

# Update a specific project's homepage URL by name
update_projects.py url "Liquid Template Engine"

# Update both dates and URL by repository path
update_projects.py all osteele/liquid

# Update by GitHub URL
update_projects.py dates https://github.com/osteele/liquid

# Update multiple projects
update_projects.py dates "Gojekyll" "p5-server"

# Show changes without writing (dry run)
update_projects.py dates "Liquid Template Engine" --dry-run

# List projects with GitHub repositories
update_projects.py list
"""

import os
import re
import sys
import time
from enum import Enum
from pathlib import Path
from typing import Any, Dict, List, Match, Optional, Set, TypedDict

import requests
import typer
from typing_extensions import Annotated

# --- GitHub API constants and helpers ---
GITHUB_API_URL = "https://api.github.com"
GITHUB_TOKEN = os.environ.get("GITHUB_TOKEN")

TTL_FILE_PATH = "src/data/projects.ttl"

REPO_PATTERN = re.compile(
    r'doap:repository\s+"https://github\.com/([^/]+)/([^\"]+)"\s+;'
)
TITLE_PATTERN = re.compile(r'dc:title\s+"([^\"]+)"\s+;')
DATE_CREATED_PATTERN = re.compile(r'schema:dateCreated\s+"([^\"]+)"\s+;')
DATE_MODIFIED_PATTERN = re.compile(r'schema:dateModified\s+"([^\"]+)"\s+;')
SCHEMA_URL_PATTERN = re.compile(r'schema:url\s+"([^"]+)"\s*;')


class ProjectUpdate(TypedDict):
    repo: str
    original: str
    updated: str
    changes: List[str]


app = typer.Typer(
    help="Update project metadata in the TTL file based on GitHub data",
    add_completion=False,
)


def setup_github_headers() -> Dict[str, str]:
    headers = {"Accept": "application/vnd.github.v3+json"}
    if GITHUB_TOKEN:
        headers["Authorization"] = f"token {GITHUB_TOKEN}"
    return headers


def get_repository_info(
    owner: str, repo: str, headers: Dict[str, str]
) -> Optional[Dict[str, Any]]:
    """Fetch repository information from GitHub API."""
    url = f"{GITHUB_API_URL}/repos/{owner}/{repo}"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        return response.json()
    elif response.status_code == 404:
        typer.echo(f"Repository not found: {owner}/{repo}")
        return None
    elif response.status_code == 403 and "rate limit" in response.text.lower():
        typer.echo("Rate limit exceeded. Waiting for 60 seconds...")
        time.sleep(60)
        return get_repository_info(owner, repo, headers)
    else:
        typer.echo(f"Error fetching repository {owner}/{repo}: {response.status_code}")
        typer.echo(response.text)
        return None


def parse_ttl_file(file_path: str) -> List[Dict[str, str]]:
    """Parse TTL file and extract projects with GitHub repositories."""
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()
    projects: List[str] = []
    current_project: List[str] = []
    for line in content.split("\n"):
        if line.strip().startswith("os:") and "a doap:Project" in line:
            if current_project:
                projects.append("\n".join(current_project))
            current_project = [line]
        elif current_project:
            current_project.append(line)
    if current_project:
        projects.append("\n".join(current_project))
    projects_with_repos: List[Dict[str, str]] = []
    for project in projects:
        repo_match = REPO_PATTERN.search(project)
        if repo_match:
            title_match = TITLE_PATTERN.search(project)
            title = title_match.group(1) if title_match else "Unknown"
            projects_with_repos.append(
                {
                    "content": project,
                    "owner": repo_match.group(1),
                    "repo": repo_match.group(2),
                    "title": title,
                }
            )
    return projects_with_repos


def update_project_dates(project: Dict[str, str], repo_info: Dict[str, Any]) -> str:
    """Update dateCreated and dateModified for a project based on repo info."""
    content = project["content"]
    created_at = repo_info.get("created_at")
    pushed_at = repo_info.get("pushed_at")
    if created_at:
        if DATE_CREATED_PATTERN.search(content):
            content = DATE_CREATED_PATTERN.sub(
                f'schema:dateCreated "{created_at}" ;', content
            )
    if pushed_at:
        if DATE_MODIFIED_PATTERN.search(content):
            content = DATE_MODIFIED_PATTERN.sub(
                f'schema:dateModified "{pushed_at}" ;', content
            )
    return content


def update_project_url(project: Dict[str, str], repo_info: Dict[str, Any]) -> str:
    """Update schema:url for a project based on repo homepage."""
    content = project["content"]
    homepage = repo_info.get("homepage")
    if homepage:
        if SCHEMA_URL_PATTERN.search(content):
            content = SCHEMA_URL_PATTERN.sub(f'schema:url "{homepage}" ;', content)
        else:
            # Insert after title or at end of block
            title_match = TITLE_PATTERN.search(content)
            insert_idx = title_match.end() if title_match else len(content)
            content = (
                content[:insert_idx]
                + f'\n    schema:url "{homepage}" ;'
                + content[insert_idx:]
            )
    return content


def matches_filter(project: Dict[str, str], filters: List[str]) -> bool:
    """Check if a project matches any of the specified filters."""
    if not filters:
        return True
    for filter_str in filters:
        if filter_str.lower() in project["title"].lower():
            return True
        repo_name = f"{project['owner']}/{project['repo']}"
        if filter_str in repo_name:
            return True
        if filter_str.startswith("https://github.com/"):
            clean_url = filter_str.rstrip("/")
            clean_repo = f"https://github.com/{project['owner']}/{project['repo']}"
            if clean_url == clean_repo:
                return True
    return False


def update_projects(
    projects: List[str],
    update_dates: bool = False,
    update_url: bool = False,
    dry_run: bool = False,
) -> None:
    """Update projects based on specified operations."""
    if not GITHUB_TOKEN:
        typer.echo(
            "Warning: GITHUB_TOKEN environment variable not set. Rate limits may apply."
        )

    headers = setup_github_headers()
    typer.echo(f"Parsing {TTL_FILE_PATH}...")
    all_projects = parse_ttl_file(TTL_FILE_PATH)
    typer.echo(f"Found {len(all_projects)} projects with GitHub repositories.")

    if projects:
        filtered_projects = [p for p in all_projects if matches_filter(p, projects)]
        filtered_out = len(all_projects) - len(filtered_projects)
        all_projects = filtered_projects
        typer.echo(
            f"Filtered to {len(all_projects)} projects matching filter(s), skipping {filtered_out}."
        )

    with open(TTL_FILE_PATH, "r", encoding="utf-8") as f:
        ttl_content = f.read()

    updated_count = 0
    updates: List[ProjectUpdate] = []

    for project in all_projects:
        owner = project["owner"]
        repo = project["repo"]
        typer.echo(f"Fetching data for {owner}/{repo}...")
        repo_info = get_repository_info(owner, repo, headers)
        updated_content = project["content"]
        changes = []

        if repo_info:
            if update_dates:
                before = updated_content
                updated_content = update_project_dates(
                    {"content": updated_content}, repo_info
                )
                if updated_content != before:
                    changes.append("dates")

            if update_url:
                before = updated_content
                updated_content = update_project_url(
                    {"content": updated_content}, repo_info
                )
                if updated_content != before:
                    changes.append("url")

        if changes:
            updates.append(
                {
                    "repo": f"{owner}/{repo}",
                    "original": project["content"],
                    "updated": updated_content,
                    "changes": changes,
                }
            )
            if not dry_run:
                ttl_content = ttl_content.replace(project["content"], updated_content)
            updated_count += 1

        # Sleep to avoid hitting rate limits
        time.sleep(1)

    # Print changes for dry run or save changes
    if dry_run:
        typer.echo("\nDRY RUN: The following changes would be made:")
        for update in updates:
            typer.echo(f"\nRepository: {update['repo']}")
            if "dates" in update["changes"]:
                orig_created = DATE_CREATED_PATTERN.search(update["original"])
                orig_modified = DATE_MODIFIED_PATTERN.search(update["original"])
                new_created = DATE_CREATED_PATTERN.search(update["updated"])
                new_modified = DATE_MODIFIED_PATTERN.search(update["updated"])

                if (
                    orig_created
                    and new_created
                    and orig_created.group(1) != new_created.group(1)
                ):
                    typer.echo(
                        f"  dateCreated: {orig_created.group(1)} -> {new_created.group(1)}"
                    )

                if (
                    orig_modified
                    and new_modified
                    and orig_modified.group(1) != new_modified.group(1)
                ):
                    typer.echo(
                        f"  dateModified: {orig_modified.group(1)} -> {new_modified.group(1)}"
                    )

            if "url" in update["changes"]:
                orig_url = SCHEMA_URL_PATTERN.search(update["original"])
                new_url = SCHEMA_URL_PATTERN.search(update["updated"])
                if (orig_url and new_url and orig_url.group(1) != new_url.group(1)) or (
                    not orig_url and new_url
                ):
                    typer.echo(
                        f"  schema:url: {orig_url.group(1) if orig_url else None} -> {new_url.group(1)}"
                    )

        typer.echo(f"\nTotal: {updated_count} projects would be updated.")
    elif updates:
        with open(TTL_FILE_PATH, "w", encoding="utf-8") as f:
            f.write(ttl_content)
        typer.echo(f"\nUpdated {updated_count} project(s) in {TTL_FILE_PATH}.")
    else:
        typer.echo("No changes made.")


@app.command()
def list():
    """List all projects with GitHub repositories."""
    all_projects = parse_ttl_file(TTL_FILE_PATH)
    typer.echo(f"Found {len(all_projects)} projects with GitHub repositories:")

    for i, project in enumerate(all_projects, 1):
        repo = f"{project['owner']}/{project['repo']}"
        typer.echo(f"{i}. {project['title']} ({repo})")


@app.command()
def dates(
    projects: List[str] = typer.Argument(
        None, help="Projects to update (by name, owner/repo, or GitHub URL)"
    ),
    dry_run: bool = typer.Option(
        False, "--dry-run", help="Show changes without writing to file"
    ),
):
    """Update project creation and modification dates from GitHub."""
    update_projects(projects, update_dates=True, update_url=False, dry_run=dry_run)


@app.command()
def url(
    projects: List[str] = typer.Argument(
        None, help="Projects to update (by name, owner/repo, or GitHub URL)"
    ),
    dry_run: bool = typer.Option(
        False, "--dry-run", help="Show changes without writing to file"
    ),
):
    """Update project website URLs from GitHub homepage field."""
    update_projects(projects, update_dates=False, update_url=True, dry_run=dry_run)


@app.command()
def all(
    projects: List[str] = typer.Argument(
        None, help="Projects to update (by name, owner/repo, or GitHub URL)"
    ),
    dry_run: bool = typer.Option(
        False, "--dry-run", help="Show changes without writing to file"
    ),
):
    """Update both dates and URLs for projects from GitHub data."""
    update_projects(projects, update_dates=True, update_url=True, dry_run=dry_run)


if __name__ == "__main__":
    app()
