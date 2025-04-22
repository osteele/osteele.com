#!/usr/bin/env -S uv --quiet run --script
# /// script
# requires-python = ">=3.13"
# dependencies = [
#     "click",
#     "requests",
# ]
# ///
#
"""
Update Projects Utility

This script provides project update utilities for the site. Use the --dates flag to update
creation and modification dates in the TTL file based on GitHub data.

Example usage:
# Update a specific project by name
you@host$ just update-projects "Liquid Template Engine" --dates

# Update by repository path
you@host$ just update-projects osteele/liquid --dates

# Update by GitHub URL
you@host$ just update-projects https://github.com/osteele/liquid --dates

# Update multiple projects
you@host$ just update-projects "Gojekyll" "p5-server" --dates

# Show changes without writing (dry run)
you@host$ just update-projects "Liquid Template Engine" --dates --dry-run
"""

import os
import re
import sys
import time
from typing import Any, Dict, List, Match, Optional, TypedDict

import click
import requests

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


def setup_github_headers() -> Dict[str, str]:
    headers = {"Accept": "application/vnd.github.v3+json"}
    if GITHUB_TOKEN:
        headers["Authorization"] = f"token {GITHUB_TOKEN}"
    return headers


def get_repository_info(
    owner: str, repo: str, headers: Dict[str, str]
) -> Optional[Dict[str, Any]]:
    url = f"{GITHUB_API_URL}/repos/{owner}/{repo}"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        return response.json()
    elif response.status_code == 404:
        print(f"Repository not found: {owner}/{repo}")
        return None
    elif response.status_code == 403 and "rate limit" in response.text.lower():
        print("Rate limit exceeded. Waiting for 60 seconds...")
        time.sleep(60)
        return get_repository_info(owner, repo, headers)
    else:
        print(f"Error fetching repository {owner}/{repo}: {response.status_code}")
        print(response.text)
        return None


def parse_ttl_file(file_path: str) -> List[Dict[str, str]]:
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


def matches_filter(project: Dict[str, str], filters: List[str]) -> bool:
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


def update_projects_main(projects, update_dates=False, update_url=False, dry_run=False):
    if not GITHUB_TOKEN:
        print(
            "Warning: GITHUB_TOKEN environment variable not set. Rate limits may apply."
        )
    headers = setup_github_headers()
    print(f"Parsing {TTL_FILE_PATH}...")
    all_projects = parse_ttl_file(TTL_FILE_PATH)
    print(f"Found {len(all_projects)} projects with GitHub repositories.")
    if projects:
        filtered_projects = [p for p in all_projects if matches_filter(p, projects)]
        filtered_out = len(all_projects) - len(filtered_projects)
        all_projects = filtered_projects
        print(
            f"Filtered to {len(all_projects)} projects matching filter(s), skipping {filtered_out}."
        )
    with open(TTL_FILE_PATH, "r", encoding="utf-8") as f:
        ttl_content = f.read()
    updated_count = 0
    updates: List[ProjectUpdate] = []
    for project in all_projects:
        owner = project["owner"]
        repo = project["repo"]
        print(f"Fetching data for {owner}/{repo}...")
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
                homepage = repo_info.get("homepage")
                if homepage:
                    before = updated_content
                    if SCHEMA_URL_PATTERN.search(updated_content):
                        updated_content = SCHEMA_URL_PATTERN.sub(
                            f'schema:url "{homepage}" ;', updated_content
                        )
                    else:
                        # Insert after title or at end of block
                        title_match = TITLE_PATTERN.search(updated_content)
                        insert_idx = (
                            title_match.end() if title_match else len(updated_content)
                        )
                        updated_content = (
                            updated_content[:insert_idx]
                            + f'\n    schema:url "{homepage}" ;'
                            + updated_content[insert_idx:]
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
        time.sleep(1)
    if dry_run:
        print("\nDRY RUN: The following changes would be made:")
        for update in updates:
            print(f"\nRepository: {update['repo']}")
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
                    print(
                        f"  dateCreated: {orig_created.group(1)} -> {new_created.group(1)}"
                    )
                if (
                    orig_modified
                    and new_modified
                    and orig_modified.group(1) != new_modified.group(1)
                ):
                    print(
                        f"  dateModified: {orig_modified.group(1)} -> {new_modified.group(1)}"
                    )
            if "url" in update["changes"]:
                orig_url = SCHEMA_URL_PATTERN.search(update["original"])
                new_url = SCHEMA_URL_PATTERN.search(update["updated"])
                if (orig_url and new_url and orig_url.group(1) != new_url.group(1)) or (
                    not orig_url and new_url
                ):
                    print(
                        f"  schema:url: {orig_url.group(1) if orig_url else None} -> {new_url.group(1)}"
                    )
    elif updates:
        with open(TTL_FILE_PATH, "w", encoding="utf-8") as f:
            f.write(ttl_content)
        print(f"\nUpdated {updated_count} project(s) in {TTL_FILE_PATH}.")
    else:
        print("No changes made.")


def update_dates_main(projects, dry_run=False):
    update_projects_main(projects, update_dates=True, update_url=False, dry_run=dry_run)


@click.command(context_settings={"ignore_unknown_options": True})
@click.argument("projects", nargs=-1)
@click.option("--dates", is_flag=True, help="Update project dates from GitHub.")
@click.option("--url", is_flag=True, help="Update schema:url from GitHub homepage.")
@click.option("--dry-run", is_flag=True, help="Show changes without writing.")
def main(projects, dates, url, dry_run):
    """Update project metadata. Use --dates and/or --url to update from GitHub."""
    if dates or url:
        update_projects_main(
            list(projects), update_dates=dates, update_url=url, dry_run=dry_run
        )
    else:
        click.echo(
            "No operation specified. Use --dates and/or --url to update project metadata from GitHub."
        )


if __name__ == "__main__":
    main()
