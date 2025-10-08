<?php
/**
 * Test script for HTML Social Share REST API endpoints
 * Run this in WordPress context to test REST API functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    echo "This script must be run in WordPress context.\n";
    echo "Usage: wp eval-file test-rest-api.php\n";
    exit(1);
}

echo "=== HTML Social Share REST API Test ===\n\n";

// Test 1: Check if REST API routes are registered
echo "1. Testing REST API route registration...\n";
$routes = rest_get_server()->get_routes();
$hssRoutes = array_filter($routes, function($route, $path) {
    return strpos($path, '/html-social-share/v1') === 0;
}, ARRAY_FILTER_USE_BOTH);

if (!empty($hssRoutes)) {
    echo "✅ REST API routes found:\n";
    foreach (array_keys($hssRoutes) as $route) {
        echo "   - {$route}\n";
    }
} else {
    echo "❌ No REST API routes found for html-social-share/v1\n";
}

echo "\n";

// Test 2: Check if services are registered
echo "2. Testing service container registration...\n";
try {
    $container = html_social_share_get_container();

    $services = [
        'cache' => 'WordPressCache',
        'rest_share_counts_controller' => 'ShareCountsController',
        'react_admin_interface' => 'ReactAdminInterface',
        'share_counts' => 'ShareCountManager'
    ];

    foreach ($services as $serviceKey => $expectedClass) {
        try {
            $service = $container->get($serviceKey);
            $actualClass = get_class($service);
            $shortClass = substr($actualClass, strrpos($actualClass, '\\') + 1);

            if (strpos($actualClass, $expectedClass) !== false) {
                echo "✅ {$serviceKey}: {$shortClass}\n";
            } else {
                echo "⚠️  {$serviceKey}: {$shortClass} (expected {$expectedClass})\n";
            }
        } catch (Exception $e) {
            echo "❌ {$serviceKey}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Container not available: {$e->getMessage()}\n";
}

echo "\n";

// Test 3: Test WordPress Cache functionality
echo "3. Testing WordPressCache functionality...\n";
try {
    $cache = $container->get('cache');

    // Test basic cache operations
    $testKey = 'hss_test_' . uniqid();
    $testValue = ['test' => 'data', 'timestamp' => time()];

    // Set cache
    $setResult = $cache->set($testKey, $testValue, 300);
    echo $setResult ? "✅ Cache SET: success\n" : "❌ Cache SET: failed\n";

    // Get cache
    $getValue = $cache->get($testKey);
    if ($getValue && $getValue['test'] === 'data') {
        echo "✅ Cache GET: success\n";
    } else {
        echo "❌ Cache GET: failed or data mismatch\n";
    }

    // Check cache exists
    $hasResult = $cache->has($testKey);
    echo $hasResult ? "✅ Cache HAS: success\n" : "❌ Cache HAS: failed\n";

    // Delete cache
    $deleteResult = $cache->delete($testKey);
    echo $deleteResult ? "✅ Cache DELETE: success\n" : "❌ Cache DELETE: failed\n";

    // Verify deletion
    $getAfterDelete = $cache->get($testKey, 'not_found');
    if ($getAfterDelete === 'not_found') {
        echo "✅ Cache deletion verified\n";
    } else {
        echo "❌ Cache deletion failed\n";
    }

} catch (Exception $e) {
    echo "❌ Cache test failed: {$e->getMessage()}\n";
}

echo "\n";

// Test 4: Test ShareCountManager with database operations
echo "4. Testing ShareCountManager database operations...\n";
try {
    $shareManager = $container->get('share_counts');

    // Test URL for testing
    $testUrl = 'https://example.com/test-post-' . uniqid();

    // Test updating a share count
    $updateResult = $shareManager->updateCount($testUrl, 'facebook', 42);
    echo $updateResult ? "✅ Update count: success\n" : "❌ Update count: failed\n";

    // Test getting the count back
    $count = $shareManager->getCount($testUrl, 'facebook');
    if ($count === 42) {
        echo "✅ Get count: success (count: {$count})\n";
    } else {
        echo "❌ Get count: failed or incorrect (count: {$count})\n";
    }

    // Test getting all counts for URL
    $allCounts = $shareManager->getCounts($testUrl);
    if (is_array($allCounts) && isset($allCounts['facebook']) && $allCounts['facebook'] === 42) {
        echo "✅ Get all counts: success\n";
    } else {
        echo "❌ Get all counts: failed or incorrect\n";
    }

    // Clean up test data
    $deleteResult = $shareManager->deleteUrl($testUrl);
    echo $deleteResult ? "✅ Cleanup: success\n" : "⚠️  Cleanup: failed\n";

} catch (Exception $e) {
    echo "❌ ShareCountManager test failed: {$e->getMessage()}\n";
}

echo "\n";

// Test 5: Check database table existence
echo "5. Testing database table structure...\n";
global $wpdb;
$tableName = $wpdb->prefix . 'hss_share_counts';
$tableExists = $wpdb->get_var("SHOW TABLES LIKE '{$tableName}'") === $tableName;

if ($tableExists) {
    echo "✅ Database table exists: {$tableName}\n";

    // Check table structure
    $columns = $wpdb->get_results("DESCRIBE {$tableName}");
    $requiredColumns = ['id', 'url', 'network', 'count', 'created_at', 'updated_at'];
    $actualColumns = array_column($columns, 'Field');

    $missingColumns = array_diff($requiredColumns, $actualColumns);
    if (empty($missingColumns)) {
        echo "✅ Table structure: complete\n";
    } else {
        echo "⚠️  Table structure: missing columns - " . implode(', ', $missingColumns) . "\n";
    }
} else {
    echo "❌ Database table missing: {$tableName}\n";
    echo "   Try running: wp eval 'do_action(\"activate_html-social-share/html-social-share.php\");'\n";
}

echo "\n";

// Test 6: Check WordPress capabilities and permissions
echo "6. Testing WordPress integration...\n";

// Check current user capabilities
$currentUser = wp_get_current_user();
echo "Current user: {$currentUser->user_login} (ID: {$currentUser->ID})\n";

$requiredCaps = ['manage_options', 'edit_posts'];
foreach ($requiredCaps as $cap) {
    $hasCap = current_user_can($cap);
    echo $hasCap ? "✅ Capability '{$cap}': granted\n" : "⚠️  Capability '{$cap}': not granted\n";
}

// Check if REST API is working
$restStatus = rest_url() ? "✅ REST API: available" : "❌ REST API: not available";
echo "{$restStatus}\n";

echo "\n=== Test Summary ===\n";
echo "Tests completed. Check results above for any issues.\n";
echo "If all tests pass, the WordPress-native implementation is working correctly!\n";