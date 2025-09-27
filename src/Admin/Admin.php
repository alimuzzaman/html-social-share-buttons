<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\SettingsInterface;
use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\ShareRendererInterface;

class Admin
{
    private $settings;
    private $settingsPage;
    private $profileManager;

    public function __construct(SettingsInterface $settings, ProfileManagerInterface $profileManager, ShareRendererInterface $shareRenderer)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->settingsPage = new SettingsPage($settings, $shareRenderer);

        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function addAdminMenu()
    {
        // Add main menu
        add_menu_page(
            'HTML Social Share', // Page title
            'Social Share', // Menu title
            'manage_options', // Capability
            'html-social-share', // Menu slug
            [$this, 'renderSettingsPage'], // Callback
            'dashicons-share', // Icon
            30 // Position
        );

        // Add submenu for settings (same as main)
        add_submenu_page(
            'html-social-share', // Parent slug
            'Settings', // Page title
            'Settings', // Menu title
            'manage_options', // Capability
            'html-social-share', // Menu slug (same as parent)
            [$this, 'renderSettingsPage'] // Callback
        );

        // Add submenu for profiles
        add_submenu_page(
            'html-social-share', // Parent slug
            'Social Profiles', // Page title
            'Profiles', // Menu title
            'manage_options', // Capability
            'html-social-share-profiles', // Menu slug
            [$this, 'renderProfilesPage'] // Callback
        );

        // Add submenu for shortcode generator
        add_submenu_page(
            'html-social-share', // Parent slug
            'Shortcode Generator', // Page title
            'Shortcode Generator', // Menu title
            'manage_options', // Capability
            'html-social-share-shortcode', // Menu slug
            [$this, 'renderShortcodePage'] // Callback
        );
    }

    public function renderSettingsPage()
    {
        $this->settingsPage->render();
    }

    public function renderProfilesPage()
    {
        echo '<div class="wrap">';
        echo '<h1>Social Profiles</h1>';
        echo '<p>Profiles page coming soon...</p>';
        echo '</div>';
    }

    public function renderShortcodePage()
    {
        $generated_shortcode = '';

        if (isset($_POST['generate']) && wp_verify_nonce($_POST['_wpnonce'], 'html_social_share_shortcode')) {
            $networks = isset($_POST['networks']) ? array_map('sanitize_text_field', $_POST['networks']) : [];
            $style = sanitize_text_field($_POST['style'] ?? 'default');

            // Validate networks
            $valid_networks = ['facebook', 'twitter', 'linkedin', 'pinterest', 'email'];
            $networks = array_intersect($networks, $valid_networks);

            // Validate style
            $valid_styles = ['default', 'square', 'circle', 'minimal'];
            if (!in_array($style, $valid_styles)) {
                $style = 'default';
            }

            if (!empty($networks)) {
                $shortcode = '[html_social_share';
                if ($style !== 'default') {
                    $shortcode .= ' style="' . esc_attr($style) . '"';
                }
                $shortcode .= ' networks="' . esc_attr(implode(',', $networks)) . '"';
                $shortcode .= ']';
                $generated_shortcode = $shortcode;
            }
        }

        echo '<div class="wrap">';
        echo '<h1>Shortcode Generator</h1>';
        echo '<p>Generate shortcodes for social share buttons.</p>';

        // Documentation
        echo '<div class="card" style="margin-bottom: 20px;">';
        echo '<h2>How to Use</h2>';
        echo '<p>Use the form below to generate a shortcode, then copy and paste it into your posts or pages.</p>';
        echo '<h3>Parameters</h3>';
        echo '<ul>';
        echo '<li><code>networks</code>: Comma-separated list of networks (facebook,twitter,linkedin,pinterest,email)</li>';
        echo '<li><code>style</code>: Button style (default, square, circle, minimal)</li>';
        echo '</ul>';
        echo '<h3>Example</h3>';
        echo '<code>[html_social_share networks="facebook,twitter" style="square"]</code>';
        echo '</div>';

        if (!empty($generated_shortcode)) {
            echo '<div class="notice notice-success">';
            echo '<p><strong>Generated Shortcode:</strong></p>';
            echo '<div style="display: flex; align-items: center; gap: 10px;">';
            echo '<code id="generated-shortcode" style="flex: 1;">' . esc_html($generated_shortcode) . '</code>';
            echo '<button type="button" id="copy-shortcode" class="button" onclick="copyToClipboard()">Copy</button>';
            echo '</div>';
            echo '<p><strong>Preview:</strong></p>';
            echo '<div class="hssb-shortcode-preview" style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">';
            // Simple preview
            foreach ($networks as $network) {
                echo '<a href="#" class="hssb-share hssb-' . esc_attr($network) . '" style="margin-right: 5px; padding: 5px; background: #007cba; color: white; text-decoration: none;">' . esc_html(ucfirst($network)) . '</a>';
            }
            echo '</div>';
            echo '</div>';
        }

        echo '<form method="post" action="">';
        wp_nonce_field('html_social_share_shortcode');

        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th scope="row">Select Networks</th>';
        echo '<td>';
        $networks = ['facebook', 'twitter', 'linkedin', 'pinterest', 'email'];
        foreach ($networks as $network) {
            echo '<label>';
            echo '<input type="checkbox" name="networks[]" value="' . esc_attr($network) . '" checked> ' . esc_html(ucfirst($network));
            echo '</label><br>';
        }
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th scope="row">Style</th>';
        echo '<td>';
        echo '<select name="style">';
        echo '<option value="default">Default</option>';
        echo '<option value="square">Square</option>';
        echo '<option value="circle">Circle</option>';
        echo '<option value="minimal">Minimal</option>';
        echo '</select>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        echo '<p><input type="submit" name="generate" class="button button-primary" value="Generate Shortcode"></p>';
        echo '</form>';

        echo '<script>
        function copyToClipboard() {
            const shortcode = document.getElementById("generated-shortcode");
            const text = shortcode.textContent;
            navigator.clipboard.writeText(text).then(function() {
                const button = document.getElementById("copy-shortcode");
                const originalText = button.textContent;
                button.textContent = "Copied!";
                button.classList.add("button-primary");
                setTimeout(function() {
                    button.textContent = originalText;
                    button.classList.remove("button-primary");
                }, 2000);
            });
        }
        </script>';

        echo '</div>';
    }

    public function enqueueAdminAssets($hook)
    {
        // Only load on our admin pages
        if (strpos($hook, 'html-social-share') !== false) {
            wp_enqueue_style(
                'html-social-share-admin',
                plugins_url('assets/admin.css', dirname(__DIR__, 2) . '/html-social-share.php'),
                [],
                '1.0.0'
            );
        }
    }

    private function renderIconPicker()
    {
        // Simple icon picker - in real implementation, this would be more sophisticated
        $icons = ['facebook', 'twitter', 'linkedin', 'pinterest', 'email'];
        $output = '<select id="profile_icon" name="icon" aria-describedby="icon_desc">';
        $output .= '<option value="">Select Icon</option>';
        foreach ($icons as $icon) {
            $output .= '<option value="' . esc_attr($icon) . '">' . esc_html(ucfirst($icon)) . '</option>';
        }
        $output .= '</select>';
        $output .= '<p id="icon_desc" class="description">Choose an icon for this profile</p>';
        return $output;
    }

    private function handleProfileAction()
    {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'html_social_share_profiles')) {
            return;
        }

        $action = $_POST['action'];

        if ($action === 'add') {
            $data = [
                'network' => sanitize_text_field($_POST['network']),
                'handle' => sanitize_text_field($_POST['handle']),
                'url' => esc_url_raw($_POST['url']),
                'icon' => sanitize_text_field($_POST['icon'])
            ];
            $this->profileManager->createProfile($data);
        } elseif ($action === 'delete' && isset($_POST['profile_id'])) {
            $this->profileManager->deleteProfile((int)$_POST['profile_id']);
        }
    }
}