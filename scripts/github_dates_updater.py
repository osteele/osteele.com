#!/usr/bin/env python3
"""
GitHub Repository Date Updater

This script updates the creation and modification dates in a TTL file based on
GitHub repository data.

Example usage:
# Update a specific project by name
just update-project-dates "Liquid Template Engine"

# Update by repository path
just update-project-dates osteele/liquid

# Update by GitHub URL
just update-project-dates https://github.com/osteele/liquid

# Update multiple projects
just update-project-dates "Gojekyll" "p5-server"

# Show changes without writing (dry run)
just update-project-dates --dry-run "Liquid Template Engine"
"""

import argparse
import os
import re
import time
from typing import Any, Dict, List, Match, Optional, TypedDict

import requests

# GitHub API constants
GITHUB_API_URL = "https://api.github.com"
GITHUB_TOKEN = os.environ.get("GITHUB_TOKEN")

# File paths
TTL_FILE_PATH = "src/data/projects.ttl"

# Regular expressions for parsing TTL file
REPO_PATTERN = re.compile(
    r'doap:repository\s+"https://github\.com/([^/]+)/([^"]+)"\s+;'
)
TITLE_PATTERN = re.compile(r'dc:title\s+"([^"]+)"\s+;')
DATE_CREATED_PATTERN = re.compile(r'schema:dateCreated\s+"([^"]+)"\s+;')
DATE_MODIFIED_PATTERN = re.compile(r'schema:dateModified\s+"([^"]+)"\s+;')


# Type definitions
class ProjectUpdate(TypedDict):
    """Type for tracking project updates."""

    repo: str
    original: str
    updated: str


def setup_github_headers() -> Dict[str, str]:
    """Set up the headers for GitHub API requests."""
    headers = {
        "Accept": "application/vnd.github.v3+json",
    }
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
        print(f"Repository not found: {owner}/{repo}")
        return None
    elif response.status_code == 403 and "rate limit" in response.text.lower():
        print("Rate limit exceeded. Waiting for 60 seconds...")
        time.sleep(60)  # Wait for a minute before retrying
        return get_repository_info(owner, repo, headers)
    else:
        print(f"Error fetching repository {owner}/{repo}: {response.status_code}")
        print(response.text)
        return None


def parse_ttl_file(file_path: str) -> List[Dict[str, str]]:
    """Parse TTL file and extract projects with GitHub repositories."""
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()

    # Split the content into projects
    projects: List[str] = []
    current_project: List[str] = []

    for line in content.split("\n"):
        if line.strip().startswith("os:") and "a doap:Project" in line:
            if current_project:  # Save the previous project
                projects.append("\n".join(current_project))
            current_project = [line]
        elif current_project:  # Add lines to the current project
            current_project.append(line)

    # Add the last project
    if current_project:
        projects.append("\n".join(current_project))

    # Filter projects with GitHub repositories
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

    # Extract creation date (created_at) and update date (pushed_at) from GitHub
    created_at = repo_info.get("created_at")
    pushed_at = repo_info.get("pushed_at")

    if created_at:
        # Update dateCreated
        if DATE_CREATED_PATTERN.search(content):
            content = DATE_CREATED_PATTERN.sub(
                f'schema:dateCreated "{created_at}" ;', content
            )

    if pushed_at:
        # Update dateModified
        if DATE_MODIFIED_PATTERN.search(content):
            content = DATE_MODIFIED_PATTERN.sub(
                f'schema:dateModified "{pushed_at}" ;', content
            )

    return content


def matches_filter(project: Dict[str, str], filters: List[str]) -> bool:
    """Check if a project matches any of the specified filters."""
    if not filters:
        return True  # No filters means match all projects

    for filter_str in filters:
        # Check if filter matches the project title (case-insensitive)
        if filter_str.lower() in project["title"].lower():
            return True

        # Check if filter matches the repository name
        repo_name = f"{project['owner']}/{project['repo']}"
        if filter_str in repo_name:
            return True

        # Check if filter is a GitHub URL that matches the repo
        if filter_str.startswith("https://github.com/"):
            clean_url = filter_str.rstrip("/")
            clean_repo = f"https://github.com/{project['owner']}/{project['repo']}"
            if clean_url == clean_repo:
                return True

    return False


def parse_args() -> argparse.Namespace:
    """Parse command line arguments."""
    parser = argparse.ArgumentParser(
        description="Update TTL file with GitHub repository dates."
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Show changes without writing to file",
    )
    parser.add_argument(
        "projects",
        nargs="*",
        help="Specific projects to update (by name, owner/repo, or GitHub URL). If none provided, updates all projects.",
    )
    return parser.parse_args()


def main() -> None:
    """Main function to update TTL file with GitHub repository dates."""
    args = parse_args()

    # Check for GitHub token
    if not GITHUB_TOKEN:
        print(
            "Warning: GITHUB_TOKEN environment variable not set. Rate limits may apply."
        )

    # Set up GitHub API headers
    headers = setup_github_headers()

    # Parse TTL file
    print(f"Parsing {TTL_FILE_PATH}...")
    projects = parse_ttl_file(TTL_FILE_PATH)
    print(f"Found {len(projects)} projects with GitHub repositories.")

    # Filter projects if specific ones were requested
    if args.projects:
        filtered_projects = [p for p in projects if matches_filter(p, args.projects)]
        filtered_out = len(projects) - len(filtered_projects)
        projects = filtered_projects
        print(
            f"Filtered to {len(projects)} projects matching filter(s), skipping {filtered_out}."
        )

    # Read the entire file
    with open(TTL_FILE_PATH, "r", encoding="utf-8") as f:
        ttl_content = f.read()

    # Update projects with GitHub data
    updated_count = 0
    updates: List[ProjectUpdate] = []

    for project in projects:
        owner = project["owner"]
        repo = project["repo"]

        print(f"Fetching data for {owner}/{repo}...")
        repo_info = get_repository_info(owner, repo, headers)

        if repo_info:
            updated_content = update_project_dates(project, repo_info)

            # Track changes
            if updated_content != project["content"]:
                updates.append(
                    {
                        "repo": f"{owner}/{repo}",
                        "original": project["content"],
                        "updated": updated_content,
                    }
                )

                # Replace in the content if not in dry-run mode
                if not args.dry_run:
                    ttl_content = ttl_content.replace(
                        project["content"], updated_content
                    )

                updated_count += 1

        # Sleep to avoid hitting rate limits
        time.sleep(1)

    # In dry-run mode, show what would change
    if args.dry_run:
        print("\nDRY RUN: The following changes would be made:")
        for update in updates:
            print(f"\nRepository: {update['repo']}")

            # Extract dates from content for cleaner output
            orig_created: Optional[Match[str]] = DATE_CREATED_PATTERN.search(
                update["original"]
            )
            orig_modified: Optional[Match[str]] = DATE_MODIFIED_PATTERN.search(
                update["original"]
            )
            new_created: Optional[Match[str]] = DATE_CREATED_PATTERN.search(
                update["updated"]
            )
            new_modified: Optional[Match[str]] = DATE_MODIFIED_PATTERN.search(
                update["updated"]
            )

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

        print(f"\nTotal: {updated_count} projects would be updated.")
    else:
        if updated_count > 0:
            # Write changes directly back to the original file
            with open(TTL_FILE_PATH, "w", encoding="utf-8") as f:
                f.write(ttl_content)
            print(f"Updated {updated_count} projects directly in {TTL_FILE_PATH}")
        else:
            print("No changes were needed for any projects.")


if __name__ == "__main__":
    main()
