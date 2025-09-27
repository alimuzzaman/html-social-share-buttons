<?php
require __DIR__ . '/../../src/bootstrap.php';

$container = require __DIR__ . '/../../src/bootstrap.php';
$sanitizer = null;
if (is_object($container) && method_exists($container, 'get')) {
    try {
        $sanitizer = $container->get('svg_sanitizer');
    } catch (Exception $e) {
        $sanitizer = null;
    }
}

if (! $sanitizer && class_exists('HtmlSocialShare\\Svg\\Sanitizer')) {
    $sanitizer = new HtmlSocialShare\Svg\Sanitizer();
}

if (! $sanitizer) {
    echo "SKIP: sanitizer not available\n";
    exit(0);
}

function random_svg_like($seed) {
    $parts = [
        '<svg xmlns="http://www.w3.org/2000/svg">',
        '<defs><style>.' . $seed . '{fill:#' . dechex(rand(0,0xFFFFFF)) . '}</style></defs>',
        '<rect width="' . rand(0,500) . '" height="' . rand(0,500) . '" />',
        '<g onload="' . str_repeat('x', rand(0,50)) . '"></g>',
        '<!-- ' . bin2hex(random_bytes(8)) . ' -->',
        '</svg>'
    ];
    shuffle($parts);
    return implode("", $parts);
}

$failures = [];
for ($i = 0; $i < ($argv[1] ?? 50); $i++) {
    $input = random_svg_like($i);
    try {
        $out = $sanitizer->sanitize($input);
        if (!is_string($out)) {
            $failures[] = [ 'input' => $input, 'reason' => 'non-string output' ];
        }
    } catch (Throwable $t) {
        $failures[] = [ 'input' => $input, 'exception' => $t->getMessage() ];
    }
}

if (!empty($failures)) {
    file_put_contents(__DIR__ . '/failures.json', json_encode($failures, JSON_PRETTY_PRINT));
    echo "FOUND FAILURES: " . count($failures) . " (wrote failures.json)\n";
    exit(2);
}

echo "Fuzzer PASS (no failures)\n";
exit(0);
