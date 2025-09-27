<?php
namespace HtmlSocialShare;

class Iconsets
{
    public static function getAvailableIconsets(): array
    {
        return [
            'default' => [
                'label' => 'Default',
                'description' => 'Standard social media icons'
            ],
            'square' => [
                'label' => 'Square',
                'description' => 'Square shaped icons'
            ],
            'circle' => [
                'label' => 'Circle',
                'description' => 'Circular icons'
            ],
            'minimal' => [
                'label' => 'Minimal',
                'description' => 'Minimalist design'
            ]
        ];
    }

    public static function getCurrentIconset($settings): string
    {
        return $settings->get('iconset', 'default');
    }
}