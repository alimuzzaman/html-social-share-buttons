<?php
namespace HtmlSocialShare;

class ContentDisplay
{
    private Settings $settings;
    private ProfileManager $profileManager;
    private ShareRenderer $shareRenderer;

    public function __construct(Settings $settings, ProfileManager $profileManager, ShareRenderer $shareRenderer)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->shareRenderer = $shareRenderer;

        $this->init();
    }

    private function init(): void
    {
        // Hook into content display
        add_filter('the_content', [$this, 'addShareButtonsToContent'], 10);

        // Hook into footer for floating buttons
        add_action('wp_footer', [$this, 'addFloatingShareButtons'], 10);

        // Enqueue styles for floating buttons
        add_action('wp_enqueue_scripts', [$this, 'enqueueFloatingStyles'], 10);

        // Debug: Log initialization
        error_log('HSS Debug: ContentDisplay initialized at ' . time());
    }

    public function addShareButtonsToContent(string $content): string
    {
        // Debug: Log content filter call
        error_log('HSS Debug: addShareButtonsToContent called');

        // Only add to single posts/pages
        if (!is_singular() || !in_the_loop()) {
            error_log('HSS Debug: Not a singular post or not in the loop, skipping');
            return $content;
        }

        global $post;
        if (!$post || !is_object($post)) {
            error_log('HSS Debug: No valid post object, skipping');
            return $content;
        }

        // Check exclusions
        if ($this->isContentExcluded($post)) {
            error_log('HSS Debug: Content is excluded, skipping');
            return $content;
        }

        $positions = $this->settings->get('positions', ['after_post']);
        error_log('HSS Debug: Current positions: ' . json_encode($positions));

        $shareButtons = $this->renderShareButtons($post);
        error_log('HSS Debug: Generated share buttons HTML length: ' . strlen($shareButtons));

        // Add HTML comment for debugging
        $debugComment = sprintf(
            '<!-- HSS Debug: Post ID %d, Type: %s, Positions: %s -->',
            $post->ID,
            $post->post_type,
            json_encode($positions)
        );

        $content = $debugComment . $content;

        // Add buttons in configured positions (only before/after for content injection)
        if (in_array('before_post', $positions)) {
            $content = $shareButtons . $content;
            error_log('HSS Debug: Added buttons before post');
        }

        if (in_array('after_post', $positions)) {
            $content .= $shareButtons;
            error_log('HSS Debug: Added buttons after post');
        }

        // Note: left/right positions are handled in wp_footer hook

        return $content;
    }

    private function renderShareButtons(object $post): string
    {
        $enabledNetworks = $this->settings->get('enabled_networks', ['facebook', 'twitter']);
        error_log('HSS Debug: Enabled networks: ' . json_encode($enabledNetworks));

        if (empty($enabledNetworks)) {
            error_log('HSS Debug: No enabled networks, returning empty');
            return '';
        }

        $shareButtons = [];

        foreach ($enabledNetworks as $network) {
            $profile = $this->profileManager->getProfile($network);
            if (!$profile) {
                // Create default profile if it doesn't exist
                $profile = [
                    'id' => $network,
                    'type' => 'share',
                    'label' => ucfirst($network),
                    'handle' => $network,
                    'url_template' => $this->getDefaultUrlTemplate($network),
                    'visible' => true,
                    'new_tab' => true,
                    'order' => 0,
                    'icon' => ['source' => 'builtin', 'ref' => $network],
                    'meta' => []
                ];
                error_log("HSS Debug: Created default profile for network '{$network}'");
            }

            $shareUrl = $this->generateShareUrl($network, $post, $profile);
            $buttonHtml = $this->shareRenderer->render($network, $profile, $shareUrl);
            if (!empty($buttonHtml)) {
                $shareButtons[] = $buttonHtml;
            }
        }

        if (empty($shareButtons)) {
            error_log('HSS Debug: No buttons generated, returning empty');
            return '';
        }

        $title = $this->settings->get('title', 'Share this with your friends');
        $style = $this->settings->get('style', 'minimal');
        $classes = 'hss-share-buttons hss-style-' . esc_attr($style);
        $output = sprintf(
            '<div class="%s"><!-- HSS Debug: %d buttons generated --><h3 class="hss-title">%s</h3><div class="hss-buttons">%s</div></div>',
            $classes,
            count($shareButtons),
            esc_html($title),
            implode('', $shareButtons)
        );

        // Add icon CSS
        $iconCSS = $this->shareRenderer->getIconCSS();
        if (!empty($iconCSS)) {
            // getIconCSS() returns an array of [cssClass => imageUrl]
            if (is_array($iconCSS)) {
                $output .= '<style class="hss-iconset-inline">';
                foreach ($iconCSS as $cssClass => $imageUrl) {
                    $output .= sprintf('.%s{background-image:url(%s);background-size:contain;background-repeat:no-repeat;background-position:center;} ',
                        esc_attr($cssClass), esc_url($imageUrl)
                    );
                }
                $output .= '</style>';
            } elseif (is_string($iconCSS)) {
                // Backwards compatibility: allow string CSS to be appended directly
                $output .= $iconCSS;
            }
        }

        error_log('HSS Debug: Final output length: ' . strlen($output));
        return $output;
    }

    private function isContentExcluded(object $post): bool
    {
        $exclusions = $this->settings->get('exclusions', ['ids' => [], 'slugs' => [], 'titles' => []]);

        // Check post ID exclusions
        if (in_array($post->ID, $exclusions['ids'])) {
            return true;
        }

        // Check slug exclusions
        $postSlug = $post->post_name;
        if (in_array($postSlug, $exclusions['slugs'])) {
            return true;
        }

        // Check title exclusions
        $postTitle = $post->post_title;
        if (in_array($postTitle, $exclusions['titles'])) {
            return true;
        }

        return false;
    }

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

    private function generateShareUrl(string $network, object $post, array $profile): string
    {
        $template = $profile['url_template'] ?? $this->getDefaultUrlTemplate($network);
        $url = get_permalink($post);
        $title = get_the_title($post);
        $replacements = [
            '{url}' => urlencode($url),
            '{title}' => urlencode($title),
            '{text}' => urlencode($title),
            '{handle}' => urlencode($profile['handle'] ?? ''),
        ];
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public function addFloatingShareButtons(): void
    {
        global $post;

        if (!$post || !is_singular() || $this->isContentExcluded($post)) {
            return;
        }

        $positions = $this->settings->get('positions', ['after_post']);
        error_log('HSS Debug: Floating buttons - Current positions: ' . json_encode($positions));

        if (!in_array('left', $positions) && !in_array('right', $positions)) {
            error_log('HSS Debug: No left/right positions enabled, skipping floating buttons');
            return;
        }

        $shareButtons = $this->renderShareButtons($post);
        if (empty($shareButtons)) {
            error_log('HSS Debug: No share buttons to render for floating');
            return;
        }

        // Add debug comment
        $debugComment = sprintf(
            '<!-- HSS Debug: Floating buttons for Post ID %d, Positions: %s -->',
            $post->ID,
            json_encode($positions)
        );

        echo $debugComment;

        if (in_array('left', $positions)) {
            echo '<div class="hss-share-buttons hss-pos-float-left hss-style-' . esc_attr($this->settings->get('style', 'minimal')) . '">' . $shareButtons . '</div>';
            error_log('HSS Debug: Added floating buttons on left side');
        }

        if (in_array('right', $positions)) {
            echo '<div class="hss-share-buttons hss-pos-float-right hss-style-' . esc_attr($this->settings->get('style', 'minimal')) . '">' . $shareButtons . '</div>';
            error_log('HSS Debug: Added floating buttons on right side');
        }
    }

    public function enqueueFloatingStyles(): void
    {
        $positions = $this->settings->get('positions', ['after_post']);

        if (!in_array('left', $positions) && !in_array('right', $positions)) {
            return;
        }

        wp_add_inline_style('wp-block-library', '
            .hss-floating-buttons {
                position: fixed;
                top: 50%;
                transform: translateY(-50%);
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .hss-floating-left {
                left: 20px;
            }
            .hss-floating-right {
                right: 20px;
            }
            .hss-floating-buttons .hss-share-buttons {
                margin: 0;
                padding: 12px;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                border: 1px solid #e0e0e0;
            }
            .hss-floating-buttons .hss-title {
                font-size: 14px;
                margin: 0 0 8px 0;
                text-align: center;
            }
            .hss-floating-buttons .hss-buttons {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .hss-floating-buttons .hss-button {
                display: block;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                text-align: center;
                line-height: 40px;
                text-decoration: none;
                transition: all 0.2s ease;
            }
            .hss-floating-buttons .hss-button:hover {
                transform: scale(1.1);
            }
        ');
    }

    public function enqueueFrontendStyles(): void
    {
        wp_enqueue_style(
            'html-social-share-frontend',
            HTML_SOCIAL_SHARE_ASSETS_URL . 'frontend.css',
            [],
            '1.0.0'
        );

        // Enqueue progressive enhancement JS for WeChat QR toggle
        wp_enqueue_script(
            'html-social-share-wechat-toggle',
            HTML_SOCIAL_SHARE_JS_URL . 'wechat-toggle.js',
            [],
            '1.0.0',
            true
        );
    }
}