import { test, expect } from '@playwright/test';

test('setup playground server', async ({ page }) => {
  // This test runs first and ensures the server is ready
  // The global setup should have already started the server
  await page.goto('/');
  await expect(page).toHaveTitle(/WordPress/);
});