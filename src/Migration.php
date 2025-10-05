<?php
namespace HtmlSocialShare;

class Migration
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Run the legacy options migration
     *
     * @return bool True if migration was performed, false if already done
     */
    public function run(): bool
    {
        // Check if migration already completed
        $migrationStatus = $this->settings->getMigrationStatus();
        if (!empty($migrationStatus['done'])) {
            error_log('HSS Migration: Migration already completed on ' . ($migrationStatus['date'] ?? 'unknown'));
            return false;
        }

        error_log('HSS Migration: Starting legacy options migration');

        // Get legacy options
        $legacyOptions = $this->getLegacyOptions();
        if (empty($legacyOptions)) {
            error_log('HSS Migration: No legacy options found, initializing with defaults');
            $this->initializeDefaults();
            return true;
        }

        // Migrate core settings
        $this->migrateCoreSettings($legacyOptions);

        // Migrate networks to profiles
        $this->migrateNetworksToProfiles($legacyOptions);

        // Initialize icon registry
        $this->initializeIconRegistry();

        // Mark migration as complete
        $this->settings->setMigrationStatus([
            'done' => true,
            'from_version' => $legacyOptions['version'] ?? 'unknown',
            'date' => current_time('mysql'),
            'legacy_backup' => $legacyOptions // Keep backup for rollback
        ]);

        error_log('HSS Migration: Migration completed successfully');
        return true;
    }

    /**
     * Get legacy options from old option keys
     *
     * @return array
     */
    private function getLegacyOptions(): array
    {
        // Try different possible legacy option keys
        $possibleKeys = [
            'html_social_share_options',
            'html_social_share',
            'hss_options',
            'social_share_options',
            'zm_shbt_fld' // Original key from archive settings
        ];

        foreach ($possibleKeys as $key) {
            $options = get_option($key);
            if (!empty($options) && is_array($options)) {
                error_log("HSS Migration: Found legacy options in key: {$key}");
                return $options;
            }
        }

        return [];
    }

    /**
     * Migrate core settings from legacy format
     * 
     * Maps all 12 legacy zm_shbt_fld keys to new hss_core structure:
     * - title → title (flat key, displayed in appearance group)
     * - excludes → exclude_pages
     * - g_analytics → google_analytics
     * - auto_hide_btn → auto_hide_buttons
     * - use_port → use_port_in_url
     * - nofollow → nofollow_links
     * - iconset → icon_style
     * - show_left → floating_left (derived from positions)
     * - show_right → floating_right (derived from positions)
     * - show_before_post → before_content (derived from positions)
     * - show_after_post → after_content (derived from positions)
     * - icons → enabled_networks
     *
     * @param array $legacy
     * @return void
     */
    private function migrateCoreSettings(array $legacy): void
    {
        // Legacy key mapping to new flat keys (Settings uses flat keys internally)
        $coreSettings = [
            // Version tracking
            'version' => '3.0.0',
            
            // Appearance settings (legacy: title, iconset)
            'title' => $legacy['title'] ?? 'Share this with your friends',
            'icon_style' => $legacy['iconset'] ?? 'default',
            'iconset' => $legacy['iconset'] ?? 'default', // Keep for backward compat
            
            // Network settings (legacy: icons array)
            'enabled_networks' => $this->getEnabledNetworks($legacy),
            
            // Placement settings (legacy: excludes, show_left, show_right, show_before_post, show_after_post)
            'exclude_pages' => $legacy['excludes'] ?? '',
            'floating_left' => !empty($legacy['show_left']),
            'floating_right' => !empty($legacy['show_right']),
            'before_content' => !empty($legacy['show_before_post']),
            'after_content' => !empty($legacy['show_after_post']),
            
            // Advanced settings (legacy: g_analytics, auto_hide_btn, use_port, nofollow)
            'google_analytics' => !empty($legacy['g_analytics']),
            'auto_hide_buttons' => !empty($legacy['auto_hide_btn']),
            'use_port_in_url' => !empty($legacy['use_port']),
            'nofollow_links' => !empty($legacy['nofollow']),
            
            // Legacy positions format for backward compat
            'positions' => $this->normalizePositions($legacy),
            
            // Exclusions (keep old format too)
            'exclusions' => $this->normalizeExclusions($legacy)
        ];

        // Set each core setting
        foreach ($coreSettings as $key => $value) {
            $this->settings->set($key, $value);
        }

        error_log('HSS Migration: Core settings migrated - ' . count($coreSettings) . ' keys mapped from legacy zm_shbt_fld');
    }

    /**
     * Normalize position settings from legacy format
     *
     * @param array $legacy
     * @return array
     */
    private function normalizePositions(array $legacy): array
    {
        $positions = [];

        // Map legacy position flags to new format
        $positionMapping = [
            'show_left' => 'left',
            'show_right' => 'right',
            'show_before_post' => 'before_post',
            'show_after_post' => 'after_post'
        ];

        foreach ($positionMapping as $legacyKey => $newKey) {
            if (!empty($legacy[$legacyKey])) {
                $positions[] = $newKey;
            }
        }

        // Default to after_post if no positions set
        if (empty($positions)) {
            $positions = ['after_post'];
        }

        return $positions;
    }

    /**
     * Get enabled networks from legacy format
     * 
     * Legacy 'icons' key can be:
     * - Array of network IDs: ["facebook", "twitter", ...]
     * - Associative array: ["facebook" => 1, "twitter" => 1, ...]
     *
     * @param array $legacy
     * @return array
     */
    private function getEnabledNetworks(array $legacy): array
    {
        $networks = [];

        // Check if icons array exists (primary legacy format)
        if (!empty($legacy['icons']) && is_array($legacy['icons'])) {
            // Map legacy network names to standard names
            $networkMapping = [
                'facebook' => 'facebook',
                'twitter' => 'twitter',
                'linkedin' => 'linkedin',
                'googlepluse' => 'googleplus',
                'google_plus' => 'googleplus',
                'pinterest' => 'pinterest',
                'mail' => 'email',
                'whatsapp' => 'whatsapp',
                'telegram' => 'telegram',
                'reddit' => 'reddit',
                'tumblr' => 'tumblr',
                'bookmark' => 'bookmark'
            ];

            foreach ($legacy['icons'] as $key => $value) {
                // Handle both array formats
                $icon = is_numeric($key) ? $value : $key;
                
                // Only include if value is truthy (for associative arrays) or if it's a simple array
                if ((is_numeric($key) || !empty($value)) && isset($networkMapping[$icon])) {
                    $networks[] = $networkMapping[$icon];
                }
            }
        } else {
            // Fallback to enable_ prefixed keys (older format)
            $commonNetworks = [
                'facebook', 'twitter', 'linkedin', 'google_plus', 'pinterest',
                'email', 'whatsapp', 'telegram', 'reddit', 'tumblr'
            ];

            foreach ($commonNetworks as $network) {
                $enableKey = 'enable_' . $network;
                if (!empty($legacy[$enableKey])) {
                    $networks[] = $network;
                }
            }
        }

        // Default networks if none specified
        if (empty($networks)) {
            $networks = ['facebook', 'twitter', 'linkedin'];
        }

        return array_values(array_unique($networks)); // Remove duplicates and reindex
    }

    /**
     * Normalize exclusion settings
     *
     * @param array $legacy
     * @return array
     */
    private function normalizeExclusions(array $legacy): array
    {
        $exclusions = [
            'ids' => [],
            'slugs' => [],
            'titles' => []
        ];

        if (!empty($legacy['excludes'])) {
            // Try to parse the exclude field - could be comma-separated IDs/slugs
            $excludeValue = $legacy['excludes'];
            if (is_string($excludeValue)) {
                $parts = array_map('trim', explode(',', $excludeValue));
                // For now, treat as IDs - could be enhanced to detect slugs/titles
                $exclusions['ids'] = array_filter($parts, 'is_numeric');
            }
        }

        return $exclusions;
    }

    /**
     * Migrate networks to profiles
     *
     * @param array $legacy
     * @return void
     */
    private function migrateNetworksToProfiles(array $legacy): void
    {
        $enabledNetworks = $this->settings->get('enabled_networks', []);
        $profiles = [];

        foreach ($enabledNetworks as $network) {
            $profiles[$network] = [
                'id' => $network,
                'type' => 'share',
                'label' => ucfirst(str_replace('_', ' ', $network)),
                'handle' => $network,
                'url_template' => $this->getDefaultUrlTemplate($network),
                'visible' => true,
                'new_tab' => true,
                'order' => 0,
                'icon' => ['source' => 'builtin', 'ref' => $network],
                'meta' => ['analytics' => !empty($legacy['google_social_analytics'])]
            ];
        }

        // Save all profiles
        foreach ($profiles as $profileId => $profileData) {
            $this->settings->setProfile($profileId, $profileData);
        }

        error_log('HSS Migration: Created ' . count($profiles) . ' profiles from enabled networks');
    }

    /**
     * Get default URL template for a network
     *
     * @param string $network
     * @return string
     */
    private function getDefaultUrlTemplate(string $network): string
    {
        $templates = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u={url}&t={title}',
            'twitter' => 'https://x.com/intent/tweet?url={url}&text={title}',
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
            'pinterest' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
            'email' => 'mailto:?subject={title}&body={url}',
            'whatsapp' => 'https://wa.me/?text={title}%20{url}',
            'telegram' => 'https://t.me/share/url?url={url}&text={title}',
            'reddit' => 'https://reddit.com/submit?url={url}&title={title}',
            'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}',
            'mastodon' => 'https://mastodon.social/share?text={title}%20{url}',
            'threads' => 'https://www.threads.net/intent/post?text={title}%20{url}',
            'vk' => 'https://vk.com/share.php?url={url}&title={title}',
            'bluesky' => 'https://bsky.app/intent/compose?text={title}%20{url}',
            'wechat' => 'https://web.wechat.com/?text={title}%20{url}'
        ];

        return $templates[$network] ?? 'https://example.com/share?url={url}&title={title}';
    }

    /**
     * Initialize icon registry with builtin set
     *
     * @return void
     */
    private function initializeIconRegistry(): void
    {
        $builtinSet = [
            'id' => 'builtin',
            'name' => 'Default Icons',
            'version' => '1.0.0',
            'license' => 'MIT',
            'author' => 'HTML Social Share',
            'icons' => [
                'facebook' => 'facebook_icon',
                'twitter' => 'twitter_icon',
                'linkedin' => 'linkedin_icon',
                'pinterest' => 'pinterest_icon',
                'email' => 'email_icon'
            ],
            'meta' => [
                'description' => 'Built-in social media icons'
            ]
        ];

        $this->settings->setIcon('builtin', $builtinSet, 'sets');
        error_log('HSS Migration: Initialized builtin icon set');
    }

    /**
     * Initialize with default settings if no legacy options found
     * Sets all keys that would have been migrated from legacy options
     *
     * @return void
     */
    private function initializeDefaults(): void
    {
        // Set default core settings matching the 12 legacy keys
        $defaults = [
            'version' => '3.0.0',
            
            // Appearance (legacy: title, iconset)
            'title' => 'Share this with your friends',
            'icon_style' => 'default',
            'iconset' => 'default',
            
            // Networks (legacy: icons)
            'enabled_networks' => ['facebook', 'twitter', 'linkedin'],
            
            // Placement (legacy: excludes, show_left, show_right, show_before_post, show_after_post)
            'exclude_pages' => '',
            'floating_left' => false,
            'floating_right' => false,
            'before_content' => false,
            'after_content' => true, // Default: show after post
            
            // Advanced (legacy: g_analytics, auto_hide_btn, use_port, nofollow)
            'google_analytics' => false,
            'auto_hide_buttons' => false,
            'use_port_in_url' => false,
            'nofollow_links' => true,
            
            // Legacy format for backward compat
            'positions' => ['after_post'],
            'exclusions' => ['ids' => [], 'slugs' => [], 'titles' => []]
        ];

        foreach ($defaults as $key => $value) {
            $this->settings->set($key, $value);
        }

        // Create default profiles
        $this->migrateNetworksToProfiles(['google_social_analytics' => false]);

        // Initialize icon registry
        $this->initializeIconRegistry();

        // Mark as initialized (not migrated)
        $this->settings->setMigrationStatus([
            'done' => true,
            'from_version' => 'none',
            'date' => current_time('mysql'),
            'initialized' => true
        ]);

        error_log('HSS Migration: Initialized with default settings (no legacy options found)');
    }

    /**
     * Rollback migration (restore legacy options)
     *
     * @return bool
     */
    public function rollback(): bool
    {
        $migrationStatus = $this->settings->getMigrationStatus();

        if (empty($migrationStatus['legacy_backup'])) {
            error_log('HSS Migration: No legacy backup found for rollback');
            return false;
        }

        // Restore legacy options
        $legacyOptions = $migrationStatus['legacy_backup'];
        update_option('html_social_share_options', $legacyOptions);

        // Clear new options
        delete_option(Settings::OPTION_CORE);
        delete_option(Settings::OPTION_PROFILES);
        delete_option(Settings::OPTION_ICONS);

        // Clear caches
        $this->settings->clearAllCaches();

        error_log('HSS Migration: Rollback completed, legacy options restored');
        return true;
    }
}