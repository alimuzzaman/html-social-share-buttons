// playwright.config.ts
import { defineConfig } from '@playwright/test';

// Define the source for the dynamically resolved BASE_URL
const dynamicBaseUrl = process.env.WP_PLAYGROUND_URL || 'http://localhost:3000';

export default defineConfig({
  use: {
    // Base URL will be set by the setup process
    baseURL: dynamicBaseUrl,
  },
  projects: [
    // Setup project that prepares the Playground server
    {
      name: 'setup',
      testMatch: /.*\.setup\.ts$/,
    },
    // Browser projects depend on the `setup` project
    {
      name: 'chromium',
      dependencies: ['setup'],
      use: { browserName: 'chromium' },
    },
    {
      name: 'firefox',
      dependencies: ['setup'],
      use: { browserName: 'firefox' },
    },
    // ... other browser projects
  ],
});