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

        $currentTab = $_GET['tab'] ?? 'general';

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
            $url = add_query_arg('tab', $tab);
            echo '<a href="' . esc_url($url) . '" class="' . $class . '">' . $label . '</a>';
        }
        echo '</nav>';

        echo '<form method="post" action="">';
        wp_nonce_field('html_social_share_settings');

        echo '<div class="tab-content">';
        switch ($currentTab) {
            case 'general':
                $this->renderGeneralTab();
                break;
            case 'networks':
                $this->renderNetworksTab();
                break;
            case 'appearance':
                $this->renderAppearanceTab();
                break;
            case 'placement':
                $this->renderPlacementTab();
                break;
        }
        echo '</div>';

        submit_button();
        echo '</form>';

        // Live preview
        $this->renderPreview();

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

        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    private function renderGeneralTab()
    {
        echo '<h2>General Settings</h2>';
        echo '<p>Configure general plugin settings.</p>';
        // TODO: Add form fields
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
}