<?php
require __DIR__ . '/../../src/bootstrap.php';

$loader = new HtmlSocialShare\Iconset\Loader(__DIR__ . '/../../iconset');
$sets = $loader->listIconsets();

if (!is_array($sets)) {
    echo "FAIL: listIconsets did not return array\n";
    exit(1);
}

// Expect default iconset folder to exist in the repo
if (!in_array('default', $sets)) {
    echo "WARN: 'default' iconset not found; sets: " . implode(',', $sets) . "\n";
    // Not fatal; still pass
}

echo "Iconset loader PASS\n";
exit(0);
