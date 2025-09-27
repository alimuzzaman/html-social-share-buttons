<?php
namespace HtmlSocialShare\Integrations\BuddyPress;

use HtmlSocialShare\ShareRendererInterface;

class ShareButtonsIntegration
{
    private $shareRenderer;

    public function __construct($shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
    }

    public static function register($shareRenderer)
    {
        if (!class_exists('BuddyPress')) {
            return;
        }

        $instance = new self($shareRenderer);
        $instance->init();
    }

    public function init()
    {
        // Hook into activity stream
        add_action('bp_activity_entry_meta', [$this, 'addShareButtonsToActivity'], 10);

        // Hook into member profile pages
        add_action('bp_after_profile_header', [$this, 'addShareButtonsToProfile'], 10);
    }

    public function addShareButtonsToActivity()
    {
        $activityId = bp_get_activity_id();

        if (!$activityId) {
            return;
        }

        $activityContent = bp_get_activity_content_body();
        $activityUrl = bp_activity_get_permalink($activityId);
        $userDisplayName = bp_get_displayed_user_fullname();

        // Create a shortened excerpt for sharing
        $excerpt = wp_trim_words(strip_tags($activityContent), 30, '...');

        // Generate share buttons
        $networks = $this->getEnabledNetworks();
        $iconset = $this->getIconset();

        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($iconset);
        }

        echo '<div class="html-social-share-buddypress-activity" style="margin-top: 8px; clear: both;">';
        echo '<div class="share-buttons" style="display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; font-size: 0.85em;">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example',
                'network' => $network,
                'title' => sprintf(__('Activity by %s', 'html-social-share'), $userDisplayName),
                'url' => $activityUrl,
                'description' => $excerpt,
            ];

            $buttonHtml = $this->shareRenderer->render($network, $profile);
            echo $buttonHtml;
        }

        echo '</div></div>';
    }

    public function addShareButtonsToProfile()
    {
        $userId = bp_displayed_user_id();

        if (!$userId) {
            return;
        }

        $userDisplayName = bp_get_displayed_user_fullname();
        $profileUrl = bp_get_displayed_user_link();
        $userDescription = bp_get_displayed_user_fullname(); // Could be expanded to get bio

        // Generate share buttons
        $networks = $this->getEnabledNetworks();
        $iconset = $this->getIconset();

        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($iconset);
        }

        echo '<div class="html-social-share-buddypress-profile" style="margin-top: 15px; clear: both;">';
        echo '<h4 style="margin-bottom: 8px; font-size: 1.1em;">' . esc_html__('Share this profile', 'html-social-share') . '</h4>';
        echo '<div class="share-buttons" style="display: flex; gap: 6px; flex-wrap: wrap;">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example',
                'network' => $network,
                'title' => sprintf(__('BuddyPress Profile: %s', 'html-social-share'), $userDisplayName),
                'url' => $profileUrl,
                'description' => sprintf(__('Check out %s\'s profile on our community', 'html-social-share'), $userDisplayName),
            ];

            $buttonHtml = $this->shareRenderer->render($network, $profile);
            echo $buttonHtml;
        }

        echo '</div></div>';
    }

    private function getEnabledNetworks()
    {
        // Get from plugin settings, fallback to defaults
        $options = get_option('html_social_share', []);
        $networks = isset($options['networks']) ? $options['networks'] : ['facebook', 'twitter', 'linkedin'];

        if (!is_array($networks)) {
            $networks = ['facebook', 'twitter', 'linkedin'];
        }

        return $networks;
    }

    private function getIconset()
    {
        // Get from plugin settings, fallback to default
        $options = get_option('html_social_share', []);
        return isset($options['iconset']) ? $options['iconset'] : 'default_square';
    }
}