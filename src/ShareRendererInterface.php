<?php
namespace HtmlSocialShare;

interface ShareRendererInterface
{
    /**
     * Render a share button for a given network and profile data.
     * Returns HTML string.
     *
     * @param string $network
     * @param array $profile
     * @return string
     */
    public function render(string $network, array $profile): string;
}
