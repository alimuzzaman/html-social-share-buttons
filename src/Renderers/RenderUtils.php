<?php
namespace HtmlSocialShare\Renderers;

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\UrlUtils;
use HtmlSocialShare\Utils\StringUtils;

/**
 * Pure function utilities for URL generation and content formatting
 * 
 * This class contains only pure functions with no side effects,
 * making them highly testable and reusable.
 * 
 * @since 3.0.0
 */
class RenderUtils
{
    /**
     * Format share count for display
     * 
     * @param int $count Share count
     * @return string Formatted count
     */
    public static function formatCount(int $count): string
    {
        if ($count < 0) {
            return '0';
        }
        
        if ($count < 1000) {
            return (string) $count;
        }
        
        if ($count < 1000000) {
            return round($count / 1000, 1) . 'K';
        }
        
        return round($count / 1000000, 1) . 'M';
    }

    /**
     * Generate share URL from template with replacements
     * 
     * Pure function that takes inputs and returns predictable output
     * without side effects.
     * 
     * @param string $template URL template with placeholders
     * @param string $url URL to share
     * @param string $title Title to share
     * @param array $customReplacements Additional custom replacements
     * @return string Generated share URL
     */
    public static function generateShareUrl(
        string $template,
        string $url,
        string $title = '',
        array $customReplacements = []
    ): string {
        if (empty($template)) {
            return '#';
        }

        // Validate inputs for security
        $url = SecurityUtils::sanitizeUrl($url);
        $title = SecurityUtils::sanitizeTextField($title);

        if (!$url) {
            return '#';
        }

        // Standard replacements
        $replacements = [
            '{url}' => $url,
            '{title}' => $title,
            '{encoded_url}' => rawurlencode($url),
            '{encoded_title}' => rawurlencode($title),
        ];

        // Add sanitized custom replacements
        foreach ($customReplacements as $key => $value) {
            $sanitizedKey = SecurityUtils::sanitizeKey($key);
            $sanitizedValue = SecurityUtils::sanitizeTextField((string) $value);
            $replacements['{' . $sanitizedKey . '}'] = $sanitizedValue;
            $replacements['{encoded_' . $sanitizedKey . '}'] = rawurlencode($sanitizedValue);
        }

        $result = UrlUtils::buildShareUrl($template, $replacements);
        
        // Validate result URL
        return SecurityUtils::sanitizeUrl($result) ?: '#';
    }

    /**
     * Sanitize HTML attributes (wrapper for SecurityUtils)
     * 
     * @param mixed $value Value to sanitize
     * @return string Sanitized value
     */
    public static function sanitizeAttribute($value): string
    {
        return SecurityUtils::escapeAttribute((string) $value);
    }

    /**
     * Sanitize HTML content (wrapper for SecurityUtils)
     * 
     * @param mixed $value Value to sanitize
     * @return string Sanitized value
     */
    public static function sanitizeContent($value): string
    {
        return SecurityUtils::escapeHtml((string) $value);
    }

    /**
     * Build HTML attributes string from array with security
     * 
     * @param array $attributes Key-value pairs of attributes
     * @return string HTML attributes string
     */
    public static function buildAttributes(array $attributes): string
    {
        $parts = [];
        
        foreach ($attributes as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            
            // Validate key
            $sanitizedKey = SecurityUtils::sanitizeKey((string) $key);
            if (!$sanitizedKey) {
                continue;
            }
            
            if ($value === true) {
                $parts[] = self::sanitizeAttribute($sanitizedKey);
            } else {
                $parts[] = self::sanitizeAttribute($sanitizedKey) . '="' . self::sanitizeAttribute($value) . '"';
            }
        }
        
        return implode(' ', $parts);
    }

    /**
     * Generate accessibility attributes for share button
     * 
     * @param string $network Network name
     * @param string $handle Handle or username (optional)
     * @param int $shareCount Share count (optional)
     * @return array Accessibility attributes
     */
    public static function generateA11yAttributes(
        string $network,
        string $handle = '',
        int $shareCount = 0
    ): array {
        $networkLabel = StringUtils::toTitleCase(SecurityUtils::sanitizeTextField($network));
        $handle = SecurityUtils::sanitizeTextField($handle);
        
        $ariaLabel = "Share on {$networkLabel}";
        if (!empty($handle)) {
            $ariaLabel .= " with {$handle}";
        }
        
        $title = "Share on {$networkLabel}";
        
        return [
            'aria-label' => $ariaLabel,
            'title' => $title,
            'role' => 'button',
            'tabindex' => '0'
        ];
    }

    /**
     * Generate screen reader text for share counts
     * 
     * @param int $count Share count
     * @param string $network Network name
     * @return string Screen reader text
     */
    public static function generateShareCountScreenReaderText(int $count, string $network): string
    {
        $networkLabel = StringUtils::toTitleCase(SecurityUtils::sanitizeTextField($network));
        $count = max(0, $count); // Ensure non-negative
        
        if ($count === 0) {
            return "No shares on {$networkLabel}";
        }
        
        if ($count === 1) {
            return "1 share on {$networkLabel}";
        }
        
        return "{$count} shares on {$networkLabel}";
    }

    /**
     * Generate CSS classes for share button with security
     * 
     * @param string $network Network name
     * @param array $modifiers CSS modifier classes
     * @param bool $selected Whether button is selected/active
     * @return string CSS class string
     */
    public static function generateButtonClasses(
        string $network,
        array $modifiers = [],
        bool $selected = false
    ): string {
        $sanitizedNetwork = SecurityUtils::sanitizeKey($network);
        if (!$sanitizedNetwork) {
            $sanitizedNetwork = 'unknown';
        }
        
        $classes = [
            'hssb-share',
            "hssb-{$sanitizedNetwork}"
        ];
        
        if ($selected) {
            $classes[] = 'hssb-selected';
        }
        
        foreach ($modifiers as $modifier) {
            $sanitizedModifier = SecurityUtils::sanitizeKey((string) $modifier);
            if ($sanitizedModifier) {
                $classes[] = "hssb-{$sanitizedModifier}";
            }
        }
        
        return implode(' ', array_map([SecurityUtils::class, 'escapeAttribute'], $classes));
    }

    /**
     * Validate URL template with enhanced security
     * 
     * @param string $template URL template
     * @return bool True if template is valid
     */
    public static function isValidUrlTemplate(string $template): bool
    {
        if (empty($template)) {
            return false;
        }

        // Check for XSS patterns
        if (SecurityUtils::hasXssPatterns($template)) {
            return false;
        }

        return UrlUtils::isValidUrlTemplate($template);
    }

    /**
     * Extract domain from URL (wrapper for UrlUtils)
     * 
     * @param string $url URL to parse
     * @return string|null Domain or null if invalid
     */
    public static function extractDomain(string $url): ?string
    {
        return UrlUtils::extractDomain($url);
    }

    /**
     * Generate unique ID for element with security
     * 
     * @param string $prefix ID prefix
     * @param string $suffix Additional suffix
     * @return string Unique ID
     */
    public static function generateUniqueId(string $prefix = 'hssb', string $suffix = ''): string
    {
        $sanitizedPrefix = SecurityUtils::sanitizeKey($prefix) ?: 'hssb';
        $id = $sanitizedPrefix . '-' . uniqid();
        
        if (!empty($suffix)) {
            $sanitizedSuffix = SecurityUtils::sanitizeKey($suffix);
            if ($sanitizedSuffix) {
                $id .= '-' . $sanitizedSuffix;
            }
        }
        
        return $id;
    }

    /**
     * Parse network configuration from string with validation
     * 
     * @param string $networkString Network string (e.g., "facebook|square" or "facebook")
     * @return array Network configuration
     */
    public static function parseNetworkString(string $networkString): array
    {
        $parts = explode('|', SecurityUtils::sanitizeTextField($networkString), 2);
        
        $network = SecurityUtils::sanitizeKey(trim($parts[0]));
        $iconset = isset($parts[1]) ? SecurityUtils::sanitizeKey(trim($parts[1])) : 'default';
        
        return [
            'network' => $network ?: 'unknown',
            'iconset' => $iconset ?: 'default'
        ];
    }

    /**
     * Normalize network name with security
     * 
     * @param string $network Network name
     * @return string Normalized network name
     */
    public static function normalizeNetworkName(string $network): string
    {
        return SecurityUtils::sanitizeKey(strtolower(trim($network)));
    }

    /**
     * Check if string contains only safe characters for CSS class
     * 
     * @param string $str String to check
     * @return bool True if safe
     */
    public static function isSafeCssClass(string $str): bool
    {
        return SecurityUtils::isAlphanumeric($str, true, true);
    }

    /**
     * Validate share button configuration
     * 
     * @param array $config Button configuration
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateButtonConfig(array $config): array
    {
        $errors = [];
        
        if (empty($config['network'])) {
            $errors[] = "Network is required";
        } elseif (!self::normalizeNetworkName($config['network'])) {
            $errors[] = "Invalid network name: {$config['network']}";
        }
        
        if (!empty($config['url']) && !SecurityUtils::sanitizeUrl($config['url'])) {
            $errors[] = "Invalid URL: {$config['url']}";
        }
        
        if (!empty($config['url_template']) && !self::isValidUrlTemplate($config['url_template'])) {
            $errors[] = "Invalid URL template";
        }
        
        if (isset($config['count']) && (!is_int($config['count']) || $config['count'] < 0)) {
            $errors[] = "Share count must be non-negative integer";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}