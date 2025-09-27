<?php
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
if (!function_exists('delete_option')) {
    function delete_option($key) { return true; }
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
