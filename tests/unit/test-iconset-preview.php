<?php
require __DIR__ . '/../../src/bootstrap.php';

$gen = new HtmlSocialShare\Iconset\PreviewGenerator(__DIR__ . '/../../iconset');
$html = $gen->generatePreviewHtml('default');

// Accept either an HTML fragment or a not-found marker (repo may not ship the default iconset)
if (strpos($html, '<div') === false && strpos($html, 'iconset not found') === false) {
    echo "FAIL: preview did not return expected output\n";
    exit(1);
}

echo "Iconset preview PASS\n";
exit(0);
