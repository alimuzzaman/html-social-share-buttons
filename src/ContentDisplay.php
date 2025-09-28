<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Utils\DataUtils;

/**
 * Content Display Class
 *
 * Handles the display of social share buttons in WordPress content
 * with configurable positions and styling options.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class ContentDisplay
{
    private Settings $settings;
    private ProfileManager $profileManager;
    private ShareRenderer $shareRenderer;
    private bool $initialized = false;

    public function __construct(Settings $settings, ProfileManager $profileManager, ShareRenderer $shareRenderer)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->shareRenderer = $shareRenderer;

        $this->init();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init(): void
    {
        if ($this->initialized) {
            return;
        }

        // Hook into content display
        add_filter('the_content', [$this, 'addShareButtonsToContent'], 10);

        // Hook into footer for floating buttons
        add_action('wp_footer', [$this, 'addFloatingShareButtons'], 10);

        $this->initialized = true;
    }

    /**
     * Add share buttons to post content based on position settings
     *
     * @param string $content Post content
     * @return string Modified content with share buttons
     */
    public function addShareButtonsToContent(string $content): string
    {
        // Validate context using pure functions
        if (!self::shouldDisplayInContent()) {
            return $content;
        }

        global $post;
        if (!self::isValidPost($post)) {
            return $content;
        }

        // Check exclusions using pure function
        if ($this->isContentExcluded($post)) {
            return $content;
        }

        $positions = $this->settings->get('positions', ['after_post']);
        $shareButtons = $this->renderShareButtons($post);
        
        if (empty($shareButtons)) {
            return $content;
        }

        // Apply positions using pure function
        return self::applyContentPositions($content, $shareButtons, $positions);
    }

    /**
     * Render share buttons for floating display
     */
    public function addFloatingShareButtons(): void
    {
        global $post;

        if (!self::isValidPost($post) || !self::shouldDisplayInContent() || $this->isContentExcluded($post)) {
            return;
        }

        $positions = $this->settings->get('positions', ['after_post']);
        $floatingPositions = self::extractFloatingPositions($positions);
        
        if (empty($floatingPositions)) {
            return;
        }

        $shareButtons = $this->renderShareButtons($post);
        if (empty($shareButtons)) {
            return;
        }

        $style = $this->settings->get('style', 'minimal');
        
        foreach ($floatingPositions as $position) {
            $this->renderFloatingButton($shareButtons, $position, $style);
        }
    }

    /**
     * Enqueue frontend styles when needed
     */
    public function enqueueFrontendStyles(): void
    {
        if (!self::shouldEnqueueStyles()) {
            return;
        }

        wp_enqueue_style(
            'html-social-share-frontend',
            $this->getAssetUrl('frontend.css'),
            [],
            $this->getVersion()
        );

        // Enqueue floating styles if needed
        $positions = $this->settings->get('positions', ['after_post']);
        if (self::hasFloatingPositions($positions)) {
            $this->enqueueFloatingStyles();
        }

        // Progressive enhancement for WeChat
        $this->enqueueWeChatScript();
    }

    // --- Private methods with side effects ---
    
    /**
     * Render share buttons for a specific post
     *
     * @param object $post WordPress post object
     * @return string Rendered share buttons HTML
     */
    private function renderShareButtons(object $post): string
    {
        $enabledNetworks = $this->settings->get('enabled_networks', ['facebook', 'twitter']);
        
        if (empty($enabledNetworks)) {
            return '';
        }

        $shareButtons = [];

        foreach ($enabledNetworks as $network) {
            $profile = $this->getOrCreateProfile($network);
            $shareUrl = self::generateShareUrl($network, $post, $profile);
            $buttonHtml = $this->shareRenderer->render($network, $profile, $shareUrl);
            
            if (!empty($buttonHtml)) {
                $shareButtons[] = $buttonHtml;
            }
        }

        if (empty($shareButtons)) {
            return '';
        }

        return $this->wrapShareButtons($shareButtons);
    }
    
    /**
     * Get profile for network or create default one
     *
     * @param string $network Network identifier
     * @return array Profile configuration
     */
    private function getOrCreateProfile(string $network): array
    {
        $profile = $this->profileManager->getProfile($network);
        
        if (!$profile) {
            $profile = self::createDefaultProfile($network);
        }
        
        return $profile;
    }
    
    /**
     * Wrap share buttons in container with styling
     *
     * @param array $shareButtons Array of button HTML strings
     * @return string Wrapped HTML
     */
    private function wrapShareButtons(array $shareButtons): string
    {
        $title = $this->settings->get('title', 'Share this with your friends');
        $style = $this->settings->get('style', 'minimal');
        $classes = 'hss-share-buttons hss-style-' . esc_attr($style);
        
        $output = sprintf(
            '<div class="%s"><h3 class="hss-title">%s</h3><div class="hss-buttons">%s</div></div>',
            $classes,
            esc_html($title),
            implode('', $shareButtons)
        );

        // Append icon CSS
        $iconCSS = $this->shareRenderer->getIconCSS();
        if (!empty($iconCSS)) {
            $output .= $this->formatIconCSS($iconCSS);
        }

        return $output;
    }
    
    /**
     * Format icon CSS for inline inclusion
     *
     * @param mixed $iconCSS Icon CSS data
     * @return string Formatted CSS
     */
    private function formatIconCSS($iconCSS): string
    {
        if (is_array($iconCSS)) {
            $css = '<style class="hss-iconset-inline">';
            foreach ($iconCSS as $cssClass => $imageUrl) {
                $css .= sprintf(
                    '.%s{background-image:url(%s);background-size:contain;background-repeat:no-repeat;background-position:center;} ',
                    esc_attr($cssClass), 
                    esc_url($imageUrl)
                );
            }
            $css .= '</style>';
            return $css;
        } elseif (is_string($iconCSS)) {
            return $iconCSS;
        }
        
        return '';
    }
    
    /**
     * Check if content should be excluded from share buttons
     *
     * @param object $post WordPress post object
     * @return bool True if excluded
     */
    private function isContentExcluded(object $post): bool
    {
        $exclusions = $this->settings->get('exclusions', [
            'ids' => [], 
            'slugs' => [], 
            'titles' => []
        ]);
        
        return self::isPostExcluded($post, $exclusions);
    }
    
    /**
     * Render floating button for specific position
     *
     * @param string $shareButtons Share buttons HTML
     * @param string $position Position identifier (left/right)
     * @param string $style Style identifier
     */
    private function renderFloatingButton(string $shareButtons, string $position, string $style): void
    {
        $classes = sprintf('hss-share-buttons hss-pos-float-%s hss-style-%s', 
            esc_attr($position), 
            esc_attr($style)
        );
        
        echo sprintf('<div class="%s">%s</div>', $classes, $shareButtons);
    }
    
    /**
     * Enqueue floating-specific styles
     */
    private function enqueueFloatingStyles(): void
    {
        wp_add_inline_style('wp-block-library', self::getFloatingButtonsCSS());
    }
    
    /**
     * Enqueue WeChat enhancement script
     */
    private function enqueueWeChatScript(): void
    {
        wp_enqueue_script(
            'html-social-share-wechat-toggle',
            $this->getAssetUrl('wechat-toggle.js'),
            [],
            $this->getVersion(),
            true
        );
    }
    
    /**
     * Get asset URL with fallback
     *
     * @param string $filename Asset filename
     * @return string Asset URL
     */
    private function getAssetUrl(string $filename): string
    {
        if (defined('HTML_SOCIAL_SHARE_ASSETS_URL')) {
            return HTML_SOCIAL_SHARE_ASSETS_URL . $filename;
        }
        
        return plugin_dir_url(__FILE__) . '../assets/' . $filename;
    }
    
    /**
     * Get plugin version
     *
     * @return string Version string
     */
    private function getVersion(): string
    {
        return defined('HTML_SOCIAL_SHARE_VERSION') ? HTML_SOCIAL_SHARE_VERSION : '1.0.0';
    }

    // --- Pure helper functions ---
    
    /**
     * Check if we should display share buttons in current context
     *
     * @return bool True if should display
     */
    public static function shouldDisplayInContent(): bool
    {
        return is_singular() && in_the_loop() && !is_feed() && !is_preview();
    }
    
    /**
     * Check if we should enqueue frontend styles
     *
     * @return bool True if should enqueue
     */
    public static function shouldEnqueueStyles(): bool
    {
        return !is_admin() && !wp_is_json_request() && !is_feed();
    }
    
    /**
     * Validate WordPress post object
     *
     * @param mixed $post Post object to validate
     * @return bool True if valid
     */
    public static function isValidPost($post): bool
    {
        return is_object($post) && 
               isset($post->ID) && 
               is_numeric($post->ID) && 
               $post->ID > 0 &&
               isset($post->post_status) &&
               $post->post_status === 'publish';
    }
    
    /**
     * Apply content positions to wrap content with share buttons
     *
     * @param string $content Original content
     * @param string $shareButtons Share buttons HTML
     * @param array $positions Position configuration
     * @return string Modified content
     */
    public static function applyContentPositions(string $content, string $shareButtons, array $positions): string
    {
        if (empty($shareButtons) || empty($positions)) {
            return $content;
        }
        
        if (in_array('before_post', $positions)) {
            $content = $shareButtons . $content;
        }
        
        if (in_array('after_post', $positions)) {
            $content .= $shareButtons;
        }
        
        return $content;
    }
    
    /**
     * Extract floating positions from position array
     *
     * @param array $positions All positions
     * @return array Floating positions only
     */
    public static function extractFloatingPositions(array $positions): array
    {
        return array_intersect($positions, ['left', 'right']);
    }
    
    /**
     * Check if positions include floating options
     *
     * @param array $positions Position configuration
     * @return bool True if has floating positions
     */
    public static function hasFloatingPositions(array $positions): bool
    {
        return !empty(self::extractFloatingPositions($positions));
    }
    
    /**
     * Check if post should be excluded based on exclusion rules
     *
     * @param object $post WordPress post object
     * @param array $exclusions Exclusion configuration
     * @return bool True if excluded
     */
    public static function isPostExcluded(object $post, array $exclusions): bool
    {
        // Check post ID exclusions
        if (isset($exclusions['ids']) && in_array($post->ID, (array)$exclusions['ids'])) {
            return true;
        }
        
        // Check slug exclusions
        if (isset($exclusions['slugs']) && isset($post->post_name)) {
            if (in_array($post->post_name, (array)$exclusions['slugs'])) {
                return true;
            }
        }
        
        // Check title exclusions
        if (isset($exclusions['titles']) && isset($post->post_title)) {
            if (in_array($post->post_title, (array)$exclusions['titles'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Create default profile for network
     *
     * @param string $network Network identifier
     * @return array Default profile configuration
     */
    public static function createDefaultProfile(string $network): array
    {
        return [
            'id' => $network,
            'type' => 'share',
            'label' => ucfirst($network),
            'handle' => $network,
            'url_template' => self::getDefaultUrlTemplate($network),
            'visible' => true,
            'new_tab' => true,
            'order' => 0,
            'icon' => ['source' => 'builtin', 'ref' => $network],
            'meta' => []
        ];
    }
    
    /**
     * Get default URL template for network
     *
     * @param string $network Network identifier
     * @return string URL template
     */
    public static function getDefaultUrlTemplate(string $network): string
    {
        $templates = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u={url}&t={title}',
            'twitter' => 'https://x.com/intent/tweet?url={url}&text={title}',
            'x' => 'https://x.com/intent/tweet?url={url}&text={title}',
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
            'pinterest' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
            'email' => 'mailto:?subject={title}&body={url}',
            'whatsapp' => 'https://wa.me/?text={title}%20{url}',
            'telegram' => 'https://t.me/share/url?url={url}&text={title}',
            'reddit' => 'https://reddit.com/submit?url={url}&title={title}',
            'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}',
            'wechat' => '#wechat-qr',
            'mastodon' => 'https://mastodon.social/share?text={title}%20{url}',
            'bluesky' => 'https://bsky.app/intent/compose?text={title}%20{url}',
            'threads' => 'https://threads.net/intent/post?text={title}%20{url}',
            'vk' => 'https://vk.com/share.php?url={url}&title={title}'
        ];
        
        return $templates[$network] ?? 'https://example.com/share?url={url}&title={title}';
    }
    
    /**
     * Generate share URL for network and post
     *
     * @param string $network Network identifier
     * @param object $post WordPress post object
     * @param array $profile Network profile
     * @return string Generated share URL
     */
    public static function generateShareUrl(string $network, object $post, array $profile): string
    {
        $template = $profile['url_template'] ?? self::getDefaultUrlTemplate($network);
        
        $replacements = [
            '{url}' => urlencode(get_permalink($post)),
            '{title}' => urlencode(get_the_title($post)),
            '{text}' => urlencode(get_the_title($post)),
            '{handle}' => urlencode($profile['handle'] ?? ''),
            '{excerpt}' => urlencode(wp_trim_words(get_the_excerpt($post), 20)),
            '{site_name}' => urlencode(get_bloginfo('name')),
            '{author}' => urlencode(get_the_author_meta('display_name', $post->post_author ?? 0))
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    /**
     * Get CSS for floating buttons
     *
     * @return string CSS styles
     */
    public static function getFloatingButtonsCSS(): string
    {
        return '
            .hss-pos-float-left, .hss-pos-float-right {
                position: fixed;
                top: 50%;
                transform: translateY(-50%);
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin: 0;
                padding: 12px;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                border: 1px solid #e0e0e0;
                backdrop-filter: blur(5px);
            }
            .hss-pos-float-left {
                left: 20px;
            }
            .hss-pos-float-right {
                right: 20px;
            }
            .hss-pos-float-left .hss-title, 
            .hss-pos-float-right .hss-title {
                font-size: 14px;
                margin: 0 0 8px 0;
                text-align: center;
            }
            .hss-pos-float-left .hss-buttons, 
            .hss-pos-float-right .hss-buttons {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .hss-pos-float-left .hss-button, 
            .hss-pos-float-right .hss-button {
                display: block;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                text-align: center;
                line-height: 40px;
                text-decoration: none;
                transition: all 0.2s ease;
            }
            .hss-pos-float-left .hss-button:hover, 
            .hss-pos-float-right .hss-button:hover {
                transform: scale(1.1);
            }
            @media (max-width: 768px) {
                .hss-pos-float-left, .hss-pos-float-right {
                    display: none;
                }
            }
        ';
    }
}