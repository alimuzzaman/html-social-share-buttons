<?php
namespace HtmlSocialShare;

class Networks
{
    public static function getAvailableNetworks(): array
    {
        return [
            'facebook' => [
                'label' => 'Facebook',
                'url_template' => 'https://www.facebook.com/sharer/sharer.php?u={url}&t={title}',
                'icon' => 'facebook'
            ],
            'twitter' => [
                'label' => 'Twitter',
                'url_template' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
                'icon' => 'twitter'
            ],
            'linkedin' => [
                'label' => 'LinkedIn',
                'url_template' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                'icon' => 'linkedin'
            ],
            'pinterest' => [
                'label' => 'Pinterest',
                'url_template' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
                'icon' => 'pinterest'
            ],
            'email' => [
                'label' => 'Email',
                'url_template' => 'mailto:?subject={title}&body={url}',
                'icon' => 'email'
            ]
        ];
    }

    public static function getEnabledNetworks($settings): array
    {
        $available = self::getAvailableNetworks();
        $enabled = $settings->get('enabled_networks', array_keys($available));
        return array_intersect_key($available, array_flip($enabled));
    }
}