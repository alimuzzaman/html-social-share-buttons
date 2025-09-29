<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

/**
 * Interface for share count adapters
 * 
 * Provides methods for fetching social media share counts from various networks.
 * Implementations should handle rate limiting, caching, and error conditions gracefully.
 * 
 * @package HtmlSocialShare\ShareCounts\Adapters
 * @since 3.0.0
 */
interface AdapterInterface
{
    /**
     * Fetch share count for a URL.
     *
     * @param string $url The URL to fetch share count for
     * @return int Share count, or 0 if unavailable
     * @throws \InvalidArgumentException If URL is invalid
     * @throws \RuntimeException If network request fails
     */
    public function fetch(string $url): int;

    /**
     * Get the network name this adapter handles.
     *
     * @return string Network identifier (e.g., 'facebook', 'twitter')
     */
    public function getNetworkName(): string;

    /**
     * Check if the adapter supports a given URL.
     *
     * @param string $url URL to check
     * @return bool True if URL is supported
     */
    public function supportsUrl(string $url): bool;

    /**
     * Validate adapter configuration.
     *
     * @param array $config Configuration array
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public function validateConfig(array $config): array;

    /**
     * Get rate limiting information.
     *
     * @return array Rate limit info with keys: 'requests_per_minute', 'burst_limit'
     */
    public function getRateLimits(): array;

    /**
     * Check if adapter is currently rate limited.
     *
     * @return bool True if rate limited
     */
    public function isRateLimited(): bool;

    /**
     * Get cache TTL in seconds for this adapter.
     *
     * @return int Cache TTL in seconds
     */
    public function getCacheTtl(): int;
}
