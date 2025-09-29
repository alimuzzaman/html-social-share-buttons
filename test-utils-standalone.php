<?php
/**
 * Standalone utility testing script
 * Tests utility classes without WordPress environment
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load utility classes
require_once __DIR__ . '/src/Utils/SecurityUtils.php';
require_once __DIR__ . '/src/Utils/StringUtils.php';
require_once __DIR__ . '/src/Utils/UrlUtils.php';

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use HtmlSocialShare\Utils\UrlUtils;

echo "Testing Utility Classes...\n\n";

// Test StringUtils::truncate
echo "Testing StringUtils::truncate...\n";
$result = StringUtils::truncate('Hello World', 8);
echo "Expected: 'Hello...', Got: '$result'\n";
echo ($result === 'Hello...' ? "✅ PASS" : "❌ FAIL") . "\n\n";

// Test SecurityUtils::sanitizeTextField (if method exists)
echo "Testing SecurityUtils::sanitizeTextField...\n";
if (method_exists(SecurityUtils::class, 'sanitizeTextField')) {
    $result = SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World');
    echo "Input: 'Hello <script>alert(\"xss\")</script>World'\n";
    echo "Got: '$result'\n";
    echo (strpos($result, '<script>') === false ? "✅ PASS (XSS removed)" : "❌ FAIL (XSS not removed)") . "\n";
} else {
    echo "❌ Method sanitizeTextField not found\n";
}
echo "\n";

// Test UrlUtils::buildShareUrl (if method exists)
echo "Testing UrlUtils::buildShareUrl...\n";
if (method_exists(UrlUtils::class, 'buildShareUrl')) {
    $result = UrlUtils::buildShareUrl(
        'https://example.com/share?url={{url}}&title={{title}}',
        ['url' => 'https://test.com', 'title' => 'Test Title']
    );
    echo "Template: 'https://example.com/share?url={{url}}&title={{title}}'\n";
    echo "Params: ['url' => 'https://test.com', 'title' => 'Test Title']\n";
    echo "Got: '$result'\n";
    $expected = 'https://example.com/share?url=https%3A%2F%2Ftest.com&title=Test+Title';
    echo "Expected: '$expected'\n";
    echo ($result === $expected ? "✅ PASS" : "❌ FAIL") . "\n";
} else {
    echo "❌ Method buildShareUrl not found\n";
}
echo "\n";

echo "Testing complete!\n";