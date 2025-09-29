<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\ArrayUtils;

/**
 * WordPress-native cache implementation using transients
 *
 * Provides persistent caching using WordPress transients API with fallback to object cache.
 * Automatically handles cache expiration and cleanup.
 *
 * @package HtmlSocialShare
 * @since 3.1.0
 */
class WordPressCache implements CacheInterface
{
    /** @var string Cache prefix to avoid conflicts */
    private const CACHE_PREFIX = 'hss_cache_';

    /** @var int Default TTL in seconds (12 hours) */
    private const DEFAULT_TTL = 43200;

    /** @var array In-memory cache for current request */
    private array $requestCache = [];

    /** @var array Cache statistics */
    private array $stats = [
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0,
        'transient_hits' => 0,
        'request_cache_hits' => 0,
    ];

    public function set(string $key, $value, ?int $ttl = null): bool
    {
        try {
            $cacheKey = $this->buildCacheKey($key);
            $ttl = $ttl ?? self::DEFAULT_TTL;

            // Store in WordPress transients for persistence
            $success = set_transient($cacheKey, $value, $ttl);

            // Also store in request cache for faster access
            $this->requestCache[$key] = [
                'value' => $value,
                'expires' => time() + $ttl,
            ];

            $this->stats['sets']++;
            return $success;
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to set cache key '{$key}': " . $e->getMessage());
            return false;
        }
    }

    public function get(string $key, $default = null)
    {
        try {
            // Check request cache first
            if (isset($this->requestCache[$key])) {
                $entry = $this->requestCache[$key];
                if (time() < $entry['expires']) {
                    $this->stats['hits']++;
                    $this->stats['request_cache_hits']++;
                    return $entry['value'];
                } else {
                    // Expired, remove from request cache
                    unset($this->requestCache[$key]);
                }
            }

            // Check WordPress transients
            $cacheKey = $this->buildCacheKey($key);
            $value = get_transient($cacheKey);

            if ($value !== false) {
                // Store in request cache for faster subsequent access
                $this->requestCache[$key] = [
                    'value' => $value,
                    'expires' => time() + self::DEFAULT_TTL, // Approximate expiration
                ];

                $this->stats['hits']++;
                $this->stats['transient_hits']++;
                return $value;
            }

            $this->stats['misses']++;
            return $default;
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to get cache key '{$key}': " . $e->getMessage());
            $this->stats['misses']++;
            return $default;
        }
    }

    public function delete(string $key): bool
    {
        try {
            $cacheKey = $this->buildCacheKey($key);

            // Remove from both caches
            unset($this->requestCache[$key]);
            $success = delete_transient($cacheKey);

            $this->stats['deletes']++;
            return $success;
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to delete cache key '{$key}': " . $e->getMessage());
            return false;
        }
    }

    public function clear(): bool
    {
        try {
            // Clear request cache
            $this->requestCache = [];

            // Clear WordPress transients with our prefix
            $this->clearTransientsByPrefix();

            return true;
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to clear cache: " . $e->getMessage());
            return false;
        }
    }

    public function has(string $key): bool
    {
        // Check request cache
        if (isset($this->requestCache[$key])) {
            $entry = $this->requestCache[$key];
            if (time() < $entry['expires']) {
                return true;
            }
            unset($this->requestCache[$key]);
        }

        // Check WordPress transients
        $cacheKey = $this->buildCacheKey($key);
        return get_transient($cacheKey) !== false;
    }

    public function multiple(array $keys): array
    {
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }

        return $results;
    }

    public function setMultiple(array $items, ?int $ttl = null): bool
    {
        $success = true;

        foreach ($items as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }

        return $success;
    }

    public function deleteMultiple(array $keys): bool
    {
        $success = true;

        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $success = false;
            }
        }

        return $success;
    }

    public function deletePattern(string $pattern): int
    {
        global $wpdb;

        if (!$wpdb instanceof \wpdb) {
            return 0;
        }

        $deletedCount = 0;

        try {
            // Convert pattern to SQL LIKE pattern
            $sqlPattern = str_replace('*', '%', $pattern);
            $cachePattern = self::CACHE_PREFIX . $sqlPattern;

            // Delete from request cache first
            foreach (array_keys($this->requestCache) as $key) {
                if (fnmatch($pattern, $key)) {
                    unset($this->requestCache[$key]);
                    $deletedCount++;
                }
            }

            // Delete from WordPress transients
            $result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                    '_transient_' . $cachePattern,
                    '_transient_timeout_' . $cachePattern
                )
            );

            if ($result !== false) {
                $deletedCount += intval($result / 2); // Each transient has a timeout entry
            }
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to delete by pattern '{$pattern}': " . $e->getMessage());
        }

        return $deletedCount;
    }

    public function getTtl(string $key): ?int
    {
        try {
            // Check request cache first
            if (isset($this->requestCache[$key])) {
                $entry = $this->requestCache[$key];
                $remaining = $entry['expires'] - time();
                return max(0, $remaining);
            }

            // WordPress transients don't expose TTL directly
            // We can only check if the key exists
            $cacheKey = $this->buildCacheKey($key);
            if (get_transient($cacheKey) !== false) {
                // We can't determine exact TTL from WordPress transients
                // Return a conservative estimate
                return self::DEFAULT_TTL;
            }

            return null; // Key not found
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to get TTL for key '{$key}': " . $e->getMessage());
            return null;
        }
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Get cache information and statistics
     *
     * @return array Cache information including size, statistics, and cleanup status
     */
    public function getInfo(): array
    {
        return [
            'type' => 'WordPress Transients',
            'request_cache_size' => count($this->requestCache),
            'stats' => $this->stats,
            'hit_ratio' => $this->calculateHitRatio(),
        ];
    }

    /**
     * Build cache key with prefix to avoid conflicts
     *
     * @param string $key Original cache key
     * @return string Prefixed cache key
     */
    private function buildCacheKey(string $key): string
    {
        // Ensure key is valid for WordPress transients (max 45 characters)
        $cacheKey = self::CACHE_PREFIX . $key;

        if (strlen($cacheKey) > 45) {
            // Use hash for long keys
            $cacheKey = self::CACHE_PREFIX . md5($key);
        }

        return $cacheKey;
    }

    /**
     * Clear WordPress transients with our prefix
     *
     * This is a best-effort cleanup since WordPress doesn't provide
     * a native way to clear transients by prefix.
     */
    private function clearTransientsByPrefix(): void
    {
        global $wpdb;

        if (!$wpdb instanceof \wpdb) {
            return;
        }

        try {
            // Clear from options table (non-persistent transients)
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                    '_transient_' . self::CACHE_PREFIX . '%',
                    '_transient_timeout_' . self::CACHE_PREFIX . '%'
                )
            );

            // Clear from meta table if using persistent object cache
            if (wp_using_ext_object_cache()) {
                wp_cache_flush_group(self::CACHE_PREFIX);
            }
        } catch (\Exception $e) {
            error_log("HSS Cache: Failed to clear transients by prefix: " . $e->getMessage());
        }
    }

    /**
     * Calculate cache hit ratio
     *
     * @return float Hit ratio as percentage (0.0 to 100.0)
     */
    private function calculateHitRatio(): float
    {
        $totalRequests = $this->stats['hits'] + $this->stats['misses'];

        if ($totalRequests === 0) {
            return 0.0;
        }

        return ($this->stats['hits'] / $totalRequests) * 100;
    }
}