<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Utils\StringUtils;
use HtmlSocialShare\Svg\SanitizerInterface;

/**
 * Icon registry with enhanced security and pure functions
 * 
 * Manages icon sets, custom icons, and SVG rendering with proper sanitization
 * and caching. Separates pure icon processing from WordPress-specific I/O.
 * 
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class IconRegistry implements IconRegistryInterface
{
    private Settings $settings;
    private ?SanitizerInterface $svgSanitizer;
    private array $loadedIcons = [];
    private string $currentIconset = 'builtin';
    private array $iconCSS = [];
    private array $cache = [];

    public function __construct(Settings $settings, ?SanitizerInterface $svgSanitizer = null)
    {
        $this->settings = $settings;
        $this->svgSanitizer = $svgSanitizer;
        $this->currentIconset = $this->settings->get('iconset', 'builtin');
        $this->loadIcons();
    }

    /**
     * Set the current iconset with validation
     *
     * @param string $iconset Iconset identifier
     * @return void
     * @throws \InvalidArgumentException If iconset is invalid
     */
    public function setIconset(string $iconset): void
    {
        if (!self::isValidIconsetId($iconset)) {
            throw new \InvalidArgumentException("Invalid iconset ID: {$iconset}");
        }

        $this->currentIconset = $iconset;
        $this->settings->set('iconset', $iconset);
        $this->clearCache();
        $this->loadIcons();
    }

    /**
     * Get the current iconset
     *
     * @return string
     */
    public function getCurrentIconset(): string
    {
        return $this->currentIconset;
    }

    /**
     * Load icons from the current iconset
     *
     * @return void
     */
    private function loadIcons(): void
    {
        $this->loadedIcons = [];

        // For builtin iconset, use SVG icons
        if ($this->currentIconset === 'builtin') {
            $this->loadBuiltinIcons();
            return;
        }

        // Load iconsets from assets/iconset directory
        $iconsetData = $this->loadIconsetFromDirectory($this->currentIconset);

        if (!$iconsetData) {
            // Fallback to default_square if current iconset doesn't exist
            $iconsetData = $this->loadIconsetFromDirectory('default_square');
            if (!$iconsetData) {
                // Ultimate fallback to builtin SVG
                $this->loadBuiltinIcons();
                return;
            }
        }

        if ($iconsetData && isset($iconsetData['icons'])) {
            foreach ($iconsetData['icons'] as $network => $iconData) {
                $this->loadedIcons[$network] = $this->renderIcon($network, $iconData);
            }
        }
    }

    /**
     * Load builtin SVG icons
     *
     * @return void
     */
    private function loadBuiltinIcons(): void
    {
        $networks = ['facebook', 'twitter', 'linkedin', 'pinterest', 'email', 'whatsapp', 'telegram', 'reddit', 'tumblr', 'mastodon', 'threads', 'vk', 'bluesky', 'wechat', 'instagram', 'messenger'];

        foreach ($networks as $network) {
            $this->loadedIcons[$network] = $this->renderIcon($network, []);
        }
    }

    /**
     * Load iconset data from the assets/iconset directory
     *
     * @param string $iconsetName
     * @return array|null
     */
    private function loadIconsetFromDirectory(string $iconsetName): ?array
    {
        $iconsetPath = HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/iconset/' . $iconsetName;

        if (!is_dir($iconsetPath)) {
            return null;
        }

        // Map iconset names to network names
        $networkMapping = [
            'facebook.png' => 'facebook',
            'twitter.png' => 'twitter',
            'linkedin.png' => 'linkedin',
            'googlepluse.png' => 'googleplus',
            'bookmark.png' => 'bookmark',
            'pinterest.png' => 'pinterest',
            'mail.png' => 'email',
            'whatsapp.png' => 'whatsapp',
            'telegram.png' => 'telegram',
            'reddit.png' => 'reddit',
            'tumblr.png' => 'tumblr',
            // New network icons
            'mastodon.png' => 'mastodon',
            'threads.png' => 'threads',
            'vk.png' => 'vk',
            'bluesky.png' => 'bluesky',
            'wechat.png' => 'wechat',
            'instagram.png' => 'instagram',
            'messenger.png' => 'messenger'
        ];

        $icons = [];
        $files = scandir($iconsetPath);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'png') {
                $network = $networkMapping[$file] ?? null;
                if ($network) {
                    $icons[$network] = [
                        'image' => $file,
                        'url' => HTML_SOCIAL_SHARE_ICONSET_URL . $iconsetName . '/' . $file
                    ];
                }
            }
        }

        if (empty($icons)) {
            return null;
        }

        return [
            'id' => $iconsetName,
            'name' => ucwords(str_replace(['_', '-'], ' ', $iconsetName)),
            'icons' => $icons
        ];
    }

    /**
     * Initialize the builtin iconset if it doesn't exist
     *
     * @return void
     */
    private function initializeBuiltinIconset(): void
    {
        $builtinSet = [
            'id' => 'builtin',
            'name' => 'Default Icons',
            'version' => '1.0.0',
            'license' => 'MIT',
            'author' => 'HTML Social Share',
            'icons' => [
                'facebook' => 'facebook_icon',
                'twitter' => 'twitter_icon',
                'linkedin' => 'linkedin_icon',
                'pinterest' => 'pinterest_icon',
                'email' => 'email_icon',
                'whatsapp' => 'whatsapp_icon',
                'telegram' => 'telegram_icon',
                'reddit' => 'reddit_icon',
                'tumblr' => 'tumblr_icon'
            ],
            'meta' => [
                'description' => 'Built-in social media icons'
            ]
        ];

        $this->settings->setIcon('builtin', $builtinSet, 'sets');
    }

    /**
     * Render an icon from its reference
     *
     * @param string $network
     * @param array $iconData
     * @return string
     */
    private function renderIcon(string $network, array $iconData): string
    {
        // For iconsets loaded from directory, return CSS class that will use background-image
        if (isset($iconData['url'])) {
            // Generate a unique CSS class for this icon
            $cssClass = 'hss-icon-' . $network;

            // Store CSS for later output
            $this->enqueueIconCSS($cssClass, $iconData['url']);

            return sprintf(
                '<span class="hss-icon %s" aria-hidden="true"></span>',
                esc_attr($cssClass)
            );
        }

        // Fallback to builtin SVG
        $svgContent = $this->getBuiltinSvg($network);

        if ($svgContent) {
            return sprintf(
                '<svg class="hss-icon hss-icon-%s" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">%s</svg>',
                esc_attr($network),
                $svgContent
            );
        }

        // Fallback to dashicon
        return sprintf(
            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
            esc_attr($network === 'email' ? 'email' : 'share')
        );
    }

    /**
     * Enqueue CSS for an icon
     *
     * @param string $cssClass
     * @param string $imageUrl
     * @return void
     */
    private function enqueueIconCSS(string $cssClass, string $imageUrl): void
    {
        $this->iconCSS[$cssClass] = $imageUrl;
    }

    /**
     * Get all enqueued icon CSS
     *
     * @return array
     */
    public function getIconCSS(): array
    {
        return $this->iconCSS;
    }

    /**
     * Get builtin SVG content for a network
     *
     * @param string $network
     * @return string|null
     */
    private function getBuiltinSvg(string $network): ?string
    {
        $svgs = [
            'facebook' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
            'twitter' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
            'linkedin' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
            'pinterest' => '<path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.749.097.118.112.221.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.747-1.378 0 0-.599 2.282-.744 2.84-.282 1.084-1.064 2.456-1.549 3.235C9.584 23.815 10.77 24 12.017 24c6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001.012.017z"/>',
            'email' => '<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>',
            // New simple placeholders for additional networks to avoid dashicon fallback
            'mastodon' => '<circle cx="12" cy="12" r="10"/><path d="M7 12c0-2 2-4 5-4s5 2 5 4v1c0 1-1 2-2 2H9c-1 0-2-1-2-2v-1z" fill="#fff"/>',
            'threads' => '<circle cx="12" cy="12" r="10"/><path d="M8 9h8v2H8zM8 12h6v2H8z" fill="#fff"/>',
            'vk' => '<circle cx="12" cy="12" r="10"/><path d="M7 8c1 2 2 4 4 4s2-2 3-3c1 0 1 3 2 3 0 0 1-4 3-5v8c-2 1-3 2-6 2s-5-3-6-7z" fill="#fff"/>',
            'bluesky' => '<circle cx="12" cy="12" r="10"/><path d="M8 8h8v8H8z" fill="#fff"/>',
            'wechat' => '<circle cx="12" cy="12" r="10"/><path d="M9 10a1 1 0 11.001-2.001A1 1 0 009 10zm6 0a1 1 0 11.001-2.001A1 1 0 0115 10zM7 14c1 1 2 1 5 1s4 0 5-1c-1 1-2 2-5 2s-4-1-5-2z" fill="#fff"/>',
            'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4" ry="4"/><circle cx="12" cy="12" r="3" fill="#fff"/>',
            'messenger' => '<circle cx="12" cy="12" r="10"/><path d="M7 12l3-3 2 3 3-3-4 6-4-3z" fill="#fff"/>'
        ];

        return $svgs[$network] ?? null;
    }

    /**
     * Register an icon SVG by key
     *
     * @param string $key
     * @param string $svg
     * @return void
     */
    public function registerIcon(string $key, string $svg): void
    {
        // Sanitize the SVG
        $sanitizedSvg = $this->sanitizeSvg($svg);
        $this->loadedIcons[$key] = $sanitizedSvg;
    }

    /**
     * Sanitize SVG content for security
     *
     * @param string $svg
     * @return string
     */
    private function sanitizeSvg(string $svg): string
    {
        // Basic sanitization - remove script tags and event handlers
        $svg = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $svg);
        $svg = preg_replace('/on\w+="[^"]*"/i', '', $svg);
        $svg = preg_replace('/on\w+=\'[^\']*\'/i', '', $svg);

        return $svg;
    }

    /**
     * Get the icon SVG for a key
     *
     * @param string $key
     * @return string|null
     */
    public function getIcon(string $key): ?string
    {
        return $this->loadedIcons[$key] ?? null;
    }

    /**
     * Check if an icon exists
     *
     * @param string $key
     * @return bool
     */
    public function hasIcon(string $key): bool
    {
        return array_key_exists($key, $this->loadedIcons);
    }

    /**
     * List registered icon keys
     *
     * @return string[]
     */
    public function listIcons(): array
    {
        return array_keys($this->loadedIcons);
    }

    /**
     * Add a custom icon
     *
     * @param string $key
     * @param string $svg
     * @param array $meta
     * @return void
     */
    public function addCustomIcon(string $key, string $svg, array $meta = []): void
    {
        $sanitizedSvg = $this->sanitizeSvg($svg);

        $iconData = [
            'id' => $key,
            'svg' => $sanitizedSvg,
            'meta' => $meta,
            'created' => current_time('mysql')
        ];

        $this->settings->setIcon($key, $iconData, 'custom');
        $this->loadedIcons[$key] = $sanitizedSvg;
    }

    /**
     * Remove a custom icon
     *
     * @param string $key
     * @return void
     */
    public function removeCustomIcon(string $key): void
    {
        $this->settings->deleteIcon($key, 'custom');
        unset($this->loadedIcons[$key]);
    }

    /**
     * Get all available iconsets
     *
     * @return array
     */
    public function getAvailableIconsets(): array
    {
        $iconsets = [];

        // Scan assets/iconset directory for available iconsets
        $iconsetDir = HTML_SOCIAL_SHARE_ICONSET_DIR;
        if (is_dir($iconsetDir)) {
            $dirs = scandir($iconsetDir);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($iconsetDir . '/' . $dir)) {
                    $label = ucfirst(str_replace(['_', '-'], ' ', $dir));
                    $iconsets[$dir] = [
                        'label' => $label,
                        'description' => 'Icon set: ' . $label
                    ];
                }
            }
        }

        // If no iconsets found, provide a default
        if (empty($iconsets)) {
            $iconsets['default'] = [
                'label' => 'Default',
                'description' => 'Default icon set'
            ];
        }

        return $iconsets;
    }
}
