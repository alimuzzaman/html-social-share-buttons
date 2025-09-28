<?php
namespace HtmlSocialShare\ShareCounts;

use HtmlSocialShare\CacheInterface;
use HtmlSocialShare\Settings;

/**
 * Responsible for storing and retrieving share counts.
 *
 * This implementation focuses on server-side storage + caching.
 * Network-fetching logic is intentionally left as stubs to be implemented
 * in SHARECOUNT-002.
 */
class ShareCountManager
{
    private CacheInterface $cache;
    private Settings $settings;
    private \wpdb $wpdb;

    public function __construct(CacheInterface $cache, Settings $settings)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->cache = $cache;
        $this->settings = $settings;
    }

    /**
     * Create or update the database table used to store share counts.
     */
    public function installSchema(): void
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = $this->wpdb->prefix . 'hss_share_counts';
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            url varchar(255) NOT NULL,
            network varchar(64) NOT NULL,
            count bigint(20) unsigned NOT NULL DEFAULT 0,
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY  (id),
            UNIQUE KEY post_network (post_id, network),
            KEY url (url),
            KEY network_idx (network)
        ) {$charset_collate};";

        dbDelta($sql);
    }

    /**
     * Get a cached share count for a given post and network. Falls back to DB.
     */
    public function getCountForPostNetwork(int $postId, string $network): int
    {
        $cacheKey = $this->getCacheKey($postId, $network);
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return (int) $cached;
        }

        $table = $this->wpdb->prefix . 'hss_share_counts';
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT count, updated_at FROM {$table} WHERE post_id = %d AND network = %s", $postId, $network),
            ARRAY_A
        );

        if ($row) {
            $this->cache->set($cacheKey, (int) $row['count'], 3600);
            return (int) $row['count'];
        }

        return 0;
    }

    /**
     * Save the count for a given post/network combination and update cache.
     */
    public function saveCount(int $postId, string $network, int $count): bool
    {
        $table = $this->wpdb->prefix . 'hss_share_counts';
        $now = current_time('mysql');

        $existing = $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT id FROM {$table} WHERE post_id = %d AND network = %s", $postId, $network)
        );

        if ($existing) {
            $updated = $this->wpdb->update(
                $table,
                ['count' => $count, 'updated_at' => $now],
                ['id' => $existing],
                ['%d', '%s'],
                ['%d']
            );

            $success = $updated !== false;
        } else {
            $inserted = $this->wpdb->insert(
                $table,
                [
                    'post_id' => $postId,
                    'url' => get_permalink($postId),
                    'network' => $network,
                    'count' => $count,
                    'updated_at' => $now
                ],
                ['%d', '%s', '%s', '%d', '%s']
            );

            $success = $inserted !== false;
        }

        if ($success) {
            $this->cache->set($this->getCacheKey($postId, $network), $count, 3600);
        }

        return $success;
    }

    /**
     * Fetch counts from the network for a given URL/network. Stubbed for now.
     * To be implemented in SHARECOUNT-002.
     */
    public function fetchCountFromNetwork(string $url, string $network): int
    {
        // Check remote cache first
        $remoteCacheKey = sprintf('hss:sharecount:remote:%s:%s', $network, md5($url));
        $cached = $this->cache->get($remoteCacheKey);
        if ($cached !== null) {
            return (int) $cached;
        }

        // Choose adapter based on network
        switch (strtolower($network)) {
            case 'facebook':
                $adapter = new \HtmlSocialShare\ShareCounts\Adapters\FacebookAdapter($this->settings);
                break;
            case 'pinterest':
                $adapter = new \HtmlSocialShare\ShareCounts\Adapters\PinterestAdapter($this->settings);
                break;
            case 'x':
            case 'twitter':
                $adapter = new \HtmlSocialShare\ShareCounts\Adapters\XAdapter();
                break;
            case 'linkedin':
                $adapter = new \HtmlSocialShare\ShareCounts\Adapters\LinkedInAdapter($this->settings);
                break;
            case 'vk':
                $adapter = new \HtmlSocialShare\ShareCounts\Adapters\VkAdapter();
                break;
            default:
                $adapter = new \HtmlSocialShare\ShareCounts\Adapters\GenericAdapter();
                break;
        }

        // Attempt to fetch count via adapter
        try {
            $count = (int) $adapter->fetch($url);
        } catch (\Exception $e) {
            error_log('HSS ShareCounts: Adapter fetch failed: ' . $e->getMessage());
            $count = 0;
        }

        // Cache remote result for a reasonable TTL (default 12 hours)
        $ttl = (int) ($this->settings->get('share_counts_cache_ttl', 43200));
        if ($ttl <= 0) {
            $ttl = 43200;
        }
        $this->cache->set($remoteCacheKey, $count, $ttl);

        return $count;
    }

    /**
     * Refresh counts for a list of posts or for recent posts when none provided.
     * This method is intended to be safe for scheduled execution.
     *
     * @param array|null $postIds
     * @return void
     */
    public function refreshCounts(?array $postIds = null): void
    {
        // Determine posts to refresh
        if (empty($postIds)) {
            $queryArgs = [
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 100,
                'orderby' => 'modified',
                'order' => 'DESC'
            ];

            $posts = get_posts($queryArgs);
        } else {
            $posts = [];
            foreach ($postIds as $pid) {
                $p = get_post($pid);
                if ($p) {
                    $posts[] = $p;
                }
            }
        }

        if (empty($posts)) {
            return;
        }

        $enabledNetworks = $this->settings->get('enabled_networks', []);
        foreach ($posts as $post) {
            $postId = (int) $post->ID;
            $url = get_permalink($postId);
            if (empty($url)) {
                continue;
            }

            foreach ($enabledNetworks as $network) {
                $count = $this->fetchCountFromNetwork($url, $network);
                // Save only when we obtained a valid integer
                if (is_int($count) && $count >= 0) {
                    $this->saveCount($postId, $network, $count);
                }
            }
        }
    }

    /**
     * Schedule a WP-Cron event for periodic share count refreshes.
     */
    public function scheduleCron(): void
    {
        $interval = $this->settings->get('share_counts_cron_interval', 'daily');
        $when = $this->settings->get('share_counts_cron_first_run', null);

        if (!wp_next_scheduled('hss_refresh_share_counts')) {
            // Default to daily if interval is not valid
            $validIntervals = ['hourly', 'twicedaily', 'daily'];
            if (!in_array($interval, $validIntervals, true)) {
                $interval = 'daily';
            }
            wp_schedule_event(time(), $interval, 'hss_refresh_share_counts');
        }
    }

    /**
     * Unschedule the WP-Cron refresh event.
     */
    public function unscheduleCron(): void
    {
        wp_clear_scheduled_hook('hss_refresh_share_counts');
    }

    /**
     * Flush internal caches used for share counts. Optionally limit to a set of post IDs
     * and optionally remove DB-stored counts as well (destructive).
     *
     * @param array|null $postIds
     * @param bool $removeDbEntries
     * @return void
     */
    public function flushCache(?array $postIds = null, bool $removeDbEntries = false): void
    {
        // If no specific post IDs provided, clear entire cache store
        if (empty($postIds)) {
            $this->cache->clear();
        } else {
            foreach ($postIds as $pid) {
                $keyPattern = $this->getCacheKey((int)$pid, '%s');
                // CacheInterface does not provide pattern deletion - attempt to delete known keys for networks
                $enabledNetworks = $this->settings->get('enabled_networks', []);
                foreach ($enabledNetworks as $network) {
                    $this->cache->delete(sprintf($this->getCacheKey((int)$pid, $network)));
                }
            }
        }

        // Optionally remove DB entries (destructive) when requested
        if ($removeDbEntries) {
            global $wpdb;
            $table = $this->wpdb->prefix . 'hss_share_counts';
            if (empty($postIds)) {
                $wpdb->query("TRUNCATE TABLE {$table}");
            } else {
                $ids = array_map('intval', $postIds);
                $in = implode(',', $ids);
                $wpdb->query("DELETE FROM {$table} WHERE post_id IN ({$in})");
            }
        }
    }

    private function getCacheKey(int $postId, string $network): string
    {
        return sprintf('hss:sharecount:%d:%s', $postId, $network);
    }
}
