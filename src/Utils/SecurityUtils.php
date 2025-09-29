<?php
namespace HtmlSocialShare\Utils;

/**
 * Pure security and sanitization functions
 *
 * This class contains only pure functions for input validation,
 * sanitization, and security-related operations without side effects.
 *
 * @since 3.0.0
 */
class SecurityUtils
{
    /**
     * Sanitize text field input (WordPress-style)
     *
     * @param string $value Raw input value
     * @return string Sanitized value
     */
    public static function sanitizeTextField(string $value): string
    {
        // Remove null bytes and control characters (replace with spaces)
        $value = str_replace(chr(0), ' ', $value);
        $value = preg_replace('/[\x01-\x08\x0B\x0C\x0E-\x1F\x7F]/', ' ', $value);

        // Remove script tags and their content
        $value = preg_replace('/<script[^>]*>.*?<\/script>/s', '', $value);

        // Strip remaining HTML tags
        $value = strip_tags($value);

        // Remove line breaks and normalize whitespace
        $value = preg_replace('/[\r\n\t ]+/', ' ', $value);

        return trim($value);
    }

    /**
     * Sanitize key/slug input
     *
     * @param string $key Raw key value
     * @return string Sanitized key (lowercase, alphanumeric, underscore, dash)
     */
    public static function sanitizeKey(string $key): string
    {
        $key = strtolower($key);
        // Convert all non-alphanumeric characters to underscores
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        // Remove multiple underscores
        $key = preg_replace('/_+/', '_', $key);
        return trim($key, '_');
    }

    /**
     * Sanitize HTML class name
     *
     * @param string $class Raw class name
     * @return string Sanitized class name
     */
    public static function sanitizeHtmlClass(string $class): string
    {
        // Remove dangerous patterns
        $class = preg_replace('/\b(on\w+|javascript|script|alert|eval)\b/i', '', $class);
        // Convert special characters to hyphens
        $class = preg_replace('/[^A-Za-z0-9_\-\s]/', '-', $class);
        // Collapse multiple hyphens
        $class = preg_replace('/-+/', '-', $class);
        // Remove extra whitespace
        $class = preg_replace('/\s+/', ' ', $class);
        return trim($class, '- ');
    }

    /**
     * Escape HTML attribute value
     *
     * @param string $value Raw attribute value
     * @return string Escaped attribute value
     */
    public static function escapeAttribute(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
    }

    /**
     * Escape HTML content
     *
     * @param string $content Raw HTML content
     * @return string Escaped HTML content
     */
    public static function escapeHtml(string $content): string
    {
        return htmlspecialchars($content, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
    }

    /**
     * Sanitize URL and validate safety
     *
     * @param string $url Raw URL
     * @return string Sanitized URL or empty string if invalid/dangerous
     */
    public static function sanitizeUrl(string $url): string
    {
        $url = trim($url);

        // Block dangerous protocols
        if (preg_match('/^(javascript|data|vbscript|file|ftp):/i', $url)) {
            return '';
        }

        // Sanitize URL
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Validate URL format
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '';
        }

        return $url;
    }

    /**
     * Validate email address
     *
     * @param string $email Email to validate
     * @return bool True if valid email format
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if string contains only alphanumeric characters
     *
     * @param string $value String to check
     * @param bool $allowUnderscore Whether to allow underscores
     * @param bool $allowDashes Whether to allow dashes
     * @return bool True if valid
     */
    public static function isAlphanumeric(string $value, bool $allowUnderscore = false, bool $allowDashes = false): bool
    {
        $pattern = '/^[a-zA-Z0-9';
        if ($allowUnderscore) {
            $pattern .= '_';
        }
        if ($allowDashes) {
            $pattern .= '\-';
        }
        $pattern .= ']+$/';

        return preg_match($pattern, $value) === 1;
    }

    /**
     * Validate nonce-like token
     *
     * @param string $token Token to validate
     * @param int $minLength Minimum token length
     * @param int $maxLength Maximum token length
     * @return bool True if valid format
     */
    public static function isValidToken(string $token, int $minLength = 8, int $maxLength = 64): bool
    {
        $length = strlen($token);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }

        // Token should contain only safe characters
        return preg_match('/^[a-zA-Z0-9\-_]+$/', $token) === 1;
    }

    /**
     * Generate secure random string
     *
     * @param int $length String length
     * @param string $chars Character set to use
     * @return string Random string
     */
    public static function generateRandomString(int $length = 32, string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
    {
        if ($length <= 0) {
            return '';
        }

        $result = '';
        $charCount = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $charCount - 1)];
        }

        return $result;
    }

    /**
     * Hash password or sensitive data
     *
     * @param string $data Data to hash
     * @param string $salt Optional salt
     * @return string Hashed value
     */
    public static function hashData(string $data, string $salt = ''): string
    {
        return hash('sha256', $salt . $data);
    }

    /**
     * Check for XSS patterns in input
     *
     * @param string $input Input to check
     * @return bool True if potential XSS detected
     */
    public static function hasXssPatterns(string $input): bool
    {
        $patterns = [
            '/<script\b[^>]*>/i',
            '/<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe\b[^>]*>/i',
            '/<object\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
            '/<applet\b[^>]*>/i',
            '/data:\s*text\/html/i',
            '/vbscript:/i',
            '/expression\s*\(/i',
            // Additional patterns for edge cases
            '/alert\s*\(/i',               // Direct alert calls
            '/String\.fromCharCode/i',     // Encoded JavaScript
            '/eval\s*\(/i',               // eval() function
            '/document\.(write|cookie)/i', // Document manipulation
            '/window\.(location|open)/i',  // Window manipulation
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for SQL injection patterns
     *
     * @param string $input Input to check
     * @return bool True if potential SQL injection detected
     */
    public static function hasSqlInjectionPatterns(string $input): bool
    {
        $patterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/\'\s*;\s*--/i',
            '/\'\s*or\s*\'/i',
            '/\'\s*and\s*\'/i',
            '/\bor\b\s*1\s*=\s*1/i',
            '/\bor\b\s*true/i',
            // Additional patterns for missed cases
            '/\/\*.*?\*\//',            // SQL comments like /* comment */
            '/\'\s*\/\*.*?\*\//i',      // Comments like admin'/*
            '/"\s*or\s*""/i',           // Double quote SQL injection
            '/\bexec\b.*?xp_/i',        // SQL Server xp_ commands
            '/waitfor\s+delay/i',       // Time-based SQL injection
            '/\bhaving\b.*?1\s*=\s*1/i', // HAVING clause injection
            '/\bor\b.*?sleep/i',        // MySQL sleep-based injection
            '/benchmark\s*\(/i',        // MySQL benchmark function
            '/pg_sleep\s*\(/i',         // PostgreSQL sleep function
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate file extension against whitelist
     *
     * @param string $filename Filename to check
     * @param array $allowedExtensions Allowed extensions (without dots)
     * @return bool True if extension is allowed
     */
    public static function isAllowedFileExtension(string $filename, array $allowedExtensions): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, array_map('strtolower', $allowedExtensions), true);
    }

    /**
     * Sanitize filename for safe storage
     *
     * @param string $filename Raw filename
     * @return string Sanitized filename
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path traversal attempts
        $filename = str_replace(['../', '..\\'], '', $filename);

        // Use basename to get just the filename
        $filename = basename($filename);

        // Remove dangerous characters
        $filename = preg_replace('/[<>:"|?*\\\\]/', '', $filename);

        // Handle reserved names on Windows
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $reservedNames = ['CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9', 'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'];
        if (in_array(strtoupper($basename), $reservedNames)) {
            $basename = '_' . $basename;
            $filename = $basename . ($extension ? '.' . $extension : '');
        }

        // Ensure it doesn't start with a dot
        $filename = ltrim($filename, '.');

        // Limit length
        if (strlen($filename) > 100) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($basename, 0, 96 - strlen($extension)) . '.' . $extension;
        }

        return $filename ?: 'file.txt';
    }

    /**
     * Check if IP address is in private range
     *
     * @param string $ip IP address to check
     * @return bool True if private IP
     */
    public static function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Rate limit check helper (pure function - doesn't store state)
     *
     * @param array $attempts Array of attempt timestamps
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $timeWindow Time window in seconds
     * @param int $currentTime Current timestamp
     * @return array Result with 'allowed' boolean and 'reset_time' timestamp
     */
    public static function checkRateLimit(array $attempts, int $maxAttempts, int $timeWindow, int $currentTime): array
    {
        // Filter attempts within time window
        $recentAttempts = array_filter($attempts, function($timestamp) use ($currentTime, $timeWindow) {
            return $timestamp > ($currentTime - $timeWindow);
        });

        $allowed = count($recentAttempts) < $maxAttempts;
        $oldestAttempt = empty($recentAttempts) ? $currentTime : min($recentAttempts);
        $resetTime = $oldestAttempt + $timeWindow;

        // Add current attempt to the list
        $recentAttempts[] = $currentTime;

        return [
            'exceeded' => !$allowed,
            'attempts' => array_values($recentAttempts),
            'remaining' => max(0, $maxAttempts - count($recentAttempts) + 1),
            'reset_time' => $resetTime
        ];
    }

    /**
     * Validate CSRF token format (pure validation - doesn't check against stored token)
     *
     * @param string $token Token to validate
     * @return bool True if token has valid format
     */
    public static function isValidCsrfTokenFormat(string $token): bool
    {
        return self::isValidToken($token, 8, 128);
    }

    /**
     * Strip dangerous HTML tags but preserve safe formatting
     *
     * @param string $html Raw HTML
     * @param array $allowedTags Allowed HTML tags
     * @return string Cleaned HTML
     */
    public static function stripDangerousHtml(string $html, array $allowedTags = ['p', 'br', 'strong', 'em']): string
    {
        // First, remove dangerous tags and their content
        $dangerousTags = ['script', 'style', 'iframe', 'object', 'embed', 'applet', 'form', 'input', 'button'];
        foreach ($dangerousTags as $tag) {
            $html = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $html);
            // Also remove self-closing tags
            $html = preg_replace('/<' . $tag . '[^>]*\/?>/i', '', $html);
        }

        // Then strip remaining tags, keeping only allowed ones
        $allowedTagsString = '<' . implode('><', $allowedTags) . '>';
        return strip_tags($html, $allowedTagsString);
    }
}