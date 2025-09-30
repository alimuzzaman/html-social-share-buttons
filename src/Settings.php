<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Utils\DataUtils;
use HtmlSocialShare\Utils\SecurityUtils;

/**
 * Settings management with validation and caching
 *
 * Handles core settings, profiles, and icons with proper validation,
 * sanitization, and caching. Separates pure validation functions from
 * WordPress-specific storage operations.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class Settings implements SettingsInterface
{
    // Option keys for the new schema
    const OPTION_CORE = 'hss_core';
    const OPTION_PROFILES = 'hss_profiles';
    const OPTION_ICONS = 'hss_icons';

    // Cache keys
    const CACHE_CORE = 'hss_core_cache';
    const CACHE_PROFILES = 'hss_profiles_cache';
    const CACHE_ICONS = 'hss_icons_cache';
    const CACHE_VERSION = 'hss_cache_version';

    // Cache expiration (24 hours)
    const CACHE_EXPIRATION = 86400;

    // Cache version for invalidation
    private static $cacheVersion = null;

    /**
     * Get cache version for invalidation
     *
     * @return string
     */
    private function getCacheVersion(): string
    {
        if (self::$cacheVersion === null) {
            self::$cacheVersion = get_option(self::CACHE_VERSION, '1.0');
        }
        return self::$cacheVersion;
    }

    /**
     * Increment cache version to invalidate all caches
     *
     * @return void
     */
    private function incrementCacheVersion(): void
    {
        $newVersion = (string) (floatval($this->getCacheVersion()) + 0.1);
        update_option(self::CACHE_VERSION, $newVersion);
        self::$cacheVersion = $newVersion;
    }

    /**
     * Get a setting value by key from hss_core option
     *
     * @param string $key Setting key (supports dot notation)
     * @param mixed $default Default value if not found
     * @return mixed Setting value or default
     */
    public function get(string $key, $default = null)
    {
        $coreData = $this->getCoreData();
        return ArrayUtils::get($coreData, $key, $default);
    }

    /**
     * Set a setting value in hss_core option with validation
     *
     * @param string $key Setting key (supports dot notation)
     * @param mixed $value Value to set
     * @return void
     * @throws \InvalidArgumentException If key or value is invalid
     */
    public function set(string $key, $value): void
    {
        // Validate key format
        if (!self::isValidSettingKey($key)) {
            throw new \InvalidArgumentException("Invalid setting key: {$key}");
        }

        // Sanitize value based on key type
        $sanitizedValue = $this->sanitizeSettingValue($key, $value);

        $coreData = $this->getCoreData();
        $coreData = ArrayUtils::set($coreData, $key, $sanitizedValue);
        $this->setCoreData($coreData);
    }

    /**
     * Delete a setting from hss_core option
     *
     * @param string $key Setting key (supports dot notation)
     * @return void
     */
    public function delete(string $key): void
    {
        $coreData = $this->getCoreData();
        $coreData = ArrayUtils::unset($coreData, $key);
        $this->setCoreData($coreData);
    }

    /**
     * Get all core settings as an array
     *
     * @return array All core settings
     */
    public function getAll(): array
    {
        return $this->getCoreData();
    }

    /**
     * Get a profile by ID from hss_profiles option
     *
     * @param string $profileId Profile identifier
     * @return array|null Profile data or null if not found
     */
    public function getProfile(string $profileId): ?array
    {
        if (!self::isValidProfileId($profileId)) {
            return null;
        }

        $profiles = $this->getProfilesData();
        $profile = $profiles[$profileId] ?? null;

        return $profile ? self::sanitizeProfileData($profile) : null;
    }

    /**
     * Get all profiles from hss_profiles option
     *
     * @return array All profiles with sanitized data
     */
    public function getAllProfiles(): array
    {
        $profiles = $this->getProfilesData();
        $sanitizedProfiles = [];

        foreach ($profiles as $profileId => $profile) {
            if (self::isValidProfileId($profileId)) {
                $sanitizedProfiles[$profileId] = self::sanitizeProfileData($profile);
            }
        }

        return $sanitizedProfiles;
    }

    /**
     * Set a profile in hss_profiles option with validation
     *
     * @param string $profileId Profile identifier
     * @param array $profileData Profile data
     * @return void
     * @throws \InvalidArgumentException If profile data is invalid
     */
    public function setProfile(string $profileId, array $profileData): void
    {
        // Validate profile ID
        if (!self::isValidProfileId($profileId)) {
            throw new \InvalidArgumentException("Invalid profile ID: {$profileId}");
        }

        // Validate and sanitize profile data
        $validation = self::validateProfileData($profileData);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException("Invalid profile data: " . implode(', ', $validation['errors']));
        }

        $sanitizedProfile = self::sanitizeProfileData($profileData);

        $profiles = $this->getProfilesData();
        $profiles[$profileId] = $sanitizedProfile;
        $this->setProfilesData($profiles);
    }

    /**
     * Delete a profile from hss_profiles option
     *
     * @param string $profileId Profile identifier
     * @return void
     */
    public function deleteProfile(string $profileId): void
    {
        $profiles = $this->getProfilesData();
        unset($profiles[$profileId]);
        $this->setProfilesData($profiles);
    }

    /**
     * Get icon data from hss_icons option
     *
     * @param string $iconId Icon identifier
     * @param string $type Icon type ('sets' or 'custom')
     * @return array|null Icon data or null if not found
     */
    public function getIcon(string $iconId, string $type = 'sets'): ?array
    {
        if (!self::isValidIconId($iconId) || !self::isValidIconType($type)) {
            return null;
        }

        $icons = $this->getIconsData();
        $icon = $icons[$type][$iconId] ?? null;

        return $icon ? self::sanitizeIconData($icon) : null;
    }

    /**
     * Get all icons from hss_icons option
     *
     * @return array All icons with sanitized data
     */
    public function getAllIcons(): array
    {
        $icons = $this->getIconsData();
        $sanitizedIcons = [];

        foreach ($icons as $type => $iconSet) {
            if (self::isValidIconType($type) && is_array($iconSet)) {
                $sanitizedIcons[$type] = [];
                foreach ($iconSet as $iconId => $icon) {
                    if (self::isValidIconId($iconId)) {
                        $sanitizedIcons[$type][$iconId] = self::sanitizeIconData($icon);
                    }
                }
            }
        }

        return $sanitizedIcons;
    }

    /**
     * Set icon data in hss_icons option with validation
     *
     * @param string $iconId Icon identifier
     * @param array $iconData Icon data
     * @param string $type Icon type ('sets' or 'custom')
     * @return void
     * @throws \InvalidArgumentException If icon data is invalid
     */
    public function setIcon(string $iconId, array $iconData, string $type = 'sets'): void
    {
        // Validate parameters
        if (!self::isValidIconId($iconId)) {
            throw new \InvalidArgumentException("Invalid icon ID: {$iconId}");
        }

        if (!self::isValidIconType($type)) {
            throw new \InvalidArgumentException("Invalid icon type: {$type}");
        }

        // Validate and sanitize icon data
        $validation = self::validateIconData($iconData);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException("Invalid icon data: " . implode(', ', $validation['errors']));
        }

        $sanitizedIcon = self::sanitizeIconData($iconData);

        $icons = $this->getIconsData();
        $icons[$type][$iconId] = $sanitizedIcon;
        $this->setIconsData($icons);
    }

    /**
     * Delete icon from hss_icons option
     *
     * @param string $iconId Icon identifier
     * @param string $type Icon type ('sets' or 'custom')
     * @return void
     */
    public function deleteIcon(string $iconId, string $type = 'sets'): void
    {
        $icons = $this->getIconsData();
        unset($icons[$type][$iconId]);
        $this->setIconsData($icons);
    }

    /**
     * Get core data with caching
     *
     * @return array Core settings data
     */
    private function getCoreData(): array
    {
        $cached = wp_cache_get(self::CACHE_CORE);
        if ($cached !== false) {
            return $cached;
        }

        $data = get_option(self::OPTION_CORE, []);
        if (!is_array($data)) {
            $data = [];
        }

        // Apply default schema if needed
        $data = self::applyDefaultCoreSchema($data);

        wp_cache_set(self::CACHE_CORE, $data, '', self::CACHE_EXPIRATION);
        return $data;
    }

    /**
     * Set core data with cache invalidation
     *
     * @param array $data Core settings data
     * @return void
     */
    private function setCoreData(array $data): void
    {
        update_option(self::OPTION_CORE, $data);
        wp_cache_delete(self::CACHE_CORE);
    }

    /**
     * Get profiles data with caching
     *
     * @return array Profiles data
     */
    private function getProfilesData(): array
    {
        $cached = wp_cache_get(self::CACHE_PROFILES);
        if ($cached !== false) {
            return $cached;
        }

        $data = get_option(self::OPTION_PROFILES, []);
        if (!is_array($data)) {
            $data = [];
        }

        wp_cache_set(self::CACHE_PROFILES, $data, '', self::CACHE_EXPIRATION);
        return $data;
    }

    /**
     * Set profiles data with cache invalidation
     *
     * @param array $data Profiles data
     * @return void
     */
    private function setProfilesData(array $data): void
    {
        update_option(self::OPTION_PROFILES, $data);
        wp_cache_delete(self::CACHE_PROFILES);
    }

    /**
     * Get icons data with caching
     *
     * @return array Icons data
     */
    private function getIconsData(): array
    {
        $cached = wp_cache_get(self::CACHE_ICONS);
        if ($cached !== false) {
            return $cached;
        }

        $data = get_option(self::OPTION_ICONS, []);
        if (!is_array($data)) {
            $data = ['sets' => [], 'custom' => []];
        }

        // Ensure structure exists
        if (!isset($data['sets'])) {
            $data['sets'] = [];
        }
        if (!isset($data['custom'])) {
            $data['custom'] = [];
        }

        wp_cache_set(self::CACHE_ICONS, $data, '', self::CACHE_EXPIRATION);
        return $data;
    }

    /**
     * Set icons data with cache invalidation
     *
     * @param array $data Icons data
     * @return void
     */
    private function setIconsData(array $data): void
    {
        update_option(self::OPTION_ICONS, $data);
        wp_cache_delete(self::CACHE_ICONS);
    }

    /**
     * Clear all caches
     *
     * @return void
     */
    public function clearAllCaches(): void
    {
        wp_cache_delete(self::CACHE_CORE);
        wp_cache_delete(self::CACHE_PROFILES);
        wp_cache_delete(self::CACHE_ICONS);
    }

    /**
     * Clear all caches with version increment (forces invalidation)
     *
     * @return void
     */
    public function clearAllCachesHard(): void
    {
        $this->incrementCacheVersion();
        $this->clearAllCaches();
    }

    /**
     * Clear specific cache type
     *
     * @param string $type Cache type ('core', 'profiles', or 'icons')
     * @return void
     */
    public function clearCache(string $type): void
    {
        switch ($type) {
            case 'core':
                wp_cache_delete(self::CACHE_CORE);
                break;
            case 'profiles':
                wp_cache_delete(self::CACHE_PROFILES);
                break;
            case 'icons':
                wp_cache_delete(self::CACHE_ICONS);
                break;
            default:
                // Clear all if invalid type
                $this->clearAllCaches();
        }
    }

    /**
     * Warm up caches by preloading data
     *
     * @return void
     */
    public function warmCaches(): void
    {
        // Preload all data to warm caches
        $this->getCoreData();
        $this->getProfilesData();
        $this->getIconsData();
    }

    /**
     * Get cache statistics
     *
     * @return array Cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'version' => $this->getCacheVersion(),
            'core_cached' => wp_cache_get(self::CACHE_CORE) !== false,
            'profiles_cached' => wp_cache_get(self::CACHE_PROFILES) !== false,
            'icons_cached' => wp_cache_get(self::CACHE_ICONS) !== false,
            'expiration' => self::CACHE_EXPIRATION
        ];
    }

    /**
     * Get migration status
     *
     * @return array Migration status
     */
    public function getMigrationStatus(): array
    {
        $coreData = $this->getCoreData();
        return $coreData['legacy_migration'] ?? ['done' => false];
    }

    /**
     * Set migration status
     *
     * @param array $status Migration status
     * @return void
     */
    public function setMigrationStatus(array $status): void
    {
        $coreData = $this->getCoreData();
        $coreData['legacy_migration'] = $status;
        $this->setCoreData($coreData);
    }

    // ===== PURE FUNCTIONS (NO SIDE EFFECTS) =====

    /**
     * Pure function: Validate setting key format
     *
     * @param string $key Setting key
     * @return bool True if valid
     */
    public static function isValidSettingKey(string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        // Allow dot notation for nested keys
        return preg_match('/^[a-zA-Z0-9_][a-zA-Z0-9_\.]*$/', $key) === 1;
    }

    /**
     * Pure function: Validate profile ID format
     *
     * @param string $profileId Profile identifier
     * @return bool True if valid
     */
    public static function isValidProfileId(string $profileId): bool
    {
        return SecurityUtils::isAlphanumeric($profileId, true, true) && strlen($profileId) <= 50;
    }

    /**
     * Pure function: Validate icon ID format
     *
     * @param string $iconId Icon identifier
     * @return bool True if valid
     */
    public static function isValidIconId(string $iconId): bool
    {
        return SecurityUtils::isAlphanumeric($iconId, true, true) && strlen($iconId) <= 50;
    }

    /**
     * Pure function: Validate icon type
     *
     * @param string $type Icon type
     * @return bool True if valid
     */
    public static function isValidIconType(string $type): bool
    {
        return in_array($type, ['sets', 'custom'], true);
    }

    /**
     * Pure function: Validate profile data
     *
     * @param array $profile Profile data
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateProfileData(array $profile): array
    {
        return DataUtils::validateProfileConfig($profile);
    }

    /**
     * Pure function: Validate icon data
     *
     * @param array $icon Icon data
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateIconData(array $icon): array
    {
        $errors = [];

        // Check required fields
        if (empty($icon['name'])) {
            $errors[] = "Missing required field: name";
        }

        if (empty($icon['svg']) && empty($icon['image'])) {
            $errors[] = "Icon must have either 'svg' or 'image' field";
        }

        // Validate SVG if provided
        if (!empty($icon['svg'])) {
            if (!is_string($icon['svg'])) {
                $errors[] = "SVG field must be a string";
            } else if (SecurityUtils::hasXssPatterns($icon['svg'])) {
                $errors[] = "SVG contains potentially dangerous content";
            }
        }

        // Validate image URL if provided
        if (!empty($icon['image'])) {
            if (!SecurityUtils::sanitizeUrl($icon['image'])) {
                $errors[] = "Invalid image URL";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Pure function: Sanitize profile data
     *
     * @param array $profile Raw profile data
     * @return array Sanitized profile data
     */
    public static function sanitizeProfileData(array $profile): array
    {
        return DataUtils::sanitizeProfileConfig($profile);
    }

    /**
     * Pure function: Sanitize icon data
     *
     * @param array $icon Raw icon data
     * @return array Sanitized icon data
     */
    public static function sanitizeIconData(array $icon): array
    {
        $sanitized = [];

        // Sanitize name
        if (isset($icon['name'])) {
            $sanitized['name'] = SecurityUtils::sanitizeTextField($icon['name']);
        }

        // Sanitize SVG (basic cleaning - full sanitization should use SanitizerInterface)
        if (isset($icon['svg'])) {
            $sanitized['svg'] = SecurityUtils::stripDangerousHtml($icon['svg'], []);
        }

        // Sanitize image URL
        if (isset($icon['image'])) {
            $sanitized['image'] = SecurityUtils::sanitizeUrl($icon['image']);
        }

        // Sanitize optional fields
        if (isset($icon['description'])) {
            $sanitized['description'] = SecurityUtils::sanitizeTextField($icon['description']);
        }

        if (isset($icon['category'])) {
            $sanitized['category'] = SecurityUtils::sanitizeKey($icon['category']);
        }

        if (isset($icon['tags'])) {
            $tags = is_array($icon['tags']) ? $icon['tags'] : [];
            $sanitized['tags'] = array_map([SecurityUtils::class, 'sanitizeKey'], $tags);
        }

        return $sanitized;
    }

    /**
     * Pure function: Apply default core schema
     *
     * @param array $data Existing core data
     * @return array Data with defaults applied
     */
    public static function applyDefaultCoreSchema(array $data): array
    {
        $defaults = [
            'version' => '3.0.0',
            'theme' => 'default',
            'display' => [
                'style' => 'buttons',
                'size' => 'medium',
                'shape' => 'rounded'
            ],
            'behavior' => [
                'target' => '_blank',
                'nofollow' => true,
                'track_clicks' => false
            ],
            'performance' => [
                'cache_enabled' => true,
                'cache_ttl' => 3600,
                'lazy_load' => false
            ],
            'opengraph' => [
                'enabled' => false,
                'front_page' => true,
                'archive_pages' => false,
                'twitter_card' => true,
                'default_image' => ''
            ]
        ];

        return ArrayUtils::deepMerge($defaults, $data);
    }

    /**
     * Sanitize setting value based on key type
     *
     * @param string $key Setting key
     * @param mixed $value Raw value
     * @return mixed Sanitized value
     */
    private function sanitizeSettingValue(string $key, $value)
    {
        // Key-specific sanitization rules
        $booleanKeys = ['behavior.nofollow', 'behavior.track_clicks', 'performance.cache_enabled', 'performance.lazy_load'];
        $integerKeys = ['performance.cache_ttl'];
        $urlKeys = ['display.custom_css_url'];
        $keyKeys = ['theme', 'display.style', 'display.size', 'display.shape'];

        if (in_array($key, $booleanKeys, true)) {
            return DataUtils::sanitizeBoolean($value);
        }

        if (in_array($key, $integerKeys, true)) {
            return DataUtils::sanitizeInteger($value);
        }

        if (in_array($key, $urlKeys, true)) {
            return SecurityUtils::sanitizeUrl((string) $value);
        }

        if (in_array($key, $keyKeys, true)) {
            return SecurityUtils::sanitizeKey((string) $value);
        }

        // Default text sanitization
        if (is_string($value)) {
            return SecurityUtils::sanitizeTextField($value);
        }

        return $value;
    }
}
