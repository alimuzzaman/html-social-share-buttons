<?php
/**
 * Legacy Button Renderer
 *
 * Renders share buttons using the legacy HTML/CSS-only approach from v2.x.
 * Maintains backward compatibility while integrating with v3.x architecture.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\Frontend;

use HtmlSocialShare\IconRegistryInterface;
use HtmlSocialShare\Settings;
use HtmlSocialShare\Networks;

class LegacyButtonRenderer
{
    private IconRegistryInterface $iconRegistry;
    private Settings $settings;
    private Networks $networks;
    private array $printedIcons = [];
    private array $stylesheets = [];

    public function __construct(
        IconRegistryInterface $iconRegistry,
        Settings $settings,
        Networks $networks
    ) {
        $this->iconRegistry = $iconRegistry;
        $this->settings = $settings;
        $this->networks = $networks;

        // Register footer hooks for CSS injection
        add_action('wp_footer', [$this, 'renderFooterStyles'], 999);
    }

    /**
     * Render share buttons with legacy HTML structure
     *
     * @param array $options Legacy options array
     * @return string HTML output
     */
    public function render(array $options = []): string
    {
        // Merge with defaults
        $defaults = $this->getDefaultOptions();
        $options = array_merge($defaults, $options);

        // Check exclusions
        if ($this->isExcluded()) {
            return '';
        }

        // Sanitize inputs
        $class = sanitize_html_class($options['class'] ?? 'left');
        $iconsetId = sanitize_key($options['iconset'] ?? 'default');
        $iconsetType = sanitize_key($options['iconset_type'] ?? 'square');
        $selectedIcons = $options['icons'] ?? [];
        $nofollow = isset($options['nofollow']) && $options['nofollow'] ? ' rel="nofollow"' : '';
        $showOn = $options['show_on'] ?? 'show_left';

        // Build output
        $output = '';

        // Add title for content placement
        if ($this->shouldShowTitle($options)) {
            $output .= '<h3>' . esc_html($options['title']) . '</h3>';
        }

        // Start button container
        $output .= sprintf(
            '<div class="zmshbt %s %s %s">',
            esc_attr($class),
            esc_attr($iconsetId),
            esc_attr($iconsetType)
        );

        // Get icon data
        $iconsetData = $this->getIconsetData($iconsetId);
        
        if (!$iconsetData) {
            error_log("HSS Legacy: Iconset '{$iconsetId}' not found, using default");
            $iconsetId = 'default';
            $iconsetData = $this->getIconsetData('default');
        }

        $icons = $iconsetData['icons'] ?? [];

        // Render each selected icon
        if (is_array($selectedIcons)) {
            foreach ($selectedIcons as $id => $enabled) {
                // Skip if not enabled (legacy format uses 1/0)
                if (!$enabled) {
                    continue;
                }

                $icon = $icons[$id] ?? null;
                if (!$icon) {
                    continue;
                }

                // Extract icon properties
                $iconClass = $icon['class'] ?? $id;
                $iconImage = $icon['image'] ?? ($id . '.png');
                $iconUrl = $icon['url'] ?? '#';

                // Replace custom URL placeholder if provided
                if (isset($options['url']) && !empty($options['url'])) {
                    $iconUrl = str_replace('%%permalink%%', $options['url'], $iconUrl);
                }

                // Process URL placeholders
                $iconUrl = $this->processUrlPlaceholders($iconUrl);

                // Track printed icons for CSS generation
                $this->printedIcons[$iconsetId . '_' . $iconsetType . '_' . $id] = [
                    'class' => $iconClass,
                    'image' => $iconImage,
                    'iconset_id' => $iconsetId,
                    'iconset_type' => $iconsetType,
                    'iconset_url' => $iconsetData['url'] ?? $this->getIconsetUrl($iconsetId)
                ];

                // Render icon link
                $output .= sprintf(
                    '<a class="%s" target="_blank" href="%s"%s></a>' . "\n",
                    esc_attr($iconClass),
                    esc_url($iconUrl),
                    $nofollow
                );
            }
        }

        $output .= '</div>';

        // Register stylesheet for this iconset
        $this->registerStylesheet($iconsetId, $iconsetData);

        return $output;
    }

    /**
     * Get default options matching legacy structure
     *
     * @return array
     */
    private function getDefaultOptions(): array
    {
        return [
            'title' => 'Share this with your friends',
            'iconset' => 'default',
            'iconset_type' => 'square',
            'class' => 'left',
            'icons' => [
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
                'googleplus' => 1,
                'bookmark' => 1,
                'pinterest' => 1,
                'mail' => 1,
            ],
            'nofollow' => false,
            'show_on' => 'show_left'
        ];
    }

    /**
     * Check if current post is excluded
     *
     * @return bool
     */
    private function isExcluded(): bool
    {
        global $post;

        if (empty($post->ID)) {
            return false;
        }

        // Check exclusion list
        $excludes = $this->settings->get('excludes', '');
        $excludes = array_map('trim', explode(',', $excludes));
        
        if (in_array($post->ID, $excludes, true)) {
            return true;
        }

        // Check per-post disable meta
        $disableShare = get_post_meta($post->ID, '_zm_sh_disable_share', true);
        if ($disableShare === 'on') {
            return true;
        }

        return false;
    }

    /**
     * Check if title should be shown
     *
     * @param array $options
     * @return bool
     */
    private function shouldShowTitle(array $options): bool
    {
        $showOn = $options['show_on'] ?? '';
        $class = $options['class'] ?? '';

        // Show title for content placement or shortcode
        if (in_array($showOn, ['show_before_post', 'show_after_post'], true)) {
            return true;
        }

        if ($class === 'in_shortcode' && !empty($options['title'])) {
            return true;
        }

        return false;
    }

    /**
     * Get iconset data from directory
     *
     * @param string $iconsetId
     * @return array|null
     */
    private function getIconsetData(string $iconsetId): ?array
    {
        $iconsetPath = HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/iconset/' . $iconsetId;

        if (!is_dir($iconsetPath)) {
            return null;
        }

        // Get network definitions
        $networkDefinitions = $this->networks->getNetworks();

        $icons = [];
        $files = @scandir($iconsetPath);

        if (!$files) {
            return null;
        }

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'png') {
                continue;
            }

            $network = pathinfo($file, PATHINFO_FILENAME);
            
            // Map legacy names to modern network IDs
            $networkMap = [
                'googlepluse' => 'googleplus',
                'mail' => 'email'
            ];
            
            $networkId = $networkMap[$network] ?? $network;
            $networkDef = $networkDefinitions[$networkId] ?? null;

            if (!$networkDef) {
                continue;
            }

            $icons[$network] = [
                'id' => $network,
                'name' => $networkDef['name'] ?? ucfirst($network),
                'class' => $network,
                'image' => $file,
                'url' => $networkDef['shareUrl'] ?? '#'
            ];
        }

        return [
            'id' => $iconsetId,
            'icons' => $icons,
            'url' => $this->getIconsetUrl($iconsetId),
            'stylesheet' => 'style.css'
        ];
    }

    /**
     * Get iconset URL
     *
     * @param string $iconsetId
     * @return string
     */
    private function getIconsetUrl(string $iconsetId): string
    {
        return HTML_SOCIAL_SHARE_ICONSET_URL . $iconsetId . '/';
    }

    /**
     * Register stylesheet for iconset
     *
     * @param string $iconsetId
     * @param array $iconsetData
     */
    private function registerStylesheet(string $iconsetId, array $iconsetData): void
    {
        $stylesheetUrl = ($iconsetData['url'] ?? $this->getIconsetUrl($iconsetId)) . 
                        ($iconsetData['stylesheet'] ?? 'style.css');
        
        $this->stylesheets[$iconsetId] = $stylesheetUrl;
    }

    /**
     * Process URL placeholders
     *
     * @param string $url
     * @return string
     */
    private function processUrlPlaceholders(string $url): string
    {
        // Replace %%permalink%%
        $permalink = get_permalink();
        if ($permalink) {
            $url = str_replace('%%permalink%%', $permalink, $url);
        }

        // Replace %%title%%
        $title = get_the_title();
        if ($title) {
            $url = str_replace('%%title%%', rawurlencode($title), $url);
        }

        // Replace %%description%%
        $description = get_the_excerpt();
        if ($description) {
            $url = str_replace('%%description%%', rawurlencode($description), $url);
        }

        // Replace %%imageurl%%
        if (strpos($url, '%%imageurl%%') !== false) {
            $thumbnail = get_the_post_thumbnail_url(null, 'large');
            if (!$thumbnail) {
                $thumbnail = '';
            }
            $url = str_replace('%%imageurl%%', rawurlencode($thumbnail), $url);
        }

        return $url;
    }

    /**
     * Render footer styles (called via wp_footer hook)
     */
    public function renderFooterStyles(): void
    {
        if (is_admin()) {
            return;
        }

        if ($this->isExcluded()) {
            return;
        }

        // Register stylesheets
        $this->renderStylesheets();

        // Render icon styles
        $this->renderIconStyles();
    }

    /**
     * Render stylesheet links
     */
    private function renderStylesheets(): void
    {
        if (empty($this->stylesheets)) {
            // Load default stylesheet
            $defaultUrl = $this->getIconsetUrl('default') . 'style.css';
            echo sprintf(
                "<link rel='stylesheet' id='social-share-default' href='%s' type='text/css' media='all' />\n",
                esc_url($defaultUrl)
            );
            return;
        }

        foreach ($this->stylesheets as $id => $stylesheet) {
            echo sprintf(
                "<link rel='stylesheet' id='social-share-%s' href='%s' type='text/css' media='all' />\n",
                esc_attr($id),
                esc_url($stylesheet)
            );
        }
    }

    /**
     * Render dynamic icon styles
     */
    private function renderIconStyles(): void
    {
        if (empty($this->printedIcons)) {
            return;
        }

        echo "<style>\n";

        // Generate CSS for each icon
        foreach ($this->printedIcons as $key => $icon) {
            $iconsetId = $icon['iconset_id'];
            $iconsetType = $icon['iconset_type'];
            $class = $icon['class'];
            $iconsetUrl = $icon['iconset_url'];
            $image = $icon['image'];

            echo sprintf(
                ".zmshbt.%s.%s .%s {\n    background-image: url('%s%s/%s');\n}\n",
                esc_attr($iconsetId),
                esc_attr($iconsetType),
                esc_attr($class),
                esc_url($iconsetUrl),
                esc_attr($iconsetType),
                esc_attr($image)
            );
        }

        // Handle auto-hide option
        $autoHide = $this->settings->get('auto_hide_btn', false);
        if (!$autoHide) {
            echo ".zmshbt.left {\n    left: 0 !important;\n}\n";
            echo ".zmshbt.right {\n    right: 0 !important;\n}\n";
        }

        echo "</style>\n";
    }

    /**
     * Render floating buttons (called from footer)
     *
     * @param array $options
     */
    public function renderFloatingButtons(array $options = []): void
    {
        if (is_admin() || $this->isExcluded()) {
            return;
        }

        $showIn = $options['show_in'] ?? $this->settings->get('show_in', []);

        // Left floating button
        if (!empty($showIn['show_left'])) {
            $leftOptions = array_merge($options, [
                'class' => 'left',
                'show_on' => 'show_left'
            ]);
            echo $this->render($leftOptions);
        }

        // Right floating button
        if (!empty($showIn['show_right'])) {
            $rightOptions = array_merge($options, [
                'class' => 'right',
                'show_on' => 'show_right'
            ]);
            echo $this->render($rightOptions);
        }
    }

    /**
     * Render Google Analytics tracking script
     *
     * @param bool $enabled
     */
    public function renderAnalyticsTracking(bool $enabled = false): void
    {
        if (!$enabled || is_admin() || $this->isExcluded()) {
            return;
        }

        ?>
        <script>
        jQuery(document).ready(function($){
            var _gaq = _gaq || [];
            jQuery('.zmshbt a').click(function(event){
                var network = this.className;
                var action = 'Share';
                
                switch(network) {
                    case 'googleplus':
                        action = '+1';
                        break;
                    case 'twitter':
                        action = 'Tweet';
                        break;
                    case 'mail':
                    case 'email':
                        action = 'Mail';
                        break;
                }
                
                _gaq.push(['_trackSocial', network, action]);
            });
        });
        </script>
        <?php
    }
}
