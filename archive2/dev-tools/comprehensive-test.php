<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Utils/SecurityUtils.php';
require_once __DIR__ . '/src/Utils/StringUtils.php';
require_once __DIR__ . '/src/Utils/UrlUtils.php';

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use HtmlSocialShare\Utils\UrlUtils;

echo "=== COMPREHENSIVE UTILITY TESTING ===\n\n";

// Test StringUtils methods that were failing
$stringTests = [
    ['toSnakeCase', 'Hello World', 'hello_world'],
    ['extractHashtags', 'Hello #world test #world', ['world']],
    ['cleanText', "Hello\x01World", 'HelloWorld'],
    ['wordCount', 'Hello world test more', 4],
    ['toTitleCase', 'this is a test', 'This Is A Test'],
];

echo "=== StringUtils Tests ===\n";
foreach ($stringTests as [$method, $input, $expected]) {
    if (method_exists(StringUtils::class, $method)) {
        $result = StringUtils::$method($input);
        $match = ($result === $expected);

        // Special handling for arrays
        if (is_array($expected)) {
            $match = (json_encode($result) === json_encode($expected));
            $result = json_encode($result);
            $expected = json_encode($expected);
        }

        $status = $match ? "✅ PASS" : "❌ FAIL";
        echo "$status $method('$input')\n";
        echo "    Expected: $expected\n";
        echo "    Got: $result\n";
    } else {
        echo "❌ MISSING $method\n";
    }
    echo "\n";
}

// Test SecurityUtils methods that were failing
$securityTests = [
    ['sanitizeKey', 'Hello-World@#$', 'hello_world'],
    ['isValidCsrfTokenFormat', 'abc123def456abc123def456abc123de', true],
    ['hasSqlInjectionPatterns', "'; DROP TABLE users; --", true],
];

echo "=== SecurityUtils Tests ===\n";
foreach ($securityTests as [$method, $input, $expected]) {
    if (method_exists(SecurityUtils::class, $method)) {
        $result = SecurityUtils::$method($input);
        $match = ($result === $expected);
        $status = $match ? "✅ PASS" : "❌ FAIL";
        echo "$status $method('$input')\n";
        echo "    Expected: " . var_export($expected, true) . "\n";
        echo "    Got: " . var_export($result, true) . "\n";
    } else {
        echo "❌ MISSING $method\n";
    }
    echo "\n";
}

// Test UrlUtils methods that were failing
$urlTests = [
    ['isValidUrl', 'javascript:alert("xss")', false],
    ['getPath', 'https://example.com/', '/'],
    ['normalizeUrl', 'https://example.com//path///to//page', 'https://example.com/path/to/page'],
];

echo "=== UrlUtils Tests ===\n";
foreach ($urlTests as [$method, $input, $expected]) {
    if (method_exists(UrlUtils::class, $method)) {
        $result = UrlUtils::$method($input);
        $match = ($result === $expected);
        $status = $match ? "✅ PASS" : "❌ FAIL";
        echo "$status $method('$input')\n";
        echo "    Expected: $expected\n";
        echo "    Got: $result\n";
    } else {
        echo "❌ MISSING $method\n";
    }
    echo "\n";
}