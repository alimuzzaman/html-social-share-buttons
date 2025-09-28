<?php
namespace HtmlSocialShare\Integrations\BetterLinks;

class BetterLinksIntegration
{
    /**
     * Check if BetterLinks plugin is available
     *
     * @return bool
     */
    public static function isAvailable(): bool
    {
        // Check if BetterLinks class exists
        if (!class_exists('BetterLinks')) {
            return false;
        }

        // Check if required helper functions exist
        if (!method_exists('\BetterLinks\Helper', 'insert_link')) {
            return false;
        }

        if (!method_exists('\BetterLinks\Helper', 'generate_random_slug')) {
            return false;
        }

        if (!method_exists('\BetterLinks\Helper', 'generate_short_url')) {
            return false;
        }

        return true;
    }

    /**
     * Check if BetterLinks Pro is available
     *
     * @return bool
     */
    public static function isProAvailable(): bool
    {
        return defined('BETTERLINKS_PRO_VERSION');
    }

    /**
     * Get BetterLinks version
     *
     * @return string|null
     */
    public static function getVersion(): ?string
    {
        if (defined('BETTERLINKS_VERSION')) {
            return BETTERLINKS_VERSION;
        }

        return null;
    }

    /**
     * Create a short link using BetterLinks
     *
     * @param string $targetUrl The original URL to shorten
     * @param string $title Optional title for the link
     * @return string|null The short URL or null on failure
     */
    public function createShortLink(string $targetUrl, string $title = ''): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }

        try {
            // Generate a random slug
            $slug = \BetterLinks\Helper::generate_random_slug();

            // Prepare link data
            $linkData = [
                'link_title' => $title ?: __('Social Share Link', 'html-social-share-buttons'),
                'target_url' => $targetUrl,
                'short_url' => $slug,
                'link_status' => 'publish',
                'redirect_type' => '307', // Temporary redirect
            ];

            // Apply filters for customization
            $linkData = apply_filters('betterlinks/api/params', $linkData);

            // Insert the link
            $result = \BetterLinks\Helper::insert_link($linkData);

            if ($result && isset($result['short_url'])) {
                // Generate the full short URL
                return \BetterLinks\Helper::generate_short_url($result['short_url']);
            }

        } catch (\Exception $e) {
            error_log('BetterLinks integration error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Add UTM parameters to URL for tracking
     *
     * @param string $url The URL to add parameters to
     * @param string $network The social network
     * @param array $trackingData Additional tracking data
     * @return string
     */
    public function addTrackingParameters(string $url, string $network, array $trackingData = []): string
    {
        $params = [
            'utm_source' => $network,
            'utm_medium' => 'social',
            'utm_campaign' => 'share_buttons',
        ];

        // Add custom tracking data
        if (!empty($trackingData)) {
            $params = array_merge($params, $trackingData);
        }

        // Apply filters for customization
        $params = apply_filters('hss_betterlinks_tracking_params', $params, $network, $url);

        $urlParts = parse_url($url);
        $query = isset($urlParts['query']) ? $urlParts['query'] : '';

        if (!empty($query)) {
            parse_str($query, $existingParams);
            $params = array_merge($existingParams, $params);
        }

        $urlParts['query'] = http_build_query($params);

        return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . $urlParts['query'];
    }
}