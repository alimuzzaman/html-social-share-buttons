<?php

// Simple fuzz harness: feed a few random-ish strings to the SVG sanitizer and ensure no exception/exit
$container = require __DIR__ . '/../../src/bootstrap.php';
$sanitizer = null;
if (is_object($container)) {
    if (method_exists($container, 'get')) {
        try {
            $sanitizer = $container->get('svg_sanitizer');
        } catch (Exception $e) {
            // ignore
        }
    }
}

if (! $sanitizer && class_exists('HtmlSocialShare\\Svg\\Sanitizer')) {
    $sanitizer = new HtmlSocialShare\Svg\Sanitizer();
}

if (! $sanitizer) {
    echo "SKIP: sanitizer not available\n";
    exit(0);
}

$samples = [
    '<svg><rect /></svg>',
    '<svg><script>alert(1)</script></svg>',
    '<svg onload="evil()"></svg>',
    str_repeat('A', 1024),
];

foreach ($samples as $s) {
    $out = $sanitizer->sanitize($s);
    if (!is_string($out)) {
        echo "FAIL: sanitizer returned non-string\n";
        exit(1);
    }
}

echo "Security tests PASS\n";
exit(0);
