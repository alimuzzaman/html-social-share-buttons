<?php
namespace HtmlSocialShare\Integrations\Divi;

use HtmlSocialShare\ShareRendererInterface;

class ShareButtonsModule extends \ET_Builder_Module
{
    private $shareRenderer;

    public function __construct($shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
        parent::__construct();
    }

    public $slug = 'html_social_share_buttons';
    public $vb_support = 'on';

    protected $module_credits = [
        'module_uri' => '',
        'author' => 'Alimuzzaman Alim',
        'author_uri' => 'https://alim.dev',
    ];

    public static function register($shareRenderer)
    {
        if (!class_exists('ET_Builder_Module')) {
            return;
        }

        new self($shareRenderer);
    }

    public function init()
    {
        $this->name = esc_html__('HTML Social Share Buttons', 'html-social-share');
        $this->icon_path = plugin_dir_path(__FILE__) . 'icon.svg';

        // Create a simple icon if the file doesn't exist
        if (!file_exists($this->icon_path)) {
            $this->icon_path = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>');
        }
    }

    public function get_fields()
    {
        return [
            'title' => [
                'label' => esc_html__('Title', 'html-social-share'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Text to display above the share buttons.', 'html-social-share'),
                'default' => 'Share this with your friends',
                'toggle_slug' => 'main_content',
            ],
            'networks' => [
                'label' => esc_html__('Social Networks', 'html-social-share'),
                'type' => 'multiple_checkboxes',
                'option_category' => 'basic_option',
                'description' => esc_html__('Select which social networks to display.', 'html-social-share'),
                'options' => [
                    'facebook' => esc_html__('Facebook', 'html-social-share'),
                    'twitter' => esc_html__('Twitter', 'html-social-share'),
                    'linkedin' => esc_html__('LinkedIn', 'html-social-share'),
                    'googleplus' => esc_html__('Google+', 'html-social-share'),
                    'pinterest' => esc_html__('Pinterest', 'html-social-share'),
                    'email' => esc_html__('Email', 'html-social-share'),
                ],
                'default' => 'facebook|twitter|linkedin|on|on|on',
                'toggle_slug' => 'main_content',
            ],
            'iconset' => [
                'label' => esc_html__('Icon Set', 'html-social-share'),
                'type' => 'select',
                'option_category' => 'configuration',
                'description' => esc_html__('Choose the style for social icons.', 'html-social-share'),
                'options' => [
                    'default' => esc_html__('Default (Square)', 'html-social-share'),
                    'square' => esc_html__('Flat Square', 'html-social-share'),
                    'circle' => esc_html__('Flat Circle', 'html-social-share'),
                    'minimal' => esc_html__('Minimal', 'html-social-share'),
                ],
                'default' => 'default',
                'toggle_slug' => 'style',
            ],
            'alignment' => [
                'label' => esc_html__('Alignment', 'html-social-share'),
                'type' => 'text_align',
                'option_category' => 'layout',
                'description' => esc_html__('Align the share buttons.', 'html-social-share'),
                'options' => ['left', 'center', 'right', 'justify'],
                'default' => 'left',
                'toggle_slug' => 'alignment',
            ],
        ];
    }

    public function render($attrs, $content = null, $render_slug)
    {
        $title = $this->props['title'] ?? 'Share this with your friends';
        $networks_string = $this->props['networks'] ?? 'facebook|twitter|linkedin|on|on|on';
        $iconset = $this->props['iconset'] ?? 'default';
        $alignment = $this->props['alignment'] ?? 'left';

        // Parse networks
        $networks = [];
        $network_options = ['facebook', 'twitter', 'linkedin', 'googleplus', 'pinterest', 'email'];
        $network_values = explode('|', $networks_string);

        foreach ($network_options as $index => $network) {
            if (isset($network_values[$index]) && $network_values[$index] === 'on') {
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
        $internalIconset = $iconsetMappings[$iconset] ?? 'default_square';

        // Set iconset on renderer
        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($internalIconset);
        }

        // Generate HTML
        $output = sprintf('<div class="et_pb_html_social_share_buttons et_pb_module" style="text-align: %s;">', esc_attr($alignment));

        if (!empty($title)) {
            $output .= sprintf('<div class="share-title">%s</div>', esc_html($title));
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