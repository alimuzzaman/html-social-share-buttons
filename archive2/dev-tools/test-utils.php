<?php
require_once __DIR__ . '/vendor/autoload.php';

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use HtmlSocialShare\Utils\UrlUtils;

echo "Testing Utility Classes...\n";

// Test SecurityUtils
echo "\n=== SecurityUtils Tests ===\n";
$result = SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World');
echo "sanitizeTextField: " . $result . "\n";

$result = SecurityUtils::isValidEmail('test@example.com');
echo "isValidEmail: " . ($result ? 'true' : 'false') . "\n";

$result = SecurityUtils::hasXssPatterns('<script>alert("xss")</script>');
echo "hasXssPatterns: " . ($result ? 'true' : 'false') . "\n";

// Test StringUtils
echo "\n=== StringUtils Tests ===\n";
$result = StringUtils::truncate('Hello World Test', 10);
echo "truncate: " . $result . "\n";

$result = StringUtils::toSlug('Hello World');
echo "toSlug: " . $result . "\n";

$result = StringUtils::wordCount('This is a test sentence');
echo "wordCount: " . $result . "\n";

// Test UrlUtils
echo "\n=== UrlUtils Tests ===\n";
$result = UrlUtils::extractDomain('https://example.com/path');
echo "extractDomain: " . $result . "\n";

$result = UrlUtils::isValidUrl('https://example.com');
echo "isValidUrl: " . ($result ? 'true' : 'false') . "\n";

$result = UrlUtils::addQueryParams('https://example.com', ['param' => 'value']);
echo "addQueryParams: " . $result . "\n";

echo "\nAll utility tests completed successfully!\n";