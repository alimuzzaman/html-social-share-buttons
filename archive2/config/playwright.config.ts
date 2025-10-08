// playwright.config.ts
import { defineConfig } from '@playwright/test';

// Define the source for the dynamically resolved BASE_URL
const dynamicBaseUrl = process.env.WP_BASE_URL || 'http://hssb.test';

export default defineConfig({
  use: {
    // Base URL will be set by the setup process
    baseURL: dynamicBaseUrl,
  },
  projects: [
    {
      name: 'chromium',
      use: { browserName: 'chromium' },
    },
    {
      name: 'firefox',
      use: { browserName: 'firefox' },
    },
    // ... other browser projects
  ],
});