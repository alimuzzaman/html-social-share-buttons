<?php
namespace HtmlSocialShare\Integrations\WooCommerce;

use HtmlSocialShare\ShareRendererInterface;
use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Renderers\RenderUtils;
use HtmlSocialShare\Renderers\ShareUrlBuilder;

/**
 * WooCommerce Share Buttons Integration with enhanced security
 * 
 * Provides social sharing functionality for WooCommerce products with proper
 * input validation, output escaping, and pure function separation.
 * 
 * @since 3.0.0
 */
class ShareButtonsIntegration
{
    /**
     * Share renderer instance
     * 
     * @var ShareRendererInterface
     */
    private $shareRenderer;

    /**
     * URL builder instance
     * 
     * @var ShareUrlBuilder
     */
    private $urlBuilder;

    /**
     * Integration configuration
     * 
     * @var array
     */
    private $config;

    /**
     * Constructor with dependency injection
     * 
     * @param ShareRendererInterface $shareRenderer Share renderer
     * @param ShareUrlBuilder|null $urlBuilder URL builder (optional)
     * @param array $config Integration configuration
     */
    public function __construct(
        ShareRendererInterface $shareRenderer,
        ?ShareUrlBuilder $urlBuilder = null,
        array $config = []
    ) {
        $this->shareRenderer = $shareRenderer;
        $this->urlBuilder = $urlBuilder ?: ShareUrlBuilder::createWithWordPressIntegration();
        $this->config = $this->sanitizeConfig($config);
    }

    /**
     * Register integration with WooCommerce
     * 
     * @param ShareRendererInterface $shareRenderer Share renderer
     * @param array $config Integration configuration
     * @return void
     */
    public static function register(ShareRendererInterface $shareRenderer, array $config = [])
    {
        // Check if WooCommerce is available
        if (!self::isWooCommerceActive()) {
            return;
        }

        try {
            $instance = new self($shareRenderer, null, $config);
            $instance->init();
        } catch (\Throwable $e) {
            error_log('HTML Social Share: WooCommerce integration error - ' . $e->getMessage());
        }
    }

    /**
     * Initialize integration hooks
     * 
     * @return void
     */
    public function init()
    {
        if (!self::isWooCommerceActive()) {
            return;
        }

        // Hook into single product page
        add_action('woocommerce_single_product_summary', [$this, 'addShareButtonsToProduct'], 25);

        // Hook into shop/archive pages (optional)
        if ($this->isArchiveSharingEnabled()) {
            add_action('woocommerce_after_shop_loop_item', [$this, 'addShareButtonsToArchive'], 15);
        }

        // Add share buttons to cart/checkout if enabled
        if ($this->isCartSharingEnabled()) {
            add_action('woocommerce_proceed_to_checkout', [$this, 'addShareButtonsToCart'], 10);
        }

        // Add CSS for styling
        add_action('wp_head', [$this, 'addStyles']);

        // Add JavaScript for tracking
        add_action('wp_footer', [$this, 'addScripts']);
    }

    /**
     * Add share buttons to single product page
     * 
     * @return void
     */
    public function addShareButtonsToProduct()
    {
        try {
            global $product;

            if (!$this->isValidProduct($product)) {
                return;
            }

            $productData = $this->extractProductData($product);
            $this->renderShareButtons($productData, 'product');

        } catch (\Throwable $e) {
            error_log('HTML Social Share: Product sharing error - ' . $e->getMessage());
            $this->renderErrorState('product');
        }
    }

    /**
     * Add share buttons to archive/shop page
     * 
     * @return void
     */
    public function addShareButtonsToArchive()
    {
        try {
            global $product;

            if (!$this->isValidProduct($product) || !$this->isArchiveSharingEnabled()) {
                return;
            }

            $productData = $this->extractProductData($product);
            $this->renderShareButtons($productData, 'archive');

        } catch (\Throwable $e) {
            error_log('HTML Social Share: Archive sharing error - ' . $e->getMessage());
        }
    }

    /**
     * Add share buttons to cart page
     * 
     * @return void
     */
    public function addShareButtonsToCart()
    {
        try {
            if (!WC()->cart || WC()->cart->is_empty()) {
                return;
            }

            // Generate generic cart sharing data
            $cartData = [
                'title' => __('Check out these great products!', 'html-social-share'),
                'url' => wc_get_cart_url(),
                'image' => '',
                'description' => $this->generateCartDescription(),
            ];

            $this->renderShareButtons($cartData, 'cart');

        } catch (\Throwable $e) {
            error_log('HTML Social Share: Cart sharing error - ' . $e->getMessage());
        }
    }

    /**
     * Render share buttons with proper escaping
     * 
     * @param array $data Sharing data
     * @param string $context Sharing context (product, archive, cart)
     * @return void
     */
    private function renderShareButtons(array $data, string $context)
    {
        $config = $this->getContextConfig($context);
        $networks = $this->getEnabledNetworks($context);

        if (empty($networks)) {
            return;
        }

        $containerId = RenderUtils::generateUniqueId('woocommerce-share', $context);
        $containerClass = RenderUtils::generateButtonClasses('container', ['woocommerce', $context]);

        echo '<div id="' . SecurityUtils::escapeAttribute($containerId) . '" class="' . SecurityUtils::escapeAttribute($containerClass) . '">';

        // Add title if configured
        if (!empty($config['show_title'])) {
            $title = $config['title'] ?? $this->getDefaultTitle($context);
            echo '<h4 class="hssb-woo-title">' . SecurityUtils::escapeHtml($title) . '</h4>';
        }

        echo '<div class="hssb-woo-buttons">';

        foreach ($networks as $network) {
            $this->renderShareButton($network, $data, $config);
        }

        echo '</div>';

        // Output icon CSS
        $iconCSS = $this->shareRenderer->getIconCSS();
        if (!empty($iconCSS)) {
            $this->outputIconCSS($iconCSS);
        }

        echo '</div>';
    }

    /**
     * Output icon CSS
     *
     * @param array $iconCSS Icon CSS data
     * @return void
     */
    private function outputIconCSS(array $iconCSS): void
    {
        echo '<style class="hss-iconset-inline">';
        echo '.hss-icon{display:inline-block;width:24px;height:24px;background-size:contain;background-repeat:no-repeat;background-position:center;vertical-align:middle;} ';
        foreach ($iconCSS as $cssClass => $imageUrl) {
            printf(
                '.%s{background-image:url(%s);} ',
                esc_attr($cssClass),
                esc_url($imageUrl)
            );
        }
        echo '</style>';
    }

    /**
     * Render individual share button
     * 
     * @param string $network Network identifier
     * @param array $data Sharing data
     * @param array $config Button configuration
     * @return void
     */
    private function renderShareButton(string $network, array $data, array $config)
    {
        try {
            $profile = $this->buildProfile($network, $data);
            $shareUrl = $this->urlBuilder->buildUrl($network, $profile, $data['url'], $data['title']);
            
            $buttonClass = RenderUtils::generateButtonClasses($network, ['woocommerce', $config['style']]);
            $attributes = RenderUtils::generateA11yAttributes($network);

            // Add WooCommerce-specific attributes
            $attributes['data-product-context'] = 'woocommerce';
            $attributes['data-network'] = $network;

            if (!empty($config['target_blank'])) {
                $attributes['target'] = '_blank';
            }
            if (!empty($config['nofollow'])) {
                $attributes['rel'] = 'nofollow noopener';
            }

            $attributesString = RenderUtils::buildAttributes(array_merge($attributes, [
                'href' => $shareUrl,
                'class' => $buttonClass
            ]));

            echo '<a ' . $attributesString . '>';

            // Render using share renderer if available
            if (method_exists($this->shareRenderer, 'renderIcon')) {
                echo $this->shareRenderer->renderIcon($network);
            } else {
                echo '<span class="hssb-icon">' . SecurityUtils::escapeHtml(ucfirst($network)) . '</span>';
            }

            if (!empty($config['show_labels'])) {
                echo '<span class="hssb-label">' . SecurityUtils::escapeHtml(ucfirst($network)) . '</span>';
            }

            echo '</a>';

        } catch (\Throwable $e) {
            error_log("HTML Social Share: Error rendering {$network} button - " . $e->getMessage());
        }
    }

    /**
     * Add CSS styles for WooCommerce integration
     * 
     * @return void
     */
    public function addStyles()
    {
        if (!$this->shouldLoadAssets()) {
            return;
        }

        $css = $this->generateCSS();
        if ($css) {
            echo '<style id="hssb-woocommerce-styles">' . $css . '</style>';
        }
    }

    /**
     * Add JavaScript for tracking and functionality
     * 
     * @return void
     */
    public function addScripts()
    {
        if (!$this->shouldLoadAssets()) {
            return;
        }

        $script = $this->generateJavaScript();
        if ($script) {
            echo '<script id="hssb-woocommerce-script">' . $script . '</script>';
        }
    }

    /**
     * Render error state for debugging
     * 
     * @param string $context Error context
     * @return void
     */
    private function renderErrorState(string $context)
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo '<div class="hssb-error" style="color: #dc3232; font-size: 12px;">';
            echo SecurityUtils::escapeHtml(__('Share buttons temporarily unavailable', 'html-social-share'));
            echo '</div>';
        }
    }

    /**
     * Sanitize integration configuration
     * 
     * @param array $config Raw configuration
     * @return array Sanitized configuration
     */
    private function sanitizeConfig(array $config): array
    {
        $defaults = self::getDefaultConfig();
        $sanitized = ArrayUtils::deepMerge($defaults, $config);

        // Sanitize specific fields
        if (isset($sanitized['networks'])) {
            $sanitized['networks'] = array_filter(
                array_map([SecurityUtils::class, 'sanitizeKey'], (array) $sanitized['networks'])
            );
        }

        return $sanitized;
    }

    // ===== PURE FUNCTIONS (NO SIDE EFFECTS) =====

    /**
     * Pure function: Check if WooCommerce is active
     * 
     * @return bool True if WooCommerce is active
     */
    public static function isWooCommerceActive(): bool
    {
        return class_exists('WooCommerce') && function_exists('WC');
    }

    /**
     * Pure function: Validate WooCommerce product object
     * 
     * @param mixed $product Product object to validate
     * @return bool True if valid product
     */
    public static function isValidProduct($product): bool
    {
        return $product instanceof \WC_Product && $product->get_id() > 0;
    }

    /**
     * Pure function: Extract product data for sharing
     * 
     * @param \WC_Product $product WooCommerce product
     * @return array Sanitized product data
     */
    public static function extractProductData(\WC_Product $product): array
    {
        return [
            'title' => SecurityUtils::sanitizeTextField($product->get_title()),
            'url' => SecurityUtils::sanitizeUrl(get_permalink($product->get_id())),
            'image' => self::getProductImageUrl($product),
            'description' => SecurityUtils::sanitizeTextField($product->get_short_description()),
            'price' => $product->get_price_html(),
            'sku' => SecurityUtils::sanitizeTextField($product->get_sku()),
        ];
    }

    /**
     * Pure function: Get product image URL
     * 
     * @param \WC_Product $product WooCommerce product
     * @return string Sanitized image URL
     */
    public static function getProductImageUrl(\WC_Product $product): string
    {
        $imageId = $product->get_image_id();
        if ($imageId) {
            $imageUrl = wp_get_attachment_image_url($imageId, 'full');
            return SecurityUtils::sanitizeUrl($imageUrl) ?: '';
        }
        return '';
    }

    /**
     * Pure function: Get default integration configuration
     * 
     * @return array Default configuration
     */
    public static function getDefaultConfig(): array
    {
        return [
            'networks' => ['facebook', 'twitter', 'linkedin', 'pinterest'],
            'show_on_product' => true,
            'show_on_archive' => false,
            'show_on_cart' => false,
            'show_title' => true,
            'show_labels' => false,
            'style' => 'filled',
            'target_blank' => true,
            'nofollow' => true,
            'position' => 'after_summary',
        ];
    }

    /**
     * Pure function: Get context-specific configuration
     * 
     * @param string $context Sharing context
     * @return array Context configuration
     */
    private function getContextConfig(string $context): array
    {
        $baseConfig = $this->config;
        
        $contextDefaults = [
            'product' => [
                'title' => __('Share this product', 'html-social-share'),
                'style' => 'filled',
                'show_title' => true,
                'show_labels' => false,
            ],
            'archive' => [
                'title' => __('Share', 'html-social-share'),
                'style' => 'minimal',
                'show_title' => false,
                'show_labels' => false,
            ],
            'cart' => [
                'title' => __('Share your cart', 'html-social-share'),
                'style' => 'outline',
                'show_title' => true,
                'show_labels' => true,
            ]
        ];

        return ArrayUtils::deepMerge($baseConfig, $contextDefaults[$context] ?? []);
    }

    /**
     * Get enabled networks for context
     * 
     * @param string $context Sharing context
     * @return array Enabled networks
     */
    private function getEnabledNetworks(string $context): array
    {
        $options = get_option('html_social_share', []);
        $networks = $options['networks'] ?? $this->config['networks'] ?? ['facebook', 'twitter', 'linkedin'];

        if (!is_array($networks)) {
            $networks = ['facebook', 'twitter', 'linkedin'];
        }

        // Filter networks based on context
        return array_filter($networks, function($network) use ($context) {
            return SecurityUtils::sanitizeKey($network) !== '';
        });
    }

    /**
     * Check if archive sharing is enabled
     * 
     * @return bool True if enabled
     */
    private function isArchiveSharingEnabled(): bool
    {
        $options = get_option('html_social_share', []);
        return !empty($options['woocommerce_archive']) || !empty($this->config['show_on_archive']);
    }

    /**
     * Check if cart sharing is enabled
     * 
     * @return bool True if enabled
     */
    private function isCartSharingEnabled(): bool
    {
        $options = get_option('html_social_share', []);
        return !empty($options['woocommerce_cart']) || !empty($this->config['show_on_cart']);
    }

    /**
     * Build profile data for network sharing
     * 
     * @param string $network Network identifier
     * @param array $data Sharing data
     * @return array Profile configuration
     */
    private function buildProfile(string $network, array $data): array
    {
        return [
            'network' => $network,
            'title' => $data['title'],
            'url' => $data['url'],
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'hashtags' => $this->config['hashtags'] ?? '',
            'via' => $this->config['via'] ?? '',
        ];
    }

    /**
     * Get default title for context
     * 
     * @param string $context Sharing context
     * @return string Default title
     */
    private function getDefaultTitle(string $context): string
    {
        $titles = [
            'product' => __('Share this product', 'html-social-share'),
            'archive' => __('Share', 'html-social-share'),
            'cart' => __('Share your cart', 'html-social-share'),
        ];

        return $titles[$context] ?? __('Share', 'html-social-share');
    }

    /**
     * Generate cart description from items
     * 
     * @return string Cart description
     */
    private function generateCartDescription(): string
    {
        if (!WC()->cart) {
            return '';
        }

        $itemCount = WC()->cart->get_cart_contents_count();
        return sprintf(
            /* translators: %d: number of items */
            __('Shopping cart with %d items', 'html-social-share'),
            $itemCount
        );
    }

    /**
     * Check if assets should be loaded on current page
     * 
     * @return bool True if should load
     */
    private function shouldLoadAssets(): bool
    {
        return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
    }

    /**
     * Generate CSS for WooCommerce integration
     * 
     * @return string CSS rules
     */
    private function generateCSS(): string
    {
        return '
            .hssb-woocommerce-container { margin: 20px 0; clear: both; }
            .hssb-woo-title { margin-bottom: 10px; font-size: 16px; }
            .hssb-woo-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
            .hssb-woo-buttons .hssb-share { padding: 8px 12px; text-decoration: none; border-radius: 4px; }
            .hssb-woocommerce-archive .hssb-woo-buttons { justify-content: center; gap: 4px; }
        ';
    }

    /**
     * Generate JavaScript for tracking
     * 
     * @return string JavaScript code
     */
    private function generateJavaScript(): string
    {
        return '
            (function() {
                document.addEventListener("click", function(e) {
                    if (e.target.closest(".hssb-woo-buttons a")) {
                        var link = e.target.closest("a");
                        var network = link.dataset.network;
                        if (network && typeof gtag === "function") {
                            gtag("event", "share", {
                                event_category: "woocommerce",
                                event_label: network,
                                value: 1
                            });
                        }
                    }
                });
            })();
        ';
    }
}