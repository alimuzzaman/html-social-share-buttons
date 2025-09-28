<?php
// Minimal bootstrap for local development and tests
// Loads composer's autoloader and returns a simple service container placeholder.

if (! file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Composer dependencies not installed yet.
    return null;
}

$loader = require __DIR__ . '/../vendor/autoload.php';

// Boot a very small container and register basic services.
use HtmlSocialShare\Container;

$container = new Container();

$container->set('info', function () {
    return new HtmlSocialShare\Info();
});

$container->set('profile_manager', function ($c) {
    return new HtmlSocialShare\ProfileManager($c->get('settings'));
});

$container->set('share_renderer', function ($c) {
    $renderer = new HtmlSocialShare\ShareRenderer($c->get('icon_registry'), $c->get('settings'), $c->get('share_counts'));
    return $renderer;
});

$container->set('icon_registry', function ($c) {
    return new HtmlSocialShare\IconRegistry($c->get('settings'));
});

$container->set('settings', function () {
    return new HtmlSocialShare\Settings();
});

$container->set('cache', function () {
    return new HtmlSocialShare\Cache();
});

// Register share counts manager service
$container->set('share_counts', function ($c) {
    return new \HtmlSocialShare\ShareCounts\ShareCountManager($c->get('cache'), $c->get('settings'));
});

$container->set('migration', function ($c) {
    return new HtmlSocialShare\Migration($c->get('settings'));
});

$container->set('back_compat', function ($c) {
    return new HtmlSocialShare\BackCompatShim($c->get('settings'));
});

$container->set('admin', function ($c) {
    return new HtmlSocialShare\Admin\Admin($c->get('settings'), $c->get('profile_manager'), $c->get('share_renderer'));
});

$container->set('content_display', function ($c) {
    return new HtmlSocialShare\ContentDisplay(
        $c->get('settings'),
        $c->get('profile_manager'),
        $c->get('share_renderer')
    );
});

$container->set('widget', function ($c) {
    return new HtmlSocialShare\Widget\Widget($c->get('share_renderer'), $c->get('settings'));
});

$container->set('svg_sanitizer', function () {
    return new HtmlSocialShare\Svg\Sanitizer();
});

// Hook the scheduled refresh event to the ShareCountManager refresh method
add_action('hss_refresh_share_counts', function() use ($container) {
    $container->get('share_counts')->refreshCounts();
});

// Admin AJAX endpoint to trigger a manual refresh (requires manage_options)
add_action('wp_ajax_hss_refresh_counts', function() use ($container) {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }

    check_ajax_referer('hss_refresh_counts', '_hss_nonce');

    try {
        $container->get('share_counts')->refreshCounts();
        wp_send_json_success(['message' => 'Refresh completed']);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()], 500);
    }
});

// Admin AJAX endpoint to flush share count caches (and optionally DB) (requires manage_options)
add_action('wp_ajax_hss_flush_share_counts', function() use ($container) {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }

    check_ajax_referer('hss_flush_counts', '_hss_flush_nonce');

    // Optionally accept post_ids[] and remove_db flag
    $postIds = isset($_POST['post_ids']) && is_array($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : null;
    $removeDb = !empty($_POST['remove_db']);

    try {
        $container->get('share_counts')->flushCache($postIds, $removeDb);
        wp_send_json_success(['message' => 'Flush completed']);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()], 500);
    }
});

// Initialize admin interface (instantiates Admin class and registers admin hooks)
$container->get('admin');

// Initialize content display and ensure frontend styles are enqueued
$container->get('content_display');
add_action('wp_enqueue_scripts', [$container->get('content_display'), 'enqueueFrontendStyles']);

// Register widget at widgets_init
add_action('widgets_init', function() use ($container) {
    $widget = $container->get('widget');
    register_widget($widget);
});

// Register Gutenberg block server-side registration
add_action('init', function() use ($container) {
    $shareRenderer = $container->get('share_renderer');
    $block = new \HtmlSocialShare\Blocks\ShareButtons\Block($shareRenderer);
    $block->register();
});

// Initialize integrations loader
if (class_exists('\HtmlSocialShare\IntegrationLoader')) {
    $integrationLoader = new \HtmlSocialShare\IntegrationLoader($container);
    if (method_exists($integrationLoader, 'init')) {
        $integrationLoader->init();
    }
}

// Activation and deactivation hooks should reference the plugin file constant so
// bootstrap can be used from the main plugin file without duplicating logic.
if (defined('HTML_SOCIAL_SHARE_PLUGIN_FILE')) {
    register_activation_hook(HTML_SOCIAL_SHARE_PLUGIN_FILE, function() use ($container) {
        $migration = $container->get('migration');
        $migration->run();

        if ($container->get('share_counts') && method_exists($container->get('share_counts'), 'installSchema')) {
            $container->get('share_counts')->installSchema();
        }

        if ($container->get('share_counts') && method_exists($container->get('share_counts'), 'scheduleCron')) {
            $container->get('share_counts')->scheduleCron();
        }
    });

    register_deactivation_hook(HTML_SOCIAL_SHARE_PLUGIN_FILE, function() use ($container) {
        if ($container && method_exists($container->get('share_counts'), 'unscheduleCron')) {
            $container->get('share_counts')->unscheduleCron();
        }
    });
}

return $container;
