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

// Register widget
$widget = $container->get('widget');
add_action('widgets_init', function() use ($widget) {
    register_widget($widget);
});

// Register Elementor widget
if (defined('ELEMENTOR_VERSION')) {
    require_once __DIR__ . '/src/Elementor/register_widget.php';
}

// Register Gutenberg block
add_action('init', function() use ($container) {
    $shareRenderer = $container->get('share_renderer');
    $block = new \HtmlSocialShare\Blocks\ShareButtons\Block($shareRenderer);
    $block->register();
});

// Register WPBakery element
if (defined('WPB_VC_VERSION')) {
    add_action('vc_before_init', function() use ($container) {
        $shareRenderer = $container->get('share_renderer');
        \HtmlSocialShare\Integrations\WPBakery\ShareButtonsElement::register($shareRenderer);
    });
}

// Register Divi module
if (class_exists('ET_Builder_Module')) {
    add_action('et_builder_ready', function() use ($container) {
        $shareRenderer = $container->get('share_renderer');
        \HtmlSocialShare\Integrations\Divi\ShareButtonsModule::register($shareRenderer);
    });
}

// Register Beaver Builder module
if (class_exists('FLBuilder')) {
    add_action('init', function() use ($container) {
        $shareRenderer = $container->get('share_renderer');
        \HtmlSocialShare\Integrations\BeaverBuilder\ShareButtonsModule::register($shareRenderer);
    });
}

// Register WooCommerce integration
if (class_exists('WooCommerce')) {
    add_action('init', function() use ($container) {
        $shareRenderer = $container->get('share_renderer');
        \HtmlSocialShare\Integrations\WooCommerce\ShareButtonsIntegration::register($shareRenderer);
    });
}

// Register bbPress integration
if (class_exists('bbPress')) {
    add_action('init', function() use ($container) {
        $shareRenderer = $container->get('share_renderer');
        \HtmlSocialShare\Integrations\bbPress\ShareButtonsIntegration::register($shareRenderer);
    });
}

// Register BuddyPress integration
if (class_exists('BuddyPress')) {
    add_action('bp_init', function() use ($container) {
        $shareRenderer = $container->get('share_renderer');
        \HtmlSocialShare\Integrations\BuddyPress\ShareButtonsIntegration::register($shareRenderer);
    });
}

// Hook into WordPress
register_activation_hook(__FILE__, function() {
    // Activation logic here
});

register_deactivation_hook(__FILE__, function() {
    // Deactivation logic here
});