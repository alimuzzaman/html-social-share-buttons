import { test, expect } from '@playwright/test';

/**
 * Visual Regression Test Suite - Iconsets
 * Tests all iconset variations for pixel-perfect rendering
 */

test.describe('Iconset Variations - Visual Regression', () => {
  
  test('Default Iconset - Square', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=default&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const buttons = page.locator('.zmshbt.in_shortcode.default.square');
    await expect(buttons).toHaveScreenshot('iconset-default-square.png', {
      maxDiffPixels: 10,
    });
  });

  test('Default Iconset - Circle', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=default&type=circle');
    await page.waitForSelector('.zmshbt.in_shortcode.default.circle');
    
    const buttons = page.locator('.zmshbt.in_shortcode.default.circle');
    await expect(buttons).toHaveScreenshot('iconset-default-circle.png', {
      maxDiffPixels: 10,
    });
  });

  test('Flat Iconset - Square', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=flat&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.flat.square');
    
    const buttons = page.locator('.zmshbt.in_shortcode.flat.square');
    await expect(buttons).toHaveScreenshot('iconset-flat-square.png', {
      maxDiffPixels: 10,
    });
  });

  test('Flat Iconset - Circle', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=flat&type=circle');
    await page.waitForSelector('.zmshbt.in_shortcode.flat.circle');
    
    const buttons = page.locator('.zmshbt.in_shortcode.flat.circle');
    await expect(buttons).toHaveScreenshot('iconset-flat-circle.png', {
      maxDiffPixels: 10,
    });
  });

  test('Long Shadow Iconset - Square', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=long_shadow&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.long_shadow.square');
    
    const buttons = page.locator('.zmshbt.in_shortcode.long_shadow.square');
    await expect(buttons).toHaveScreenshot('iconset-long-shadow-square.png', {
      maxDiffPixels: 10,
    });
  });

  test('Long Shadow Iconset - Circle', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=long_shadow&type=circle');
    await page.waitForSelector('.zmshbt.in_shortcode.long_shadow.circle');
    
    const buttons = page.locator('.zmshbt.in_shortcode.long_shadow.circle');
    await expect(buttons).toHaveScreenshot('iconset-long-shadow-circle.png', {
      maxDiffPixels: 10,
    });
  });

  test('Prajin Iconset - Square', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=prajin&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.prajin.square');
    
    const buttons = page.locator('.zmshbt.in_shortcode.prajin.square');
    await expect(buttons).toHaveScreenshot('iconset-prajin-square.png', {
      maxDiffPixels: 10,
    });
  });

  test('Prajin Iconset - Circle', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=prajin&type=circle');
    await page.waitForSelector('.zmshbt.in_shortcode.prajin.circle');
    
    const buttons = page.locator('.zmshbt.in_shortcode.prajin.circle');
    await expect(buttons).toHaveScreenshot('iconset-prajin-circle.png', {
      maxDiffPixels: 10,
    });
  });
});

test.describe('Iconset Hover Effects', () => {
  
  test('Default - Hover Scale Effect', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=default&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    // Hover over Facebook icon
    const fbIcon = page.locator('.zmshbt.in_shortcode.default.square a.facebook');
    await fbIcon.hover();
    await page.waitForTimeout(300); // Wait for transition
    
    await expect(fbIcon).toHaveScreenshot('iconset-default-hover.png', {
      maxDiffPixels: 10,
    });
  });

  test('Flat - Hover Scale Effect', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=flat&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.flat.square');
    
    const fbIcon = page.locator('.zmshbt.in_shortcode.flat.square a.facebook');
    await fbIcon.hover();
    await page.waitForTimeout(300);
    
    await expect(fbIcon).toHaveScreenshot('iconset-flat-hover.png', {
      maxDiffPixels: 10,
    });
  });
});

test.describe('All Networks - Visual Test', () => {
  
  test('All Networks - Default Square', async ({ page }) => {
    // Test page with all networks enabled
    await page.goto('/all-networks-test/?iconset=default&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const buttons = page.locator('.zmshbt.in_shortcode.default.square');
    
    // Verify all networks are present
    await expect(buttons.locator('a.facebook')).toBeVisible();
    await expect(buttons.locator('a.twitter')).toBeVisible();
    await expect(buttons.locator('a.linkedin')).toBeVisible();
    await expect(buttons.locator('a.pinterest')).toBeVisible();
    await expect(buttons.locator('a.googlepluse')).toBeVisible();
    await expect(buttons.locator('a.mail')).toBeVisible();
    
    await expect(buttons).toHaveScreenshot('all-networks-default-square.png', {
      maxDiffPixels: 10,
    });
  });
});

test.describe('Icon Sizing and Spacing', () => {
  
  test('Icon Dimensions - Default', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=default&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square a.facebook');
    
    const fbIcon = page.locator('.zmshbt.in_shortcode.default.square a.facebook');
    
    // Check that icon has correct dimensions (32x32 typically)
    const box = await fbIcon.boundingBox();
    expect(box?.width).toBeGreaterThan(28);
    expect(box?.width).toBeLessThan(36);
    expect(box?.height).toBeGreaterThan(28);
    expect(box?.height).toBeLessThan(36);
  });

  test('Icon Spacing - Inline Display', async ({ page }) => {
    await page.goto('/shortcode-test/?iconset=default&type=square');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const buttons = page.locator('.zmshbt.in_shortcode.default.square');
    
    // Icons should have margin between them
    const firstIcon = buttons.locator('a').first();
    const secondIcon = buttons.locator('a').nth(1);
    
    const firstBox = await firstIcon.boundingBox();
    const secondBox = await secondIcon.boundingBox();
    
    // There should be some space between icons (margin)
    if (firstBox && secondBox) {
      const gap = secondBox.x - (firstBox.x + firstBox.width);
      expect(gap).toBeGreaterThan(0);
    }
  });
});
