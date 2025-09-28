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
if (!function_exists('current_time')) {
    function current_time($type) { return date('Y-m-d H:i:s'); }
}

require __DIR__ . '/../src/bootstrap.php';

$c = require __DIR__ . '/../src/bootstrap.php';

$api = new HtmlSocialShare\Rest\Api();
$controller = new HtmlSocialShare\Rest\Controller($c->get('profile_manager'));

$api->register('GET', '/profiles', function() use ($controller) { return $controller->listProfiles(); });
$api->register('POST', '/profiles', function($p) use ($controller) { return $controller->createProfile($p); });

$res1 = $api->dispatch('GET', '/profiles');
if ($res1['status'] !== 200) {
    echo "FAIL GET /profiles -> status " . $res1['status'] . "\n";
    exit(1);
}

$res2 = $api->dispatch('POST', '/profiles', ['network' => 'twitter', 'handle' => '@api']);
if ($res2['status'] !== 201) {
    echo "FAIL POST /profiles -> status " . $res2['status'] . "\n";
    exit(1);
}

echo "REST tests PASS\n";
exit(0);
