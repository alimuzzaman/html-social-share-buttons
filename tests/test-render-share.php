<?php
require __DIR__ . '/../src/bootstrap.php';

$renderer = new HtmlSocialShare\ShareRenderer();
$html = $renderer->render('twitter', ['handle' => '@example']);

if (strpos($html, '<a') === false || strpos($html, 'twitter') === false) {
    echo "FAIL: render did not produce expected output\n";
    exit(1);
}

echo "Render integration PASS\n";
exit(0);
