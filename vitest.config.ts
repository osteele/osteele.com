import { defineConfig } from 'vitest/config';

export default defineConfig({
  test: {
    // Enable parallel test execution
    pool: 'threads',
    poolOptions: {
      threads: {
        // Adjust based on your machine's capabilities
        singleThread: false,
      },
    },
    // Increase timeout for integration tests but not too much
    testTimeout: 30000,
    // Coverage configuration
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/**',
        'dist/**',
        '**/*.test.ts',
        '**/*.config.*',
        '**/types/**',
      ],
    },
  },
});
