<?php
namespace HtmlSocialShare\Integrations\WPBakery;

use HtmlSocialShare\ShareRendererInterface;

class ShareButtonsElement extends \WPBakeryShortCode
{
    private $shareRenderer;

    public function __construct($shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
        parent::__construct();
    }

    public static function register($shareRenderer)
    {
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        vc_map([
            'name' => __('HTML Social Share Buttons', 'html-social-share'),
            'base' => 'html_social_share_buttons',
            'description' => __('Modern social share buttons', 'html-social-share'),
            'category' => __('Content', 'html-social-share'),
            'icon' => 'icon-wpb-share',
            'params' => self::getParams(),
            'php_class_name' => __CLASS__,
        ]);

        // Store share renderer instance for later use
        global $hssb_wpbakery_renderer;
        $hssb_wpbakery_renderer = $shareRenderer;
    }

    private static function getParams()
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

        // Networks checkboxes
        $availableNetworks = [
            'facebook' => __('Facebook', 'html-social-share'),
            'twitter' => __('X', 'html-social-share'),
            'linkedin' => __('LinkedIn', 'html-social-share'),
            'googleplus' => __('Google+', 'html-social-share'),
            'pinterest' => __('Pinterest', 'html-social-share'),
            'email' => __('Email', 'html-social-share'),
        ];

        foreach ($availableNetworks as $key => $label) {
            $params[] = [
                'type' => 'checkbox',
                'heading' => $label,
                'param_name' => 'network_' . $key,
                'value' => 'yes',
                'description' => sprintf(__('Enable %s sharing', 'html-social-share'), $label),
                'group' => __('Networks', 'html-social-share'),
            ];
        }

        // Iconset dropdown
        $params[] = [
            'type' => 'dropdown',
            'heading' => __('Icon Set', 'html-social-share'),
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

        return $params;
    }

    protected function content($atts, $content = null)
    {
        $atts = shortcode_atts([
            'title' => 'Share this with your friends',
            'iconset' => 'default',
            'alignment' => 'left',
            'network_facebook' => 'yes',
            'network_twitter' => 'yes',
            'network_linkedin' => '',
            'network_googleplus' => '',
            'network_pinterest' => '',
            'network_email' => '',
        ], $atts);

        // Collect enabled networks
        $networks = [];
        $availableNetworks = ['facebook', 'twitter', 'linkedin', 'googleplus', 'pinterest', 'email'];
        foreach ($availableNetworks as $network) {
            if (!empty($atts['network_' . $network])) {
                $networks[] = $network;
            }
        }

        // Map iconset
        $iconsetMappings = [
            'default' => 'default_square',
            'square' => 'flat_square',
            'circle' => 'flat_circle',
            'minimal' => 'prajin_square'
        ];
        $internalIconset = $iconsetMappings[$atts['iconset']] ?? 'default_square';

        // Set iconset on renderer
        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($internalIconset);
        }

        // Generate HTML
        $output = '<div class="wpbakery-html-social-share-buttons" style="text-align: ' . esc_attr($atts['alignment']) . ';">';

        if (!empty($atts['title'])) {
            $output .= '<div class="share-title">' . esc_html($atts['title']) . '</div>';
        }

        $output .= '<div class="share-buttons">';

        foreach ($networks as $network) {
            $profile = ['handle' => '@example', 'network' => $network];
            $buttonHtml = $this->shareRenderer->render($network, $profile);
            $output .= $buttonHtml . ' ';
        }

        $output .= '</div></div>';

        return $output;
    }
}