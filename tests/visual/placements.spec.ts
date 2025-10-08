import { test, expect } from '@playwright/test';

/**
 * Visual Regression Test Suite - Placements
 * Tests all button placement variations for pixel-perfect rendering
 */

test.describe('Button Placements - Visual Regression', () => {
  
  test.beforeEach(async ({ page }) => {
    // Set up test page with the plugin active
    await page.goto('/wp-admin/post-new.php');
  });

  test('Left Placement - Default Square', async ({ page }) => {
    // Navigate to a page with left placement enabled
    await page.goto('/?left-placement=1&iconset=default&type=square');
    
    // Wait for buttons to render
    await page.waitForSelector('.zmshbt.left.default.square');
    
    // Take screenshot of left placement
    const leftButtons = page.locator('.zmshbt.left.default.square');
    await expect(leftButtons).toHaveScreenshot('left-placement-default-square.png', {
      maxDiffPixels: 10,
    });
  });

  test('Left Placement - Hover State', async ({ page }) => {
    await page.goto('/?left-placement=1&iconset=default&type=square');
    await page.waitForSelector('.zmshbt.left.default.square');
    
    const leftButtons = page.locator('.zmshbt.left.default.square');
    
    // Hover over the buttons to trigger auto-show
    await leftButtons.hover();
    
    // Wait for transition
    await page.waitForTimeout(300);
    
    await expect(leftButtons).toHaveScreenshot('left-placement-hover.png', {
      maxDiffPixels: 10,
    });
  });

  test('Right Placement - Default Square', async ({ page }) => {
    await page.goto('/?right-placement=1&iconset=default&type=square');
    await page.waitForSelector('.zmshbt.right.default.square');
    
    const rightButtons = page.locator('.zmshbt.right.default.square');
    await expect(rightButtons).toHaveScreenshot('right-placement-default-square.png', {
      maxDiffPixels: 10,
    });
  });

  test('Right Placement - Hover State', async ({ page }) => {
    await page.goto('/?right-placement=1&iconset=default&type=square');
    await page.waitForSelector('.zmshbt.right.default.square');
    
    const rightButtons = page.locator('.zmshbt.right.default.square');
    await rightButtons.hover();
    await page.waitForTimeout(300);
    
    await expect(rightButtons).toHaveScreenshot('right-placement-hover.png', {
      maxDiffPixels: 10,
    });
  });

  test('Before Post Placement', async ({ page }) => {
    // Go to a post with before_post placement
    await page.goto('/sample-post/?before-post=1');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const beforeButtons = page.locator('.zmshbt.in_shortcode.default.square').first();
    await expect(beforeButtons).toHaveScreenshot('before-post-placement.png', {
      maxDiffPixels: 10,
    });
  });

  test('After Post Placement', async ({ page }) => {
    await page.goto('/sample-post/?after-post=1');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const afterButtons = page.locator('.zmshbt.in_shortcode.default.square').last();
    await expect(afterButtons).toHaveScreenshot('after-post-placement.png', {
      maxDiffPixels: 10,
    });
  });

  test('Widget Placement - Sidebar', async ({ page }) => {
    await page.goto('/');
    await page.waitForSelector('.zmshbt.in_widget.default.square');
    
    const widgetButtons = page.locator('.zmshbt.in_widget.default.square');
    await expect(widgetButtons).toHaveScreenshot('widget-placement.png', {
      maxDiffPixels: 10,
    });
  });

  test('Shortcode Inline Placement', async ({ page }) => {
    // Page with shortcode: [zm_sh_btn]
    await page.goto('/shortcode-test/');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const shortcodeButtons = page.locator('.zmshbt.in_shortcode.default.square');
    await expect(shortcodeButtons).toHaveScreenshot('shortcode-placement.png', {
      maxDiffPixels: 10,
    });
  });
});

test.describe('Mobile Viewport Tests', () => {
  test.use({ viewport: { width: 375, height: 667 } }); // iPhone SE

  test('Left Placement - Mobile', async ({ page }) => {
    await page.goto('/?left-placement=1');
    await page.waitForSelector('.zmshbt.left.default.square');
    
    const leftButtons = page.locator('.zmshbt.left.default.square');
    await expect(leftButtons).toHaveScreenshot('left-placement-mobile.png', {
      maxDiffPixels: 10,
    });
  });

  test('Shortcode - Mobile', async ({ page }) => {
    await page.goto('/shortcode-test/');
    await page.waitForSelector('.zmshbt.in_shortcode.default.square');
    
    const shortcodeButtons = page.locator('.zmshbt.in_shortcode.default.square');
    await expect(shortcodeButtons).toHaveScreenshot('shortcode-mobile.png', {
      maxDiffPixels: 10,
    });
  });
});

test.describe('Tablet Viewport Tests', () => {
  test.use({ viewport: { width: 768, height: 1024 } }); // iPad

  test('Left Placement - Tablet', async ({ page }) => {
    await page.goto('/?left-placement=1');
    await page.waitForSelector('.zmshbt.left.default.square');
    
    const leftButtons = page.locator('.zmshbt.left.default.square');
    await expect(leftButtons).toHaveScreenshot('left-placement-tablet.png', {
      maxDiffPixels: 10,
    });
  });
});
