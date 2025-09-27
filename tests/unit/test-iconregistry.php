<?php
require __DIR__ . '/../../src/bootstrap.php';

// Define WordPress functions for testing
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($attr) { return $attr; }
}

$reg = new HtmlSocialShare\IconRegistry(null, __DIR__ . '/../../', 'http://example.com/wp-content/plugins/html-social-share-buttons');

// initial list should contain at least 'twitter' from constructor
$list = $reg->listIcons();
if (!in_array('twitter', $list)) {
    echo "FAIL: initial icons missing twitter\n";
    exit(1);
}

$reg->registerIcon('foo/bar.svg', '<svg></svg>');
if (!$reg->hasIcon('foo/bar.svg')) {
    echo "FAIL: did not register icon\n";
    exit(1);
}

$svg = $reg->getIcon('foo/bar.svg');
if (strpos($svg, '<svg') === false) {
    echo "FAIL: getIcon returned invalid svg\n";
    exit(1);
}

echo "IconRegistry PASS\n";
exit(0);
