<?php
namespace HtmlSocialShare;

interface ProfileManagerInterface
{
    /**
     * Create a new profile
     *
     * @param array $data Profile data
     * @return string Profile ID
     */
    public function createProfile(array $data): string;

    /**
     * Get a profile by ID
     *
     * @param string $id Profile ID
     * @return array|null Profile data or null if not found
     */
    public function getProfile(string $id): ?array;

    /**
     * Update an existing profile
     *
     * @param string $id Profile ID
     * @param array $data Updated profile data
     * @return bool True if updated, false if profile not found
     */
    public function updateProfile(string $id, array $data): bool;

    /**
     * Delete a profile
     *
     * @param string $id Profile ID
     * @return bool True if deleted, false if profile not found
     */
    public function deleteProfile(string $id): bool;

    /**
     * List all profiles
     *
     * @return array Array of profiles keyed by ID
     */
    public function listProfiles(): array;

    /**
     * Get profiles by type
     *
     * @param string $type Profile type (share, follow, etc.)
     * @return array Array of profiles of the specified type
     */
    public function getProfilesByType(string $type): array;

    /**
     * Get enabled share profiles
     *
     * @return array Array of enabled share profiles
     */
    public function getEnabledShareProfiles(): array;
}
