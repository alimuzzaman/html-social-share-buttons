<?php
namespace HtmlSocialShare;

class ShareRenderer implements ShareRendererInterface
{
    public function render(string $network, array $profile): string
    {
        $label = htmlspecialchars($network, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $handle = isset($profile['handle']) ? htmlspecialchars($profile['handle'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
        $url = '#';
        return sprintf('<a class="hssb-share hssb-%s" href="%s">%s</a>', $label, $url, $label . ' ' . $handle);
    }
}
