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
                'label' => 'X (formerly Twitter)',
                'url_template' => 'https://x.com/intent/tweet?url={url}&text={title}',
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
            ],
            'whatsapp' => [
                'label' => 'WhatsApp',
                'url_template' => 'https://wa.me/?text={title}%20{url}',
                'icon' => 'whatsapp'
            ],
            'telegram' => [
                'label' => 'Telegram',
                'url_template' => 'https://t.me/share/url?url={url}&text={title}',
                'icon' => 'telegram'
            ],
            'reddit' => [
                'label' => 'Reddit',
                'url_template' => 'https://reddit.com/submit?url={url}&title={title}',
                'icon' => 'reddit'
            ],
            'tumblr' => [
                'label' => 'Tumblr',
                'url_template' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}',
                'icon' => 'tumblr'
            ],
            'mastodon' => [
                'label' => 'Mastodon',
                'url_template' => 'https://mastodon.social/share?text={title}%20{url}',
                'icon' => 'mastodon'
            ],
            'threads' => [
                'label' => 'Threads',
                'url_template' => 'https://www.threads.net/intent/post?text={title}%20{url}',
                'icon' => 'threads'
            ],
            'vk' => [
                'label' => 'VK',
                'url_template' => 'https://vk.com/share.php?url={url}&title={title}',
                'icon' => 'vk'
            ],
            'bluesky' => [
                'label' => 'Bluesky',
                'url_template' => 'https://bsky.app/intent/compose?text={title}%20{url}',
                'icon' => 'bluesky'
            ],
            'wechat' => [
                'label' => 'WeChat',
                'url_template' => 'https://web.wechat.com/?text={title}%20{url}',
                'icon' => 'wechat'
            ],
            'instagram' => [
                'label' => 'Instagram Direct',
                'url_template' => 'https://www.instagram.com/direct/inbox/',
                'icon' => 'instagram'
            ],
            'messenger' => [
                'label' => 'Messenger',
                'url_template' => 'https://www.facebook.com/dialog/send?link={url}&app_id=123456789&redirect_uri={url}',
                'icon' => 'messenger'
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