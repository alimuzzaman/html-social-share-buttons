<?php
/**
 * Minimal PHPUnit bootstrap for utility testing only
 * Bypasses WordPress completely for pure function testing
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load utility classes directly only if autoloader can't find them
$utilityClasses = [
    'HtmlSocialShare\Utils\SecurityUtils' => __DIR__ . '/../src/Utils/SecurityUtils.php',
    'HtmlSocialShare\Utils\StringUtils' => __DIR__ . '/../src/Utils/StringUtils.php',
    'HtmlSocialShare\Utils\UrlUtils' => __DIR__ . '/../src/Utils/UrlUtils.php',
    'HtmlSocialShare\Utils\ArrayUtils' => __DIR__ . '/../src/Utils/ArrayUtils.php',
    'HtmlSocialShare\Utils\DataUtils' => __DIR__ . '/../src/Utils/DataUtils.php',
];

foreach ($utilityClasses as $class => $file) {
    if (!class_exists($class, false)) {
        require_once $file;
    }
}

// Define minimal constants for any WordPress functions that might be called
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

// Mock WordPress functions that utility classes might call
if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('wp_kses')) {
    function wp_kses($data, $allowed) {
        return strip_tags($data);
    }
}