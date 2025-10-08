<?php
/**
 * Debug script to check admin menu registration
 */

if (!defined('ABSPATH')) {
    echo "Run with: wp eval-file debug-admin.php\n";
    exit(1);
}

echo "=== Admin Menu Debug ===\n\n";

echo "1. Checking admin menu registration...\n";
global $menu;
$found = false;
foreach ($menu as $item) {
    if (isset($item[0]) && strpos($item[0], 'Html Social Share') !== false) {
        echo "✅ Found menu item: {$item[0]}\n";
        $found = true;
    }
}

if (!$found) {
    echo "❌ No HTML Social Share menu item found\n";
}

echo "\n2. Checking admin service instantiation...\n";
try {
    $container = html_social_share_get_container();
    $admin = $container->get('admin');
    echo "✅ Admin service instantiated successfully\n";
    echo "   Class: " . get_class($admin) . "\n";
} catch (Exception $e) {
    echo "❌ Admin service error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n3. Checking user capabilities...\n";
if (current_user_can('manage_options')) {
    echo "✅ Current user has manage_options capability\n";
} else {
    echo "❌ Current user does NOT have manage_options capability\n";
}

echo "\n4. Checking plugin activation...\n";
if (function_exists('html_social_share_get_container')) {
    echo "✅ Plugin bootstrap function available\n";
} else {
    echo "❌ Plugin bootstrap function not available\n";
}