<?php
/*
Plugin Name: HTML Social Share Buttons
Plugin URI: https://github.com/alimuzzaman/html-social-share-buttons
Description: Modern, lightweight social share buttons for WordPress with PSR-4 architecture
Author: Alimuzzaman Alim
Version: 3.0.0
Author URI: https://alim.dev
Text Domain: html-social-share
Domain Path: /languages
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('HTML_SOCIAL_SHARE_PLUGIN_FILE', __FILE__);
define('HTML_SOCIAL_SHARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HTML_SOCIAL_SHARE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Additional helper constants for assets and URLs
define('HTML_SOCIAL_SHARE_ASSETS_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/');
define('HTML_SOCIAL_SHARE_ASSETS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/');
define('HTML_SOCIAL_SHARE_ICONSET_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/iconset/');
define('HTML_SOCIAL_SHARE_ICONSET_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/iconset/');
define('HTML_SOCIAL_SHARE_ADMIN_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/admin/');
define('HTML_SOCIAL_SHARE_CSS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/css/');
define('HTML_SOCIAL_SHARE_JS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/js/');
define('HTML_SOCIAL_SHARE_IMAGES_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/images/');

// NOTE: Do not require the autoloader here at top-level. The helper will
// perform a combined check for the autoload file and the Bootstrap class
// and report a single, clear admin notice when issues occur.

/**
 * Get the plugin service container.
 *
 * Lazily instantiates the Bootstrap class and returns a shared Container instance.
 * If Composer autoloader is missing or the Bootstrap class cannot be loaded,
 * registers an admin notice and returns null.
 *
 * @return ?\HtmlSocialShare\Container
 */
function html_social_share_get_container(): ?\HtmlSocialShare\Container
{
    static $container = null;

    // If vendor autoload is missing, show clear instructions
    $autoloadPath = __DIR__ . '/vendor/autoload.php';
    if (! file_exists($autoloadPath)) {
        add_action('admin_notices', function() use ($autoloadPath) {
            $pluginDir = esc_html(HTML_SOCIAL_SHARE_PLUGIN_DIR);
            $cmd = '<code>composer install</code>';
            echo '<div class="notice notice-error"><p><strong>HTML Social Share Buttons:</strong> Composer autoloader not found in <code>' . esc_html($autoloadPath) . '</code>. Please run ' . $cmd . ' in the plugin directory (<code>' . $pluginDir . '</code>).</p></div>';
        });

        if (function_exists('error_log')) {
            error_log('HTML Social Share: vendor/autoload.php not found. Run composer install.');
        }

        return null;
    }

    // Load the autoloader (once)
    require_once $autoloadPath;

    // After loading, ensure our Bootstrap class is available
    if (! class_exists('\HtmlSocialShare\\Bootstrap')) {
        add_action('admin_notices', function() {
            $pluginDir = esc_html(HTML_SOCIAL_SHARE_PLUGIN_DIR);
            echo '<div class="notice notice-error"><p><strong>HTML Social Share Buttons:</strong> The plugin autoloader was loaded but the plugin classes could not be found. Please verify that the plugin files are present in <code>' . $pluginDir . '</code> and that the autoloader is up-to-date (run <code>composer dump-autoload</code>).</p></div>';
        });

        if (function_exists('error_log')) {
            error_log('HTML Social Share: Bootstrap class not found after autoloader was loaded. Check composer dump-autoload.');
        }

        return null;
    }

    if ($container === null) {
        $bootstrap = new \HtmlSocialShare\Bootstrap(HTML_SOCIAL_SHARE_PLUGIN_FILE);
        $container = $bootstrap->getContainer();
    }
    return $container;
}

// Ensure Bootstrap is instantiated (do not keep a local reference)
html_social_share_get_container();

// Bootstrap performs initialization and hook wiring; container is available