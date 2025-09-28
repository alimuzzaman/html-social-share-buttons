<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\Networks;
use HtmlSocialShare\Iconsets;
use HtmlSocialShare\ShareRendererInterface;

class SettingsPage
{
    private $settings;
    private $shareRenderer;

    public function __construct($settings, ShareRendererInterface $shareRenderer)
    {
        $this->settings = $settings;
        $this->shareRenderer = $shareRenderer;
    }

    public function render()
    {
        // Handle form submission
        if (isset($_POST['submit'])) {
            $this->saveSettings();
        }

        $currentTab = $_POST['current_tab'] ?? 'general';

        echo '<div class="wrap">';
        echo '<h1>HTML Social Share Settings</h1>';

        // Tabs
        echo '<nav class="nav-tab-wrapper">';
        $tabs = [
            'general' => 'General',
            'networks' => 'Networks',
            'profiles' => 'Profiles',
            'integrations' => 'Integrations',
            'appearance' => 'Appearance',
            'placement' => 'Placement'
        ];

        foreach ($tabs as $tab => $label) {
            $class = ($currentTab === $tab) ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo '<a href="#" class="nav-tab-link" data-tab="' . esc_attr($tab) . '" class="' . $class . '">' . $label . '</a>';
        }
        echo '</nav>';

        echo '<form method="post" action="">';
        wp_nonce_field('html_social_share_settings');
        echo '<input type="hidden" name="current_tab" id="current_tab" value="' . esc_attr($currentTab) . '">';

        // Render all tab content
        echo '<div id="tab-general" class="tab-content" style="display: ' . ($currentTab === 'general' ? 'block' : 'none') . ';">';
        $this->renderGeneralTab();
        echo '</div>';

        echo '<div id="tab-networks" class="tab-content" style="display: ' . ($currentTab === 'networks' ? 'block' : 'none') . ';">';
        $this->renderNetworksTab();
        echo '</div>';

        echo '<div id="tab-profiles" class="tab-content" style="display: ' . ($currentTab === 'profiles' ? 'block' : 'none') . ';">';
        $this->renderProfilesTab();
        echo '</div>';

        echo '<div id="tab-integrations" class="tab-content" style="display: ' . ($currentTab === 'integrations' ? 'block' : 'none') . ';">';
        $this->renderIntegrationsTab();
        echo '</div>';

        echo '<div id="tab-appearance" class="tab-content" style="display: ' . ($currentTab === 'appearance' ? 'block' : 'none') . ';">';
        $this->renderAppearanceTab();
        echo '</div>';

        echo '<div id="tab-placement" class="tab-content" style="display: ' . ($currentTab === 'placement' ? 'block' : 'none') . ';">';
        $this->renderPlacementTab();
        echo '</div>';

        submit_button();
        echo '</form>';

        // Live preview
        echo '<div id="hssb-live-preview-container">';
        $this->renderPreview();
        echo '</div>';

        // Add JavaScript for tab switching
        $this->enqueueTabScript();

        // Add JavaScript for live preview
        $this->enqueuePreviewScript();

        echo '</div>';
    }

    private function saveSettings()
    {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'html_social_share_settings')) {
            return;
        }

        // Save general settings
        if (isset($_POST['title'])) {
            $this->settings->set('title', sanitize_text_field($_POST['title']));
        }

        // Save enabled networks
        if (isset($_POST['enabled_networks'])) {
            $networks = array_map('sanitize_text_field', $_POST['enabled_networks']);
            $this->settings->set('enabled_networks', $networks);
        }

        // Save positions
        if (isset($_POST['positions'])) {
            $positions = array_map('sanitize_text_field', $_POST['positions']);
            $validPositions = ['left', 'right', 'before_post', 'after_post'];
            $positions = array_intersect($positions, $validPositions);
            $this->settings->set('positions', $positions);
        }

        // Save exclusions
        $exclusions = [
            'ids' => [],
            'slugs' => [],
            'titles' => []
        ];

        if (isset($_POST['exclude_ids'])) {
            $exclusions['ids'] = array_map('intval', array_filter($_POST['exclude_ids']));
        }

        if (isset($_POST['exclude_slugs'])) {
            $exclusions['slugs'] = array_map('sanitize_text_field', $_POST['exclude_slugs']);
        }

        if (isset($_POST['exclude_titles'])) {
            $exclusions['titles'] = array_map('sanitize_text_field', $_POST['exclude_titles']);
        }

        $this->settings->set('exclusions', $exclusions);

        // Save boolean settings
        $booleanSettings = [
            'auto_hide' => 'auto_hide',
            'use_port' => 'use_port',
            'google_analytics' => 'google_analytics',
            'nofollow' => 'nofollow',
            'betterlinks_enabled' => 'betterlinks_enabled',
            'betterlinks_shorten_urls' => 'betterlinks_shorten_urls',
            'betterlinks_add_tracking' => 'betterlinks_add_tracking'
        ];

        foreach ($booleanSettings as $settingKey => $postKey) {
            $this->settings->set($settingKey, isset($_POST[$postKey]));
        }

        // Save iconset
        if (isset($_POST['iconset'])) {
            $iconset = sanitize_text_field($_POST['iconset']);
            $this->settings->set('iconset', $iconset);
        }

        // Save style
        if (isset($_POST['style'])) {
            $style = sanitize_text_field($_POST['style']);
            $validStyles = ['minimal', 'rounded', 'outlined', 'gradient', 'contrast'];
            if (in_array($style, $validStyles)) {
                $this->settings->set('style', $style);
            }
        }

        // Clear caches after settings update
        $this->settings->clearAllCaches();

        // Show success message
        add_settings_error(
            'html_social_share_settings',
            'settings_updated',
            __('Settings saved successfully.', 'html-social-share'),
            'updated'
        );
    }

    private function renderGeneralTab()
    {
        echo '<h2>General Settings</h2>';
        echo '<p>Configure general plugin settings.</p>';

        $title = $this->settings->get('title', 'Share this with your friends');
        $exclusions = $this->settings->get('exclusions', ['ids' => [], 'slugs' => [], 'titles' => []]);
        $googleAnalytics = $this->settings->get('google_analytics', false);
        $autoHide = $this->settings->get('auto_hide', false);
        $usePort = $this->settings->get('use_port', false);
        $nofollow = $this->settings->get('nofollow', false);
        $betterlinksEnabled = $this->settings->get('betterlinks_enabled', false);
        $betterlinksShortenUrls = $this->settings->get('betterlinks_shorten_urls', true);
        $betterlinksAddTracking = $this->settings->get('betterlinks_add_tracking', true);

        // Migration status
        $migrationStatus = $this->settings->getMigrationStatus();
        if (!empty($migrationStatus)) {
            if (!empty($migrationStatus['done'])) {
                echo '<div class="notice notice-success inline">';
                echo '<p><strong>Migration completed successfully!</strong></p>';
                if (!empty($migrationStatus['from_version'])) {
                    echo '<p>Migrated from version: ' . esc_html($migrationStatus['from_version']) . '</p>';
                }
                if (!empty($migrationStatus['date'])) {
                    echo '<p>Migration date: ' . esc_html($migrationStatus['date']) . '</p>';
                }
                echo '</div>';
            } else {
                echo '<div class="notice notice-warning inline">';
                echo '<p><strong>Migration pending.</strong> Legacy options detected that need to be migrated to the new format.</p>';
                echo '</div>';
            }
}        echo '<table class="form-table">';
        echo '<tbody>';

        // Title field
        echo '<tr>';
        echo '<th scope="row"><label for="title">Share Title</label></th>';
        echo '<td>';
        echo '<input type="text" id="title" name="title" value="' . esc_attr($title) . '" class="regular-text" aria-describedby="title_desc">';
        echo '<p id="title_desc" class="description">The text to display above the share buttons.</p>';
        echo '</td>';
        echo '</tr>';

        // Exclude field
        echo '<tr>';
        echo '<th scope="row">Exclude Content</th>';
        echo '<td>';
        echo '<fieldset>';

        // Exclude by IDs
        echo '<label for="exclude_ids">';
        echo '<strong>Exclude by Post/Page IDs:</strong><br>';
        echo '<input type="text" id="exclude_ids" name="exclude_ids[]" value="' . esc_attr(implode(', ', $exclusions['ids'])) . '" class="regular-text" placeholder="1, 2, 3">';
        echo '<p class="description">Comma-separated list of post/page IDs to exclude.</p>';
        echo '</label><br><br>';

        // Exclude by slugs
        echo '<label for="exclude_slugs">';
        echo '<strong>Exclude by Slugs:</strong><br>';
        echo '<input type="text" id="exclude_slugs" name="exclude_slugs[]" value="' . esc_attr(implode(', ', $exclusions['slugs'])) . '" class="regular-text" placeholder="about, contact">';
        echo '<p class="description">Comma-separated list of post/page slugs to exclude.</p>';
        echo '</label><br><br>';

        // Exclude by titles
        echo '<label for="exclude_titles">';
        echo '<strong>Exclude by Titles:</strong><br>';
        echo '<input type="text" id="exclude_titles" name="exclude_titles[]" value="' . esc_attr(implode(', ', $exclusions['titles'])) . '" class="regular-text" placeholder="Privacy Policy, Terms of Service">';
        echo '<p class="description">Comma-separated list of post/page titles to exclude.</p>';
        echo '</label>';

        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';

        // Advanced Options
        echo '<tr>';
        echo '<th scope="row">Advanced Options</th>';
        echo '<td>';
        echo '<fieldset>';
        echo '<label for="google_analytics">';
        echo '<input type="checkbox" id="google_analytics" name="google_analytics" value="1" ' . checked($googleAnalytics, true, false) . '> ';
        echo 'Enable Google Social Analytics';
        echo '</label><br>';
        echo '<span class="description">Be sure you have Google Analytics already installed on your site.</span><br><br>';

        echo '<label for="auto_hide">';
        echo '<input type="checkbox" id="auto_hide" name="auto_hide" value="1" ' . checked($autoHide, true, false) . '> ';
        echo 'Auto hide buttons on page load (for left/right side placement)';
        echo '</label><br><br>';

        echo '<label for="use_port">';
        echo '<input type="checkbox" id="use_port" name="use_port" value="1" ' . checked($usePort, true, false) . '> ';
        echo 'Use port in URLs (e.g., SSL port :443)';
        echo '</label><br><br>';

        echo '<label for="nofollow">';
        echo '<input type="checkbox" id="nofollow" name="nofollow" value="1" ' . checked($nofollow, true, false) . '> ';
        echo 'Add nofollow to social links';
        echo '</label><br><br>';

        // BetterLinks Integration
        echo '<strong>BetterLinks Integration</strong><br>';
        echo '<label for="betterlinks_enabled">';
        echo '<input type="checkbox" id="betterlinks_enabled" name="betterlinks_enabled" value="1" ' . checked($betterlinksEnabled, true, false) . '> ';
        echo 'Enable BetterLinks integration';
        echo '</label><br>';
        echo '<span class="description">Automatically shorten share URLs using BetterLinks plugin.</span><br><br>';

        echo '<label for="betterlinks_shorten_urls">';
        echo '<input type="checkbox" id="betterlinks_shorten_urls" name="betterlinks_shorten_urls" value="1" ' . checked($betterlinksShortenUrls, true, false) . ' ' . disabled($betterlinksEnabled, false, false) . '> ';
        echo 'Shorten URLs with BetterLinks';
        echo '</label><br><br>';

        echo '<label for="betterlinks_add_tracking">';
        echo '<input type="checkbox" id="betterlinks_add_tracking" name="betterlinks_add_tracking" value="1" ' . checked($betterlinksAddTracking, true, false) . ' ' . disabled($betterlinksEnabled, false, false) . '> ';
        echo 'Add UTM tracking parameters';
        echo '</label><br>';
        echo '<span class="description">Add UTM parameters for better analytics tracking.</span>';

        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
    }

    private function renderNetworksTab()
    {
        echo '<h2>Social Networks</h2>';
        echo '<p>Enable or disable social networks.</p>';

        $availableNetworks = Networks::getAvailableNetworks();
        $enabledNetworks = $this->settings->get('enabled_networks', array_keys($availableNetworks));

        echo '<table class="form-table">';
        echo '<tbody>';
        foreach ($availableNetworks as $key => $network) {
            $checked = in_array($key, $enabledNetworks) ? 'checked' : '';
            echo '<tr>';
            echo '<th scope="row"><label for="enabled_' . esc_attr($key) . '">' . esc_html($network['label']) . '</label></th>';
            echo '<td>';
            echo '<input type="checkbox" id="enabled_' . esc_attr($key) . '" name="enabled_networks[]" value="' . esc_attr($key) . '" ' . $checked . ' aria-describedby="desc_' . esc_attr($key) . '">';
            echo '<span id="desc_' . esc_attr($key) . '" class="description">Enable sharing to ' . esc_html($network['label']) . '</span>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    private function renderProfilesTab()
    {
        echo '<h2>Social Profiles</h2>';
        echo '<p>Manage social network profiles and share configurations.</p>';

        // This is a basic implementation - in a real scenario, you'd have forms to edit profiles
        echo '<div class="notice notice-info inline">';
        echo '<p>Profile management is handled automatically based on enabled networks. Custom profiles can be managed programmatically.</p>';
        echo '</div>';

        // Show current profiles
        $profiles = $this->settings->getAllProfiles();
        if (!empty($profiles)) {
            echo '<h3>Current Profiles</h3>';
            echo '<table class="widefat">';
            echo '<thead><tr><th>Network</th><th>Type</th><th>Label</th><th>URL Template</th><th>Status</th></tr></thead>';
            echo '<tbody>';
            foreach ($profiles as $profile) {
                $status = !empty($profile['visible']) ? 'Enabled' : 'Disabled';
                echo '<tr>';
                echo '<td>' . esc_html($profile['handle'] ?? $profile['id']) . '</td>';
                echo '<td>' . esc_html($profile['type'] ?? 'share') . '</td>';
                echo '<td>' . esc_html($profile['label']) . '</td>';
                echo '<td>' . esc_html($profile['url_template']) . '</td>';
                echo '<td>' . $status . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
    }

    private function renderIntegrationsTab()
    {
        echo '<h2>Integrations</h2>';
        echo '<p>Configure integrations with third-party plugins and services.</p>';

        // Get integration status
        $integrations = $this->getIntegrationStatus();

        echo '<table class="form-table">';
        echo '<tbody>';

        foreach ($integrations as $slug => $integration) {
            echo '<tr>';
            echo '<th scope="row"><label for="integration_' . esc_attr($slug) . '">' . esc_html($integration['name']) . '</label></th>';
            echo '<td>';
            if ($integration['available']) {
                echo '<span class="dashicons dashicons-yes" style="color: green;"></span> Available';
                if ($integration['active']) {
                    echo ' | <a href="' . esc_url(admin_url('admin.php?page=html-social-share&tab=integrations&integration=' . $slug)) . '" class="button">Configure</a>';
                }
            } else {
                echo '<span class="dashicons dashicons-no" style="color: red;"></span> Not Available';
                echo '<p class="description">Install and activate ' . esc_html($integration['name']) . ' to enable this integration.</p>';
            }
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    private function renderAppearanceTab()
    {
        echo '<h2>Appearance</h2>';
        echo '<p>Customize the appearance of share buttons.</p>';

        // Get available iconsets from the icon registry
        $iconRegistry = new \HtmlSocialShare\IconRegistry($this->settings);
        $availableIconsets = $iconRegistry->getAvailableIconsets();
        $currentIconset = $this->settings->get('iconset', 'default');

        $currentStyle = $this->settings->get('style', 'minimal');

        echo '<table class="form-table">';
        echo '<tbody>';
        echo '<tr>';
        echo '<th scope="row"><label for="iconset_select">Icon Set</label></th>';
        echo '<td>';
        echo '<select id="iconset_select" name="iconset" aria-describedby="iconset_desc">';
        foreach ($availableIconsets as $key => $iconset) {
            $selected = ($currentIconset === $key) ? 'selected' : '';
            $label = is_array($iconset) ? ($iconset['label'] ?? $key) : $iconset;
            $description = is_array($iconset) ? ($iconset['description'] ?? 'Social media icons') : 'Social media icons';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($label) . ' - ' . esc_html($description) . '</option>';
        }
        echo '</select>';
        echo '<p id="iconset_desc" class="description">Choose the style for your social share icons.</p>';
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th scope="row"><label for="style_select">Button Style</label></th>';
        echo '<td>';
        echo '<select id="style_select" name="style" aria-describedby="style_desc">';
        $styles = [
            'minimal' => 'Minimal Flat',
            'rounded' => 'Rounded Accent',
            'outlined' => 'Outlined Subtle',
            'gradient' => 'Gradient Hover',
            'contrast' => 'High-Contrast Accessible'
        ];
        foreach ($styles as $key => $label) {
            $selected = ($currentStyle === $key) ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p id="style_desc" class="description">Choose the visual style for your share buttons.</p>';
        echo '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    }

    private function renderPlacementTab()
    {
        echo '<h2>Placement</h2>';
        echo '<p>Choose where to display share buttons.</p>';

        $positions = [
            'before_post' => 'Before post content',
            'after_post' => 'After post content',
            'left' => 'Show on left side',
            'right' => 'Show on right side'
        ];

        $currentPositions = $this->settings->get('positions', ['after_post']);

        echo '<table class="form-table">';
        echo '<tbody>';
        foreach ($positions as $key => $label) {
            $checked = in_array($key, $currentPositions) ? 'checked' : '';
            echo '<tr>';
            echo '<th scope="row"><label for="position_' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
            echo '<td>';
            echo '<input type="checkbox" id="position_' . esc_attr($key) . '" name="positions[]" value="' . esc_attr($key) . '" ' . $checked . ' aria-describedby="position_desc_' . esc_attr($key) . '">';
            echo '<span id="position_desc_' . esc_attr($key) . '" class="description">Display share buttons ' . esc_html(strtolower($label)) . '</span>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    private function renderPreview()
    {
        echo '<h2>Live Preview</h2>';
        echo '<p>Preview of how share buttons will appear:</p>';

        $enabledNetworks = Networks::getEnabledNetworks($this->settings);

        if (empty($enabledNetworks)) {
            echo '<p>No networks enabled.</p>';
            return;
        }

        echo '<div class="hssb-preview">';
        foreach ($enabledNetworks as $network => $config) {
            $profile = ['handle' => '@example', 'network' => $network];
            $html = $this->shareRenderer->render($network, $profile);
            echo $html . ' ';
        }
        echo '</div>';
        echo '<p><em>Note: This is a basic preview. Actual styling may vary.</em></p>';
    }

    private function enqueueTabScript()
    {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.nav-tab-link');
            const tabContents = document.querySelectorAll('.tab-content');
            const currentTabInput = document.getElementById('current_tab');

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabId = this.getAttribute('data-tab');

                    // Update active tab
                    tabLinks.forEach(l => l.classList.remove('nav-tab-active'));
                    this.classList.add('nav-tab-active');

                    // Show selected tab content
                    tabContents.forEach(content => {
                        content.style.display = content.id === 'tab-' + tabId ? 'block' : 'none';
                    });

                    // Update hidden input
                    if (currentTabInput) {
                        currentTabInput.value = tabId;
                    }
                });
            });
        });
        </script>
        <?php
    }

    private function enqueuePreviewScript()
    {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const previewContainer = document.getElementById('hssb-live-preview-container');

            if (!form || !previewContainer) return;

            // Watch for changes on form inputs
            const inputs = form.querySelectorAll('input, select');
            let updateTimeout;

            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    clearTimeout(updateTimeout);
                    updateTimeout = setTimeout(updatePreview, 500);
                });
            });

            function updatePreview() {
                const formData = new FormData(form);
                formData.append('action', 'hssb_live_preview');
                formData.append('_wpnonce', '<?php echo wp_create_nonce('hssb_live_preview'); ?>');

                fetch(ajaxurl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.html) {
                        previewContainer.innerHTML = data.data.html;
                    }
                })
                .catch(error => {
                    console.error('Preview update failed:', error);
                });
            }
        });
        </script>
        <?php
    }

    public function getShareRenderer()
    {
        return $this->shareRenderer;
    }

    private function getIntegrationStatus()
    {
        return [
            'betterlinks' => [
                'name' => 'BetterLinks',
                'available' => class_exists('BetterLinks'),
                'active' => $this->settings->get('betterlinks_enabled', false)
            ],
            'woocommerce' => [
                'name' => 'WooCommerce',
                'available' => class_exists('WooCommerce'),
                'active' => true // Always available when WooCommerce is active
            ],
            'elementor' => [
                'name' => 'Elementor',
                'available' => defined('ELEMENTOR_VERSION'),
                'active' => true // Always available when Elementor is active
            ]
        ];
    }
}