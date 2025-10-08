<?php
require __DIR__ . '/../../src/bootstrap.php';

$c = require __DIR__ . '/../../src/bootstrap.php';
$san = $c->get('svg_sanitizer');

$raw = '<svg><script>alert(1)</script><a xlink:href="http://evil.example.com"></a><rect onload="doEvil()"/></svg>';
$clean = $san->sanitize($raw);

if (strpos($clean, '<script') !== false) {
    echo "FAIL: script tag not removed\n";
    exit(1);
}
if (strpos($clean, 'onload') !== false) {
    echo "FAIL: event attribute not removed\n";
    exit(1);
}
if (strpos($clean, 'http://evil.example.com') !== false) {
    echo "FAIL: external href not removed\n";
    exit(1);
}

echo "SVG sanitizer PASS\n";
exit(0);
