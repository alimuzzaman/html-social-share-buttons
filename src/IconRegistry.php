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
    private string $currentIconset = 'default_square';
    private array $iconCSS = [];
    private array $cache = [];

    public function __construct(Settings $settings, ?SanitizerInterface $svgSanitizer = null)
    {
        $this->settings = $settings;
        $this->svgSanitizer = $svgSanitizer;
        $this->currentIconset = $this->settings->get('iconset', 'default_square');
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

        // Load iconsets from assets/iconset directory
        $iconsetData = $this->loadIconsetFromDirectory($this->currentIconset);

        if (!$iconsetData) {
            // Fallback to default_square if current iconset doesn't exist
            $iconsetData = $this->loadIconsetFromDirectory('default_square');
        }

        if ($iconsetData && isset($iconsetData['icons'])) {
            foreach ($iconsetData['icons'] as $network => $iconData) {
                $this->loadedIcons[$network] = $this->renderIcon($network, $iconData);
            }
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

        // Defensive cap: avoid scanning extremely large directories
        $maxFiles = 200;
        if (is_array($files) && count($files) > $maxFiles) {
            error_log('HSS IconRegistry: Large iconset directory detected (' . count($files) . " files); limiting processed files to {$maxFiles}");
            // Keep only first $maxFiles (scandir returns sorted list; maintain deterministic behavior)
            $files = array_slice($files, 0, $maxFiles);
        }

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

        // No SVG fallback allowed - return dashicon
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

    /**
     * Validate iconset ID (pure function)
     *
     * @param string $iconsetId
     * @return bool
     */
    private static function isValidIconsetId(string $iconsetId): bool
    {
        // Basic validation - alphanumeric with underscores and hyphens
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $iconsetId)) {
            return false;
        }

        // Length check
        if (strlen($iconsetId) < 1 || strlen($iconsetId) > 50) {
            return false;
        }

        // Reserved names
        $reserved = ['..', '.', 'admin', 'wp-admin', 'wp-content'];
        if (in_array($iconsetId, $reserved, true)) {
            return false;
        }

        return true;
    }

    /**
     * Clear internal cache
     *
     * @return void
     */
    private function clearCache(): void
    {
        $this->cache = [];
        $this->iconCSS = [];
    }
}
