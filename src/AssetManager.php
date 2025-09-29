<?php
/**
 * Asset Manager for HTML Social Share Buttons
 *
 * Handles enqueueing of compiled CSS and JavaScript assets
 *
 * @package HTMLSocialShare
 * @since 1.1.0
 */

namespace HTMLSocialShare;

class AssetManager {

    private $plugin_url;
    private $plugin_version;
    private $dist_path;

    public function __construct($plugin_url, $plugin_version) {
        $this->plugin_url = $plugin_url;
        $this->plugin_version = $plugin_version;
        $this->dist_path = $plugin_url . 'dist/';

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
    }

    /**
     * Enqueue admin assets for the plugin settings page
     */
    public function enqueue_admin_assets($hook_suffix) {
        // Only load on our admin pages
        if (!$this->is_plugin_admin_page($hook_suffix)) {
            return;
        }

        // React admin interface
        wp_enqueue_script(
            'html-social-share-admin',
            $this->dist_path . 'admin-ui.js',
            ['wp-element', 'wp-api-fetch', 'wp-i18n'],
            $this->plugin_version,
            true
        );

        wp_enqueue_style(
            'html-social-share-admin',
            $this->dist_path . 'admin-ui.css',
            [],
            $this->plugin_version
        );

        // Localize script with admin data
        wp_localize_script('html-social-share-admin', 'htmlSocialShareAdmin', [
            'apiUrl' => rest_url('html-social-share/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'currentUser' => wp_get_current_user_id(),
            'pluginUrl' => $this->plugin_url,
            'isDevMode' => defined('WP_DEBUG') && WP_DEBUG,
            'availableNetworks' => $this->get_available_networks(),
            'defaultSettings' => $this->get_default_settings(),
        ]);

        // WordPress admin styles for consistency
        wp_enqueue_style('wp-components');
        wp_enqueue_style('wp-admin');

        // FontAwesome for icons
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            [],
            '6.4.0'
        );
    }

    /**
     * Enqueue frontend assets for share buttons
     */
    public function enqueue_frontend_assets() {
        // Only enqueue if share buttons are being displayed
        if (!$this->should_load_frontend_assets()) {
            return;
        }

        wp_enqueue_script(
            'html-social-share-frontend',
            $this->dist_path . 'frontend.js',
            [],
            $this->plugin_version,
            true
        );

        wp_enqueue_style(
            'html-social-share-frontend',
            $this->dist_path . 'frontend.css',
            [],
            $this->plugin_version
        );

        // Localize frontend script
        wp_localize_script('html-social-share-frontend', 'htmlSocialShare', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('html_social_share_nonce'),
            'currentUrl' => get_permalink(),
            'currentTitle' => get_the_title(),
            'currentDescription' => $this->get_current_description(),
            'shareCount' => $this->is_share_count_enabled(),
            'analytics' => $this->get_analytics_config(),
        ]);

        // FontAwesome for social icons
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            [],
            '6.4.0'
        );
    }

    /**
     * Enqueue block editor assets for Gutenberg blocks
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'html-social-share-blocks',
            $this->dist_path . 'blocks/html-social-share/index.js',
            [
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-data',
                'wp-block-editor',
                'wp-api-fetch',
            ],
            $this->plugin_version,
            true
        );

        wp_enqueue_style(
            'html-social-share-blocks-editor',
            $this->dist_path . 'blocks/html-social-share/index.css',
            ['wp-edit-blocks'],
            $this->plugin_version
        );

        // Localize block editor script
        wp_localize_script('html-social-share-blocks', 'htmlSocialShareBlocks', [
            'apiUrl' => rest_url('html-social-share/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'availableNetworks' => $this->get_available_networks(),
            'availableProfiles' => $this->get_available_profiles(),
            'iconBaseUrl' => $this->plugin_url . 'assets/iconset/',
        ]);
    }

    /**
     * Check if we're on a plugin admin page
     */
    private function is_plugin_admin_page($hook_suffix) {
        $plugin_pages = [
            'toplevel_page_html-social-share',
            'html-social-share_page_html-social-share-networks',
            'html-social-share_page_html-social-share-profiles',
            'html-social-share_page_html-social-share-advanced',
        ];

        return in_array($hook_suffix, $plugin_pages);
    }

    /**
     * Determine if frontend assets should be loaded
     */
    private function should_load_frontend_assets() {
        global $post;

        // Don't load in admin
        if (is_admin()) {
            return false;
        }

        // Check if share buttons are enabled globally
        $settings = get_option('html_social_share_settings', []);
        if (empty($settings['enabled']) || !$settings['enabled']) {
            return false;
        }

        // Check if current post type is enabled
        if (is_singular()) {
            $enabled_post_types = $settings['post_types'] ?? ['post', 'page'];
            if (!in_array(get_post_type(), $enabled_post_types)) {
                return false;
            }
        }

        // Check for blocks that might contain share buttons
        if ($post && has_blocks($post->post_content)) {
            if (has_block('html-social-share/social-share-buttons', $post)) {
                return true;
            }
        }

        // Check if shortcode is present
        if ($post && has_shortcode($post->post_content, 'html_social_share')) {
            return true;
        }

        // Check if widget is active
        if (is_active_widget(false, false, 'html_social_share_widget')) {
            return true;
        }

        return true; // Default to loading if settings indicate it should be enabled
    }

    /**
     * Get available social networks
     */
    private function get_available_networks() {
        return [
            'facebook' => [
                'name' => 'Facebook',
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877f2',
                'share_url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
            ],
            'twitter' => [
                'name' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'color' => '#1da1f2',
                'share_url' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
            ],
            'linkedin' => [
                'name' => 'LinkedIn',
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0077b5',
                'share_url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
            ],
            'pinterest' => [
                'name' => 'Pinterest',
                'icon' => 'fab fa-pinterest-p',
                'color' => '#bd081c',
                'share_url' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
            ],
            'reddit' => [
                'name' => 'Reddit',
                'icon' => 'fab fa-reddit-alien',
                'color' => '#ff4500',
                'share_url' => 'https://reddit.com/submit?url={url}&title={title}',
            ],
            'whatsapp' => [
                'name' => 'WhatsApp',
                'icon' => 'fab fa-whatsapp',
                'color' => '#25d366',
                'share_url' => 'https://wa.me/?text={title}%20{url}',
            ],
            'telegram' => [
                'name' => 'Telegram',
                'icon' => 'fab fa-telegram-plane',
                'color' => '#0088cc',
                'share_url' => 'https://t.me/share/url?url={url}&text={title}',
            ],
            'email' => [
                'name' => 'Email',
                'icon' => 'fas fa-envelope',
                'color' => '#666666',
                'share_url' => 'mailto:?subject={title}&body={url}',
            ],
        ];
    }

    /**
     * Get default plugin settings
     */
    private function get_default_settings() {
        return [
            'enabled' => true,
            'post_types' => ['post', 'page'],
            'position' => 'after_content',
            'style' => 'default',
            'size' => 'medium',
            'show_labels' => false,
            'icon_only' => true,
        ];
    }

    /**
     * Get available profiles for block editor
     */
    private function get_available_profiles() {
        $profiles = get_option('html_social_share_profiles', []);
        $formatted_profiles = [];

        foreach ($profiles as $id => $profile) {
            $formatted_profiles[] = [
                'id' => $id,
                'name' => $profile['name'] ?? 'Unnamed Profile',
                'networks' => $profile['networks'] ?? [],
                'display_settings' => $profile['display_settings'] ?? [],
            ];
        }

        return $formatted_profiles;
    }

    /**
     * Get current page description for sharing
     */
    private function get_current_description() {
        global $post;

        if (is_singular() && $post) {
            $excerpt = wp_trim_words($post->post_excerpt ?: $post->post_content, 20);
            return wp_strip_all_tags($excerpt);
        }

        return get_bloginfo('description');
    }

    /**
     * Check if share count is enabled
     */
    private function is_share_count_enabled() {
        $settings = get_option('html_social_share_settings', []);
        return !empty($settings['show_share_count']);
    }

    /**
     * Get analytics configuration
     */
    private function get_analytics_config() {
        $settings = get_option('html_social_share_settings', []);

        return [
            'enabled' => !empty($settings['analytics_enabled']),
            'ga_tracking' => !empty($settings['ga_tracking']),
            'facebook_pixel' => !empty($settings['facebook_pixel']),
        ];
    }
}