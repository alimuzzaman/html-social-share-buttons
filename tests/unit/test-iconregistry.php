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
$reg = new HtmlSocialShare\IconRegistry($settings, __DIR__ . '/../../', 'http://example.com/wp-content/plugins/html-social-share-buttons');

// initial list should contain at least 'twitter' from constructor
$list = $reg->listIcons();
if (!in_array('twitter', $list)) {
    echo "FAIL: initial icons missing twitter\n";
    exit(1);
}

$reg->registerIcon('foo/bar.svg', '<svg></svg>');
if (!$reg->hasIcon('foo/bar.svg')) {
    echo "FAIL: did not register icon\n";
    exit(1);
}

$svg = $reg->getIcon('foo/bar.svg');
if (strpos($svg, '<svg') === false) {
    echo "FAIL: getIcon returned invalid svg\n";
    exit(1);
}

echo "IconRegistry PASS\n";
exit(0);
