<?php
namespace HtmlSocialShare\Integrations\BetterLinks;

class BetterLinksUrlFilter
{
    private BetterLinksIntegration $integration;
    private array $settings;

    public function __construct(BetterLinksIntegration $integration, array $settings = [])
    {
        $this->integration = $integration;
        $this->settings = [
            'enabled' => $settings['betterlinks_enabled'] ?? false,
            'shorten_urls' => $settings['betterlinks_shorten_urls'] ?? true,
            'add_tracking' => $settings['betterlinks_add_tracking'] ?? true,
            'custom_tracking' => $settings['betterlinks_custom_tracking'] ?? []
        ];
    }

    /**
     * Register the URL filter
     */
    public function register(): void
    {
        if (!$this->integration->isAvailable()) {
            return;
        }

        add_filter('hss_share_url', [$this, 'filterShareUrl'], 10, 5);
    }

    /**
     * Filter share URLs to apply BetterLinks shortening
     *
     * @param string $shareUrl The generated share URL
     * @param string $network The social network
     * @param string $originalUrl The original URL being shared
     * @param string $title The title being shared
     * @param array $profile The network profile data
     * @return string
     */
    public function filterShareUrl(string $shareUrl, string $network, string $originalUrl, string $title, array $profile): string
    {
        // Check if BetterLinks integration is enabled
        if (!$this->isEnabled()) {
            return $shareUrl;
        }

        // Only shorten if URL shortening is enabled
        if (!$this->shouldShortenUrls()) {
            return $shareUrl;
        }

        // Create short link
        $shortUrl = $this->integration->createShortLink($originalUrl, $title);

        if ($shortUrl) {
            // Replace the original URL in the share URL with the short URL
            $shareUrl = str_replace(urlencode($originalUrl), urlencode($shortUrl), $shareUrl);

            // Add tracking parameters if enabled
            if ($this->shouldAddTracking()) {
                $trackingData = $this->getTrackingData($network, $originalUrl, $title);
                $shareUrl = $this->integration->addTrackingParameters($shareUrl, $network, $trackingData);
            }
        }

        return $shareUrl;
    }

    /**
     * Check if BetterLinks integration is enabled
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->settings['enabled'] ?? false;
    }

    /**
     * Check if URL shortening is enabled
     *
     * @return bool
     */
    private function shouldShortenUrls(): bool
    {
        return $this->settings['shorten_urls'] ?? true;
    }

    /**
     * Check if tracking parameters should be added
     *
     * @return bool
     */
    private function shouldAddTracking(): bool
    {
        return $this->settings['add_tracking'] ?? true;
    }

    /**
     * Get tracking data for the share
     *
     * @param string $network
     * @param string $url
     * @param string $title
     * @return array
     */
    private function getTrackingData(string $network, string $url, string $title): array
    {
        $data = [];

        // Add post ID if available
        if (is_singular()) {
            global $post;
            if ($post) {
                $data['utm_content'] = $post->ID;
            }
        }

        // Add custom tracking parameters
        if (!empty($this->settings['custom_tracking'])) {
            $data = array_merge($data, $this->settings['custom_tracking']);
        }

        return $data;
    }
}