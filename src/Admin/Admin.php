<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\SettingsInterface;
use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\ShareRendererInterface;
use HtmlSocialShare\IconRegistryInterface;
use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Renderers\RenderUtils;

/**
 * WordPress Admin interface with enhanced security
 *
 * Handles admin menu registration, page rendering, and AJAX operations
 * with proper CSRF protection, input validation, and output escaping.
 *
 * @since 3.0.0
 */
class Admin
{
    private $settings;
    private $profileManager;
    private $reactAdminInterface;
    private $shareRenderer;
    private $iconRegistry;

    public function __construct(
        SettingsInterface $settings,
        ProfileManagerInterface $profileManager,
        ShareRendererInterface $shareRenderer,
        IconRegistryInterface $iconRegistry
    ) {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->shareRenderer = $shareRenderer;
        $this->iconRegistry = $iconRegistry;

        try {
            $this->reactAdminInterface = new ReactAdminInterface($settings, $iconRegistry);
        } catch (\Throwable $e) {
            error_log('HTML Social Share: Admin initialization error - ' . $e->getMessage());
            return;
        }

        $this->initializeHooks();
    }

    private function initializeHooks()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_ajax_hssb_live_preview', [$this, 'handleLivePreview']);
        add_action('admin_notices', [$this, 'showAdminNotices']);

        // Initialize React admin interface scripts
        add_action('admin_enqueue_scripts', [$this->reactAdminInterface, 'enqueueScripts']);
    }

    public function addAdminMenu()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        try {
            add_menu_page(
                __('HTML Social Share', 'html-social-share'),
                __('Html Social Share', 'html-social-share'),
                'manage_options',
                'html-social-share-react',
                [$this->reactAdminInterface, 'renderAdminPage'],
                'dashicons-share',
                59.679861
            );

        } catch (\Throwable $e) {
            error_log('HTML Social Share: Admin menu error - ' . $e->getMessage());
        }
    }

    public function handleLivePreview()
    {
        try {
            if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'hssb_live_preview')) {
                wp_send_json_error(['message' => __('Security check failed', 'html-social-share')]);
                return;
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => __('Insufficient permissions', 'html-social-share')]);
                return;
            }

            $enabledNetworks = array_map([SecurityUtils::class, 'sanitizeKey'], $_POST['enabled_networks'] ?? []);
            $iconset = SecurityUtils::sanitizeKey($_POST['iconset'] ?? 'default');

            $previewHtml = $this->generatePreviewHtml($enabledNetworks, $iconset);

            wp_send_json_success(['html' => $previewHtml]);

        } catch (\Throwable $e) {
            error_log('HTML Social Share: Live preview error - ' . $e->getMessage());
            wp_send_json_error(['message' => __('Preview generation failed', 'html-social-share')]);
        }
    }

    public function showAdminNotices()
    {
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'html-social-share') === false) {
            return;
        }

        $notices = get_transient('hssb_admin_notices');
        if ($notices && is_array($notices)) {
            foreach ($notices as $notice) {
                $type = SecurityUtils::sanitizeKey($notice['type'] ?? 'info');
                $message = SecurityUtils::escapeHtml($notice['message'] ?? '');
                echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $message . '</p></div>';
            }
            delete_transient('hssb_admin_notices');
        }
    }

    public function enqueueAdminAssets($hook)
    {
        if (strpos($hook, 'html-social-share') === false) {
            return;
        }

        try {
            // Load asset data first so we can use the same version for CSS and JS
            $asset_file = HTML_SOCIAL_SHARE_BUILD_DIR . 'admin.asset.php';
            $asset_data = file_exists($asset_file) ? include $asset_file : [
                'dependencies' => ['jquery'],
                'version' => '3.0.0'
            ];

            // Load admin CSS from build directory (admin-specific stylesheet)
            wp_enqueue_style(
                'html-social-share-admin',
                HTML_SOCIAL_SHARE_BUILD_URL . 'admin.css',
                [],
                $asset_data['version']
            );

            // Load admin JS from build directory with proper asset file handling
            wp_enqueue_script(
                'html-social-share-admin',
                HTML_SOCIAL_SHARE_BUILD_URL . 'admin.js',
                $asset_data['dependencies'],
                $asset_data['version'],
                true
            );

            wp_localize_script('html-social-share-admin', 'hssbAdmin', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('hssb_live_preview'),
                'strings' => [
                    'copied' => __('Copied!', 'html-social-share'),
                    'error' => __('Error occurred', 'html-social-share'),
                ]
            ]);

        } catch (\Throwable $e) {
            error_log('HTML Social Share: Asset enqueue error - ' . $e->getMessage());
        }
    }

    // Helper methods

    private function renderShortcodeGeneratorInterface(string $generatedShortcode, array $errors)
    {
        echo '<div class="wrap hssb-shortcode-generator">';
        echo '<h1><span class="dashicons dashicons-shortcode"></span> ' . SecurityUtils::escapeHtml(__('Shortcode Generator', 'html-social-share')) . '</h1>';

        if (!empty($errors)) {
            echo '<div class="notice notice-error"><ul>';
            foreach ($errors as $error) {
                echo '<li>' . SecurityUtils::escapeHtml($error) . '</li>';
            }
            echo '</ul></div>';
        }

        echo '<form method="post">';
        wp_nonce_field('html_social_share_shortcode');

        echo '<table class="form-table">';
        echo '<tr><th>' . __('Networks', 'html-social-share') . '</th><td>';

        $networks = $this->getAvailableNetworks();
        foreach ($networks as $key => $network) {
            $checked = in_array($key, $_POST['networks'] ?? []) ? 'checked' : '';
            echo '<label><input type="checkbox" name="networks[]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($network['label']) . '</label><br>';
        }

        echo '</td></tr>';
        echo '<tr><th>' . __('Style', 'html-social-share') . '</th><td>';
        echo '<select name="style">';

        $styles = $this->getAvailableStyles();
        foreach ($styles as $value => $label) {
            $selected = ($_POST['style'] ?? 'default') === $value ? 'selected' : '';
            echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
        }

        echo '</select>';
        echo '</td></tr></table>';

        echo '<button type="submit" name="generate" class="button button-primary">' . __('Generate Shortcode', 'html-social-share') . '</button>';
        echo '</form>';

        if (!empty($generatedShortcode)) {
            echo '<h3>' . __('Generated Shortcode', 'html-social-share') . '</h3>';
            echo '<code>' . esc_html($generatedShortcode) . '</code>';
        }

        echo '</div>';
    }

    private function validateShortcodeRequest(array $postData): array
    {
        $errors = [];

        if (!wp_verify_nonce($postData['_wpnonce'] ?? '', 'html_social_share_shortcode')) {
            $errors[] = __('Security check failed', 'html-social-share');
            return ['valid' => false, 'errors' => $errors];
        }

        $networks = $postData['networks'] ?? [];
        if (empty($networks)) {
            $errors[] = __('Please select at least one network', 'html-social-share');
        }

        $style = SecurityUtils::sanitizeKey($postData['style'] ?? 'default');

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => [
                'networks' => array_map([SecurityUtils::class, 'sanitizeKey'], (array)$networks),
                'style' => $style,
            ]
        ];
    }

    private function generateShortcode(array $data): string
    {
        $shortcode = '[html_social_share';

        if (!empty($data['networks'])) {
            $shortcode .= ' networks="' . implode(',', $data['networks']) . '"';
        }

        if ($data['style'] !== 'default') {
            $shortcode .= ' style="' . $data['style'] . '"';
        }

        $shortcode .= ']';

        return $shortcode;
    }

    private function generatePreviewHtml(array $networks, string $iconset): string
    {
        ob_start();
        echo '<div class="hssb-preview">';

        if (empty($networks)) {
            echo '<p>' . __('No networks selected', 'html-social-share') . '</p>';
        } else {
            foreach ($networks as $network) {
                echo '<span class="button">' . ucfirst($network) . '</span> ';
            }
        }

        echo '</div>';
        return ob_get_clean();
    }

    private function renderErrorPage(string $title, \Throwable $e)
    {
        echo '<div class="wrap">';
        echo '<h1>' . SecurityUtils::escapeHtml($title) . '</h1>';
        echo '<div class="notice notice-error"><p>' . SecurityUtils::escapeHtml(__('An error occurred.', 'html-social-share')) . '</p></div>';
        echo '</div>';
    }

    private function getAvailableNetworks(): array
    {
        return [
            'facebook' => ['label' => __('Facebook', 'html-social-share'), 'color' => '#1877f2'],
            'twitter' => ['label' => __('Twitter', 'html-social-share'), 'color' => '#1da1f2'],
            'linkedin' => ['label' => __('LinkedIn', 'html-social-share'), 'color' => '#0077b5'],
            'pinterest' => ['label' => __('Pinterest', 'html-social-share'), 'color' => '#e60023'],
            'email' => ['label' => __('Email', 'html-social-share'), 'color' => '#666666'],
        ];
    }

    private function getAvailableStyles(): array
    {
        return [
            'default' => __('Default', 'html-social-share'),
            'square' => __('Square', 'html-social-share'),
            'circle' => __('Circle', 'html-social-share'),
            'minimal' => __('Minimal', 'html-social-share'),
        ];
    }
}