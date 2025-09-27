<?php
namespace HtmlSocialShare\Svg;

class Sanitizer implements SanitizerInterface
{
    public function sanitize(string $svg): string
    {
        // Basic, conservative sanitizer: remove <script> and on* attributes and external references.
        // This is intentionally simple for unit tests; real production code should use a robust parser.

        // Remove script tags
        $svg = preg_replace('#<script[^>]*>.*?</script>#is', '', $svg);

        // Remove event handler attributes like onclick, onload
        $svg = preg_replace('#\s+on[a-z]+\s*=\s*"[^"]*"#is', '', $svg);
        $svg = preg_replace('#\s+on[a-z]+\s*=\s*\'[^\']*\'#is', '', $svg);

        // Strip external references (xlink:href or href starting with http)
        $svg = preg_replace_callback('#(xlink:href|href)\s*=\s*["\']([^"\']+)["\']#i', function($m){
            $url = $m[2];
            // If it's a data: or internal fragment (#...), keep; otherwise remove
            if (stripos($url, 'data:') === 0 || strpos($url, '#') === 0) {
                return $m[0];
            }
            return '';
        }, $svg);

        return $svg;
    }
}
