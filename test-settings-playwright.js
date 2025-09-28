const { chromium } = require('playwright');

// Make admin URL and credentials configurable
const ADMIN_URL = process.env.WP_ADMIN_URL || 'https://s3.templately-multi.test/wp-admin/';
const ADMIN_PAGE = process.env.WP_ADMIN_PAGE || 'admin.php?page=html-social-share';
const ADMIN_USER = process.env.WP_ADMIN_USER || process.env.USER || null;
const ADMIN_PASS = process.env.WP_ADMIN_PASS || null;

async function testSettingsSave() {
    console.log('Starting HTML Social Share Settings Save Test...');

    const browser = await chromium.launch({
        headless: true, // Set to true for headless mode
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const context = await browser.newContext({
        // Add any necessary cookies or authentication here
        // For WordPress admin, you might need to set authentication cookies
    });

    const page = await context.newPage();

    // Enable console logging
    page.on('console', msg => {
        console.log('PAGE LOG:', msg.text());
    });

    // Monitor network requests
    page.on('request', request => {
        if (request.url().includes('admin.php') && request.method() === 'POST') {
            console.log('POST REQUEST:', request.url());
            console.log('POST DATA:', request.postData());
        }
    });

    // Monitor network responses
    page.on('response', response => {
        if (response.url().includes('admin.php') && response.request().method() === 'POST') {
            console.log('RESPONSE STATUS:', response.status());
            console.log('RESPONSE URL:', response.url());
        }
    });

    try {
        // Optional login step: if credentials provided, attempt to log in
        if (ADMIN_USER && ADMIN_PASS) {
            console.log('Attempting to login with provided credentials...');
            await page.goto(ADMIN_URL, { waitUntil: 'networkidle' });
            // Wait for login form
            if (await page.$('form#loginform')) {
                await page.fill('input#user_login', ADMIN_USER);
                await page.fill('input#user_pass', ADMIN_PASS);
                await Promise.all([
                    page.waitForNavigation({ waitUntil: 'networkidle' }),
                    page.click('input#wp-submit')
                ]);
                console.log('Login attempted, current URL:', page.url());
            } else {
                console.log('No login form found on admin page; assuming already authenticated or alternate auth method in use.');
            }
        }

        // Navigate to the settings page
        console.log('Navigating to settings page...');
        await page.goto(`${ADMIN_URL}${ADMIN_PAGE}`, {
            waitUntil: 'networkidle'
        });

        console.log('Page loaded. Current URL:', page.url());

        // Wait for the form to be visible
        await page.waitForSelector('form[method="post"]', { timeout: 10000 });
        console.log('Form found');

        // Check if we're on the correct page
        const pageTitle = await page.textContent('h1');
        console.log('Page title:', pageTitle);

        if (!pageTitle || !pageTitle.includes('HTML Social Share Settings')) {
            throw new Error('Not on the expected settings page');
        }

        // Take a screenshot before making changes
        await page.screenshot({ path: 'before-settings.png' });
        console.log('Screenshot taken: before-settings.png');

        // Check available tabs and switch to the appropriate ones
        console.log('Checking available tabs...');
        const tabs = await page.$$eval('.nav-tab-link', tabs => tabs.map(tab => ({
            text: tab.textContent,
            dataTab: tab.getAttribute('data-tab')
        })));
        console.log('Available tabs:', tabs);

        // Switch to General tab first (for title)
        console.log('Switching to General tab...');
        await page.click('.nav-tab-link[data-tab="general"]');
        await page.waitForTimeout(500); // Wait for tab content to load

        // Test 1: Change the share title
        console.log('Testing title change...');
        const titleSelector = 'input[name="title"]';
        await page.fill(titleSelector, 'Test Share Title - ' + new Date().toISOString());
        console.log('Title field updated');

        // Switch to Appearance tab (for iconset)
        console.log('Switching to Appearance tab...');
        await page.click('.nav-tab-link[data-tab="appearance"]');
        await page.waitForTimeout(500); // Wait for tab content to load

        // Test 2: Change iconset
        console.log('Testing iconset change...');
        const iconsetSelector = 'select[name="iconset"]';
        await page.selectOption(iconsetSelector, 'square');
        console.log('Iconset changed to square');

        // Switch back to General tab for checkboxes
        console.log('Switching back to General tab...');
        await page.click('.nav-tab-link[data-tab="general"]');
        await page.waitForTimeout(500);

        // Test 3: Toggle some checkboxes
        console.log('Testing checkbox toggles...');
        const gaCheckbox = 'input[name="google_analytics"]';
        const nofollowCheckbox = 'input[name="nofollow"]';

        // Check current state and toggle
        const gaChecked = await page.isChecked(gaCheckbox);
        const nofollowChecked = await page.isChecked(nofollowCheckbox);

        console.log('GA checkbox was:', gaChecked ? 'checked' : 'unchecked');
        console.log('Nofollow checkbox was:', nofollowChecked ? 'checked' : 'unchecked');

        // Toggle them
        await page.setChecked(gaCheckbox, !gaChecked);
        await page.setChecked(nofollowCheckbox, !nofollowChecked);
        console.log('Checkboxes toggled');

        // Test 4: Select some networks
        console.log('Testing network selection...');
        const networkCheckboxes = [
            'input[name="enabled_networks[]"][value="facebook"]',
            'input[name="enabled_networks[]"][value="twitter"]',
            'input[name="enabled_networks[]"][value="linkedin"]'
        ];

        for (const checkbox of networkCheckboxes) {
            try {
                await page.setChecked(checkbox, true);
                console.log('Checked network:', checkbox);
            } catch (e) {
                console.log('Could not check network:', checkbox, e.message);
            }
        }

        // Submit the form
        console.log('Submitting form...');
        const submitButton = 'input[type="submit"][name="submit"]';
        await page.click(submitButton);

        // Wait for navigation or response
        await page.waitForLoadState('networkidle');
        console.log('Form submitted, page reloaded');

        // Take a screenshot after submission
        await page.screenshot({ path: 'after-settings.png' });
        console.log('Screenshot taken: after-settings.png');

        // Check for success message
        const successMessage = await page.textContent('.notice-success');
        if (successMessage) {
            console.log('SUCCESS: Settings saved message found:', successMessage);
        } else {
            console.log('WARNING: No success message found');
        }

        // Check for error messages (don't fail if none found)
        try {
            const errorMessage = await page.textContent('.notice-error', { timeout: 2000 });
            if (errorMessage) {
                console.log('ERROR: Error message found:', errorMessage);
            } else {
                console.log('No error messages found - this is good!');
            }
        } catch (e) {
            console.log('No error messages found - this is good!');
        }

        // Extract debug comments from the page
        const debugComments = await page.evaluate(() => {
            const comments = [];
            const allComments = document.querySelectorAll('*');

            allComments.forEach(element => {
                element.childNodes.forEach(node => {
                    if (node.nodeType === Node.COMMENT_NODE && node.textContent.includes('DEBUG:')) {
                        comments.push(node.textContent);
                    }
                });
            });

            return comments;
        });

        console.log('DEBUG COMMENTS FOUND:');
        debugComments.forEach(comment => {
            console.log('  ', comment);
        });

        // Check if the title was actually saved
        const savedTitle = await page.inputValue(titleSelector);
        console.log('Saved title value:', savedTitle);

        // Check if iconset was saved
        const savedIconset = await page.selectOption(iconsetSelector, { force: true });
        console.log('Current iconset selection:', savedIconset);

        console.log('Test completed successfully!');

    } catch (error) {
        console.error('Test failed:', error);

        // Take error screenshot
        await page.screenshot({ path: 'error-settings.png' });
        console.log('Error screenshot taken: error-settings.png');

        // Get page content for debugging
        const content = await page.content();
        console.log('Page content length:', content.length);

    } finally {
        await browser.close();
    }
}

// Run the test
testSettingsSave().catch(console.error);