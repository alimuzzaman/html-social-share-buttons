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

        if (isset($_POST['enabled_networks'])) {
            $this->settings->set('enabled_networks', $_POST['enabled_networks']);
        }

        if (isset($_POST['iconset'])) {
            $this->settings->set('iconset', sanitize_text_field($_POST['iconset']));
        }

        if (isset($_POST['placement'])) {
            $this->settings->set('placement', $_POST['placement']);
        }

        // Save general settings
        if (isset($_POST['title'])) {
            $this->settings->set('title', sanitize_text_field($_POST['title']));
        }

        if (isset($_POST['exclude'])) {
            $this->settings->set('exclude', sanitize_text_field($_POST['exclude']));
        }

        // Save advanced options
        $this->settings->set('google_analytics', isset($_POST['google_analytics']));
        $this->settings->set('auto_hide', isset($_POST['auto_hide']));
        $this->settings->set('use_port', isset($_POST['use_port']));
        $this->settings->set('nofollow', isset($_POST['nofollow']));

        // Refresh icon registry with new iconset
        if (isset($_POST['iconset']) && method_exists($this->shareRenderer, 'setIconset')) {
            $iconset = sanitize_text_field($_POST['iconset']);
            $mappings = [
                'default' => 'default_square',
                'square' => 'flat_square',
                'circle' => 'flat_circle',
                'minimal' => 'prajin_square'
            ];
            $path = $mappings[$iconset] ?? 'default_square';
            $this->shareRenderer->setIconset($path);
        }

        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    private function renderGeneralTab()
    {
        echo '<h2>General Settings</h2>';
        echo '<p>Configure general plugin settings.</p>';

        $title = $this->settings->get('title', 'Share this with your friends');
        $exclude = $this->settings->get('exclude', '');
        $googleAnalytics = $this->settings->get('google_analytics', false);
        $autoHide = $this->settings->get('auto_hide', false);
        $usePort = $this->settings->get('use_port', false);
        $nofollow = $this->settings->get('nofollow', false);

        echo '<table class="form-table">';
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
        echo '<th scope="row"><label for="exclude">Exclude Pages</label></th>';
        echo '<td>';
        echo '<input type="text" id="exclude" name="exclude" value="' . esc_attr($exclude) . '" class="regular-text" aria-describedby="exclude_desc">';
        echo '<p id="exclude_desc" class="description">Exclude by Page ID, Page Title or Page Slug (comma separated).</p>';
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
        echo '</label>';
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

    private function renderAppearanceTab()
    {
        echo '<h2>Appearance</h2>';
        echo '<p>Customize the appearance of share buttons.</p>';

        $availableIconsets = Iconsets::getAvailableIconsets();
        $currentIconset = Iconsets::getCurrentIconset($this->settings);

        echo '<table class="form-table">';
        echo '<tbody>';
        echo '<tr>';
        echo '<th scope="row"><label for="iconset_select">Icon Set</label></th>';
        echo '<td>';
        echo '<select id="iconset_select" name="iconset" aria-describedby="iconset_desc">';
        foreach ($availableIconsets as $key => $iconset) {
            $selected = ($currentIconset === $key) ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($iconset['label']) . ' - ' . esc_html($iconset['description']) . '</option>';
        }
        echo '</select>';
        echo '<p id="iconset_desc" class="description">Choose the style for your social share icons.</p>';
        echo '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    }

    private function renderPlacementTab()
    {
        echo '<h2>Placement</h2>';
        echo '<p>Choose where to display share buttons.</p>';

        $placements = [
            'before_post' => 'Before post content',
            'after_post' => 'After post content',
            'before_page' => 'Before page content',
            'after_page' => 'After page content',
            'left_side' => 'Show on left side',
            'right_side' => 'Show on right side',
            'manual' => 'Manual placement only (via shortcode/widget)'
        ];

        $currentPlacements = $this->settings->get('placement', ['after_post']);

        echo '<table class="form-table">';
        echo '<tbody>';
        foreach ($placements as $key => $label) {
            $checked = in_array($key, $currentPlacements) ? 'checked' : '';
            echo '<tr>';
            echo '<th scope="row"><label for="placement_' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
            echo '<td>';
            echo '<input type="checkbox" id="placement_' . esc_attr($key) . '" name="placement[]" value="' . esc_attr($key) . '" ' . $checked . ' aria-describedby="placement_desc_' . esc_attr($key) . '">';
            echo '<span id="placement_desc_' . esc_attr($key) . '" class="description">Display share buttons ' . esc_html(strtolower($label)) . '</span>';
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
}