<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\SettingsInterface;
use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\ShareRendererInterface;

class Admin
{
    private $settings;
    private $settingsPage;
    private $profilesPage;
    private $profileManager;

    public function __construct(SettingsInterface $settings, ProfileManagerInterface $profileManager, ShareRendererInterface $shareRenderer)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->settingsPage = new SettingsPage($settings, $shareRenderer);
        $this->profilesPage = new ProfilesPage($profileManager);

        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_ajax_hssb_live_preview', [$this, 'handleLivePreview']);
    }

    public function addAdminMenu()
    {
        // Add main menu
        add_menu_page(
            'HTML Social Share', // Page title
            'Html Social Share', // Menu title
            'manage_options', // Capability
            'html-social-share', // Menu slug
            [$this, 'renderSettingsPage'], // Callback
            'dashicons-share', // Icon
            59.679861 // Position
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
        $this->profilesPage->render();
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

        echo '<div class="wrap hssb-shortcode-generator">';
        echo '<h1><span class="dashicons dashicons-shortcode"></span> Shortcode Generator</h1>';
        echo '<p class="description">Create custom shortcodes for your social share buttons with an easy-to-use interface.</p>';

        // Quick Start Guide
        echo '<div class="hssb-quick-guide card">';
        echo '<h3><span class="dashicons dashicons-lightbulb"></span> Quick Start</h3>';
        echo '<ol>';
        echo '<li>Select the social networks you want to include</li>';
        echo '<li>Choose a button style from the dropdown</li>';
        echo '<li>Click "Generate Shortcode" to create your code</li>';
        echo '<li>Copy the shortcode and paste it in your posts or pages</li>';
        echo '</ol>';
        echo '</div>';

        echo '<div class="hssb-generator-container">';

        // Left Column - Form
        echo '<div class="hssb-generator-form">';
        echo '<h3>Configure Your Share Buttons</h3>';

        echo '<form method="post" action="" id="shortcode-form">';
        wp_nonce_field('html_social_share_shortcode');

        // Networks Section
        echo '<div class="hssb-form-section">';
        echo '<h4><span class="dashicons dashicons-share"></span> Social Networks</h4>';
        echo '<p class="description">Choose which social networks to include in your share buttons.</p>';
        echo '<div class="hssb-networks-grid">';

        $networks = [
            'facebook' => ['label' => 'Facebook', 'icon' => 'dashicons-facebook', 'color' => '#1877f2'],
            'twitter' => ['label' => 'Twitter', 'icon' => 'dashicons-twitter', 'color' => '#1da1f2'],
            'linkedin' => ['label' => 'LinkedIn', 'icon' => 'dashicons-linkedin', 'color' => '#0077b5'],
            'pinterest' => ['label' => 'Pinterest', 'icon' => 'dashicons-pinterest', 'color' => '#e60023'],
            'email' => ['label' => 'Email', 'icon' => 'dashicons-email', 'color' => '#666']
        ];

        foreach ($networks as $key => $network) {
            $checked = (!isset($_POST['generate']) || in_array($key, $_POST['networks'] ?? [])) ? 'checked' : '';
            echo '<label class="hssb-network-option" for="network_' . esc_attr($key) . '">';
            echo '<input type="checkbox" id="network_' . esc_attr($key) . '" name="networks[]" value="' . esc_attr($key) . '" ' . $checked . '>';
            echo '<span class="hssb-network-icon" style="background-color: ' . esc_attr($network['color']) . ';"><span class="' . esc_attr($network['icon']) . '"></span></span>';
            echo '<span class="hssb-network-label">' . esc_html($network['label']) . '</span>';
            echo '</label>';
        }
        echo '</div>';
        echo '</div>';

        // Style Section
        echo '<div class="hssb-form-section">';
        echo '<h4><span class="dashicons dashicons-art"></span> Button Style</h4>';
        echo '<p class="description">Select the visual style for your share buttons.</p>';
        echo '<select name="style" id="style-select">';
        echo '<option value="default" ' . (($_POST['style'] ?? 'default') === 'default' ? 'selected' : '') . '>Default</option>';
        echo '<option value="square" ' . (($_POST['style'] ?? '') === 'square' ? 'selected' : '') . '>Square</option>';
        echo '<option value="circle" ' . (($_POST['style'] ?? '') === 'circle' ? 'selected' : '') . '>Circle</option>';
        echo '<option value="minimal" ' . (($_POST['style'] ?? '') === 'minimal' ? 'selected' : '') . '>Minimal</option>';
        echo '</select>';
        echo '</div>';

        echo '<div class="hssb-form-actions">';
        echo '<button type="submit" name="generate" class="button button-primary button-large">';
        echo '<span class="dashicons dashicons-shortcode"></span> Generate Shortcode';
        echo '</button>';
        echo '</div>';

        echo '</form>';
        echo '</div>';

        // Right Column - Preview/Result
        echo '<div class="hssb-generator-preview">';
        echo '<h3>Preview & Code</h3>';

        if (!empty($generated_shortcode)) {
            echo '<div class="hssb-result-card">';
            echo '<h4>Generated Shortcode</h4>';
            echo '<div class="hssb-shortcode-display">';
            echo '<code id="generated-shortcode">' . esc_html($generated_shortcode) . '</code>';
            echo '<button type="button" id="copy-shortcode" class="button button-secondary" title="Copy to clipboard">';
            echo '<span class="dashicons dashicons-clipboard"></span> Copy';
            echo '</button>';
            echo '</div>';
            echo '</div>';

            echo '<div class="hssb-preview-section">';
            echo '<h4>Live Preview</h4>';
            echo '<div class="hssb-preview-container" id="shortcode-preview">';
            // Generate preview based on selected networks and style
            $preview_networks = isset($_POST['networks']) ? $_POST['networks'] : [];
            $preview_style = $_POST['style'] ?? 'default';

            if (!empty($preview_networks)) {
                echo '<div class="hssb-share-buttons hssb-style-' . esc_attr($preview_style) . '">';
                foreach ($preview_networks as $network) {
                    if (isset($networks[$network])) {
                        $config = $networks[$network];
                        echo '<a href="#" class="hssb-share-button hssb-' . esc_attr($network) . '" style="background-color: ' . esc_attr($config['color']) . ';" title="Share on ' . esc_attr($config['label']) . '">';
                        echo '<span class="' . esc_attr($config['icon']) . '"></span>';
                        echo '<span class="hssb-button-text">' . esc_html($config['label']) . '</span>';
                        echo '</a>';
                    }
                }
                echo '</div>';
            }
            echo '</div>';
            echo '<p class="description">This is how your share buttons will appear on your site.</p>';
            echo '</div>';
        } else {
            echo '<div class="hssb-placeholder">';
            echo '<div class="dashicons dashicons-visibility"></div>';
            echo '<p>Select networks and style, then click "Generate Shortcode" to see the preview.</p>';
            echo '</div>';
        }

        echo '</div>'; // End preview column

        echo '</div>'; // End generator container

        // Usage Documentation
        echo '<div class="hssb-documentation card">';
        echo '<h3><span class="dashicons dashicons-book"></span> Usage Guide</h3>';
        echo '<div class="hssb-docs-grid">';
        echo '<div class="hssb-doc-section">';
        echo '<h4>In Posts & Pages</h4>';
        echo '<p>Paste the shortcode directly into your content:</p>';
        echo '<code>[html_social_share networks="facebook,twitter" style="square"]</code>';
        echo '</div>';
        echo '<div class="hssb-doc-section">';
        echo '<h4>In Theme Files</h4>';
        echo '<p>Use the PHP function in your templates:</p>';
        echo '<code>&lt;?php echo do_shortcode(\'[html_social_share networks="facebook,twitter"]\'); ?&gt;</code>';
        echo '</div>';
        echo '<div class="hssb-doc-section">';
        echo '<h4>Parameters</h4>';
        echo '<ul>';
        echo '<li><code>networks</code>: Comma-separated network names</li>';
        echo '<li><code>style</code>: Button style (default, square, circle, minimal)</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Copy functionality script
        echo '<script>
        document.getElementById("copy-shortcode")?.addEventListener("click", function() {
            const shortcode = document.getElementById("generated-shortcode");
            if (shortcode) {
                navigator.clipboard.writeText(shortcode.textContent).then(function() {
                    const button = document.getElementById("copy-shortcode");
                    const originalHTML = button.innerHTML;
                    button.innerHTML = \'<span class="dashicons dashicons-yes"></span> Copied!\';
                    button.classList.add("button-primary");
                    setTimeout(function() {
                        button.innerHTML = originalHTML;
                        button.classList.remove("button-primary");
                    }, 2000);
                });
            }
        });
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

    public function handleLivePreview()
    {
        check_ajax_referer('hssb_live_preview');

        // Get current settings from POST data
        $enabledNetworks = isset($_POST['enabled_networks']) ? $_POST['enabled_networks'] : [];
        $iconset = sanitize_text_field($_POST['iconset'] ?? 'default');

        // Update iconset temporarily
        if (method_exists($this->settingsPage, 'getShareRenderer')) {
            $renderer = $this->settingsPage->getShareRenderer();
            if ($renderer && method_exists($renderer, 'setIconset')) {
                $mappings = [
                    'default' => 'default/square',
                    'square' => 'flat/square',
                    'circle' => 'flat/circle',
                    'minimal' => 'prajin/square'
                ];
                $path = $mappings[$iconset] ?? 'default/square';
                $renderer->setIconset($path);
            }
        }

        // Generate preview HTML
        ob_start();
        echo '<h2>Live Preview</h2>';
        echo '<p>Preview of how share buttons will appear:</p>';

        if (empty($enabledNetworks)) {
            echo '<p>No networks enabled.</p>';
        } else {
            echo '<div class="hssb-preview">';
            foreach ($enabledNetworks as $network) {
                $profile = ['handle' => '@example', 'network' => $network];
                $html = $this->settingsPage->getShareRenderer()->render($network, $profile);
                echo $html . ' ';
            }
            echo '</div>';
        }
        echo '<p><em>Note: This is a basic preview. Actual styling may vary.</em></p>';

        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }
}