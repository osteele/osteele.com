default:
    @just --list

# Build app
build:
    bun run build

# Build production app
build-prod:
    bun run build:prod

# Run all checks
check: lint typecheck test

# Clean up
clean:
    bun run clean

# Start development server
dev:
    bun run dev

# Fix common lint errors
fix:
    bun run fix

# Format code
format:
    bun run format

# Run linting
lint:
    bun run lint

# Run all tests
test:
    bun test

# Run only unit tests (faster)
test-unit:
    bun test --pattern "**/*.test.ts" --ignore "**/integration.test.ts"

# Run only integration tests
test-integration:
    bun test "./src/lib/integration.test.ts"

# Run type checking
typecheck:
    bun run typecheck
