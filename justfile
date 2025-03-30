default:
    @just --list

# Format code
format:
    bun run format

# Run all checks
check: lint typecheck test

# Fix common lint errors
fix:
    bun run fix

# Run linting
lint:
    bun run lint

test:
    bun test

# Run type checking
typecheck:
    bun run typecheck
