# Scripts Documentation

This document describes the maintenance scripts available in the project for managing project data and site content.

## update_projects.py

The `update_projects.py` script provides utilities for automatically updating project metadata in the `src/data/projects.ttl` file based on GitHub repository data.

### Prerequisites

- **Python 3.12+** required
- **GitHub Token** (optional but recommended): Set the `GITHUB_TOKEN` environment variable to avoid rate limits
- **Dependencies**: The script uses [uv](https://github.com/astral-sh/uv) to automatically manage dependencies

### Available Commands

#### 1. Update Project Dates (`dates`)

Updates `schema:dateCreated` and `schema:dateModified` fields for projects based on GitHub repository data.

```bash
# Update dates for all projects with GitHub repositories
scripts/update_projects.py dates

# Update dates for specific projects (by name)
scripts/update_projects.py dates "Liquid Template Engine"

# Update dates for specific projects (by repository)
scripts/update_projects.py dates osteele/liquid

# Update dates for specific projects (by GitHub URL)
scripts/update_projects.py dates https://github.com/osteele/liquid

# Update multiple projects at once
scripts/update_projects.py dates "Gojekyll" "p5-server"

# Preview changes without writing to file (dry run)
scripts/update_projects.py dates --dry-run

# Update dates ONLY for contributions to others' monorepos
scripts/update_projects.py dates --contributions-only

# Update dates for a specific contribution
scripts/update_projects.py dates "Raycast ArXiv Extension" --contributions-only --dry-run
```

**Data Updated:**
- `schema:dateCreated`: Set from GitHub repository's creation date (`created_at`)
- `schema:dateModified`: Set from GitHub repository's last push date (`pushed_at`)

**Monorepo Handling:**
- **Default behavior**: Skips contributions to others' monorepos (e.g., raycast/extensions)
- **With `--contributions-only`**: Updates ONLY contributions to others' monorepos
- For monorepo paths, fetches commit history specific to that subdirectory

#### 2. Update Project URLs (`url`)

Updates the `schema:url` field for projects based on the GitHub repository's homepage setting.

```bash
# Update homepage URLs for all projects
scripts/update_projects.py url

# Update URL for specific project
scripts/update_projects.py url "Liquid Template Engine"

# Preview URL changes (dry run)
scripts/update_projects.py url --dry-run
```

**Data Updated:**
- `schema:url`: Set from GitHub repository's homepage field (if configured)

#### 3. Update Both Dates and URLs (`all`)

Combines the functionality of both `dates` and `url` commands.

```bash
# Update both dates and URLs for all projects
scripts/update_projects.py all

# Update both for specific projects
scripts/update_projects.py all "Gojekyll" "Liquid Template Engine"

# Preview all changes (dry run)
scripts/update_projects.py all --dry-run

# Update both dates and URLs for contributions only
scripts/update_projects.py all --contributions-only
```

#### 4. List Projects (`list`)

Display all projects that have GitHub repositories configured in the TTL file.

```bash
# List all projects with GitHub repositories
scripts/update_projects.py list
```

### Usage Patterns

#### Update All Projects
The most common use case is updating dates for all projects to keep modification times current:

```bash
# Update all project modification times
scripts/update_projects.py dates

# Or update both dates and URLs
scripts/update_projects.py all
```

#### Selective Updates
Update only specific projects by using various identification methods:

```bash
# By project title (partial matching, case-insensitive)
scripts/update_projects.py dates "liquid"

# By GitHub repository owner/name
scripts/update_projects.py dates osteele/liquid

# By full GitHub URL
scripts/update_projects.py dates https://github.com/osteele/liquid
```

#### Safe Preview Mode
Always use `--dry-run` first to preview changes before applying them:

```bash
# Preview what would be updated
scripts/update_projects.py dates --dry-run

# If the changes look correct, run without --dry-run
scripts/update_projects.py dates
```

### Working with Monorepos

The script intelligently handles projects that are part of monorepos (repositories containing multiple projects in subdirectories).

#### Monorepo Project Configuration

In your TTL file, monorepo projects should have repository URLs that include the path to the specific subdirectory:

```turtle
os:raycast-arxiv a doap:Project ;
    dc:title "Raycast ArXiv Extension" ;
    doap:repository "https://github.com/raycast/extensions/tree/main/extensions/arxiv" ;
    os:contribution [
        os:pullRequest "https://github.com/raycast/extensions/pull/21033" ;
    ] .
```

#### Default Behavior: Skip Contributions

By default, the script **skips** updating dates for contributions to others' monorepos:

```bash
# This will update your own projects but skip contributions like raycast/extensions
scripts/update_projects.py dates

# Output will show:
# Skipping 1 contribution(s) to others' monorepos (use --contributions-only to update these).
```

#### Update Contributions Only

To update dates for your contributions to others' monorepos, use the `--contributions-only` flag:

```bash
# Update ALL contributions to others' monorepos
scripts/update_projects.py dates --contributions-only

# Update a specific contribution
scripts/update_projects.py dates "Raycast ArXiv Extension" --contributions-only

# Preview what would be updated
scripts/update_projects.py dates --contributions-only --dry-run
```

#### How It Works

1. **Ownership Detection**: The script determines if a monorepo is yours or someone else's by:
   - Checking if the repository owner matches your GitHub username
   - Looking for `os:contribution` blocks in the project definition

2. **Path-Specific History**: For monorepo subdirectories, the script:
   - Extracts the path from URLs like `github.com/owner/repo/tree/main/path/to/project`
   - Fetches commit history specific to that path only
   - For contributions, attempts to filter commits by your author email (requires GITHUB_TOKEN)

3. **Date Updates**:
   - **For your own projects**: Both `dateCreated` and `dateModified` are updated
   - **For contributions** (`--contributions-only`):
     - `dateCreated`: **NOT updated** (preserves your first contribution date)
     - `dateModified`: Updated to the most recent commit in the subdirectory

#### Examples

```bash
# Scenario 1: You have your own monorepo with multiple projects
# These will be updated by default
scripts/update_projects.py dates

# Scenario 2: You contributed to raycast/extensions
# These need the --contributions-only flag
scripts/update_projects.py dates --contributions-only

# Scenario 3: Update a specific contribution with preview
scripts/update_projects.py dates "Raycast ArXiv Extension" --contributions-only --dry-run

# Scenario 4: Update everything (your repos + contributions)
# Run both commands:
scripts/update_projects.py dates                    # Your projects
scripts/update_projects.py dates --contributions-only  # Your contributions
```

### Rate Limiting and Performance

- The script uses GitHub's GraphQL API for efficient batch fetching
- Fetches up to 30 repositories per request to minimize API calls
- With a GitHub token, you get 5,000 requests per hour
- Without a token: Limited functionality and may hit rate limits quickly
- Automatic retry logic for network errors and server issues
- For monorepo contributions, fetches up to 100 commits per path to find earliest contribution

### Project Matching

The script matches projects using flexible criteria:

1. **By Title**: Case-insensitive substring matching against project titles
2. **By Repository**: Matches `owner/repo` format against GitHub repositories
3. **By URL**: Matches full GitHub URLs (with or without trailing slashes)

### Output and Logging

The script provides detailed feedback:

```
Parsing src/data/projects.ttl...
Found 45 projects with GitHub repositories.
Filtered to 1 projects matching filter(s), skipping 44.
Fetching data for osteele/liquid...
Updated 1 project(s) in src/data/projects.ttl.
```

For dry runs, it shows exactly what would be changed:

```
DRY RUN: The following changes would be made:

Repository: osteele/liquid
  dateModified: 2024-01-15T10:30:00Z -> 2024-12-20T15:45:00Z

Total: 1 projects would be updated.
```

### Error Handling

The script handles common error scenarios:

- **Repository not found (404)**: Skips the project with a warning
- **Rate limits (403)**: Automatically waits and retries
- **API errors**: Reports the error and continues with other projects
- **File parsing errors**: Reports syntax errors in the TTL file

### Integration with Site Workflow

The script is designed to be run as part of regular site maintenance:

1. **After adding new repositories**: Run `scripts/update_projects.py all` to populate initial dates and URLs
2. **Regular updates**: Run `scripts/update_projects.py dates` periodically to keep modification times current
3. **Before publishing**: Use `--dry-run` to verify changes before committing updates

### Technical Details

- **TTL Parsing**: Uses regex patterns to identify and update project entries
- **Date Format**: Preserves ISO 8601 datetime format with timezone information
- **Backup Safety**: The script reads the entire file, makes changes in memory, and writes atomically
- **Unicode Support**: Properly handles UTF-8 encoded TTL files

### Troubleshooting

#### GitHub Token Issues
```bash
# Set your GitHub token
export GITHUB_TOKEN="your_token_here"

# Verify it's set
scripts/update_projects.py list
```

#### No Projects Found
If no projects are found, check that:
1. The TTL file exists at `src/data/projects.ttl`
2. Projects have `doap:repository` fields with GitHub URLs
3. The TTL syntax is valid

#### Partial Updates
If some projects aren't being updated:
1. Verify the repository exists and is public
2. Check that the GitHub URL in the TTL file is correct
3. Use `--dry-run` to see what the script detects

#### Monorepo Issues

**Contributions not updating:**
- Make sure to use the `--contributions-only` flag for contributions to others' repos
- Verify the path in the URL is correct (e.g., `/tree/main/extensions/arxiv`)
- Check that your GITHUB_TOKEN is set for author filtering

**Wrong dates for monorepo projects:**
- The script fetches dates for the specific subdirectory, not the entire repo
- If no commits are found for a path, check the path is correct
- Use `--dry-run` to see what commits are being detected

**"Skipping X contribution(s)" message:**
- This is normal behavior - contributions are skipped by default
- Use `scripts/update_projects.py dates --contributions-only` to update them

### Related Documentation

- [Projects Data Format](./projects-data-format.md) - Details on the TTL file structure
- [Technical Documentation](./technical-documentation.md) - Overall site architecture
