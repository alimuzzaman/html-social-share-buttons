<?php
namespace HtmlSocialShare\Utils;

/**
 * Pure validation and data processing functions
 * 
 * This class contains only pure functions with no side effects,
 * dependencies on global state, or WordPress functions.
 * All functions are deterministic and easily testable.
 * 
 * @since 3.0.0
 */
class DataUtils
{
    /**
     * Validate network configuration array
     * 
     * @param array $config Network configuration
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateNetworkConfig(array $config): array
    {
        $errors = [];
        
        // Check required fields
        $requiredFields = ['network', 'label'];
        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        // Validate network name format
        if (isset($config['network']) && !self::isValidNetworkName($config['network'])) {
            $errors[] = "Invalid network name format: {$config['network']}";
        }

        // Validate URL template if provided
        if (!empty($config['url_template']) && !self::isValidUrlTemplate($config['url_template'])) {
            $errors[] = "Invalid URL template: {$config['url_template']}";
        }

        // Validate color if provided
        if (!empty($config['color']) && !self::isValidHexColor($config['color'])) {
            $errors[] = "Invalid color format: {$config['color']}";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate profile configuration array
     * 
     * @param array $profile Profile configuration
     * @return array Validation result
     */
    public static function validateProfileConfig(array $profile): array
    {
        $errors = [];
        
        // Check required fields
        if (empty($profile['network'])) {
            $errors[] = "Missing required field: network";
        }

        // Validate handle format for specific networks
        if (!empty($profile['handle'])) {
            $network = $profile['network'] ?? '';
            $handle = $profile['handle'];
            
            if (!self::isValidHandle($network, $handle)) {
                $errors[] = "Invalid handle format for {$network}: {$handle}";
            }
        }

        // Validate URL if provided
        if (!empty($profile['url']) && !self::isValidUrl($profile['url'])) {
            $errors[] = "Invalid URL format: {$profile['url']}";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Sanitize and normalize network configuration
     * 
     * @param array $config Raw configuration
     * @return array Sanitized configuration
     */
    public static function sanitizeNetworkConfig(array $config): array
    {
        $sanitized = [];

        // Network name - lowercase, alphanumeric + underscore
        if (isset($config['network'])) {
            $sanitized['network'] = self::sanitizeNetworkName($config['network']);
        }

        // Label - plain text
        if (isset($config['label'])) {
            $sanitized['label'] = self::sanitizeText($config['label']);
        }

        // URL template - URL format
        if (isset($config['url_template'])) {
            $sanitized['url_template'] = self::sanitizeUrl($config['url_template']);
        }

        // Color - hex format
        if (isset($config['color'])) {
            $sanitized['color'] = self::sanitizeHexColor($config['color']);
        }

        // Boolean fields
        $booleanFields = ['enabled', 'visible', 'requires_login'];
        foreach ($booleanFields as $field) {
            if (isset($config[$field])) {
                $sanitized[$field] = self::sanitizeBoolean($config[$field]);
            }
        }

        // Numeric fields
        $numericFields = ['order', 'priority'];
        foreach ($numericFields as $field) {
            if (isset($config[$field])) {
                $sanitized[$field] = self::sanitizeInteger($config[$field]);
            }
        }

        // Description - HTML allowed
        if (isset($config['description'])) {
            $sanitized['description'] = self::sanitizeHtml($config['description']);
        }

        return $sanitized;
    }

    /**
     * Sanitize and normalize profile configuration
     * 
     * @param array $profile Raw profile
     * @return array Sanitized profile
     */
    public static function sanitizeProfileConfig(array $profile): array
    {
        $sanitized = [];

        // Network name
        if (isset($profile['network'])) {
            $sanitized['network'] = self::sanitizeNetworkName($profile['network']);
        }

        // Handle - network-specific format
        if (isset($profile['handle'])) {
            $network = $sanitized['network'] ?? ($profile['network'] ?? '');
            $sanitized['handle'] = self::sanitizeHandle($network, $profile['handle']);
        }

        // URL
        if (isset($profile['url'])) {
            $sanitized['url'] = self::sanitizeUrl($profile['url']);
        }

        // Type
        if (isset($profile['type'])) {
            $sanitized['type'] = self::sanitizeText($profile['type']);
        }

        // Boolean fields
        $booleanFields = ['visible', 'enabled'];
        foreach ($booleanFields as $field) {
            if (isset($profile[$field])) {
                $sanitized[$field] = self::sanitizeBoolean($profile[$field]);
            }
        }

        return $sanitized;
    }

    /**
     * Check if network name is valid
     * 
     * @param string $network Network name
     * @return bool True if valid
     */
    public static function isValidNetworkName(string $network): bool
    {
        return preg_match('/^[a-z0-9_]+$/', $network) === 1;
    }

    /**
     * Check if URL template is valid
     * 
     * @param string $template URL template
     * @return bool True if valid
     */
    public static function isValidUrlTemplate(string $template): bool
    {
        if (empty($template)) {
            return false;
        }

        // Must contain URL placeholder
        if (strpos($template, '{url}') === false && strpos($template, '{encoded_url}') === false) {
            return false;
        }

        // Test with sample values
        $testUrl = str_replace(
            ['{url}', '{title}', '{encoded_url}', '{encoded_title}'],
            ['http://example.com', 'Test', 'http://example.com', 'Test'],
            $template
        );

        return filter_var($testUrl, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if hex color is valid
     * 
     * @param string $color Color string
     * @return bool True if valid
     */
    public static function isValidHexColor(string $color): bool
    {
        return preg_match('/^#[a-fA-F0-9]{6}$/', $color) === 1;
    }

    /**
     * Check if URL is valid
     * 
     * @param string $url URL string
     * @return bool True if valid
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if handle is valid for network
     * 
     * @param string $network Network name
     * @param string $handle Handle string
     * @return bool True if valid
     */
    public static function isValidHandle(string $network, string $handle): bool
    {
        switch ($network) {
            case 'twitter':
                return preg_match('/^@?[a-zA-Z0-9_]{1,15}$/', $handle) === 1;
            
            case 'instagram':
                return preg_match('/^@?[a-zA-Z0-9_.]{1,30}$/', $handle) === 1;
            
            case 'linkedin':
                return preg_match('/^[a-zA-Z0-9-]{3,100}$/', $handle) === 1;
            
            case 'facebook':
                return preg_match('/^[a-zA-Z0-9.]{5,50}$/', $handle) === 1;
            
            default:
                // Generic validation - no special characters except underscore and dash
                return preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $handle) === 1;
        }
    }

    /**
     * Sanitize network name
     * 
     * @param string $network Raw network name
     * @return string Sanitized network name
     */
    public static function sanitizeNetworkName(string $network): string
    {
        return strtolower(preg_replace('/[^a-z0-9_]/', '', $network));
    }

    /**
     * Sanitize plain text
     * 
     * @param string $text Raw text
     * @return string Sanitized text
     */
    public static function sanitizeText(string $text): string
    {
        return trim(strip_tags($text));
    }

    /**
     * Sanitize URL
     * 
     * @param string $url Raw URL
     * @return string Sanitized URL
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize hex color
     * 
     * @param string $color Raw color
     * @return string Sanitized color
     */
    public static function sanitizeHexColor(string $color): string
    {
        $color = trim($color);
        
        // Add # if missing
        if (substr($color, 0, 1) !== '#') {
            $color = '#' . $color;
        }

        // Remove invalid characters
        $color = preg_replace('/[^#a-fA-F0-9]/', '', $color);

        // Ensure valid length
        if (strlen($color) === 7 && self::isValidHexColor($color)) {
            return $color;
        }

        // Default fallback
        return '#000000';
    }

    /**
     * Sanitize boolean value
     * 
     * @param mixed $value Raw value
     * @return bool Sanitized boolean
     */
    public static function sanitizeBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    /**
     * Sanitize integer value
     * 
     * @param mixed $value Raw value
     * @return int Sanitized integer
     */
    public static function sanitizeInteger($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize HTML (basic allowed tags)
     * 
     * @param string $html Raw HTML
     * @return string Sanitized HTML
     */
    public static function sanitizeHtml(string $html): string
    {
        $allowedTags = '<p><br><strong><em><a><span>';
        return strip_tags(trim($html), $allowedTags);
    }

    /**
     * Sanitize handle for specific network
     * 
     * @param string $network Network name
     * @param string $handle Raw handle
     * @return string Sanitized handle
     */
    public static function sanitizeHandle(string $network, string $handle): string
    {
        $handle = trim($handle);

        switch ($network) {
            case 'twitter':
                // Remove @ if present, keep only valid characters
                $handle = ltrim($handle, '@');
                $handle = preg_replace('/[^a-zA-Z0-9_]/', '', $handle);
                return substr($handle, 0, 15);
            
            case 'instagram':
                $handle = ltrim($handle, '@');
                $handle = preg_replace('/[^a-zA-Z0-9_.]/', '', $handle);
                return substr($handle, 0, 30);
            
            case 'linkedin':
                $handle = preg_replace('/[^a-zA-Z0-9-]/', '', $handle);
                return substr($handle, 0, 100);
            
            case 'facebook':
                $handle = preg_replace('/[^a-zA-Z0-9.]/', '', $handle);
                return substr($handle, 0, 50);
            
            default:
                $handle = preg_replace('/[^a-zA-Z0-9_-]/', '', $handle);
                return substr($handle, 0, 50);
        }
    }

    /**
     * Merge network configurations with priority
     * 
     * @param array $base Base configuration
     * @param array $override Override configuration
     * @return array Merged configuration
     */
    public static function mergeNetworkConfigs(array $base, array $override): array
    {
        $merged = $base;

        foreach ($override as $key => $value) {
            if ($value !== null && $value !== '') {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Filter array by allowed keys
     * 
     * @param array $data Input data
     * @param array $allowedKeys Allowed keys
     * @return array Filtered data
     */
    public static function filterByAllowedKeys(array $data, array $allowedKeys): array
    {
        return array_intersect_key($data, array_flip($allowedKeys));
    }

    /**
     * Convert string to array (comma-separated)
     * 
     * @param string|array $value Input value
     * @return array Array value
     */
    public static function toArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            return array_filter(array_map('trim', explode(',', $value)));
        }

        return [];
    }

    /**
     * Convert array to string (comma-separated)
     * 
     * @param array|string $value Input value
     * @return string String value
     */
    public static function toString($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            return implode(', ', array_filter($value));
        }

        return (string) $value;
    }
}