<?php
require __DIR__ . '/../../src/bootstrap.php';

// Define WordPress functions for testing
global $mock_options;
$mock_options = [];

if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($attr) { return $attr; }
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
    function get_option($key, $default = false) {
        global $mock_options;
        return $mock_options[$key] ?? $default;
    }
}
if (!function_exists('update_option')) {
    function update_option($key, $value) {
        global $mock_options;
        $mock_options[$key] = $value;
        return true;
    }
}

$settings = new HtmlSocialShare\Settings();
$registry = new HtmlSocialShare\IconRegistry($settings, __DIR__ . '/../../', 'http://example.com/wp-content/plugins/html-social-share-buttons');
$ctrl = new HtmlSocialShare\Admin\IconsController($registry);
$html = $ctrl->index();

if (strpos($html, '<ul') === false) {
    echo "FAIL: Admin IconsController did not render list\n";
    exit(1);
}

echo "Admin IconsController PASS\n";
exit(0);
