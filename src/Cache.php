<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\ArrayUtils;

/**
 * In-memory cache implementation
 *
 * Provides a simple in-memory cache with TTL support and statistics.
 * For production use, consider using a persistent cache like Redis or Memcached.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class Cache implements CacheInterface
{
    private array $store = [];
    private array $stats = [
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0,
    ];

    public function set(string $key, $value, ?int $ttl = null): bool
    {
        try {
            $entry = self::createCacheEntry($value, $ttl);
            $this->store[$key] = $entry;
            $this->stats['sets']++;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function get(string $key, $default = null)
    {
        if (!isset($this->store[$key])) {
            $this->stats['misses']++;
            return $default;
        }

        $entry = $this->store[$key];
        
        if (self::isExpired($entry)) {
            unset($this->store[$key]);
            $this->stats['misses']++;
            return $default;
        }

        $this->stats['hits']++;
        return $entry['value'];
    }

    public function has(string $key): bool
    {
        if (!isset($this->store[$key])) {
            return false;
        }

        $entry = $this->store[$key];
        
        if (self::isExpired($entry)) {
            unset($this->store[$key]);
            return false;
        }

        return true;
    }

    public function delete(string $key): bool
    {
        if (isset($this->store[$key])) {
            unset($this->store[$key]);
            $this->stats['deletes']++;
            return true;
        }
        return false;
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
        $deleted = 0;
        $matchingKeys = self::findKeysMatchingPattern(array_keys($this->store), $pattern);
        
        foreach ($matchingKeys as $key) {
            if ($this->delete($key)) {
                $deleted++;
            }
        }
        
        return $deleted;
    }

    public function clear(): bool
    {
        try {
            $this->store = [];
            $this->stats['deletes'] += count($this->store);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStats(): array
    {
        return array_merge($this->stats, [
            'size' => count($this->store),
            'memory_usage' => $this->getMemoryUsage(),
        ]);
    }

    public function getTtl(string $key): ?int
    {
        if (!isset($this->store[$key])) {
            return null;
        }

        $entry = $this->store[$key];
        
        if ($entry['expire'] === null) {
            return null; // No expiration
        }

        if (self::isExpired($entry)) {
            unset($this->store[$key]);
            return null;
        }

        return (int) ($entry['expire'] - microtime(true));
    }

    /**
     * Pure function to create cache entry with expiration
     *
     * @param mixed $value Value to cache
     * @param int|null $ttl Time to live in seconds
     * @return array Cache entry with value and expiration
     */
    public static function createCacheEntry($value, ?int $ttl = null): array
    {
        $expire = null;
        if ($ttl !== null && $ttl > 0) {
            $expire = microtime(true) + $ttl;
        }
        
        return [
            'value' => $value,
            'expire' => $expire,
            'created' => microtime(true),
        ];
    }

    /**
     * Pure function to check if cache entry is expired
     *
     * @param array $entry Cache entry
     * @return bool True if expired
     */
    public static function isExpired(array $entry): bool
    {
        return $entry['expire'] !== null && microtime(true) > $entry['expire'];
    }

    /**
     * Pure function to find keys matching a pattern
     *
     * @param array $keys Array of keys to search
     * @param string $pattern Pattern with wildcard support (* and ?)
     * @return array Matching keys
     */
    public static function findKeysMatchingPattern(array $keys, string $pattern): array
    {
        // Convert wildcard pattern to regex
        $regex = '/^' . str_replace(['*', '?'], ['.*', '.'], preg_quote($pattern, '/')) . '$/';
        
        return array_filter($keys, function($key) use ($regex) {
            return preg_match($regex, $key);
        });
    }

    /**
     * Get approximate memory usage of cache store
     *
     * @return int Memory usage in bytes
     */
    private function getMemoryUsage(): int
    {
        return strlen(serialize($this->store));
    }
}
