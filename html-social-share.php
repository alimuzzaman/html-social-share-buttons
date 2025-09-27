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

// Hook into WordPress
register_activation_hook(__FILE__, function() {
    // Activation logic here
});

register_deactivation_hook(__FILE__, function() {
    // Deactivation logic here
});