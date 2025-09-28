<?php
namespace HtmlSocialShare\Integrations\BeaverBuilder;

use HtmlSocialShare\ShareRendererInterface;

class ShareButtonsModule extends \FLBuilderModule
{
    private $shareRenderer;

    public function __construct($shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
        parent::__construct([
            'name' => __('HTML Social Share Buttons', 'html-social-share'),
            'description' => __('Display social share buttons with customizable networks and styles.', 'html-social-share'),
            'category' => __('Social', 'html-social-share'),
            'dir' => plugin_dir_path(__FILE__),
            'url' => plugins_url('/', __FILE__),
            'icon' => 'share.svg',
            'editor_export' => true,
            'enabled' => true,
            'partial_refresh' => false,
            'file' => __DIR__ . '/includes/frontend.php',
        ]);
    }

    public static function register($shareRenderer)
    {
        if (!class_exists('FLBuilderModule')) {
            return;
        }

        \FLBuilder::register_module(__CLASS__, [
            'general' => [
                'title' => __('General', 'html-social-share'),
                'sections' => [
                    'general' => [
                        'title' => __('Settings', 'html-social-share'),
                        'fields' => [
                            'title' => [
                                'type' => 'text',
                                'label' => __('Title', 'html-social-share'),
                                'default' => 'Share this with your friends',
                                'help' => __('Text to display above the share buttons.', 'html-social-share'),
                            ],
                            'networks' => [
                                'type' => 'select',
                                'label' => __('Social Networks', 'html-social-share'),
                                'default' => ['facebook', 'twitter', 'linkedin'],
                                'options' => [
                                    'facebook' => __('Facebook', 'html-social-share'),
                                    'twitter' => __('X', 'html-social-share'),
                                    'linkedin' => __('LinkedIn', 'html-social-share'),
                                    'googleplus' => __('Google+', 'html-social-share'),
                                    'pinterest' => __('Pinterest', 'html-social-share'),
                                    'email' => __('Email', 'html-social-share'),
                                ],
                                'multi-select' => true,
                                'help' => __('Select which social networks to display.', 'html-social-share'),
                            ],
                        ],
                    ],
                ],
            ],
            'style' => [
                'title' => __('Style', 'html-social-share'),
                'sections' => [
                    'style' => [
                        'title' => __('Appearance', 'html-social-share'),
                        'fields' => [
                            'iconset' => [
                                'type' => 'select',
                                'label' => __('Icon Set', 'html-social-share'),
                                'default' => 'default_square',
                                'options' => [
                                    'default_square' => __('Default (Square)', 'html-social-share'),
                                    'flat_square' => __('Flat Square', 'html-social-share'),
                                    'flat_circle' => __('Flat Circle', 'html-social-share'),
                                    'prajin_square' => __('Minimal', 'html-social-share'),
                                ],
                                'help' => __('Choose the style for social icons.', 'html-social-share'),
                            ],
                            'alignment' => [
                                'type' => 'align',
                                'label' => __('Alignment', 'html-social-share'),
                                'default' => 'left',
                                'help' => __('Align the share buttons.', 'html-social-share'),
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        new self($shareRenderer);
    }

    public function update($settings)
    {
        // Process networks array
        if (isset($settings->networks) && is_array($settings->networks)) {
            $settings->networks = array_values($settings->networks);
        }

        return $settings;
    }

    public function get_css()
    {
        $settings = $this->settings;
        $alignment = $settings->alignment ?? 'left';

        \FLBuilderCSS::rule([
            'selector' => ".fl-node-{$this->node} .fl-html-social-share-buttons",
            'props' => [
                'text-align' => $alignment,
            ],
        ]);

        \FLBuilderCSS::rule([
            'selector' => ".fl-node-{$this->node} .share-buttons",
            'props' => [
                'display' => 'flex',
                'gap' => '8px',
                'flex-wrap' => 'wrap',
            ],
        ]);

        \FLBuilderCSS::rule([
            'selector' => ".fl-node-{$this->node} .share-title",
            'props' => [
                'margin-bottom' => '10px',
                'font-weight' => 'bold',
            ],
        ]);
    }
}