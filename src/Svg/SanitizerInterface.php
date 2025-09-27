<?php
namespace HtmlSocialShare\Svg;

interface SanitizerInterface
{
    /**
     * Sanitize an SVG string and return a safe string.
     *
     * @param string $svg
     * @return string
     */
    public function sanitize(string $svg): string;
}
