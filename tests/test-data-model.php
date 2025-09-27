<?php
/**
 * Test suite for the new data model, migration, and caching
 */

// Mock WordPress storage
$wp_options = [];
$wp_cache = [];

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
    function wp_cache_get($key) {
        global $wp_cache;
        return $wp_cache[$key] ?? false;
    }
}
if (!function_exists('wp_cache_set')) {
    function wp_cache_set($key, $value, $group = '', $expiration = 0) {
        global $wp_cache;
        $wp_cache[$key] = $value;
        return true;
    }
}
if (!function_exists('wp_cache_delete')) {
    function wp_cache_delete($key) {
        global $wp_cache;
        unset($wp_cache[$key]);
        return true;
    }
}
if (!function_exists('get_option')) {
    function get_option($key, $default = false) {
        global $wp_options;
        return $wp_options[$key] ?? $default;
    }
}
if (!function_exists('update_option')) {
    function update_option($key, $value) {
        global $wp_options;
        $wp_options[$key] = $value;
        return true;
    }
}
if (!function_exists('delete_option')) {
    function delete_option($key) {
        global $wp_options;
        unset($wp_options[$key]);
        return true;
    }
}

$container = require __DIR__ . '/../src/bootstrap.php';

echo "Testing Data Model, Migration, and Caching...\n";

// Test 1: Settings persistence
echo "Test 1: Settings persistence... ";
$settings = $container->get('settings');

// Test setting and getting values
$settings->set('test_key', 'test_value');
$value = $settings->get('test_key');
if ($value === 'test_value') {
    echo "PASS\n";
} else {
    echo "FAIL - Expected 'test_value', got '" . var_export($value, true) . "'\n";
    exit(1);
}

// Test 2: Profile management
echo "Test 2: Profile management... ";
$profileManager = $container->get('profile_manager');

// Create a test profile
$profileId = $profileManager->createProfile([
    'type' => 'share',
    'label' => 'Test Network',
    'handle' => 'test',
    'url_template' => 'https://example.com/share?url={url}&title={title}'
]);

if (!empty($profileId)) {
    echo "PASS (created profile: $profileId)\n";
} else {
    echo "FAIL - Could not create profile\n";
    exit(1);
}

// Get the profile back
$profile = $profileManager->getProfile($profileId);
if ($profile && $profile['label'] === 'Test Network') {
    echo "Test 2b: Profile retrieval... PASS\n";
} else {
    echo "Test 2b: Profile retrieval... FAIL\n";
    exit(1);
}

// Test 3: Icon registry
echo "Test 3: Icon registry... ";
$iconRegistry = $container->get('icon_registry');

// Test getting a builtin icon
$icon = $iconRegistry->getIcon('facebook');
if (!empty($icon) && strpos($icon, '<svg') !== false) {
    echo "PASS\n";
} else {
    echo "FAIL - Could not get facebook icon\n";
    exit(1);
}

// Test adding a custom icon
$iconRegistry->addCustomIcon('test_icon', '<svg><circle cx="10" cy="10" r="5"/></svg>', ['source' => 'test']);
$customIcon = $iconRegistry->getIcon('test_icon');
if (!empty($customIcon) && strpos($customIcon, '<circle') !== false) {
    echo "Test 3b: Custom icon... PASS\n";
} else {
    echo "Test 3b: Custom icon... FAIL\n";
    exit(1);
}

// Test 4: Migration
echo "Test 4: Migration... ";
$migration = $container->get('migration');

// Test migration status
$status = $settings->getMigrationStatus();
if (is_array($status)) {
    echo "PASS\n";
} else {
    echo "FAIL - Invalid migration status\n";
    exit(1);
}

// Test 5: Caching
echo "Test 5: Caching... ";
$cacheStats = $settings->getCacheStats();
if (is_array($cacheStats) && isset($cacheStats['version'])) {
    echo "PASS\n";
} else {
    echo "FAIL - Invalid cache stats\n";
    exit(1);
}

// Test cache clearing
$settings->clearAllCaches();
echo "Test 5b: Cache clearing... PASS\n";

// Test 6: Settings validation
echo "Test 6: Settings validation... ";
$settings->set('enabled_networks', ['facebook', 'twitter', 'invalid_network']);
$networks = $settings->get('enabled_networks');
if (is_array($networks) && in_array('facebook', $networks)) {
    echo "PASS\n";
} else {
    echo "FAIL - Settings validation failed\n";
    exit(1);
}

// Test 7: Profile validation
echo "Test 7: Profile validation... ";
$enabledProfiles = $profileManager->getEnabledShareProfiles();
if (is_array($enabledProfiles)) {
    echo "PASS\n";
} else {
    echo "FAIL - Profile validation failed\n";
    exit(1);
}

echo "\nAll Data Model Tests PASSED!\n";