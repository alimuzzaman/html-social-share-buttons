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
     * @param string $url
     * @param string $title
     * @return string
     */
    public function render(string $network, array $profile, string $url = '#', string $title = ''): string;

    /**
     * Set the iconset to use for rendering.
     *
     * @param string $iconset
     * @return void
     */
    public function setIconset(string $iconset): void;
}
