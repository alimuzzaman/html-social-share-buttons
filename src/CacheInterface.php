<?php
namespace HtmlSocialShare;

/**
 * Cache interface for storing and retrieving temporary data
 *
 * Provides PSR-16 Simple Cache compatible methods with additional
 * functionality for pattern-based operations and cache statistics.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
interface CacheInterface
{
    /**
     * Set a value in cache with optional TTL in seconds.
     *
     * @param string $key Cache key
     * @param mixed $value Value to store
     * @param int|null $ttl Time to live in seconds, null for no expiration
     * @return bool True on success, false on failure
     */
    public function set(string $key, $value, ?int $ttl = null): bool;

    /**
     * Get a value from cache or null if missing/expired.
     *
     * @param string $key Cache key
     * @param mixed $default Default value if key not found
     * @return mixed Cached value or default
     */
    public function get(string $key, $default = null);

    /**
     * Check if a cache key exists and is not expired.
     *
     * @param string $key Cache key
     * @return bool True if key exists and is valid
     */
    public function has(string $key): bool;

    /**
     * Delete a cache key.
     *
     * @param string $key Cache key
     * @return bool True on success, false on failure
     */
    public function delete(string $key): bool;

    /**
     * Delete multiple cache keys.
     *
     * @param array $keys Array of cache keys
     * @return bool True if all keys deleted successfully
     */
    public function deleteMultiple(array $keys): bool;

    /**
     * Delete cache keys matching a pattern.
     *
     * @param string $pattern Pattern to match (supports wildcards)
     * @return int Number of keys deleted
     */
    public function deletePattern(string $pattern): int;

    /**
     * Clear entire cache.
     *
     * @return bool True on success, false on failure
     */
    public function clear(): bool;

    /**
     * Get cache statistics.
     *
     * @return array Statistics array with keys: hits, misses, size, etc.
     */
    public function getStats(): array;

    /**
     * Get remaining TTL for a cache key.
     *
     * @param string $key Cache key
     * @return int|null Remaining seconds, null if no expiration, false if not found
     */
    public function getTtl(string $key): ?int;
}
