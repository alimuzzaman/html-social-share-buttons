<?php
// Define WordPress functions for testing
global $mock_options;
$mock_options = [];

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

require __DIR__ . '/../src/bootstrap.php';

$c = require __DIR__ . '/../src/bootstrap.php';

// Seed legacy keys in Settings
$settings = $c->get('settings');
$settings->set('hssb_profiles', [
    '1' => ['network' => 'twitter', 'handle' => '@ex']
]);
$settings->set('hssb_settings', ['show_after_post' => true]);

// older plugin versions stored social links under this key as an associative map
$settings->set('hssb_social_links', [
    'facebook' => ['network' => 'facebook', 'url' => 'https://facebook.com/example'],
    'linkedin' => ['network' => 'linkedin', 'url' => 'https://linkedin.com/in/example'],
]);

$back = $c->get('back_compat');
$canonical = $back->migrate();

echo "Canonical keys after migration:\n";
print_r($canonical);
