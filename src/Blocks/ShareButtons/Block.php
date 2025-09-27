<?php
namespace HtmlSocialShare\Blocks\ShareButtons;

use HtmlSocialShare\ShareRendererInterface;

class Block
{
    private $shareRenderer;

    public function __construct(ShareRendererInterface $shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
    }

    public function register()
    {
        register_block_type(
            'html-social-share/buttons',
            [
                'render_callback' => [$this, 'render'],
                'attributes' => [
                    'networks' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'default' => ['facebook', 'twitter', 'linkedin']
                    ],
                    'iconset' => [
                        'type' => 'string',
                        'default' => 'default'
                    ],
                    'title' => [
                        'type' => 'string',
                        'default' => 'Share this with your friends'
                    ],
                    'alignment' => [
                        'type' => 'string',
                        'default' => 'left'
                    ]
                ]
            ]
        );
    }

    public function render($attributes)
    {
        $networks = $attributes['networks'] ?? ['facebook', 'twitter', 'linkedin'];
        $iconset = $attributes['iconset'] ?? 'default';
        $title = $attributes['title'] ?? 'Share this with your friends';
        $alignment = $attributes['alignment'] ?? 'left';

        // Map iconset to our internal naming
        $iconsetMappings = [
            'default' => 'default_square',
            'square' => 'flat_square',
            'circle' => 'flat_circle',
            'minimal' => 'prajin_square'
        ];
        $internalIconset = $iconsetMappings[$iconset] ?? 'default_square';

        // Set the iconset on the renderer
        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($internalIconset);
        }

        $output = '<div class="wp-block-html-social-share-buttons" style="text-align: ' . esc_attr($alignment) . ';">';

        if (!empty($title)) {
            $output .= '<div class="share-title">' . esc_html($title) . '</div>';
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