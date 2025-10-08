<?php
namespace HtmlSocialShare\Integrations\bbPress;

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
        if (!class_exists('bbPress')) {
            return;
        }

        $instance = new self($shareRenderer);
        $instance->init();
    }

    public function init()
    {
        // Hook into single topic pages
        add_action('bbp_template_after_single_topic', [$this, 'addShareButtonsToTopic'], 10);

        // Hook into reply content
        add_action('bbp_theme_after_reply_content', [$this, 'addShareButtonsToReply'], 10);
    }

    public function addShareButtonsToTopic()
    {
        $topicId = bbp_get_topic_id();

        if (!$topicId) {
            return;
        }

        $topicTitle = bbp_get_topic_title($topicId);
        $topicUrl = bbp_get_topic_permalink($topicId);

        // Generate share buttons
        $networks = $this->getEnabledNetworks();
        $iconset = $this->getIconset();

        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($iconset);
        }

        echo '<div class="html-social-share-bbpress-topic" style="margin-top: 20px; clear: both;">';
        echo '<h4 style="margin-bottom: 10px;">' . esc_html__('Share this topic', 'html-social-share') . '</h4>';
        echo '<div class="share-buttons" style="display: flex; gap: 8px; flex-wrap: wrap;">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example',
                'network' => $network,
                'title' => $topicTitle,
                'url' => $topicUrl,
            ];

            $buttonHtml = $this->shareRenderer->render($network, $profile);
            echo $buttonHtml;
        }

        echo '</div></div>';
    }

    public function addShareButtonsToReply()
    {
        $replyId = bbp_get_reply_id();

        if (!$replyId) {
            return;
        }

        // Only show on replies if enabled in settings
        if (!$this->isReplySharingEnabled()) {
            return;
        }

        $replyUrl = bbp_get_reply_url($replyId);
        $topicTitle = bbp_get_topic_title(bbp_get_reply_topic_id($replyId));
        $replyContent = bbp_get_reply_content($replyId);

        // Create a shortened excerpt for sharing
        $excerpt = wp_trim_words(strip_tags($replyContent), 20, '...');

        // Generate share buttons (simplified for replies)
        $networks = $this->getEnabledNetworks();
        $iconset = $this->getIconset();

        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($iconset);
        }

        echo '<div class="html-social-share-bbpress-reply" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">';
        echo '<div class="share-buttons" style="display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; font-size: 0.9em;">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example',
                'network' => $network,
                'title' => sprintf(__('Reply to: %s', 'html-social-share'), $topicTitle),
                'url' => $replyUrl,
                'description' => $excerpt,
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

    private function isReplySharingEnabled()
    {
        // Get from plugin settings
        $options = get_option('html_social_share', []);
        return isset($options['bbpress_replies']) ? (bool) $options['bbpress_replies'] : false;
    }
}