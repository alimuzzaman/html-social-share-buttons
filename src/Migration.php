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
            'social_share_options'
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
     * @param array $legacy
     * @return void
     */
    private function migrateCoreSettings(array $legacy): void
    {
        $coreSettings = [
            'version' => '3.0.0',
            'title' => $legacy['title'] ?? 'Share this with your friends',
            'positions' => $this->normalizePositions($legacy),
            'auto_hide' => !empty($legacy['auto_hide']),
            'use_port' => !empty($legacy['use_port']),
            'google_analytics' => !empty($legacy['google_social_analytics']),
            'nofollow' => !empty($legacy['nofollow']),
            'enabled_networks' => $this->getEnabledNetworks($legacy),
            'exclusions' => $this->normalizeExclusions($legacy)
        ];

        // Set each core setting
        foreach ($coreSettings as $key => $value) {
            $this->settings->set($key, $value);
        }

        error_log('HSS Migration: Core settings migrated: ' . json_encode($coreSettings));
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
            'show_on_left' => 'left',
            'show_on_right' => 'right',
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
     * @param array $legacy
     * @return array
     */
    private function getEnabledNetworks(array $legacy): array
    {
        $networks = [];

        // Common social networks to check
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

        // Default networks if none specified
        if (empty($networks)) {
            $networks = ['facebook', 'twitter', 'linkedin'];
        }

        return $networks;
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

        if (!empty($legacy['exclude'])) {
            // Try to parse the exclude field - could be comma-separated IDs/slugs
            $excludeValue = $legacy['exclude'];
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
            'twitter' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
            'pinterest' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
            'email' => 'mailto:?subject={title}&body={url}',
            'whatsapp' => 'https://wa.me/?text={title}%20{url}',
            'telegram' => 'https://t.me/share/url?url={url}&text={title}',
            'reddit' => 'https://reddit.com/submit?url={url}&title={title}',
            'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}'
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
     *
     * @return void
     */
    private function initializeDefaults(): void
    {
        // Set default core settings
        $defaults = [
            'version' => '3.0.0',
            'title' => 'Share this with your friends',
            'positions' => ['after_post'],
            'auto_hide' => false,
            'use_port' => false,
            'google_analytics' => false,
            'nofollow' => true,
            'enabled_networks' => ['facebook', 'twitter', 'linkedin'],
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

        error_log('HSS Migration: Initialized with default settings');
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