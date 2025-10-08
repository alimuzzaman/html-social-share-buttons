<?php
namespace HtmlSocialShare;

interface SettingsInterface
{
    /**
     * Get a setting value by key, with optional default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Delete a setting.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;

    /**
     * Get all settings as an array.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Get a profile by ID.
     *
     * @param string $profileId
     * @return array|null
     */
    public function getProfile(string $profileId): ?array;

    /**
     * Get all profiles.
     *
     * @return array
     */
    public function getAllProfiles(): array;

    /**
     * Set a profile.
     *
     * @param string $profileId
     * @param array $profileData
     * @return void
     */
    public function setProfile(string $profileId, array $profileData): void;

    /**
     * Delete a profile.
     *
     * @param string $profileId
     * @return void
     */
    public function deleteProfile(string $profileId): void;

    /**
     * Get icon data.
     *
     * @param string $iconId
     * @param string $type
     * @return array|null
     */
    public function getIcon(string $iconId, string $type = 'sets'): ?array;

    /**
     * Get all icons.
     *
     * @return array
     */
    public function getAllIcons(): array;

    /**
     * Set icon data.
     *
     * @param string $iconId
     * @param array $iconData
     * @param string $type
     * @return void
     */
    public function setIcon(string $iconId, array $iconData, string $type = 'sets'): void;

    /**
     * Delete icon.
     *
     * @param string $iconId
     * @param string $type
     * @return void
     */
    public function deleteIcon(string $iconId, string $type = 'sets'): void;

    /**
     * Clear all caches.
     *
     * @return void
     */
    public function clearAllCaches(): void;

    /**
     * Get migration status.
     *
     * @return array
     */
    public function getMigrationStatus(): array;

    /**
     * Set migration status.
     *
     * @param array $status
     * @return void
     */
    public function setMigrationStatus(array $status): void;
}
