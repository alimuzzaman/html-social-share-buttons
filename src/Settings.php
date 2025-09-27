<?php
namespace HtmlSocialShare;

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
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $coreData = $this->getCoreData();
        return $coreData[$key] ?? $default;
    }

    /**
     * Set a setting value in hss_core option
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $coreData = $this->getCoreData();
        $coreData[$key] = $value;
        $this->setCoreData($coreData);
    }

    /**
     * Delete a setting from hss_core option
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $coreData = $this->getCoreData();
        unset($coreData[$key]);
        $this->setCoreData($coreData);
    }

    /**
     * Get all core settings as an array
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->getCoreData();
    }

    /**
     * Get a profile by ID from hss_profiles option
     *
     * @param string $profileId
     * @return array|null
     */
    public function getProfile(string $profileId): ?array
    {
        $profiles = $this->getProfilesData();
        return $profiles[$profileId] ?? null;
    }

    /**
     * Get all profiles from hss_profiles option
     *
     * @return array
     */
    public function getAllProfiles(): array
    {
        return $this->getProfilesData();
    }

    /**
     * Set a profile in hss_profiles option
     *
     * @param string $profileId
     * @param array $profileData
     * @return void
     */
    public function setProfile(string $profileId, array $profileData): void
    {
        $profiles = $this->getProfilesData();
        $profiles[$profileId] = $profileData;
        $this->setProfilesData($profiles);
    }

    /**
     * Delete a profile from hss_profiles option
     *
     * @param string $profileId
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
     * @param string $iconId
     * @param string $type 'sets' or 'custom'
     * @return array|null
     */
    public function getIcon(string $iconId, string $type = 'sets'): ?array
    {
        $icons = $this->getIconsData();
        return $icons[$type][$iconId] ?? null;
    }

    /**
     * Get all icons from hss_icons option
     *
     * @return array
     */
    public function getAllIcons(): array
    {
        return $this->getIconsData();
    }

    /**
     * Set icon data in hss_icons option
     *
     * @param string $iconId
     * @param array $iconData
     * @param string $type 'sets' or 'custom'
     * @return void
     */
    public function setIcon(string $iconId, array $iconData, string $type = 'sets'): void
    {
        $icons = $this->getIconsData();
        $icons[$type][$iconId] = $iconData;
        $this->setIconsData($icons);
    }

    /**
     * Delete icon from hss_icons option
     *
     * @param string $iconId
     * @param string $type 'sets' or 'custom'
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
     * @return array
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

        wp_cache_set(self::CACHE_CORE, $data, '', self::CACHE_EXPIRATION);
        return $data;
    }

    /**
     * Set core data with cache invalidation
     *
     * @param array $data
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
     * @return array
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
     * @param array $data
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
     * @return array
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
     * @param array $data
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
     * @param string $type 'core', 'profiles', or 'icons'
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
     * @return array
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
     * @return array
     */
    public function getMigrationStatus(): array
    {
        $coreData = $this->getCoreData();
        return $coreData['legacy_migration'] ?? ['done' => false];
    }

    /**
     * Set migration status
     *
     * @param array $status
     * @return void
     */
    public function setMigrationStatus(array $status): void
    {
        $coreData = $this->getCoreData();
        $coreData['legacy_migration'] = $status;
        $this->setCoreData($coreData);
    }
}
