<?php
// Define plugin constants for testing
define('HTML_SOCIAL_SHARE_PLUGIN_FILE', __DIR__ . '/../html-social-share.php');
define('HTML_SOCIAL_SHARE_PLUGIN_DIR', __DIR__ . '/../');
define('HTML_SOCIAL_SHARE_PLUGIN_URL', 'http://example.com/wp-content/plugins/html-social-share-buttons/');

$container = require __DIR__ . '/../src/bootstrap.php';

// Define WordPress functions for testing
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($attr) { return $attr; }
}
if (!function_exists('current_time')) {
    function current_time($type) { return date('Y-m-d H:i:s'); }
}
if (!function_exists('wp_cache_get')) {
    function wp_cache_get($key) { return false; }
}
if (!function_exists('wp_cache_set')) {
    function wp_cache_set($key, $value, $group = '', $expiration = 0) { return true; }
}
if (!function_exists('wp_cache_delete')) {
    function wp_cache_delete($key) { return true; }
}
if (!function_exists('get_option')) {
    function get_option($key, $default = false) { return $default; }
}
if (!function_exists('update_option')) {
    function update_option($key, $value) { return true; }
}
if (!function_exists('plugins_url')) {
    function plugins_url($path = '', $plugin = '') {
        return 'http://example.com/wp-content/plugins/html-social-share-buttons/' . ltrim($path, '/');
    }
}

$registry = $container->get('icon_registry');
$renderer = $container->get('share_renderer');
$html = $renderer->render('twitter', ['handle' => '@example']);

if (strpos($html, '<a') === false || strpos($html, 'twitter') === false) {
    echo "FAIL: render did not produce expected output\n";
    exit(1);
}

echo "Render integration PASS\n";
exit(0);
