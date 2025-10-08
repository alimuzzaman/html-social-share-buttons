<?php
/**
 * URL Builder Service
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Services;

use HtmlSocialShare\IconSystem\IconRegistry;
use HtmlSocialShare\IconSystem\Icon;

/**
 * Builds share URLs for social networks
 */
class UrlBuilder {
    /**
     * @var IconRegistry
     */
    private IconRegistry $iconRegistry;
    
    /**
     * Constructor
     *
     * @param IconRegistry $iconRegistry
     */
    public function __construct(IconRegistry $iconRegistry) {
        $this->iconRegistry = $iconRegistry;
    }
    
    /**
     * Build a share URL for a network
     *
     * @param string $network Network ID (e.g., 'facebook')
     * @param string $iconsetId Iconset ID
     * @param string $type Type
     * @param array $context Context data (permalink, title, image)
     * @return string
     */
    public function buildUrl(
        string $network,
        string $iconsetId,
        string $type,
        array $context = []
    ): string {
        $icon = $this->iconRegistry->getIcon($iconsetId, $type, $network);
        
        if (!$icon) {
            return '';
        }
        
        $template = $icon->urlTemplate;
        $url = $this->replacePlaceholders($template, $context);
        
        // Allow filtering of the final URL
        $url = apply_filters(
            'html_social_share_url',
            $url,
            $network,
            $context
        );
        
        // Legacy filter for backward compatibility
        $url = apply_filters(
            'zm_sh_placeholder',
            $url,
            $context
        );
        
        return $url;
    }
    
    /**
     * Replace placeholders in a template
     *
     * @param string $template URL template
     * @param array $context Context data
     * @return string
     */
    public function replacePlaceholders(string $template, array $context): string {
        $permalink = $context['permalink'] ?? $this->getCurrentUrl();
        $title = $context['title'] ?? $this->getCurrentTitle();
        $image = $context['image'] ?? $this->getFeaturedImage();
        
        // Encode for URL
        $replacements = [
            '%%permalink%%' => urlencode($permalink),
            '%%title%%' => urlencode($title),
            '%%image%%' => urlencode($image),
        ];
        
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }
    
    /**
     * Get current URL
     *
     * @return string
     */
    private function getCurrentUrl(): string {
        if (is_singular()) {
            return get_permalink();
        }
        
        // Construct URL from server variables
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        
        return $protocol . $host . $uri;
    }
    
    /**
     * Get current page title
     *
     * @return string
     */
    private function getCurrentTitle(): string {
        if (is_singular()) {
            return get_the_title();
        }
        
        return wp_get_document_title();
    }
    
    /**
     * Get featured image URL
     *
     * @return string
     */
    private function getFeaturedImage(): string {
        if (is_singular() && has_post_thumbnail()) {
            $thumbnail_id = get_post_thumbnail_id();
            $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'full');
            
            if ($thumbnail && isset($thumbnail[0])) {
                return $thumbnail[0];
            }
        }
        
        // Try to get first image from content
        global $post;
        if ($post && !empty($post->post_content)) {
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            if ($output && isset($matches[1][0])) {
                return $matches[1][0];
            }
        }
        
        // Fallback to site icon or empty
        $site_icon = get_site_icon_url();
        return $site_icon ?: '';
    }
}
