<?php
require __DIR__ . '/../../src/bootstrap.php';

// Define WordPress functions for testing
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($attr) { return $attr; }
}

$registry = new HtmlSocialShare\IconRegistry(null, __DIR__ . '/../../', 'http://example.com/wp-content/plugins/html-social-share-buttons');
$ctrl = new HtmlSocialShare\Admin\IconsController($registry);
$html = $ctrl->index();

if (strpos($html, '<ul') === false) {
    echo "FAIL: Admin IconsController did not render list\n";
    exit(1);
}

echo "Admin IconsController PASS\n";
exit(0);
