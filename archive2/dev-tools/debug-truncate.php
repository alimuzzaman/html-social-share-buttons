<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Utils/StringUtils.php';

use HtmlSocialShare\Utils\StringUtils;

echo "=== TESTING StringUtils::truncate ===\n\n";

// Test case from test file: 'Hello World' with length 11 and breakWords true
echo "Test: StringUtils::truncate('Hello World', 11, '...', true)\n";
$result = StringUtils::truncate('Hello World', 11, '...', true);
echo "Expected: 'Hello Wo...'\n";
echo "Got: '$result'\n";
echo "Length of input: " . strlen('Hello World') . "\n";
echo "Length of result: " . strlen($result) . "\n";
echo "\n";

// Let's trace the calculation
echo "=== TRACING ===\n";
$text = 'Hello World';
$length = 11;
$ellipsis = '...';
echo "Original text: '$text' (length: " . mb_strlen($text) . ")\n";
echo "Target length: $length\n";
echo "Ellipsis: '$ellipsis' (length: " . mb_strlen($ellipsis) . ")\n";
echo "Available for text: " . ($length - mb_strlen($ellipsis)) . "\n";
$truncated = mb_substr($text, 0, $length - mb_strlen($ellipsis));
echo "Truncated text: '$truncated'\n";
echo "Final: '$truncated$ellipsis'\n";
echo "\n";

// Test another failing case
echo "Test: StringUtils::truncate('Hello World Test', 10, '...', false)\n";
$result2 = StringUtils::truncate('Hello World Test', 10, '...', false);
echo "Expected: 'Hello...'\n";
echo "Got: '$result2'\n";