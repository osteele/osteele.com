#!/usr/bin/env -S uv --quiet run --script
# /// script
# requires-python = ">=3.12"
# dependencies = [
#     "typer",
#     "requests",
# ]
# ///

"""
Update Projects Utility (GraphQL Optimized)

This script provides project update utilities for the site using GitHub's GraphQL API
for much faster batch operations.

Example usage:
# Update all project dates
update_projects_graphql.py dates

# Update specific projects
update_projects_graphql.py dates "Liquid Template Engine" "Gojekyll"

# Dry run
update_projects_graphql.py dates --dry-run

# List projects
update_projects_graphql.py list
"""

import os
import re
import sys
import time
from pathlib import Path
from typing import Any, Dict, List, Optional, TypedDict

import requests
import typer

# --- GitHub API constants and helpers ---
GITHUB_GRAPHQL_URL = "https://api.github.com/graphql"
GITHUB_TOKEN = os.environ.get("GITHUB_TOKEN")

TTL_FILE_PATH = "src/data/projects.ttl"

REPO_PATTERN = re.compile(
    r'doap:repository\s+"https://github\.com/([^/]+)/([^/\"]+)(?:/|\").*?"\s*;'
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
    help="Update project metadata in the TTL file using GitHub GraphQL API",
    add_completion=False,
)


def setup_github_headers() -> Dict[str, str]:
    if not GITHUB_TOKEN:
        typer.echo(
            "Error: GITHUB_TOKEN environment variable is required for GraphQL API."
        )
        raise typer.Exit(1)
    return {"Authorization": f"Bearer {GITHUB_TOKEN}"}


def build_graphql_query(repos: List[tuple[str, str]]) -> str:
    """Build a GraphQL query to fetch multiple repositories at once."""
    fragments = []
    for i, (owner, repo) in enumerate(repos):
        # GraphQL aliases can't have hyphens, so replace them
        alias = f"repo_{i}"
        fragments.append(
            f"""
    {alias}: repository(owner: "{owner}", name: "{repo}") {{
      createdAt
      pushedAt
      homepageUrl
      owner {{
        login
      }}
      name
    }}"""
        )
    
    # Split into chunks if needed (GitHub has query size limits)
    # We'll do 50 repos per query to be safe
    if len(fragments) > 50:
        return None  # Signal to use chunking
    
    return "{\n" + "\n".join(fragments) + "\n}"


def fetch_repositories_graphql(
    repos: List[tuple[str, str]], headers: Dict[str, str]
) -> Dict[str, Dict[str, Any]]:
    """Fetch multiple repositories using GraphQL API with retry logic."""
    results = {}
    
    # Process in chunks of 30 to avoid query size limits and reduce timeout risk
    chunk_size = 30
    for chunk_num, chunk_start in enumerate(range(0, len(repos), chunk_size)):
        chunk_end = min(chunk_start + chunk_size, len(repos))
        chunk = repos[chunk_start:chunk_end]
        
        typer.echo(f"Fetching batch {chunk_num + 1}/{(len(repos) - 1) // chunk_size + 1} ({len(chunk)} repos)...")
        
        fragments = []
        for i, (owner, repo) in enumerate(chunk):
            alias = f"repo_{i}"
            fragments.append(
                f"""
    {alias}: repository(owner: "{owner}", name: "{repo}") {{
      createdAt
      pushedAt
      homepageUrl
      owner {{
        login
      }}
      name
    }}"""
            )
        
        query = "{\n" + "\n".join(fragments) + "\n}"
        
        # Retry logic for GraphQL requests
        max_retries = 3
        for retry in range(max_retries):
            try:
                response = requests.post(
                    GITHUB_GRAPHQL_URL,
                    json={"query": query},
                    headers=headers,
                    timeout=20,  # 20 second timeout per request
                )
                
                if response.status_code == 200:
                    data = response.json()
                    
                    if "errors" in data:
                        # Some repos might not exist or be private
                        for error in data.get("errors", []):
                            if "path" in error:
                                typer.echo(f"Warning: {error.get('message', 'Unknown error')}")
                    
                    # Map results back to owner/repo format
                    for i, (owner, repo) in enumerate(chunk):
                        alias = f"repo_{i}"
                        if data.get("data", {}).get(alias):
                            repo_data = data["data"][alias]
                            results[f"{owner}/{repo}"] = {
                                "created_at": repo_data.get("createdAt"),
                                "pushed_at": repo_data.get("pushedAt"),
                                "homepage": repo_data.get("homepageUrl"),
                            }
                        else:
                            # Repo might not exist or be inaccessible
                            pass
                    break  # Success, exit retry loop
                    
                elif response.status_code == 502 or response.status_code == 503:
                    # Server error, retry
                    if retry < max_retries - 1:
                        wait_time = 2 ** retry
                        typer.echo(f"Server error {response.status_code}, retrying in {wait_time}s...")
                        time.sleep(wait_time)
                    else:
                        typer.echo(f"GraphQL API error after {max_retries} retries: {response.status_code}", err=True)
                        raise typer.Exit(1)
                else:
                    typer.echo(f"GraphQL API error: {response.status_code}", err=True)
                    if response.text:
                        typer.echo(response.text[:500], err=True)  # Limit error output
                    raise typer.Exit(1)
                    
            except (requests.exceptions.Timeout, requests.exceptions.ConnectionError) as e:
                if retry < max_retries - 1:
                    wait_time = 2 ** retry
                    typer.echo(f"Connection error, retrying in {wait_time}s... (attempt {retry + 1}/{max_retries})")
                    time.sleep(wait_time)
                else:
                    typer.echo(f"Failed to fetch batch after {max_retries} retries: {e}", err=True)
                    raise typer.Exit(1)
    
    return results


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
        # Check if this is a full repository URL or has a path
        full_repo_match = re.search(
            r'doap:repository\s+"https://github\.com/([^/]+)/([^/\"]+)(/[^\"]+)?"\s*;',
            project
        )
        if full_repo_match:
            title_match = TITLE_PATTERN.search(project)
            title = title_match.group(1) if title_match else "Unknown"
            has_path = bool(full_repo_match.group(3))  # True if there's a path after repo name
            projects_with_repos.append(
                {
                    "content": project,
                    "owner": full_repo_match.group(1),
                    "repo": full_repo_match.group(2),
                    "title": title,
                    "is_monorepo_path": has_path,  # Flag for monorepo subdirectories
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
    """Update projects using GraphQL API for batch fetching."""
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

    # Prepare list of repos to fetch
    repos_to_fetch = [(p["owner"], p["repo"]) for p in all_projects]
    
    typer.echo(f"Fetching data for {len(repos_to_fetch)} repositories via GraphQL...")
    repo_data = fetch_repositories_graphql(repos_to_fetch, headers)
    typer.echo(f"Retrieved data for {len(repo_data)} repositories.")

    with open(TTL_FILE_PATH, "r", encoding="utf-8") as f:
        ttl_content = f.read()

    updated_count = 0
    updates: List[ProjectUpdate] = []

    for project in all_projects:
        owner = project["owner"]
        repo = project["repo"]
        repo_key = f"{owner}/{repo}"
        
        if repo_key not in repo_data:
            typer.echo(f"Warning: No data for {repo_key}")
            continue
            
        repo_info = repo_data[repo_key]
        updated_content = project["content"]
        changes = []

        # Skip date updates for monorepo subdirectories
        if update_dates and not project.get("is_monorepo_path", False):
            before = updated_content
            updated_content = update_project_dates(
                {"content": updated_content}, repo_info
            )
            if updated_content != before:
                changes.append("dates")
        elif update_dates and project.get("is_monorepo_path", False):
            typer.echo(f"Skipping date update for {repo_key} (monorepo subdirectory)")

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
                    "repo": repo_key,
                    "original": project["content"],
                    "updated": updated_content,
                    "changes": changes,
                }
            )
            if not dry_run:
                ttl_content = ttl_content.replace(project["content"], updated_content)
            updated_count += 1

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