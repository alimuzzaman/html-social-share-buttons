<?php
namespace HtmlSocialShare\Renderers;

use HtmlSocialShare\Networks;
use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\UrlUtils;
use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Utils\StringUtils;

/**
 * Share URL Builder with enhanced security and validation
 * 
 * Follows Single Responsibility Principle by focusing only on
 * building share URLs from profiles and network configurations.
 * Separates pure URL building functions from side effects.
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
     * Add URL filter callback with validation
     * 
     * @param callable $filter Filter function
     * @param int $priority Priority (lower = earlier)
     * @throws \InvalidArgumentException If filter is invalid
     */
    public function addUrlFilter(callable $filter, int $priority = 10): void
    {
        if (!is_callable($filter)) {
            throw new \InvalidArgumentException("Filter must be callable");
        }
        
        if ($priority < 0 || $priority > 1000) {
            throw new \InvalidArgumentException("Priority must be between 0 and 1000");
        }

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
     * Build share URL for network and profile with security validation
     * 
     * @param string $network Network key
     * @param array $profile Profile configuration
     * @param string $url URL to share
     * @param string $title Title to share
     * @return string Share URL
     */
    public function buildUrl(string $network, array $profile, string $url, string $title = ''): string
    {
        // Validate inputs
        $network = SecurityUtils::sanitizeKey($network);
        if (!$network) {
            return '#';
        }
        
        $url = SecurityUtils::sanitizeUrl($url);
        if (!$url) {
            return '#';
        }
        
        $title = SecurityUtils::sanitizeTextField($title);
        
        // Validate profile configuration
        $validation = RenderUtils::validateButtonConfig(array_merge($profile, ['network' => $network, 'url' => $url]));
        if (!$validation['valid']) {
            error_log("ShareUrlBuilder: Invalid configuration - " . implode(', ', $validation['errors']));
            return '#';
        }

        $template = $this->getUrlTemplate($network, $profile);
        
        if (empty($template)) {
            return '#';
        }

        // Generate base URL using pure function
        $shareUrl = self::buildShareUrlPure($template, $url, $title, $profile);

        // Apply filters with error handling
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
                
                // Validate filter result
                $shareUrl = SecurityUtils::sanitizeUrl($shareUrl) ?: '#';
            } catch (\Throwable $e) {
                // Log error but continue processing
                error_log("ShareUrlBuilder: Filter error - " . $e->getMessage());
            }
        }

        return $shareUrl;
    }

    /**
     * Get URL template for network with validation
     * 
     * @param string $network Network key
     * @param array $profile Profile configuration
     * @return string URL template
     */
    public function getUrlTemplate(string $network, array $profile): string
    {
        // First check profile for custom template
        if (!empty($profile['url_template'])) {
            $template = SecurityUtils::sanitizeTextField($profile['url_template']);
            if (RenderUtils::isValidUrlTemplate($template)) {
                return $template;
            }
        }

        // Then check network configuration
        if (isset($this->networks[$network]['url_template'])) {
            $template = SecurityUtils::sanitizeTextField($this->networks[$network]['url_template']);
            if (RenderUtils::isValidUrlTemplate($template)) {
                return $template;
            }
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
        $defaultTemplates = self::getDefaultTemplates();
        return $defaultTemplates[$network] ?? '';
    }

    /**
     * Validate share URL with enhanced checks
     * 
     * @param string $url URL to validate
     * @return bool True if URL is valid
     */
    public function isValidShareUrl(string $url): bool
    {
        if ($url === '#') {
            return true; // Placeholder URLs are valid
        }

        // Check for XSS patterns
        if (SecurityUtils::hasXssPatterns($url)) {
            return false;
        }

        return UrlUtils::isValidUrl($url);
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
     * Load networks from Networks class with error handling
     * 
     * @return array Network configurations
     */
    private function loadNetworks(): array
    {
        try {
            if (class_exists(Networks::class)) {
                return Networks::getAvailableNetworks();
            }
            return self::getDefaultNetworkConfigurations();
        } catch (\Throwable $e) {
            error_log("ShareUrlBuilder: Failed to load networks - " . $e->getMessage());
            return self::getDefaultNetworkConfigurations();
        }
    }

    /**
     * Test URL template with sample data and comprehensive validation
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
            'url_valid' => false,
            'error' => null,
            'security_check' => true
        ];

        try {
            // Check for security issues
            if (SecurityUtils::hasXssPatterns($template)) {
                $result['security_check'] = false;
                $result['error'] = 'Template contains potentially dangerous content';
                return $result;
            }
            
            $result['generated_url'] = self::buildShareUrlPure($template, $testUrl, $testTitle);
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

    // ===== PURE FUNCTIONS (NO SIDE EFFECTS) =====

    /**
     * Pure function: Build share URL from template and parameters
     *
     * @param string $template URL template
     * @param string $url URL to share
     * @param string $title Title to share
     * @param array $profile Profile configuration for additional parameters
     * @return string Generated share URL
     */
    public static function buildShareUrlPure(string $template, string $url, string $title = '', array $profile = []): string
    {
        if (empty($template)) {
            return '#';
        }

        // Build replacement parameters
        $parameters = [
            'url' => $url,
            'title' => $title,
            'description' => $profile['description'] ?? $title,
            'hashtags' => $profile['hashtags'] ?? '',
            'via' => $profile['via'] ?? '',
            'image' => $profile['image'] ?? ''
        ];

        return UrlUtils::buildShareUrl($template, $parameters);
    }

    /**
     * Pure function: Get default URL templates for all networks
     *
     * @return array Network => template mapping
     */
    public static function getDefaultTemplates(): array
    {
        return [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u={encoded_url}',
            'twitter' => 'https://twitter.com/intent/tweet?url={encoded_url}&text={encoded_title}',
            'x' => 'https://twitter.com/intent/tweet?url={encoded_url}&text={encoded_title}', // X (formerly Twitter)
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url={encoded_url}',
            'pinterest' => 'https://pinterest.com/pin/create/button/?url={encoded_url}&description={encoded_title}',
            'reddit' => 'https://reddit.com/submit?url={encoded_url}&title={encoded_title}',
            'tumblr' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={encoded_url}&title={encoded_title}',
            'whatsapp' => 'https://api.whatsapp.com/send?text={encoded_title}%20{encoded_url}',
            'telegram' => 'https://t.me/share/url?url={encoded_url}&text={encoded_title}',
            'email' => 'mailto:?subject={encoded_title}&body={encoded_url}',
            'vk' => 'https://vk.com/share.php?url={encoded_url}&title={encoded_title}',
            'mastodon' => 'https://mastodon.social/share?text={encoded_title}%20{encoded_url}',
            'threads' => 'https://threads.net/intent/post?text={encoded_title}%20{encoded_url}',
            'bluesky' => 'https://bsky.app/intent/compose?text={encoded_title}%20{encoded_url}',
            'instagram' => '#', // Instagram doesn't support direct URL sharing
            'messenger' => 'fb-messenger://share/?link={encoded_url}',
            'wechat' => '#' // WeChat requires app integration
        ];
    }

    /**
     * Pure function: Get default network configurations
     *
     * @return array Network configurations
     */
    public static function getDefaultNetworkConfigurations(): array
    {
        $templates = self::getDefaultTemplates();
        $configurations = [];
        
        foreach ($templates as $network => $template) {
            $configurations[$network] = [
                'id' => $network,
                'name' => StringUtils::toTitleCase(str_replace(['_', '-'], ' ', $network)),
                'url_template' => $template,
                'enabled' => $template !== '#'
            ];
        }
        
        return $configurations;
    }

    /**
     * Pure function: Validate URL template format and security
     *
     * @param string $template URL template to validate
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateTemplate(string $template): array
    {
        $errors = [];
        
        if (empty($template)) {
            $errors[] = "Template cannot be empty";
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Security checks
        if (SecurityUtils::hasXssPatterns($template)) {
            $errors[] = "Template contains potentially dangerous content";
        }
        
        // URL structure validation
        if (!UrlUtils::isValidUrlTemplate($template)) {
            $errors[] = "Invalid URL template format";
        }
        
        // Placeholder validation
        $requiredPlaceholders = ['{url}', '{encoded_url}'];
        $hasRequiredPlaceholder = false;
        foreach ($requiredPlaceholders as $placeholder) {
            if (strpos($template, $placeholder) !== false) {
                $hasRequiredPlaceholder = true;
                break;
            }
        }
        
        if (!$hasRequiredPlaceholder) {
            $errors[] = "Template must contain either {url} or {encoded_url} placeholder";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Pure function: Extract network from share URL
     *
     * @param string $shareUrl Share URL to analyze
     * @return string|null Network identifier or null if not detected
     */
    public static function detectNetworkFromUrl(string $shareUrl): ?string
    {
        $domain = UrlUtils::extractDomain($shareUrl);
        if (!$domain) {
            return null;
        }
        
        $networkDomains = [
            'facebook.com' => 'facebook',
            'twitter.com' => 'twitter',
            'x.com' => 'x',
            'linkedin.com' => 'linkedin',
            'pinterest.com' => 'pinterest',
            'reddit.com' => 'reddit',
            'tumblr.com' => 'tumblr',
            'api.whatsapp.com' => 'whatsapp',
            't.me' => 'telegram',
            'vk.com' => 'vk',
            'mastodon.social' => 'mastodon',
            'threads.net' => 'threads',
            'bsky.app' => 'bluesky'
        ];
        
        return $networkDomains[$domain] ?? null;
    }

    /**
     * Pure function: Generate analytics parameters for share URL
     *
     * @param string $network Network identifier
     * @param array $context Additional context
     * @return array Analytics parameters
     */
    public static function generateAnalyticsParameters(string $network, array $context = []): array
    {
        $parameters = [
            'utm_source' => SecurityUtils::sanitizeKey($network),
            'utm_medium' => 'social',
            'utm_campaign' => 'share_button',
            'utm_content' => $context['content'] ?? 'button'
        ];
        
        // Add custom context parameters
        foreach ($context as $key => $value) {
            $key = SecurityUtils::sanitizeKey($key);
            $value = SecurityUtils::sanitizeTextField((string) $value);
            if ($key && $value) {
                $parameters["utm_{$key}"] = $value;
            }
        }
        
        return $parameters;
    }
}