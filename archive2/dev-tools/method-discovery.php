<?php
/**
 * Method discovery script - identifies missing utility methods
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load utility classes
require_once __DIR__ . '/src/Utils/SecurityUtils.php';
require_once __DIR__ . '/src/Utils/StringUtils.php';
require_once __DIR__ . '/src/Utils/UrlUtils.php';

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use HtmlSocialShare\Utils\UrlUtils;

echo "=== UTILITY CLASS METHOD ANALYSIS ===\n\n";

// Methods expected by tests but might be missing
$expectedMethods = [
    'SecurityUtils' => [
        'sanitizeTextField', 'sanitizeKey', 'sanitizeHtmlClass', 'escapeHtml',
        'hasSqlInjectionPatterns', 'sanitizeFilename', 'isPrivateIp',
        'isValidCsrfTokenFormat', 'stripDangerousHtml'
    ],
    'StringUtils' => [
        'truncate', 'toSlug', 'toSnakeCase', 'extractHashtags', 'extractMentions',
        'cleanText', 'wordCount', 'toTitleCase', 'extractTemplateVariables'
    ],
    'UrlUtils' => [
        'buildShareUrl', 'isValidUrl', 'getPath', 'normalizeUrl',
        'matchesDomainPattern', 'extractDomain', 'addQueryParams'
    ]
];

foreach ($expectedMethods as $className => $methods) {
    echo "=== $className ===\n";
    $class = "HtmlSocialShare\\Utils\\$className";

    foreach ($methods as $method) {
        $exists = method_exists($class, $method);
        $status = $exists ? "✅ EXISTS" : "❌ MISSING";
        echo "$status $method\n";
    }
    echo "\n";
}

echo "=== METHOD SIGNATURES FROM TESTS ===\n\n";

// Test some key methods that are failing
echo "Testing StringUtils::truncate with word boundary...\n";
if (method_exists(StringUtils::class, 'truncate')) {
    $result = StringUtils::truncate('Hello World Test', 10, '...', false);
    echo "Input: 'Hello World Test', length: 10, breakWords: false\n";
    echo "Got: '$result'\n";
    echo "Expected: 'Hello...' (should stop at word boundary)\n";
    echo "\n";
}

echo "Testing StringUtils methods that might be missing...\n";
$testMethods = ['toSnakeCase', 'extractHashtags', 'cleanText', 'wordCount'];
foreach ($testMethods as $method) {
    if (!method_exists(StringUtils::class, $method)) {
        echo "❌ StringUtils::$method - MISSING\n";
    }
}
echo "\n";