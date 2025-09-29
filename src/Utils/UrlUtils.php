<?php
namespace HtmlSocialShare\Utils;

/**
 * Pure URL processing and template functions
 *
 * This class contains only pure functions for URL manipulation,
 * template processing, and URL-related operations without side effects.
 *
 * @since 3.0.0
 */
class UrlUtils
{
    /**
     * Build share URL from template and parameters
     *
     * @param string $template URL template with placeholders
     * @param array $params Parameters for replacement
     * @return string Processed URL
     */
    public static function buildShareUrl(string $template, array $params): string
    {
        if (empty($template)) {
            return '';
        }

        // Default parameters
        $defaults = [
            'url' => '',
            'title' => '',
            'description' => '',
            'image' => '',
            'hashtags' => '',
            'via' => ''
        ];

        $params = array_merge($defaults, $params);

        // Replace placeholders with URL-encoded values
        $result = $template;
        foreach ($params as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $encodedValue = urlencode($value);
            $result = str_replace($placeholder, $encodedValue, $result);
        }

        return $result;
    }

    /**
     * Extract domain from URL
     *
     * @param string $url URL to extract from
     * @return string Domain name or empty string if invalid
     */
    public static function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['host'])) {
            return '';
        }

        return strtolower($parsed['host']);
    }

    /**
     * Check if URL is valid
     *
     * @param string $url URL to validate
     * @return bool True if valid URL
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if URL is HTTPS
     *
     * @param string $url URL to check
     * @return bool True if HTTPS
     */
    public static function isHttps(string $url): bool
    {
        $parsed = parse_url($url);
        return isset($parsed['scheme']) && $parsed['scheme'] === 'https';
    }

    /**
     * Get URL scheme (http, https, etc.)
     *
     * @param string $url URL to extract from
     * @return string Scheme or empty string
     */
    public static function getScheme(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['scheme'] ?? '';
    }

    /**
     * Get URL path component
     *
     * @param string $url URL to extract from
     * @return string Path or empty string
     */
    public static function getPath(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['path'] ?? '';
    }

    /**
     * Get URL query parameters as array
     *
     * @param string $url URL to extract from
     * @return array Query parameters
     */
    public static function getQueryParams(string $url): array
    {
        $parsed = parse_url($url);
        if (!isset($parsed['query'])) {
            return [];
        }

        parse_str($parsed['query'], $params);
        return $params;
    }

    /**
     * Add query parameters to URL
     *
     * @param string $url Base URL
     * @param array $params Parameters to add
     * @return string URL with added parameters
     */
    public static function addQueryParams(string $url, array $params): string
    {
        if (empty($params)) {
            return $url;
        }

        $parsed = parse_url($url);
        if (!$parsed) {
            return $url;
        }

        $existingParams = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $existingParams);
        }

        $allParams = array_merge($existingParams, $params);
        $queryString = http_build_query($allParams);

        // Rebuild URL
        $result = '';
        if (isset($parsed['scheme'])) {
            $result .= $parsed['scheme'] . '://';
        }
        if (isset($parsed['host'])) {
            $result .= $parsed['host'];
        }
        if (isset($parsed['port'])) {
            $result .= ':' . $parsed['port'];
        }
        if (isset($parsed['path'])) {
            $result .= $parsed['path'];
        }
        if ($queryString) {
            $result .= '?' . $queryString;
        }
        if (isset($parsed['fragment'])) {
            $result .= '#' . $parsed['fragment'];
        }

        return $result;
    }

    /**
     * Remove query parameters from URL
     *
     * @param string $url URL to clean
     * @param array $paramsToRemove Parameter names to remove
     * @return string Cleaned URL
     */
    public static function removeQueryParams(string $url, array $paramsToRemove): string
    {
        if (empty($paramsToRemove)) {
            return $url;
        }

        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['query'])) {
            return $url;
        }

        parse_str($parsed['query'], $params);

        foreach ($paramsToRemove as $param) {
            unset($params[$param]);
        }

        $queryString = http_build_query($params);

        // Rebuild URL
        $result = '';
        if (isset($parsed['scheme'])) {
            $result .= $parsed['scheme'] . '://';
        }
        if (isset($parsed['host'])) {
            $result .= $parsed['host'];
        }
        if (isset($parsed['port'])) {
            $result .= ':' . $parsed['port'];
        }
        if (isset($parsed['path'])) {
            $result .= $parsed['path'];
        }
        if ($queryString) {
            $result .= '?' . $queryString;
        }
        if (isset($parsed['fragment'])) {
            $result .= '#' . $parsed['fragment'];
        }

        return $result;
    }

    /**
     * Normalize URL (remove trailing slash, lowercase domain, etc.)
     *
     * @param string $url URL to normalize
     * @return string Normalized URL
     */
    public static function normalizeUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed) {
            return $url;
        }

        // Normalize components
        $scheme = strtolower($parsed['scheme'] ?? 'http');
        $host = strtolower($parsed['host'] ?? '');
        $port = $parsed['port'] ?? null;
        $path = $parsed['path'] ?? '/';
        $query = $parsed['query'] ?? null;
        $fragment = $parsed['fragment'] ?? null;

        // Normalize path - remove multiple slashes
        $path = preg_replace('#/+#', '/', $path);
        
        // Remove trailing slash from path (except root)
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        // Remove default ports
        if (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443)) {
            $port = null;
        }

        // Rebuild URL
        $result = $scheme . '://' . $host;
        if ($port) {
            $result .= ':' . $port;
        }
        $result .= $path;
        if ($query) {
            $result .= '?' . $query;
        }
        if ($fragment) {
            $result .= '#' . $fragment;
        }

        return $result;
    }

    /**
     * Check if URL matches a domain pattern
     *
     * @param string $url URL to check
     * @param string $pattern Domain pattern (e.g., '*.example.com')
     * @return bool True if matches
     */
    public static function matchesDomainPattern(string $url, string $pattern): bool
    {
        $domain = self::extractDomain($url);
        if (!$domain) {
            return false;
        }

        // Convert pattern to regex
        $regex = str_replace(['*', '.'], ['.*', '\.'], $pattern);
        $regex = '/^' . $regex . '$/i';

        return preg_match($regex, $domain) === 1;
    }

    /**
     * Get relative URL from absolute URL
     *
     * @param string $url Absolute URL
     * @param string $baseUrl Base URL to make relative to
     * @return string Relative URL
     */
    public static function makeRelative(string $url, string $baseUrl): string
    {
        $urlParts = parse_url($url);
        $baseParts = parse_url($baseUrl);

        if (!$urlParts || !$baseParts) {
            return $url;
        }

        // If different domains, return absolute URL
        if (($urlParts['host'] ?? '') !== ($baseParts['host'] ?? '')) {
            return $url;
        }

        $path = $urlParts['path'] ?? '/';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';
        $fragment = isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : '';

        return $path . $query . $fragment;
    }

    /**
     * Make absolute URL from relative URL
     *
     * @param string $relativeUrl Relative URL
     * @param string $baseUrl Base URL
     * @return string Absolute URL
     */
    public static function makeAbsolute(string $relativeUrl, string $baseUrl): string
    {
        // Already absolute
        if (self::isValidUrl($relativeUrl)) {
            return $relativeUrl;
        }

        $baseParts = parse_url($baseUrl);
        if (!$baseParts) {
            return $relativeUrl;
        }

        $scheme = $baseParts['scheme'] ?? 'http';
        $host = $baseParts['host'] ?? '';
        $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
        $basePath = $baseParts['path'] ?? '/';

        // Handle different relative URL formats
        if (substr($relativeUrl, 0, 1) === '/') {
            // Absolute path
            return $scheme . '://' . $host . $port . $relativeUrl;
        } else {
            // Relative path
            $basePath = dirname($basePath);
            if ($basePath === '.') {
                $basePath = '/';
            }
            return $scheme . '://' . $host . $port . $basePath . '/' . $relativeUrl;
        }
    }

    /**
     * Shorten URL by removing unnecessary parts
     *
     * @param string $url URL to shorten
     * @param int $maxLength Maximum length
     * @return string Shortened URL
     */
    public static function shortenUrl(string $url, int $maxLength = 50): string
    {
        if (strlen($url) <= $maxLength) {
            return $url;
        }

        $parsed = parse_url($url);
        if (!$parsed) {
            return substr($url, 0, $maxLength - 3) . '...';
        }

        $domain = $parsed['host'] ?? '';
        $path = $parsed['path'] ?? '/';

        // If domain is too long, truncate it
        if (strlen($domain) > $maxLength - 10) {
            return substr($domain, 0, $maxLength - 6) . '...';
        }

        // Try to fit domain + shortened path
        $availableLength = $maxLength - strlen($domain) - 6; // Account for protocol and ellipsis

        if ($availableLength > 0 && strlen($path) > $availableLength) {
            $path = substr($path, 0, $availableLength) . '...';
        }

        return $domain . $path;
    }

    /**
     * Extract social media username from URL
     *
     * @param string $url Social media profile URL
     * @return string Username or empty string
     */
    public static function extractSocialUsername(string $url): string
    {
        $domain = self::extractDomain($url);
        $path = self::getPath($url);

        // Common social media patterns
        $patterns = [
            'twitter.com' => '/^\/([a-zA-Z0-9_]+)$/',
            'instagram.com' => '/^\/([a-zA-Z0-9_.]+)$/',
            'linkedin.com' => '/^\/in\/([a-zA-Z0-9-]+)$/',
            'facebook.com' => '/^\/([a-zA-Z0-9.]+)$/',
            'youtube.com' => '/^\/(c|channel|user)\/([a-zA-Z0-9_-]+)$/',
            'tiktok.com' => '/^\/(@[a-zA-Z0-9_.]+)$/',
            'github.com' => '/^\/([a-zA-Z0-9-]+)$/',
        ];

        if (isset($patterns[$domain])) {
            $matches = [];
            if (preg_match($patterns[$domain], $path, $matches)) {
                return end($matches); // Return the last captured group
            }
        }

        return '';
    }

    /**
     * Build share URLs for multiple networks
     *
     * @param array $networks Network configurations with templates
     * @param array $params Common parameters
     * @return array Array of network => URL pairs
     */
    public static function buildShareUrls(array $networks, array $params): array
    {
        $urls = [];

        foreach ($networks as $network => $config) {
            if (isset($config['url_template'])) {
                $urls[$network] = self::buildShareUrl($config['url_template'], $params);
            }
        }

        return $urls;
    }

    /**
     * Validate URL template format
     *
     * @param string $template Template to validate
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateUrlTemplate(string $template): array
    {
        $errors = [];

        if (empty($template)) {
            $errors[] = 'URL template cannot be empty';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check for required placeholders
        if (strpos($template, '{url}') === false && strpos($template, '{encoded_url}') === false) {
            $errors[] = 'URL template must contain {url} or {encoded_url} placeholder';
        }

        // Test with sample values
        $testParams = [
            'url' => 'https://example.com/test',
            'title' => 'Test Title',
            'description' => 'Test Description'
        ];

        $testUrl = self::buildShareUrl($template, $testParams);
        if (!self::isValidUrl($testUrl)) {
            $errors[] = 'URL template produces invalid URL';
        }

        // Check for dangerous patterns
        if (SecurityUtils::hasXssPatterns($template)) {
            $errors[] = 'URL template contains potentially dangerous content';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Parse share URL and extract parameters
     *
     * @param string $shareUrl Share URL to parse
     * @param string $template Template used to build the URL
     * @return array Extracted parameters
     */
    public static function parseShareUrl(string $shareUrl, string $template): array
    {
        // This is a simplified implementation
        // In practice, this would need more sophisticated regex-based parsing
        $params = [];
        $queryParams = self::getQueryParams($shareUrl);

        // Common parameter mappings
        $mappings = [
            'url' => ['url', 'u'],
            'title' => ['title', 'text', 't'],
            'description' => ['description', 'summary', 'desc'],
            'hashtags' => ['hashtags', 'tags'],
            'via' => ['via', 'source']
        ];

        foreach ($mappings as $param => $aliases) {
            foreach ($aliases as $alias) {
                if (isset($queryParams[$alias])) {
                    $params[$param] = rawurldecode($queryParams[$alias]);
                    break;
                }
            }
        }

        return $params;
    }
}