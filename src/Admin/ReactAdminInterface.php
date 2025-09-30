<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\SettingsInterface;

/**
 * React-based admin interface for HTML Social Share Buttons
 *
 * Provides a modern React admin interface using WordPress REST API
 * for managing plugin settings.
 *
 * @package HtmlSocialShare\Admin
 * @since 3.1.0
 */
class ReactAdminInterface
{
    private SettingsInterface $settings;

    /** @var string Page slug for the admin page */
    private const PAGE_SLUG = 'html-social-share-react';

    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Initialize admin interface hooks
     */
    public function init(): void
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Add admin menu item
     */
    public function addAdminMenu(): void
    {
        add_submenu_page(
            'options-general.php',
            __('Social Share Manager', 'html-social-share-buttons'),
            __('Social Share Settings', 'html-social-share-buttons'),
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderAdminPage']
        );
    }

    /**
     * Enqueue scripts and styles for React admin
     */
    public function enqueueScripts(string $hookSuffix): void
    {
        // Only load on our admin page
        if (!$this->isOurAdminPage($hookSuffix)) {
            return;
        }

        $assetFile = $this->getAssetFile();
        $version = $assetFile['version'] ?? '1.0.0';
        $dependencies = $assetFile['dependencies'] ?? [];

        // Ensure WordPress dependencies are available
        $dependencies = array_merge($dependencies, [
            'react',
            'react-dom',
            'wp-api-fetch',
            'wp-components',
            'wp-element',
            'wp-i18n',
            'wp-icons',
        ]);

        // Enqueue the React app
        wp_enqueue_script(
            'hss-admin-react',
            $this->getAssetUrl('admin.js'),
            $dependencies,
            $version,
            true
        );

        // Enqueue styles
        wp_enqueue_style(
            'hss-admin-react-style',
            $this->getAssetUrl('admin.css'),
            ['wp-components'],
            $version
        );

        // Localize script with API configuration
        wp_localize_script('hss-admin-react', 'hssAdminConfig', [
            'restUrl' => rest_url('html-social-share/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'currentUser' => wp_get_current_user()->ID,
            'pluginUrl' => plugin_dir_url(__FILE__),
            'adminUrl' => admin_url(),
            'strings' => [
                'loadingPosts' => __('Loading posts...', 'html-social-share-buttons'),
                'noPostsFound' => __('No posts found.', 'html-social-share-buttons'),
                'refreshSuccess' => __('Settings refreshed successfully.', 'html-social-share-buttons'),
                'refreshError' => __('Error refreshing settings.', 'html-social-share-buttons'),
                'deleteConfirm' => __('Are you sure you want to delete this data?', 'html-social-share-buttons'),
            ]
        ]);

        // Set up REST API authentication
        wp_localize_script('hss-admin-react', 'wpApiSettings', [
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }

    /**
     * Render the admin page with React root
     */
    public function renderAdminPage(): void
    {
        ?>
        <div class="wrap">
            <div id="hss-admin-react-root">
                <!-- React app will mount here -->
                <div class="hss-loading-placeholder">
                    <h1><?php echo esc_html__('Social Share Settings Manager', 'html-social-share-buttons'); ?></h1>
                    <p><?php echo esc_html__('Loading admin interface...', 'html-social-share-buttons'); ?></p>
                    <div class="spinner is-active" style="float: none; margin: 20px auto;"></div>
                </div>
            </div>
        </div>

        <style>
            .hss-loading-placeholder {
                text-align: center;
                padding: 40px 20px;
                color: #646970;
            }

            .hss-loading-placeholder h1 {
                margin-bottom: 10px;
            }

            .hss-loading-placeholder p {
                margin-bottom: 20px;
                font-style: italic;
            }
        </style>
        <?php
    }

    /**
     * Check if we're on our admin page
     */
    private function isOurAdminPage(string $hookSuffix): bool
    {
        return str_contains($hookSuffix, self::PAGE_SLUG);
    }

    /**
     * Get asset file with dependencies and version
     */
    private function getAssetFile(): array
    {
        $assetFilePath = plugin_dir_path(__FILE__) . '../../build/admin.asset.php';

        if (file_exists($assetFilePath)) {
            return include $assetFilePath;
        }

        return [
            'dependencies' => [],
            'version' => '1.0.0',
        ];
    }

    /**
     * Get URL for asset file
     */
    private function getAssetUrl(string $filename): string
    {
        return plugin_dir_url(__FILE__) . '../../build/' . $filename;
    }

    /**
     * Get recent posts for the interface
     */
    public function getRecentPosts(int $limit = 20): array
    {
        $posts = get_posts([
            'numberposts' => $limit,
            'post_status' => 'publish',
            'post_type' => ['post', 'page'],
            'fields' => 'ids',
        ]);

        return array_map('intval', $posts);
    }

    /**
     * AJAX handler to get posts with plugin data
     */
    public function ajaxGetPostsWithCounts(): void
    {
        check_ajax_referer('hss_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'html-social-share-buttons'), 403);
        }

        $page = absint($_POST['page'] ?? 1);
        $perPage = absint($_POST['per_page'] ?? 20);
        $search = sanitize_text_field($_POST['search'] ?? '');

        $args = [
            'post_type' => ['post', 'page'],
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'paged' => $page,
            'meta_query' => [
                [
                    'key' => '_hss_has_share_counts',
                    'compare' => 'EXISTS',
                ],
            ],
        ];

        if (!empty($search)) {
            $args['s'] = $search;
        }

        $query = new \WP_Query($args);
        $posts = [];

        foreach ($query->posts as $post) {
            $posts[] = [
                'id' => $post->ID,
                'title' => get_the_title($post->ID),
                'url' => get_permalink($post->ID),
                'date' => get_the_date('c', $post->ID),
                'type' => $post->post_type,
            ];
        }

        wp_send_json_success([
            'posts' => $posts,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ]);
    }
}