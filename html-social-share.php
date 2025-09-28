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

// Bootstrap the plugin
$container = require __DIR__ . '/src/bootstrap.php';

if ($container === null) {
    // Composer dependencies not installed
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>HTML Social Share Buttons: Composer dependencies not installed. Please run <code>composer install</code>.</p></div>';
    });
    return;
}

// Initialize admin interface
$admin = $container->get('admin');

// Initialize content display
$contentDisplay = $container->get('content_display');

// Register widget
$widget = $container->get('widget');
add_action('widgets_init', function() use ($widget) {
    register_widget($widget);
});

// Register Gutenberg block
add_action('init', function() use ($container) {
    $shareRenderer = $container->get('share_renderer');
    $block = new \HtmlSocialShare\Blocks\ShareButtons\Block($shareRenderer);
    $block->register();
});

// Initialize integrations
$integrationLoader = new \HtmlSocialShare\IntegrationLoader($container);
$integrationLoader->init();

// Enqueue frontend styles
add_action('wp_enqueue_scripts', [$contentDisplay, 'enqueueFrontendStyles']);

// Hook into WordPress
register_activation_hook(__FILE__, function() use ($container) {
    $migration = $container->get('migration');
    $migration->run();

    // Ensure share counts DB schema is created on activation
    if ($container->get('share_counts') && method_exists($container->get('share_counts'), 'installSchema')) {
        $container->get('share_counts')->installSchema();
    }
});

register_deactivation_hook(__FILE__, function() use ($container) {
    // Unschedule cron jobs and other cleanup
    if ($container && method_exists($container->get('share_counts'), 'unscheduleCron')) {
        $container->get('share_counts')->unscheduleCron();
    }
    // Deactivation logic here
});