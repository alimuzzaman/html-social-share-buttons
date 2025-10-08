<?php
/**
 * Quick test script to verify REST API endpoints are working
 */

// Include WordPress
require_once __DIR__ . '/../../../wp-load.php';

// Test settings endpoint
echo "Testing REST API Endpoints\n";
echo "==========================\n\n";

// Test 1: Get current settings
echo "1. Testing GET /html-social-share/v1/settings\n";
$settings_url = rest_url('html-social-share/v1/settings');
echo "URL: $settings_url\n";

// Test 2: Get networks
echo "\n2. Testing GET /html-social-share/v1/networks\n";
$networks_url = rest_url('html-social-share/v1/networks');
echo "URL: $networks_url\n";

// Test 3: Get profiles
echo "\n3. Testing GET /html-social-share/v1/profiles\n";
$profiles_url = rest_url('html-social-share/v1/profiles');
echo "URL: $profiles_url\n";

// Test if the REST API service is registered
echo "\n4. Checking if REST API service is registered\n";
$registered_routes = rest_get_server()->get_routes();
$our_routes = array_filter(array_keys($registered_routes), function($route) {
    return strpos($route, '/html-social-share/v1/') === 0;
});

if (empty($our_routes)) {
    echo "❌ No routes found for /html-social-share/v1/\n";
    echo "Available routes starting with /html-social-share:\n";
    $partial_routes = array_filter(array_keys($registered_routes), function($route) {
        return strpos($route, 'html-social-share') !== false;
    });
    if (empty($partial_routes)) {
        echo "No routes found containing 'html-social-share'\n";
    } else {
        foreach ($partial_routes as $route) {
            echo "  - $route\n";
        }
    }
} else {
    echo "✅ Found routes:\n";
    foreach ($our_routes as $route) {
        echo "  - $route\n";
    }
}

echo "\nDone!\n";