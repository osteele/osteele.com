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
  },
});
