<?php
namespace HtmlSocialShare\ShareCounts;

use HtmlSocialShare\CacheInterface;
use HtmlSocialShare\Settings;
use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use HtmlSocialShare\Utils\UrlUtils;
use Exception;
use InvalidArgumentException;

/**
 * Responsible for storing and retrieving share counts with enhanced security and error handling.
 *
 * Provides comprehensive share count management with:
 * - Secure database operations with validation
 * - Pure function extraction for testability  
 * - Enhanced caching with TTL management
 * - Network adapter pattern with fallbacks
 * - Scheduled refresh capabilities
 * - Comprehensive error handling and logging
 */
class ShareCountManager
{
    private CacheInterface $cache;
    private Settings $settings;
    private \wpdb $wpdb;
    
    /** @var array<string, mixed> Valid network configurations */
    private array $validNetworks = [];
    
    /** @var int Default cache TTL in seconds */
    private const DEFAULT_CACHE_TTL = 43200; // 12 hours
    
    /** @var int Maximum posts to process in batch */
    private const MAX_BATCH_SIZE = 100;

    /**
     * Initialize ShareCountManager with dependencies and validation.
     *
     * @param CacheInterface $cache Cache implementation for storing counts
     * @param Settings $settings Plugin settings for configuration
     * @throws InvalidArgumentException If dependencies are invalid
     */
    public function __construct(CacheInterface $cache, Settings $settings)
    {
        global $wpdb;
        
        if (!$wpdb instanceof \wpdb) {
            throw new InvalidArgumentException('WordPress database not available');
        }
        
        $this->wpdb = $wpdb;
        $this->cache = $cache;
        $this->settings = $settings;
        $this->validNetworks = $this->loadValidNetworks();
    }
    
    /**
     * Load and validate supported networks configuration.
     *
     * @return array<string, mixed> Valid network configurations
     */
    private function loadValidNetworks(): array
    {
        $defaultNetworks = [
            'facebook' => ['name' => 'Facebook', 'supports_api' => true],
            'pinterest' => ['name' => 'Pinterest', 'supports_api' => true],
            'x' => ['name' => 'X (Twitter)', 'supports_api' => false],
            'twitter' => ['name' => 'Twitter', 'supports_api' => false],
            'linkedin' => ['name' => 'LinkedIn', 'supports_api' => true],
            'vk' => ['name' => 'VKontakte', 'supports_api' => true],
            'mastodon' => ['name' => 'Mastodon', 'supports_api' => false],
            'bluesky' => ['name' => 'Bluesky', 'supports_api' => false],
            'threads' => ['name' => 'Threads', 'supports_api' => false],
        ];
        
        try {
            $customNetworks = $this->settings->get('share_counts_custom_networks', []);
            if (is_array($customNetworks)) {
                return array_merge($defaultNetworks, $customNetworks);
            }
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Failed to load custom networks: {$e->getMessage()}");
        }
        
        return $defaultNetworks;
    }

    /**
     * Create or update the database table used to store share counts.
     * 
     * @throws Exception If schema creation fails
     */
    public function installSchema(): void
    {
        try {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $tableName = $this->getTableName();
            $charsetCollate = $this->wpdb->get_charset_collate();

            $sql = $this->buildCreateTableSql($tableName, $charsetCollate);
            
            $result = dbDelta($sql);
            
            // Verify table was created successfully
            if (!$this->tableExists($tableName)) {
                throw new Exception("Failed to create share counts table: {$tableName}");
            }
            
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Schema installation failed: {$e->getMessage()}");
            throw $e;
        }
    }
    
    /**
     * Build CREATE TABLE SQL statement.
     *
     * @param string $tableName Sanitized table name
     * @param string $charsetCollate Database charset collation
     * @return string SQL CREATE TABLE statement
     */
    private function buildCreateTableSql(string $tableName, string $charsetCollate): string
    {
        return "CREATE TABLE {$tableName} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            url varchar(255) NOT NULL,
            network varchar(64) NOT NULL,
            count bigint(20) unsigned NOT NULL DEFAULT 0,
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY post_network (post_id, network),
            KEY url (url),
            KEY network_idx (network),
            KEY updated_at_idx (updated_at)
        ) {$charsetCollate};";
    }
    
    /**
     * Get sanitized table name for share counts.
     *
     * @return string Sanitized table name
     */
    private function getTableName(): string
    {
        return SecurityUtils::sanitizeDbIdentifier($this->wpdb->prefix . 'hss_share_counts');
    }
    
    /**
     * Check if table exists in database.
     *
     * @param string $tableName Table name to check
     * @return bool True if table exists
     */
    private function tableExists(string $tableName): bool
    {
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare("SHOW TABLES LIKE %s", $tableName)
        );
        return $result === $tableName;
    }

    /**
     * Get cached share count for a given post and network with validation.
     * 
     * @param int $postId Post ID to get count for
     * @param string $network Network name to get count for
     * @return int Share count (0 if not found)
     * @throws InvalidArgumentException If parameters are invalid
     */
    public function getCountForPostNetwork(int $postId, string $network): int
    {
        // Validate inputs
        if (!$this->isValidPostId($postId)) {
            throw new InvalidArgumentException("Invalid post ID: {$postId}");
        }
        
        if (!$this->isValidNetworkName($network)) {
            throw new InvalidArgumentException("Invalid network name: {$network}");
        }
        
        try {
            // Check cache first
            $cacheKey = $this->getCacheKey($postId, $network);
            $cached = $this->cache->get($cacheKey);
            if ($cached !== null) {
                return $this->validateCount($cached);
            }

            // Query database with prepared statement
            $count = $this->getCountFromDatabase($postId, $network);
            
            // Cache the result
            if ($count >= 0) {
                $this->cache->set($cacheKey, $count, self::DEFAULT_CACHE_TTL);
            }
            
            return $count;
            
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Get count failed for post {$postId}, network {$network}: {$e->getMessage()}");
            return 0;
        }
    }
    
    /**
     * Get share count from database with error handling.
     *
     * @param int $postId Post ID
     * @param string $network Network name  
     * @return int Share count from database
     */
    private function getCountFromDatabase(int $postId, string $network): int
    {
        $tableName = $this->getTableName();
        
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT count, updated_at FROM {$tableName} WHERE post_id = %d AND network = %s",
                $postId,
                $network
            ),
            ARRAY_A
        );
        
        if ($this->wpdb->last_error) {
            throw new Exception("Database error: {$this->wpdb->last_error}");
        }
        
        return $row ? $this->validateCount($row['count']) : 0;
    }
    
    /**
     * Validate and sanitize a count value.
     *
     * @param mixed $count Count value to validate
     * @return int Validated count (>= 0)
     */
    private function validateCount($count): int
    {
        $count = (int) $count;
        return max(0, $count);
    }
    
    /**
     * Validate post ID is positive integer for existing post.
     *
     * @param int $postId Post ID to validate
     * @return bool True if valid
     */
    private function isValidPostId(int $postId): bool
    {
        return $postId > 0 && get_post($postId) !== null;
    }
    
    /**
     * Validate network name against supported networks.
     *
     * @param string $network Network name to validate
     * @return bool True if valid
     */
    private function isValidNetworkName(string $network): bool
    {
        $network = strtolower(trim($network));
        return !empty($network) && isset($this->validNetworks[$network]);
    }

    /**
     * Save share count with validation and error handling.
     * 
     * @param int $postId Post ID to save count for
     * @param string $network Network name
     * @param int $count Share count to save
     * @return bool True if save was successful
     * @throws InvalidArgumentException If parameters are invalid
     */
    public function saveCount(int $postId, string $network, int $count): bool
    {
        // Validate inputs
        if (!$this->isValidPostId($postId)) {
            throw new InvalidArgumentException("Invalid post ID: {$postId}");
        }
        
        if (!$this->isValidNetworkName($network)) {
            throw new InvalidArgumentException("Invalid network name: {$network}");
        }
        
        $count = $this->validateCount($count);
        
        try {
            $tableName = $this->getTableName();
            $success = $this->upsertCount($tableName, $postId, $network, $count);
            
            // Update cache on successful save
            if ($success) {
                $cacheKey = $this->getCacheKey($postId, $network);
                $this->cache->set($cacheKey, $count, self::DEFAULT_CACHE_TTL);
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Save count failed for post {$postId}, network {$network}: {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Insert or update share count in database.
     *
     * @param string $tableName Table name
     * @param int $postId Post ID
     * @param string $network Network name
     * @param int $count Share count
     * @return bool True if successful
     * @throws Exception If database operation fails
     */
    private function upsertCount(string $tableName, int $postId, string $network, int $count): bool
    {
        $now = current_time('mysql');
        $url = get_permalink($postId) ?: '';
        
        // Check if record exists
        $existingId = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT id FROM {$tableName} WHERE post_id = %d AND network = %s",
                $postId,
                $network
            )
        );
        
        if ($this->wpdb->last_error) {
            throw new Exception("Database query error: {$this->wpdb->last_error}");
        }
        
        if ($existingId) {
            // Update existing record
            $result = $this->wpdb->update(
                $tableName,
                [
                    'count' => $count,
                    'url' => $url,
                    'updated_at' => $now
                ],
                ['id' => $existingId],
                ['%d', '%s', '%s'],
                ['%d']
            );
        } else {
            // Insert new record
            $result = $this->wpdb->insert(
                $tableName,
                [
                    'post_id' => $postId,
                    'url' => $url,
                    'network' => $network,
                    'count' => $count,
                    'updated_at' => $now
                ],
                ['%d', '%s', '%s', '%d', '%s']
            );
        }
        
        if ($this->wpdb->last_error) {
            throw new Exception("Database operation error: {$this->wpdb->last_error}");
        }
        
        return $result !== false;
    }

    /**
     * Fetch share counts from network APIs with enhanced error handling and caching.
     * 
     * @param string $url URL to fetch count for
     * @param string $network Network identifier
     * @return int Share count from network (0 on error)
     */
    public function fetchCountFromNetwork(string $url, string $network): int
    {
        // Validate inputs
        if (!UrlUtils::isValidUrl($url)) {
            error_log("HSS ShareCounts: Invalid URL provided: {$url}");
            return 0;
        }
        
        if (!$this->isValidNetworkName($network)) {
            error_log("HSS ShareCounts: Invalid network name: {$network}");
            return 0;
        }
        
        try {
            // Check remote cache first
            $remoteCacheKey = $this->getRemoteCacheKey($url, $network);
            $cached = $this->cache->get($remoteCacheKey);
            if ($cached !== null) {
                return $this->validateCount($cached);
            }

            // Get adapter for network
            $adapter = $this->getNetworkAdapter($network);
            if (!$adapter) {
                return 0;
            }

            // Fetch count with timeout and error handling
            $count = $this->fetchWithAdapter($adapter, $url);
            
            // Cache result with configurable TTL
            $ttl = $this->getCacheTtl();
            $this->cache->set($remoteCacheKey, $count, $ttl);

            return $count;
            
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Network fetch failed for {$network}/{$url}: {$e->getMessage()}");
            return 0;
        }
    }
    
    /**
     * Get appropriate adapter for network with fallback support.
     *
     * @param string $network Network identifier
     * @return object|null Network adapter instance
     */
    private function getNetworkAdapter(string $network): ?object
    {
        $network = strtolower($network);
        
        try {
            switch ($network) {
                case 'facebook':
                    return new \HtmlSocialShare\ShareCounts\Adapters\FacebookAdapter($this->settings);
                case 'pinterest':
                    return new \HtmlSocialShare\ShareCounts\Adapters\PinterestAdapter($this->settings);
                case 'x':
                case 'twitter':
                    return new \HtmlSocialShare\ShareCounts\Adapters\XAdapter();
                case 'linkedin':
                    return new \HtmlSocialShare\ShareCounts\Adapters\LinkedInAdapter($this->settings);
                case 'vk':
                    return new \HtmlSocialShare\ShareCounts\Adapters\VkAdapter();
                case 'mastodon':
                    return new \HtmlSocialShare\ShareCounts\Adapters\MastodonAdapter();
                case 'bluesky':
                    return new \HtmlSocialShare\ShareCounts\Adapters\BlueskyAdapter();
                case 'threads':
                    return new \HtmlSocialShare\ShareCounts\Adapters\ThreadsAdapter();
                default:
                    return $this->getFallbackAdapter();
            }
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Failed to create adapter for {$network}: {$e->getMessage()}");
            return $this->getFallbackAdapter();
        }
    }
    
    /**
     * Get fallback adapter when specific adapter is unavailable.
     *
     * @return object|null Fallback adapter or null
     */
    private function getFallbackAdapter(): ?object
    {
        try {
            $aggregatorEndpoint = $this->settings->get('share_counts_aggregator_endpoint', '');
            if (!empty($aggregatorEndpoint) && UrlUtils::isValidUrl($aggregatorEndpoint)) {
                return new \HtmlSocialShare\ShareCounts\Adapters\AggregatorAdapter($this->settings);
            }
            
            return new \HtmlSocialShare\ShareCounts\Adapters\GenericAdapter();
            
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Failed to create fallback adapter: {$e->getMessage()}");
            return null;
        }
    }
    
    /**
     * Fetch count using adapter with error handling and timeout.
     *
     * @param object $adapter Network adapter
     * @param string $url URL to fetch count for
     * @return int Share count
     */
    private function fetchWithAdapter(object $adapter, string $url): int
    {
        if (!method_exists($adapter, 'fetch')) {
            throw new Exception('Adapter does not implement fetch method');
        }
        
        // Set timeout for network requests
        $timeout = (int) $this->settings->get('share_counts_timeout', 30);
        if ($timeout <= 0 || $timeout > 60) {
            $timeout = 30;
        }
        
        // Use WordPress HTTP API timeout if available
        add_filter('http_request_timeout', function() use ($timeout) {
            return $timeout;
        });
        
        try {
            $count = $adapter->fetch($url);
            return $this->validateCount($count);
        } finally {
            remove_all_filters('http_request_timeout');
        }
    }
    
    /**
     * Get cache TTL for remote share count results.
     *
     * @return int Cache TTL in seconds
     */
    private function getCacheTtl(): int
    {
        $ttl = (int) $this->settings->get('share_counts_cache_ttl', self::DEFAULT_CACHE_TTL);
        
        // Enforce reasonable bounds (1 hour to 7 days)
        if ($ttl < 3600) {
            $ttl = 3600;
        } elseif ($ttl > 604800) {
            $ttl = 604800;
        }
        
        return $ttl;
    }
    
    /**
     * Generate cache key for remote network results.
     *
     * @param string $url URL for cache key
     * @param string $network Network name  
     * @return string Cache key
     */
    private function getRemoteCacheKey(string $url, string $network): string
    {
        return sprintf(
            'hss:sharecount:remote:%s:%s',
            $network,
            SecurityUtils::hashForCacheKey($url)
        );
    }

    /**
     * Refresh share counts for posts with enhanced batch processing and error handling.
     *
     * @param array|null $postIds Specific post IDs or null for recent posts
     * @return array Statistics about the refresh operation
     */
    public function refreshCounts(?array $postIds = null): array
    {
        $stats = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        try {
            $posts = $this->getPostsToRefresh($postIds);
            if (empty($posts)) {
                return $stats;
            }
            
            $enabledNetworks = $this->getEnabledNetworks();
            if (empty($enabledNetworks)) {
                $stats['errors'][] = 'No enabled networks configured';
                return $stats;
            }
            
            foreach ($posts as $post) {
                $stats['processed']++;
                
                try {
                    $this->refreshPostCounts($post, $enabledNetworks);
                    $stats['successful']++;
                } catch (Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = "Post {$post->ID}: {$e->getMessage()}";
                    error_log("HSS ShareCounts: Refresh failed for post {$post->ID}: {$e->getMessage()}");
                }
            }
            
        } catch (Exception $e) {
            $stats['errors'][] = "Refresh operation failed: {$e->getMessage()}";
            error_log("HSS ShareCounts: Batch refresh failed: {$e->getMessage()}");
        }
        
        return $stats;
    }
    
    /**
     * Get posts to refresh based on criteria.
     *
     * @param array|null $postIds Specific post IDs or null for recent posts
     * @return array Posts to process
     */
    private function getPostsToRefresh(?array $postIds): array
    {
        if (empty($postIds)) {
            // Get recent posts
            $queryArgs = [
                'post_type' => $this->getAllowedPostTypes(),
                'post_status' => 'publish',
                'posts_per_page' => self::MAX_BATCH_SIZE,
                'orderby' => 'modified',
                'order' => 'DESC',
                'fields' => 'ids'
            ];
            
            $postIds = get_posts($queryArgs);
        }
        
        // Validate and get post objects
        $posts = [];
        foreach ($postIds as $postId) {
            $post = get_post((int) $postId);
            if ($post && $post->post_status === 'publish') {
                $posts[] = $post;
            }
        }
        
        return array_slice($posts, 0, self::MAX_BATCH_SIZE);
    }
    
    /**
     * Get allowed post types for share count processing.
     *
     * @return array Allowed post types
     */
    private function getAllowedPostTypes(): array
    {
        $defaultTypes = ['post', 'page'];
        $customTypes = $this->settings->get('share_counts_post_types', []);
        
        if (is_array($customTypes) && !empty($customTypes)) {
            return array_merge($defaultTypes, $customTypes);
        }
        
        return $defaultTypes;
    }
    
    /**
     * Get enabled networks from settings.
     *
     * @return array Enabled network names
     */
    private function getEnabledNetworks(): array
    {
        $enabled = $this->settings->get('enabled_networks', []);
        if (!is_array($enabled)) {
            return [];
        }
        
        // Filter out invalid networks
        return array_filter($enabled, [$this, 'isValidNetworkName']);
    }
    
    /**
     * Refresh share counts for a single post across all networks.
     *
     * @param \WP_Post $post Post to refresh counts for
     * @param array $networks Enabled networks
     * @throws Exception If post processing fails
     */
    private function refreshPostCounts(\WP_Post $post, array $networks): void
    {
        $postId = (int) $post->ID;
        $url = get_permalink($postId);
        
        if (!$url || !UrlUtils::isValidUrl($url)) {
            throw new Exception("Invalid URL for post {$postId}");
        }
        
        foreach ($networks as $network) {
            try {
                $count = $this->fetchCountFromNetwork($url, $network);
                
                // Only save valid counts
                if ($count >= 0) {
                    $this->saveCount($postId, $network, $count);
                }
                
                // Rate limiting between requests
                $this->rateLimitDelay();
                
            } catch (Exception $e) {
                error_log("HSS ShareCounts: Network fetch failed for post {$postId}, network {$network}: {$e->getMessage()}");
                // Continue with other networks even if one fails
            }
        }
    }
    
    /**
     * Apply rate limiting delay between API requests.
     */
    private function rateLimitDelay(): void
    {
        $delay = (int) $this->settings->get('share_counts_rate_limit_delay', 1);
        if ($delay > 0 && $delay <= 10) {
            sleep($delay);
        }
    }

    /**
     * Schedule WP-Cron event for periodic share count refreshes with validation.
     * 
     * @return bool True if scheduled successfully
     */
    public function scheduleCron(): bool
    {
        try {
            if (wp_next_scheduled('hss_refresh_share_counts')) {
                return true; // Already scheduled
            }
            
            $interval = $this->getValidCronInterval();
            $firstRun = $this->getFirstRunTime();
            
            return wp_schedule_event($firstRun, $interval, 'hss_refresh_share_counts') !== false;
            
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Cron scheduling failed: {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Get valid cron interval from settings.
     *
     * @return string Valid cron interval
     */
    private function getValidCronInterval(): string
    {
        $interval = $this->settings->get('share_counts_cron_interval', 'daily');
        $validIntervals = ['hourly', 'twicedaily', 'daily', 'weekly'];
        
        return in_array($interval, $validIntervals, true) ? $interval : 'daily';
    }
    
    /**
     * Get first run time for cron scheduling.
     *
     * @return int Unix timestamp for first run
     */
    private function getFirstRunTime(): int
    {
        $configuredTime = $this->settings->get('share_counts_cron_first_run', null);
        
        if ($configuredTime && is_numeric($configuredTime)) {
            $timestamp = (int) $configuredTime;
            return $timestamp > time() ? $timestamp : time() + 3600;
        }
        
        // Default to 1 hour from now
        return time() + 3600;
    }

    /**
     * Unschedule WP-Cron refresh event safely.
     * 
     * @return bool True if unscheduled successfully
     */
    public function unscheduleCron(): bool
    {
        try {
            return wp_clear_scheduled_hook('hss_refresh_share_counts') !== false;
        } catch (Exception $e) {
            error_log("HSS ShareCounts: Cron unscheduling failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Flush caches and optionally database entries with comprehensive validation.
     *
     * @param array|null $postIds Specific post IDs or null for all
     * @param bool $removeDbEntries Whether to remove database entries
     * @return array Operation statistics
     */
    public function flushCache(?array $postIds = null, bool $removeDbEntries = false): array
    {
        $stats = [
            'cache_cleared' => 0,
            'db_entries_removed' => 0,
            'errors' => []
        ];
        
        try {
            // Clear cache entries
            if (empty($postIds)) {
                $this->cache->clear();
                $stats['cache_cleared'] = -1; // Indicates full cache clear
            } else {
                $stats['cache_cleared'] = $this->clearPostCaches($postIds);
            }
            
            // Optionally remove database entries
            if ($removeDbEntries) {
                $stats['db_entries_removed'] = $this->removeDbEntries($postIds);
            }
            
        } catch (Exception $e) {
            $stats['errors'][] = "Cache flush failed: {$e->getMessage()}";
            error_log("HSS ShareCounts: Cache flush failed: {$e->getMessage()}");
        }
        
        return $stats;
    }
    
    /**
     * Clear cache entries for specific posts.
     *
     * @param array $postIds Post IDs to clear cache for
     * @return int Number of cache entries cleared
     */
    private function clearPostCaches(array $postIds): int
    {
        $cleared = 0;
        
        foreach ($postIds as $postId) {
            $postId = (int) $postId;
            if ($postId <= 0) {
                continue;
            }
            
            foreach ($this->validNetworks as $network => $config) {
                $cacheKey = $this->getCacheKey($postId, $network);
                if ($this->cache->delete($cacheKey)) {
                    $cleared++;
                }
                
                // Also clear remote cache
                $url = get_permalink($postId);
                if ($url) {
                    $remoteCacheKey = $this->getRemoteCacheKey($url, $network);
                    if ($this->cache->delete($remoteCacheKey)) {
                        $cleared++;
                    }
                }
            }
        }
        
        return $cleared;
    }
    
    /**
     * Remove database entries with safety checks.
     *
     * @param array|null $postIds Post IDs or null for all
     * @return int Number of database entries removed
     * @throws Exception If database operation fails
     */
    private function removeDbEntries(?array $postIds): int
    {
        $tableName = $this->getTableName();
        
        if (empty($postIds)) {
            // Remove all entries (destructive operation)
            $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$tableName}");
            $result = $this->wpdb->query("TRUNCATE TABLE {$tableName}");
        } else {
            // Remove specific post entries
            $validIds = array_filter(array_map('intval', $postIds), function($id) {
                return $id > 0;
            });
            
            if (empty($validIds)) {
                return 0;
            }
            
            $placeholders = implode(',', array_fill(0, count($validIds), '%d'));
            $count = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$tableName} WHERE post_id IN ({$placeholders})",
                    ...$validIds
                )
            );
            
            $result = $this->wpdb->query(
                $this->wpdb->prepare(
                    "DELETE FROM {$tableName} WHERE post_id IN ({$placeholders})",
                    ...$validIds
                )
            );
        }
        
        if ($this->wpdb->last_error) {
            throw new Exception("Database deletion error: {$this->wpdb->last_error}");
        }
        
        return $result !== false ? (int) $count : 0;
    }
    
    /**
     * Generate cache key for post/network combination.
     *
     * @param int $postId Post ID
     * @param string $network Network name
     * @return string Cache key
     */
    private function getCacheKey(int $postId, string $network): string
    {
        return sprintf('hss:sharecount:%d:%s', $postId, $network);
    }
}
