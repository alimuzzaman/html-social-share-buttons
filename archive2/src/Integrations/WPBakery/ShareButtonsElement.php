<?php
namespace HtmlSocialShare\Integrations\WPBakery;

use HtmlSocialShare\ShareRendererInterface;
use HtmlSocialShare\Networks;

/**
 * WPBakery Page Builder Element for HTML Social Share Buttons
 * 
 * @since 3.0.0
 */
class ShareButtonsElement extends \WPBakeryShortCode
{
    /**
     * Share renderer instance
     * 
     * @var ShareRendererInterface
     */
    private $shareRenderer;

    /**
     * Constructor
     * 
     * @param ShareRendererInterface $shareRenderer
     */
    public function __construct(ShareRendererInterface $shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
        parent::__construct();
    }

    /**
     * Register the element with WPBakery
     * 
     * @param ShareRendererInterface $shareRenderer
     * @return void
     */
    public static function register(ShareRendererInterface $shareRenderer): void
    {
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        vc_map([
            'name' => __('HTML Social Share Buttons', 'html-social-share'),
            'base' => 'html_social_share_buttons',
            'description' => __('Modern social share buttons with accessibility features', 'html-social-share'),
            'category' => __('Social', 'html-social-share'),
            'icon' => 'icon-wpb-share',
            'params' => self::getParams(),
            'php_class_name' => __CLASS__,
        ]);

        // Store share renderer instance for later use
        global $hssb_wpbakery_renderer;
        $hssb_wpbakery_renderer = $shareRenderer;
    }

    /**
     * Get element parameters configuration
     * 
     * @return array
     */
    private static function getParams(): array
    {
        $params = [];

        // Title parameter
        $params[] = [
            'type' => 'textfield',
            'heading' => __('Title', 'html-social-share'),
            'param_name' => 'title',
            'value' => __('Share this with your friends', 'html-social-share'),
            'description' => __('Text to display above the share buttons', 'html-social-share'),
            'admin_label' => true,
        ];

        // Show title toggle
        $params[] = [
            'type' => 'checkbox',
            'heading' => __('Show Title', 'html-social-share'),
            'param_name' => 'show_title',
            'value' => 'yes',
            'description' => __('Display the title above buttons', 'html-social-share'),
        ];

        // Get available networks dynamically
        $availableNetworks = Networks::getAvailableNetworks();
        $networkOptions = [];
        
        foreach ($availableNetworks as $key => $network) {
            $networkOptions[$network['label']] = $key;
            
            $params[] = [
                'type' => 'checkbox',
                'heading' => $network['label'],
                'param_name' => 'network_' . $key,
                'value' => in_array($key, ['facebook', 'twitter', 'linkedin']) ? 'yes' : '',
                'description' => sprintf(__('Enable %s sharing', 'html-social-share'), $network['label']),
                'group' => __('Networks', 'html-social-share'),
            ];
        }

        // Iconset dropdown
        $params[] = [
            'type' => 'dropdown',
            'heading' => __('Icon Style', 'html-social-share'),
            'param_name' => 'iconset',
            'value' => [
                __('Default (Square)', 'html-social-share') => 'default',
                __('Flat Square', 'html-social-share') => 'square',
                __('Flat Circle', 'html-social-share') => 'circle',
                __('Minimal', 'html-social-share') => 'minimal',
            ],
            'description' => __('Choose the style for social icons', 'html-social-share'),
            'group' => __('Appearance', 'html-social-share'),
            'std' => 'default',
        ];

        // Alignment
        $params[] = [
            'type' => 'dropdown',
            'heading' => __('Alignment', 'html-social-share'),
            'param_name' => 'alignment',
            'value' => [
                __('Left', 'html-social-share') => 'left',
                __('Center', 'html-social-share') => 'center',
                __('Right', 'html-social-share') => 'right',
            ],
            'description' => __('Button alignment', 'html-social-share'),
            'group' => __('Appearance', 'html-social-share'),
            'std' => 'left',
        ];

        // Button size
        $params[] = [
            'type' => 'textfield',
            'heading' => __('Button Size (px)', 'html-social-share'),
            'param_name' => 'button_size',
            'value' => '32',
            'description' => __('Size of the social buttons in pixels', 'html-social-share'),
            'group' => __('Appearance', 'html-social-share'),
        ];

        // Button spacing
        $params[] = [
            'type' => 'textfield',
            'heading' => __('Button Spacing (px)', 'html-social-share'),
            'param_name' => 'button_spacing',
            'value' => '5',
            'description' => __('Space between buttons in pixels', 'html-social-share'),
            'group' => __('Appearance', 'html-social-share'),
        ];

        // Custom CSS class
        $params[] = [
            'type' => 'textfield',
            'heading' => __('Extra CSS Class', 'html-social-share'),
            'param_name' => 'css_class',
            'description' => __('Additional CSS class for custom styling', 'html-social-share'),
            'group' => __('Design Options', 'html-social-share'),
        ];

        return $params;
    }

    /**
     * Generate the element content
     * 
     * @param array $atts Element attributes
     * @param string|null $content Element content
     * @return string
     */
    protected function content($atts, $content = null): string
    {
        // Get share renderer instance
        global $hssb_wpbakery_renderer;
        $shareRenderer = $hssb_wpbakery_renderer ?? $this->shareRenderer;

        if (!$shareRenderer) {
            return '<div class="wpbakery-error">' . 
                   esc_html__('Share renderer not available.', 'html-social-share') . 
                   '</div>';
        }

        // Default attributes
        $defaultAtts = [
            'title' => 'Share this with your friends',
            'show_title' => 'yes',
            'iconset' => 'default',
            'alignment' => 'left',
            'button_size' => '32',
            'button_spacing' => '5',
            'css_class' => '',
        ];

        // Add network defaults dynamically
        $availableNetworks = Networks::getAvailableNetworks();
        foreach ($availableNetworks as $key => $network) {
            $defaultAtts['network_' . $key] = in_array($key, ['facebook', 'twitter', 'linkedin']) ? 'yes' : '';
        }

        $atts = shortcode_atts($defaultAtts, $atts);

        // Collect enabled networks
        $networks = $this->getEnabledNetworks($atts);

        if (empty($networks)) {
            return '<div class="wpbakery-info">' . 
                   esc_html__('Please select at least one social network.', 'html-social-share') . 
                   '</div>';
        }

        // Configure renderer
        $this->configureRenderer($shareRenderer, $atts);

        // Generate HTML
        return $this->generateHtml($networks, $atts, $shareRenderer);
    }

    /**
     * Get enabled networks from attributes
     * 
     * @param array $atts Element attributes
     * @return array
     */
    private function getEnabledNetworks(array $atts): array
    {
        $networks = [];
        $availableNetworks = Networks::getAvailableNetworks();
        
        foreach ($availableNetworks as $key => $network) {
            if (!empty($atts['network_' . $key]) && $atts['network_' . $key] === 'yes') {
                $networks[] = $key;
            }
        }
        
        return $networks;
    }

    /**
     * Configure the share renderer
     * 
     * @param ShareRendererInterface $shareRenderer
     * @param array $atts
     * @return void
     */
    private function configureRenderer(ShareRendererInterface $shareRenderer, array $atts): void
    {
        // Map iconset
        $iconsetMappings = [
            'default' => 'default_square',
            'square' => 'flat_square',
            'circle' => 'flat_circle',
            'minimal' => 'prajin_square'
        ];
        
        $internalIconset = $iconsetMappings[$atts['iconset']] ?? 'default_square';

        // Set iconset on renderer
        if (method_exists($shareRenderer, 'setIconset')) {
            $shareRenderer->setIconset($internalIconset);
        }
    }

    /**
     * Generate the HTML output
     * 
     * @param array $networks Enabled networks
     * @param array $atts Element attributes
     * @param ShareRendererInterface $shareRenderer
     * @return string
     */
    private function generateHtml(array $networks, array $atts, ShareRendererInterface $shareRenderer): string
    {
        $buttonSize = absint($atts['button_size']);
        $buttonSpacing = absint($atts['button_spacing']);
        $cssClass = sanitize_html_class($atts['css_class']);
        
        $wrapperClass = 'wpbakery-html-social-share-buttons';
        if (!empty($cssClass)) {
            $wrapperClass .= ' ' . $cssClass;
        }

        $output = '<div class="' . esc_attr($wrapperClass) . '" style="text-align: ' . esc_attr($atts['alignment']) . ';" role="group" aria-label="' . esc_attr__('Social sharing buttons', 'html-social-share') . '">';

        // Add title if enabled and not empty
        if ($atts['show_title'] === 'yes' && !empty($atts['title'])) {
            $output .= '<div class="share-title">' . esc_html($atts['title']) . '</div>';
        }

        $output .= '<div class="share-buttons" role="group" aria-label="' . esc_attr__('Share buttons', 'html-social-share') . '">';

        // Add custom styles
        $buttonStyle = '';
        if ($buttonSize !== 32) {
            $buttonStyle .= 'width: ' . $buttonSize . 'px; height: ' . $buttonSize . 'px; ';
        }
        if ($buttonSpacing !== 5) {
            $buttonStyle .= 'margin-right: ' . $buttonSpacing . 'px; ';
        }

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example', 
                'network' => $network,
                'type' => 'share',
                'visible' => true
            ];
            
            $buttonHtml = $shareRenderer->render($network, $profile);
            
            // Add custom styling if needed
            if (!empty($buttonStyle)) {
                $buttonHtml = str_replace('<a ', '<a style="' . esc_attr($buttonStyle) . '" ', $buttonHtml);
            }
            
            $output .= $buttonHtml . ' ';
        }

        $output .= '</div></div>';

        return $output;
    }

    /**
     * Get element information
     * 
     * @return array
     */
    public static function getElementInfo(): array
    {
        return [
            'name' => 'HTML Social Share Buttons',
            'description' => __('Modern social share buttons with accessibility features', 'html-social-share'),
            'version' => '3.0.0',
            'available' => defined('WPB_VC_VERSION'),
            'required_plugin' => 'WPBakery Page Builder',
            'required_version' => '6.0.0',
        ];
    }
}