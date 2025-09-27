<?php
namespace HtmlSocialShare\Integrations\WooCommerce;

use HtmlSocialShare\ShareRendererInterface;

class ShareButtonsIntegration
{
    private $shareRenderer;

    public function __construct($shareRenderer)
    {
        $this->shareRenderer = $shareRenderer;
    }

    public static function register($shareRenderer)
    {
        if (!class_exists('WooCommerce')) {
            return;
        }

        $instance = new self($shareRenderer);
        $instance->init();
    }

    public function init()
    {
        // Hook into single product page
        add_action('woocommerce_single_product_summary', [$this, 'addShareButtonsToProduct'], 25);

        // Hook into shop/archive pages (optional)
        add_action('woocommerce_after_shop_loop_item', [$this, 'addShareButtonsToArchive'], 15);
    }

    public function addShareButtonsToProduct()
    {
        global $product;

        if (!$product) {
            return;
        }

        $productTitle = $product->get_title();
        $productUrl = get_permalink($product->get_id());
        $productImage = $this->getProductImage($product);

        // Generate share buttons
        $networks = $this->getEnabledNetworks();
        $iconset = $this->getIconset();

        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($iconset);
        }

        echo '<div class="html-social-share-woocommerce-product" style="margin-top: 20px; clear: both;">';
        echo '<h4>' . esc_html__('Share this product', 'html-social-share') . '</h4>';
        echo '<div class="share-buttons" style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px;">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example',
                'network' => $network,
                'title' => $productTitle,
                'url' => $productUrl,
                'image' => $productImage,
            ];

            $buttonHtml = $this->shareRenderer->render($network, $profile);
            echo $buttonHtml;
        }

        echo '</div></div>';
    }

    public function addShareButtonsToArchive()
    {
        global $product;

        if (!$product) {
            return;
        }

        // Only show on archive pages if enabled in settings
        if (!$this->isArchiveSharingEnabled()) {
            return;
        }

        $productTitle = $product->get_title();
        $productUrl = get_permalink($product->get_id());

        // Generate share buttons (simplified for archive)
        $networks = $this->getEnabledNetworks();
        $iconset = $this->getIconset();

        if (method_exists($this->shareRenderer, 'setIconset')) {
            $this->shareRenderer->setIconset($iconset);
        }

        echo '<div class="html-social-share-woocommerce-archive" style="margin-top: 10px;">';
        echo '<div class="share-buttons" style="display: flex; gap: 4px; flex-wrap: wrap; justify-content: center;">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example',
                'network' => $network,
                'title' => $productTitle,
                'url' => $productUrl,
            ];

            $buttonHtml = $this->shareRenderer->render($network, $profile);
            echo $buttonHtml;
        }

        echo '</div></div>';
    }

    private function getProductImage($product)
    {
        $imageId = $product->get_image_id();
        if ($imageId) {
            return wp_get_attachment_image_url($imageId, 'full');
        }
        return '';
    }

    private function getEnabledNetworks()
    {
        // Get from plugin settings, fallback to defaults
        $options = get_option('html_social_share', []);
        $networks = isset($options['networks']) ? $options['networks'] : ['facebook', 'twitter', 'linkedin'];

        if (!is_array($networks)) {
            $networks = ['facebook', 'twitter', 'linkedin'];
        }

        return $networks;
    }

    private function getIconset()
    {
        // Get from plugin settings, fallback to default
        $options = get_option('html_social_share', []);
        return isset($options['iconset']) ? $options['iconset'] : 'default_square';
    }

    private function isArchiveSharingEnabled()
    {
        // Get from plugin settings
        $options = get_option('html_social_share', []);
        return isset($options['woocommerce_archive']) ? (bool) $options['woocommerce_archive'] : false;
    }
}