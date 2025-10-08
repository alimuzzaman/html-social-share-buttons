<?php
require_once __DIR__ . '/vendor/autoload.php';

use HtmlSocialShare\Networks;
use HtmlSocialShare\IconRegistry;
use HtmlSocialShare\Settings;

echo "Testing New Social Networks Implementation...\n";

// Test Networks class
echo "\n=== Networks Configuration ===\n";
$networks = new Networks();
$allNetworks = $networks->getAvailableNetworks();

$newNetworks = ['mastodon', 'threads', 'vk', 'bluesky', 'wechat'];
foreach ($newNetworks as $network) {
    if (isset($allNetworks[$network])) {
        echo "✓ {$network}: {$allNetworks[$network]['label']} - {$allNetworks[$network]['url_template']}\n";
    } else {
        echo "✗ {$network}: Not found in networks registry\n";
    }
}

// Test IconRegistry
echo "\n=== Icon Registry ===\n";
$settings = new Settings();
$iconRegistry = new IconRegistry($settings);

foreach ($newNetworks as $network) {
    if ($iconRegistry->hasIcon($network)) {
        echo "✓ {$network}: Icon available\n";
    } else {
        echo "✗ {$network}: Icon missing\n";
    }
}

// Test Share URL Building
echo "\n=== Share URL Building ===\n";
foreach ($newNetworks as $network) {
    if (isset($allNetworks[$network])) {
        $template = $allNetworks[$network]['url_template'];
        $params = ['title' => 'Test Post', 'url' => 'https://example.com'];
        // Simple template replacement for testing
        $shareUrl = str_replace(['{title}', '{url}'], [$params['title'], $params['url']], $template);
        echo "✓ {$network}: {$shareUrl}\n";
    }
}

echo "\nNew social networks implementation test completed!\n";