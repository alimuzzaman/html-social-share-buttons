<?php
/**
 * Legacy Content Filter
 *
 * Handles automatic insertion of share buttons before/after post content
 * for legacy v2.x compatibility.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\Frontend;

use HtmlSocialShare\Settings;

class LegacyContentFilter
{
    private LegacyButtonRenderer $legacyRenderer;
    private Settings $settings;

    public function __construct(
        LegacyButtonRenderer $legacyRenderer,
        Settings $settings
    ) {
        $this->legacyRenderer = $legacyRenderer;
        $this->settings = $settings;

        // Register content filter
        add_filter('the_content', [$this, 'filterTheContent'], 10);
    }

    /**
     * Filter the_content to add share buttons
     *
     * @param string $content Post content
     * @return string Filtered content
     */
    public function filterTheContent(string $content): string
    {
        // Only run on singular pages
        if (!is_singular()) {
            return $content;
        }

        // Check if excluded
        if ($this->isExcluded()) {
            return $content;
        }

        // Get settings
        $showIn = $this->settings->get('show_in', []);
        
        // Get legacy-compatible options
        $options = [
            'class' => 'in_widget',
            'iconset' => $this->settings->get('iconset', 'default'),
            'iconset_type' => $this->settings->get('iconset_type', 'square'),
            'title' => $this->settings->get('title', 'Share this with your friends'),
            'icons' => $this->getEnabledIcons(),
        ];

        // Add buttons before content
        if (!empty($showIn['show_before_post']) || !empty($showIn['before_content'])) {
            $beforeOptions = array_merge($options, ['show_on' => 'show_before_post']);
            $content = $this->legacyRenderer->render($beforeOptions) . $content;
        }

        // Add buttons after content
        if (!empty($showIn['show_after_post']) || !empty($showIn['after_content'])) {
            $afterOptions = array_merge($options, ['show_on' => 'show_after_post']);
            $content = $content . $this->legacyRenderer->render($afterOptions);
        }

        return $content;
    }

    /**
     * Check if current post is excluded
     *
     * @return bool
     */
    private function isExcluded(): bool
    {
        global $post;

        if (empty($post->ID)) {
            return false;
        }

        // Check exclusion list
        $excludes = $this->settings->get('excludes', '');
        if (!empty($excludes)) {
            $excludes = array_map('trim', explode(',', $excludes));
            
            if (in_array($post->ID, $excludes, true)) {
                return true;
            }
        }

        // Check per-post disable meta
        $disableShare = get_post_meta($post->ID, '_zm_sh_disable_share', true);
        if ($disableShare === 'on') {
            return true;
        }

        return false;
    }

    /**
     * Get enabled icons from settings
     *
     * @return array
     */
    private function getEnabledIcons(): array
    {
        // Try to get from legacy option format first
        $legacyIcons = $this->settings->get('icons', null);
        
        if (is_array($legacyIcons)) {
            // Already in legacy format (network_name => 1/0)
            return $legacyIcons;
        }

        // Try new format (enabled_networks array)
        $enabledNetworks = $this->settings->get('enabled_networks', []);
        
        if (is_array($enabledNetworks)) {
            $icons = [];
            foreach ($enabledNetworks as $network) {
                $icons[$network] = 1;
            }
            return $icons;
        }

        // Default icons if nothing set
        return [
            'facebook' => 1,
            'twitter' => 1,
            'linkedin' => 1,
            'googleplus' => 1,
            'bookmark' => 1,
            'pinterest' => 1,
            'mail' => 1,
        ];
    }
}
