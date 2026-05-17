# osteele.com — Oliver Steele's personal site

# List available commands
default:
    @just --list

# Install dependencies
install:
    bun install

# Dev server with hot reload
dev:
    mise run dev

# Build the static site into dist/
build:
    mise run build

# Build the production site
build-prod:
    mise run build-prod

# Preview the built site locally
preview:
    mise run preview

# Run all checks
check:
    mise run check

# Run linting
lint:
    mise run lint

# Run TypeScript type checking
typecheck:
    mise run typecheck

# Run all tests
test:
    mise run test

# Run unit tests only
test-unit:
    mise run test-unit

# Run integration tests only
test-integration:
    mise run test-integration

# Format source files
format:
    mise run format

# Fix common lint and formatting issues
fix:
    mise run fix

# Remove generated build output
clean:
    mise run clean

# Check internal links only
check-internal-links:
    mise run check-internal-links

# Check external links only
check-external-links:
    mise run check-external-links

# Update project metadata from GitHub
update-projects:
    mise run update-projects

# Update project dates from GitHub
update-project-dates:
    mise run update-project-dates

# Add a thumbnail to a project
add-project-thumbnail:
    mise run add-project-thumbnail
