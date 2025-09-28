<?php
namespace HtmlSocialShare;

class ProfileManager implements ProfileManagerInterface
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Create a new profile
     *
     * @param array $data Profile data
     * @return string Profile ID
     */
    public function createProfile(array $data): string
    {
        // Generate a unique ID based on the handle or network
        $id = $this->generateProfileId($data);

        // Ensure the profile has required fields
        $profileData = $this->normalizeProfileData($data, $id);

        $this->settings->setProfile($id, $profileData);
        return $id;
    }

    /**
     * Get a profile by ID
     *
     * @param string $id Profile ID
     * @return array|null Profile data or null if not found
     */
    public function getProfile(string $id): ?array
    {
        return $this->settings->getProfile($id);
    }

    /**
     * Update an existing profile
     *
     * @param string $id Profile ID
     * @param array $data Updated profile data
     * @return bool True if updated, false if profile not found
     */
    public function updateProfile(string $id, array $data): bool
    {
        $existingProfile = $this->settings->getProfile($id);
        if (!$existingProfile) {
            return false;
        }

        $updatedData = array_merge($existingProfile, $data);
        $normalizedData = $this->normalizeProfileData($updatedData, $id);

        $this->settings->setProfile($id, $normalizedData);
        return true;
    }

    /**
     * Delete a profile
     *
     * @param string $id Profile ID
     * @return bool True if deleted, false if profile not found
     */
    public function deleteProfile(string $id): bool
    {
        $existingProfile = $this->settings->getProfile($id);
        if (!$existingProfile) {
            return false;
        }

        $this->settings->deleteProfile($id);
        return true;
    }

    /**
     * List all profiles
     *
     * @return array Array of profiles keyed by ID
     */
    public function listProfiles(): array
    {
        return $this->settings->getAllProfiles();
    }

    /**
     * Get profiles by type
     *
     * @param string $type Profile type (share, follow, etc.)
     * @return array Array of profiles of the specified type
     */
    public function getProfilesByType(string $type): array
    {
        $allProfiles = $this->settings->getAllProfiles();
        return array_filter($allProfiles, function($profile) use ($type) {
            return ($profile['type'] ?? 'share') === $type;
        });
    }

    /**
     * Get enabled share profiles
     *
     * @return array Array of enabled share profiles
     */
    public function getEnabledShareProfiles(): array
    {
        $shareProfiles = $this->getProfilesByType('share');
        return array_filter($shareProfiles, function($profile) {
            return ($profile['visible'] ?? true) && !empty($profile['url_template']);
        });
    }

    /**
     * Generate a unique profile ID
     *
     * @param array $data Profile data
     * @return string Unique profile ID
     */
    private function generateProfileId(array $data): string
    {
        $baseId = $data['handle'] ?? $data['id'] ?? 'profile';

        // Ensure uniqueness
        $counter = 1;
        $id = $baseId;
        while ($this->settings->getProfile($id)) {
            $id = $baseId . '_' . $counter;
            $counter++;
        }

        return $id;
    }

    /**
     * Normalize profile data to ensure required fields
     *
     * @param array $data Raw profile data
     * @param string $id Profile ID
     * @return array Normalized profile data
     */
    private function normalizeProfileData(array $data, string $id): array
    {
        return [
            'id' => $id,
            'type' => $data['type'] ?? 'share',
            'label' => $data['label'] ?? ucfirst(str_replace(['_', '-'], ' ', $id)),
            'handle' => $data['handle'] ?? $id,
            'url_template' => $data['url_template'] ?? $this->getDefaultUrlTemplate($id),
            'visible' => $data['visible'] ?? true,
            'new_tab' => $data['new_tab'] ?? true,
            'order' => $data['order'] ?? 0,
            'icon' => $data['icon'] ?? ['source' => 'builtin', 'ref' => $id],
            'meta' => $data['meta'] ?? [],
            'created' => $data['created'] ?? current_time('mysql'),
            'updated' => current_time('mysql')
        ];
    }

    /**
     * Generate share URL from profile template
     *
     * @param array $profile Profile data
     * @param string $url The URL to share
     * @param string $title The title to share
     * @return string Generated share URL
     */
    public function generateShareUrl(array $profile, string $url, string $title = ''): string
    {
        if (empty($profile['url_template'])) {
            return '#';
        }

        $template = $profile['url_template'];

        // Replace placeholders
        $replacements = [
            '{url}' => urlencode($url),
            '{title}' => urlencode($title),
            '{encoded_url}' => urlencode($url),
            '{encoded_title}' => urlencode($title),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Get default URL template for a network
     *
     * @param string $network Network name
     * @return string URL template
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
            'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}'
        ];

        return $templates[$network] ?? 'https://example.com/share?url={url}&title={title}';
    }
