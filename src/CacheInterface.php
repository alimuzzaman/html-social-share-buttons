<?php
namespace HtmlSocialShare;

interface CacheInterface
{
    /**
     * Set a value in cache with optional TTL in seconds.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return void
     */
    public function set(string $key, $value, ?int $ttl = null): void;

    /**
     * Get a value from cache or null if missing/expired.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key);

    /**
     * Delete a cache key.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;

    /**
     * Clear entire cache.
     *
     * @return void
     */
    public function clear(): void;
}
