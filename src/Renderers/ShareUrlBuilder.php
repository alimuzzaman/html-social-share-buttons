<?php
namespace HtmlSocialShare\Renderers;

use HtmlSocialShare\Networks;

/**
 * Share URL Builder
 * 
 * Follows Single Responsibility Principle by focusing only on
 * building share URLs from profiles and network configurations.
 * 
 * @since 3.0.0
 */
class ShareUrlBuilder
{
    /**
     * Network configurations
     * 
     * @var array
     */
    private $networks;

    /**
     * URL filters
     * 
     * @var array
     */
    private $urlFilters = [];

    /**
     * Constructor
     * 
     * @param array|null $networks Network configurations (optional, will load from Networks class)
     */
    public function __construct(?array $networks = null)
    {
        $this->networks = $networks ?: $this->loadNetworks();
    }

    /**
     * Add URL filter callback
     * 
     * @param callable $filter Filter function
     * @param int $priority Priority (lower = earlier)
     */
    public function addUrlFilter(callable $filter, int $priority = 10): void
    {
        $this->urlFilters[] = [
            'callback' => $filter,
            'priority' => $priority
        ];

        // Sort by priority
        usort($this->urlFilters, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    /**
     * Build share URL for network and profile
     * 
     * @param string $network Network key
     * @param array $profile Profile configuration
     * @param string $url URL to share
     * @param string $title Title to share
     * @return string Share URL
     */
    public function buildUrl(string $network, array $profile, string $url, string $title = ''): string
    {
        $template = $this->getUrlTemplate($network, $profile);
        
        if (empty($template)) {
            return '#';
        }

        // Generate base URL
        $shareUrl = RenderUtils::generateShareUrl($template, $url, $title);

        // Apply filters
        foreach ($this->urlFilters as $filter) {
            try {
                $shareUrl = call_user_func(
                    $filter['callback'],
                    $shareUrl,
                    $network,
                    $url,
                    $title,
                    $profile
                );
            } catch (\Throwable $e) {
                // Log error but continue processing
                error_log("ShareUrlBuilder: Filter error - " . $e->getMessage());
            }
        }

        return $shareUrl;
    }

    /**
     * Get URL template for network
     * 
     * @param string $network Network key
     * @param array $profile Profile configuration
     * @return string URL template
     */
    public function getUrlTemplate(string $network, array $profile): string
    {
        // First check profile for custom template
        if (!empty($profile['url_template'])) {
            return $profile['url_template'];
        }

        // Then check network configuration
        if (isset($this->networks[$network]['url_template'])) {
            return $this->networks[$network]['url_template'];
        }

        // Finally check default templates
        return $this->getDefaultTemplate($network);
    }

    /**
     * Get default URL template for network
     * 
     * @param string $network Network key
     * @return string Default template
     */
    protected function getDefaultTemplate(string $network): string
    {
        $defaultTemplates = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u={encoded_url}',
            'twitter' => 'https://twitter.com/intent/tweet?url={encoded_url}&text={encoded_title}',
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url={encoded_url}',
            'pinterest' => 'https://pinterest.com/pin/create/button/?url={encoded_url}&description={encoded_title}',
            'reddit' => 'https://reddit.com/submit?url={encoded_url}&title={encoded_title}',
            'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={encoded_url}&title={encoded_title}',
            'whatsapp' => 'https://api.whatsapp.com/send?text={encoded_title}%20{encoded_url}',
            'telegram' => 'https://t.me/share/url?url={encoded_url}&text={encoded_title}',
            'email' => 'mailto:?subject={encoded_title}&body={encoded_url}',
            'vk' => 'https://vk.com/share.php?url={encoded_url}&title={encoded_title}',
        ];

        return $defaultTemplates[$network] ?? '';
    }

    /**
     * Validate share URL
     * 
     * @param string $url URL to validate
     * @return bool True if URL is valid
     */
    public function isValidShareUrl(string $url): bool
    {
        if ($url === '#') {
            return true; // Placeholder URLs are valid
        }

        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Get all available networks with templates
     * 
     * @return array Network configurations with templates
     */
    public function getAvailableNetworks(): array
    {
        return array_filter($this->networks, function($network, $key) {
            return !empty($this->getDefaultTemplate($key));
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Load networks from Networks class
     * 
     * @return array Network configurations
     */
    private function loadNetworks(): array
    {
        try {
            return Networks::getAvailableNetworks();
        } catch (\Throwable $e) {
            error_log("ShareUrlBuilder: Failed to load networks - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Test URL template with sample data
     * 
     * @param string $template URL template
     * @return array Test results
     */
    public function testTemplate(string $template): array
    {
        $testUrl = 'https://example.com/test-post';
        $testTitle = 'Test Post Title';

        $result = [
            'template' => $template,
            'valid' => RenderUtils::isValidUrlTemplate($template),
            'generated_url' => '',
            'error' => null
        ];

        try {
            $result['generated_url'] = RenderUtils::generateShareUrl($template, $testUrl, $testTitle);
            $result['url_valid'] = $this->isValidShareUrl($result['generated_url']);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            $result['url_valid'] = false;
        }

        return $result;
    }

    /**
     * Create ShareUrlBuilder with WordPress integration
     * 
     * @return ShareUrlBuilder
     */
    public static function createWithWordPressIntegration(): self
    {
        $builder = new self();

        // Add WordPress filter integration
        $builder->addUrlFilter(function ($url, $network, $originalUrl, $title, $profile) {
            // Apply WordPress filters if available
            if (function_exists('apply_filters')) {
                return apply_filters('hss_share_url', $url, $network, $originalUrl, $title, $profile);
            }
            return $url;
        }, 100);

        return $builder;
    }
}