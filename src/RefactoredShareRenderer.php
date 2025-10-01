<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Renderers\ShareButtonRenderer;
use HtmlSocialShare\Renderers\ShareUrlBuilder;

/**
 * Refactored Share Renderer implementing SOLID principles
 *
 * This class follows sponsibility Principle by delegating
 * specific concerns to specialized classes:
 * - URL building -> ShareUrlBuilder
 * - HTML rendering -> ShareButtonRenderer
 * - Icon management -> IconRegistryInterface
 * - Settings management -> SettingsInterface
 * - Share count management -> Share count manager
 *
 * @since 3.0.0
 */
class RefactoredShareRenderer implements ShareRendererInterface
{
    /**
     * Icon registry for managing icons
     *
     * @var IconRegistryInterface
     */
    private $iconRegistry;

    /**
     * Settings manager
     *
     * @var SettingsInterface|null
     */
    private $settings;

    /**
     * Share count manager
     *
     * @var mixed|null
     */
    private $shareCountManager;

    /**
     * Button renderer
     *
     * @var ShareButtonRenderer
     */
    private $buttonRenderer;

    /**
     * URL builder
     *
     * @var ShareUrlBuilder
     */
    private $urlBuilder;

    /**
     * Constructor with dependency injection
     *
     * @param IconRegistryInterface $iconRegistry Icon registry
     * @param SettingsInterface|null $settings Settings manager (optional)
     * @param ShareButtonRenderer|null $buttonRenderer Button renderer (optional)
     * @param ShareUrlBuilder|null $urlBuilder URL builder (optional)
     */
    public function __construct(
        IconRegistryInterface $iconRegistry,
        ?SettingsInterface $settings = null,
        ?ShareButtonRenderer $buttonRenderer = null,
        ?ShareUrlBuilder $urlBuilder = null
    ) {
        $this->iconRegistry = $iconRegistry;
        $this->settings = $settings;

        // Use provided instances or create defaults
        $this->urlBuilder = $urlBuilder ?: ShareUrlBuilder::createWithWordPressIntegration();
        $this->buttonRenderer = $buttonRenderer ?: new ShareButtonRenderer($this->urlBuilder);
    }

    /**
     * Set iconset on icon registry
     *
     * @param string $iconset Iconset identifier
     */
    public function setIconset(string $iconset): void
    {
        if (method_exists($this->iconRegistry, 'setIconset')) {
            $this->iconRegistry->setIconset($iconset);
        }
    }

    /**
     * Get CSS for all icons from the icon registry
     *
     * @return array
     */
    public function getIconCSS(): array
    {
        return $this->iconRegistry->getIconCSS();
    }



    /**
     * Render share button for network and profile
     *
     * @param string $network Network key
     * @param array $profile Profile configuration
     * @param string $url URL to share (optional)
     * @param string $title Title to share (optional)
     * @return string Rendered HTML
     */
    public function render(string $network, array $profile, string $url = '#', string $title = ''): string
    {
        // Build configuration for renderer
        $config = $this->buildRenderConfig($network, $profile, $url, $title);

        // Special handling for WeChat
        if ($network === 'wechat') {
            return $this->buttonRenderer->renderWeChatButton($config);
        }

        // Render standard button
        return $this->buttonRenderer->renderButton($config);
    }

    /**
     * Build render configuration from inputs
     *
     * @param string $network Network key
     * @param array $profile Profile configuration
     * @param string $url URL to share
     * @param string $title Title to share
     * @return array Render configuration
     */
    protected function buildRenderConfig(string $network, array $profile, string $url, string $title): array
    {
        $config = [
            'network' => $network,
            'profile' => $profile,
            'url' => ($url !== '#') ? $url : $this->getCurrentUrl(),
            'title' => !empty($title) ? $title : $this->getCurrentTitle(),
            'icon' => $this->getIcon($network),
            'label' => $this->getNetworkLabel($network),
            'handle' => $profile['handle'] ?? '',
            'count' => $this->getShareCount($network),
            'show_label' => true,
            'show_count' => $this->shouldShowShareCount(),
            'css_classes' => [],
            'attributes' => []
        ];

        // Add custom attributes from profile
        if (!empty($profile['attributes'])) {
            $config['attributes'] = array_merge($config['attributes'], $profile['attributes']);
        }

        return $config;
    }

    /**
     * Get icon HTML for network
     *
     * @param string $network Network key
     * @return string Icon HTML
     */
    protected function getIcon(string $network): string
    {
        $icon = $this->iconRegistry->getIcon($network);

        if (!$icon) {
            error_log("RefactoredShareRenderer: No icon found for network '{$network}', using fallback");
            return '<span class="dashicons dashicons-share"></span>';
        }

        return $icon;
    }

    /**
     * Get human-readable label for network
     *
     * @param string $network Network key
     * @return string Network label
     */
    protected function getNetworkLabel(string $network): string
    {
        $labels = [
            'facebook' => 'Facebook',
            'twitter' => 'X',
            'linkedin' => 'LinkedIn',
            'pinterest' => 'Pinterest',
            'reddit' => 'Reddit',
            'tumblr' => 'Tumblr',
            'whatsapp' => 'WhatsApp',
            'telegram' => 'Telegram',
            'email' => 'Email',
            'wechat' => 'WeChat',
            'vk' => 'VKontakte',
        ];

        return $labels[$network] ?? ucfirst($network);
    }

    /**
     * Get share count for network
     *
     * @param string $network Network key
     * @return int Share count
     */
    protected function getShareCount(string $network): int
    {
        if (!$this->shareCountManager || !$this->shouldShowShareCount()) {
            return 0;
        }

        try {
            $postId = $this->getCurrentPostId();
            if ($postId <= 0) {
                return 0;
            }

            if (method_exists($this->shareCountManager, 'getCountForPostNetwork')) {
                return (int) $this->shareCountManager->getCountForPostNetwork($postId, $network);
            }
        } catch (\Throwable $e) {
            error_log("RefactoredShareRenderer: Failed to get share count - " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Check if share counts should be displayed
     *
     * @return bool True if share counts should be shown
     */
    protected function shouldShowShareCount(): bool
    {
        if (!$this->settings) {
            return false;
        }

        return (bool) $this->settings->get('share_counts_enabled', false);
    }

    /**
     * Get current URL for sharing
     *
     * @return string Current URL
     */
    protected function getCurrentUrl(): string
    {
        // Try WordPress function first
        if (function_exists('get_permalink')) {
            $permalink = get_permalink();
            if ($permalink) {
                return $permalink;
            }
        }

        // Fallback to current URL detection
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return '#';
    }

    /**
     * Get current title for sharing
     *
     * @return string Current title
     */
    protected function getCurrentTitle(): string
    {
        // Try WordPress function first
        if (function_exists('get_the_title')) {
            $title = get_the_title();
            if ($title) {
                return $title;
            }
        }

        // Fallback to page title
        if (function_exists('wp_get_document_title')) {
            return wp_get_document_title();
        }

        return '';
    }

    /**
     * Get current post ID
     *
     * @return int Post ID or 0 if not available
     */
    protected function getCurrentPostId(): int
    {
        if (function_exists('get_the_ID')) {
            return (int) (get_the_ID() ?: 0);
        }

        return 0;
    }

    /**
     * Factory method to create renderer with minimal dependencies
     *
     * @param IconRegistryInterface $iconRegistry Icon registry
     * @return RefactoredShareRenderer
     */
    public static function createMinimal(IconRegistryInterface $iconRegistry): self
    {
        return new self($iconRegistry);
    }

    /**
     * Factory method to create renderer with full WordPress integration
     *
     * @param IconRegistryInterface $iconRegistry Icon registry
     * @param SettingsInterface $settings Settings manager
     * @param mixed $shareCountManager Share count manager
     * @return RefactoredShareRenderer
     */
    public static function createWithWordPress(
        IconRegistryInterface $iconRegistry,
        SettingsInterface $settings,
        $shareCountManager = null
    ): self {
        return new self(
            $iconRegistry,
            $settings,
            $shareCountManager,
            ShareButtonRenderer::createWithWordPressIntegration(),
            ShareUrlBuilder::createWithWordPressIntegration()
        );
    }
}