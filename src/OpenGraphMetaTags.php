<?php
namespace HtmlSocialShare;

/**
 * Open Graph Meta Tags Service
 *
 * Adds Open Graph meta tags to the HTML head for better social media sharing
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class OpenGraphMetaTags
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Initialize the service and register hooks
     */
    public function init(): void
    {
        if ($this->isEnabled()) {
            add_action('wp_head', [$this, 'addMetaTags'], 1);
        }
    }

    /**
     * Check if OG meta tags are enabled
     */
    private function isEnabled(): bool
    {
        return $this->settings->get('opengraph.enabled', false);
    }

    /**
     * Add Open Graph meta tags to wp_head
     */
    public function addMetaTags(): void
    {
        if (!$this->shouldAddMetaTags()) {
            return;
        }

        $metaTags = $this->generateMetaTags();

        foreach ($metaTags as $property => $content) {
            if (!empty($content)) {
                echo '<meta property="' . esc_attr($property) . '" content="' . esc_attr($content) . '" />' . "\n";
            }
        }
    }

    /**
     * Determine if meta tags should be added for current page
     */
    private function shouldAddMetaTags(): bool
    {
        // Don't add on admin pages, feeds, etc.
        if (is_admin() || is_feed() || is_robots() || is_trackback()) {
            return false;
        }

        // Add on singular posts/pages by default
        if (is_singular()) {
            return true;
        }

        // Add on front page if enabled
        if (is_front_page() && $this->settings->get('opengraph.front_page', true)) {
            return true;
        }

        // Add on archive pages if enabled
        if (is_archive() && $this->settings->get('opengraph.archive_pages', false)) {
            return true;
        }

        return false;
    }

    /**
     * Generate Open Graph meta tags
     */
    private function generateMetaTags(): array
    {
        $tags = [];

        // Basic tags
        $tags['og:title'] = $this->getTitle();
        $tags['og:description'] = $this->getDescription();
        $tags['og:url'] = $this->getUrl();
        $tags['og:type'] = $this->getType();
        $tags['og:site_name'] = get_bloginfo('name');

        // Image
        $image = $this->getImage();
        if ($image) {
            $tags['og:image'] = $image;
            $tags['og:image:width'] = $this->getImageWidth($image);
            $tags['og:image:height'] = $this->getImageHeight($image);
        }

        // Twitter Card (optional enhancement)
        if ($this->settings->get('opengraph.twitter_card', true)) {
            $tags['twitter:card'] = 'summary_large_image';
            $tags['twitter:title'] = $tags['og:title'];
            $tags['twitter:description'] = $tags['og:description'];
            if ($image) {
                $tags['twitter:image'] = $image;
            }
        }

        return $tags;
    }

    /**
     * Get the title for OG meta tag
     */
    private function getTitle(): string
    {
        if (is_singular()) {
            return get_the_title();
        }

        if (is_front_page()) {
            return get_bloginfo('name');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        return get_bloginfo('name');
    }

    /**
     * Get the description for OG meta tag
     */
    private function getDescription(): string
    {
        if (is_singular()) {
            $excerpt = get_the_excerpt();
            if (!empty($excerpt)) {
                return $excerpt;
            }

            // Fallback to content excerpt
            $content = get_the_content();
            $content = wp_strip_all_tags($content);
            return wp_trim_words($content, 30, '...');
        }

        return get_bloginfo('description');
    }

    /**
     * Get the URL for OG meta tag
     */
    private function getUrl(): string
    {
        if (is_singular()) {
            return get_permalink();
        }

        return home_url(add_query_arg(null, null));
    }

    /**
     * Get the type for OG meta tag
     */
    private function getType(): string
    {
        if (is_single()) {
            return 'article';
        }

        if (is_page()) {
            return 'website';
        }

        return 'website';
    }

    /**
     * Get the image for OG meta tag
     */
    private function getImage(): ?string
    {
        // Try featured image first
        if (is_singular() && has_post_thumbnail()) {
            $thumbnail_id = get_post_thumbnail_id();
            $image = wp_get_attachment_image_src($thumbnail_id, 'large');
            if ($image) {
                return $image[0];
            }
        }

        // Try custom OG image from settings
        $customImage = $this->settings->get('opengraph.default_image');
        if ($customImage) {
            return $customImage;
        }

        // Fallback to site icon/logo if available
        $siteIcon = get_site_icon_url(512);
        if ($siteIcon) {
            return $siteIcon;
        }

        return null;
    }

    /**
     * Get image width (placeholder - would need image processing)
     */
    private function getImageWidth(string $imageUrl): ?int
    {
        // For now, return null - could be enhanced with image size detection
        return null;
    }

    /**
     * Get image height (placeholder - would need image processing)
     */
    private function getImageHeight(string $imageUrl): ?int
    {
        // For now, return null - could be enhanced with image size detection
        return null;
    }
}