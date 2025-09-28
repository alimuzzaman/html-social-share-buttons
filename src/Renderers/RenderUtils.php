<?php
namespace HtmlSocialShare\Renderers;

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

        // Standard replacements
        $replacements = [
            '{url}' => urlencode($url),
            '{title}' => urlencode($title),
            '{encoded_url}' => urlencode($url),
            '{encoded_title}' => urlencode($title),
        ];

        // Merge with custom replacements
        $replacements = array_merge($replacements, $customReplacements);

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Sanitize HTML attributes
     * 
     * @param mixed $value Value to sanitize
     * @return string Sanitized value
     */
    public static function sanitizeAttribute($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Sanitize HTML content
     * 
     * @param mixed $value Value to sanitize
     * @return string Sanitized value
     */
    public static function sanitizeContent($value): string
    {
        return htmlspecialchars((string)$value, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Build HTML attributes string from array
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
            
            if ($value === true) {
                $parts[] = self::sanitizeAttribute($key);
            } else {
                $parts[] = self::sanitizeAttribute($key) . '="' . self::sanitizeAttribute($value) . '"';
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
        $networkLabel = ucfirst($network);
        
        $ariaLabel = "Share on {$networkLabel}";
        if (!empty($handle)) {
            $ariaLabel .= " with {$handle}";
        }
        
        $title = "Share on {$networkLabel}";
        
        return [
            'aria-label' => $ariaLabel,
            'title' => $title,
            'role' => 'button'
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
        $networkLabel = ucfirst($network);
        
        if ($count === 0) {
            return "No shares on {$networkLabel}";
        }
        
        if ($count === 1) {
            return "1 share on {$networkLabel}";
        }
        
        return "{$count} shares on {$networkLabel}";
    }

    /**
     * Generate CSS classes for share button
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
        $classes = [
            'hssb-share',
            "hssb-{$network}"
        ];
        
        if ($selected) {
            $classes[] = 'hssb-selected';
        }
        
        foreach ($modifiers as $modifier) {
            $classes[] = "hssb-{$modifier}";
        }
        
        return implode(' ', array_map([self::class, 'sanitizeAttribute'], $classes));
    }

    /**
     * Validate URL template
     * 
     * @param string $template URL template
     * @return bool True if template is valid
     */
    public static function isValidUrlTemplate(string $template): bool
    {
        if (empty($template)) {
            return false;
        }

        // Must contain at least {url} placeholder
        if (strpos($template, '{url}') === false && strpos($template, '{encoded_url}') === false) {
            return false;
        }

        // Must be a valid URL structure
        return filter_var(
            str_replace(['{url}', '{title}', '{encoded_url}', '{encoded_title}'], 
                       ['http://example.com', 'title', 'http://example.com', 'title'], 
                       $template),
            FILTER_VALIDATE_URL
        ) !== false;
    }

    /**
     * Extract domain from URL
     * 
     * @param string $url URL to parse
     * @return string|null Domain or null if invalid
     */
    public static function extractDomain(string $url): ?string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? null;
    }

    /**
     * Generate unique ID for element
     * 
     * @param string $prefix ID prefix
     * @param string $suffix Additional suffix
     * @return string Unique ID
     */
    public static function generateUniqueId(string $prefix = 'hssb', string $suffix = ''): string
    {
        $id = $prefix . '-' . uniqid();
        
        if (!empty($suffix)) {
            $id .= '-' . self::sanitizeAttribute($suffix);
        }
        
        return $id;
    }

    /**
     * Parse network configuration from string
     * 
     * @param string $networkString Network string (e.g., "facebook|square" or "facebook")
     * @return array Network configuration
     */
    public static function parseNetworkString(string $networkString): array
    {
        $parts = explode('|', $networkString, 2);
        
        return [
            'network' => trim($parts[0]),
            'iconset' => isset($parts[1]) ? trim($parts[1]) : 'default'
        ];
    }

    /**
     * Normalize network name
     * 
     * @param string $network Network name
     * @return string Normalized network name
     */
    public static function normalizeNetworkName(string $network): string
    {
        return strtolower(trim($network));
    }

    /**
     * Check if string contains only safe characters for CSS class
     * 
     * @param string $str String to check
     * @return bool True if safe
     */
    public static function isSafeCssClass(string $str): bool
    {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $str) === 1;
    }
}