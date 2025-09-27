<?php
require __DIR__ . '/../src/bootstrap.php';

// Define WordPress functions for testing
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($attr) { return $attr; }
}

$registry = new HtmlSocialShare\IconRegistry(null, __DIR__ . '/../', 'http://example.com/wp-content/plugins/html-social-share-buttons');
$renderer = new HtmlSocialShare\ShareRenderer($registry);
$html = $renderer->render('twitter', ['handle' => '@example']);

if (strpos($html, '<a') === false || strpos($html, 'twitter') === false) {
    echo "FAIL: render did not produce expected output\n";
    exit(1);
}

echo "Render integration PASS\n";
exit(0);
