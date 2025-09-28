<?php
namespace HtmlSocialShare\Renderers;

/**
 * Share Button HTML Renderer
 * 
 * Follows Single Responsibility Principle by focusing only on
 * rendering HTML for share buttons.
 * 
 * @since 3.0.0
 */
class ShareButtonRenderer
{
    /**
     * URL builder instance
     * 
     * @var ShareUrlBuilder
     */
    private $urlBuilder;

    /**
     * Constructor
     * 
     * @param ShareUrlBuilder $urlBuilder URL builder instance
     */
    public function __construct(ShareUrlBuilder $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Render a standard share button
     * 
     * @param array $config Button configuration
     * @return string HTML output
     */
    public function renderButton(array $config): string
    {
        $config = $this->normalizeConfig($config);

        // Generate share URL
        $shareUrl = $this->urlBuilder->buildUrl(
            $config['network'],
            $config['profile'],
            $config['url'],
            $config['title']
        );

        // Build button attributes
        $attributes = $this->buildButtonAttributes($config, $shareUrl);

        // Build button content
        $content = $this->buildButtonContent($config);

        return sprintf('<a %s>%s</a>', 
            RenderUtils::buildAttributes($attributes), 
            $content
        );
    }

    /**
     * Render WeChat QR button (special case)
     * 
     * @param array $config Button configuration
     * @return string HTML output
     */
    public function renderWeChatButton(array $config): string
    {
        $config = $this->normalizeConfig($config);
        
        $shareUrl = $this->urlBuilder->buildUrl(
            $config['network'],
            $config['profile'],
            $config['url'],
            $config['title']
        );

        $qrId = 'wechat-' . crc32($shareUrl);
        $iconHtml = $config['icon'] ?: $this->getDefaultIcon('wechat');
        $label = RenderUtils::sanitizeContent($config['label']);

        $button = '<div class="hssb-share hssb-wechat" role="group" aria-label="Share on WeChat">';
        
        $buttonAttrs = [
            'class' => 'hssb-wechat-btn',
            'type' => 'button',
            'aria-expanded' => 'false',
            'aria-controls' => $qrId
        ];

        $button .= sprintf('<button %s>%s<span class="hssb-label">%s</span></button>',
            RenderUtils::buildAttributes($buttonAttrs),
            $iconHtml,
            $label
        );

        // QR Code container
        $qrDataUri = $this->generateQrCode($shareUrl);
        $button .= sprintf('<div id="%s" class="hssb-wechat-qr" style="display:none">', $qrId);
        $button .= sprintf('<img src="%s" alt="QR code to share on WeChat" width="200" height="200">', 
            RenderUtils::sanitizeAttribute($qrDataUri));
        $button .= '</div></div>';

        return $button;
    }

    /**
     * Render share count badge
     * 
     * @param int $count Share count
     * @param string $network Network name
     * @return string HTML output
     */
    public function renderShareCount(int $count, string $network): string
    {
        if ($count <= 0) {
            return '';
        }

        $visibleCount = RenderUtils::formatCount($count);
        $srText = RenderUtils::generateShareCountScreenReaderText($count, $network);

        $visible = sprintf('<span class="hssb-count" aria-hidden="true">%s</span>', 
            RenderUtils::sanitizeContent($visibleCount));
        
        $screenReader = sprintf('<span class="hss-sr">%s</span>', 
            RenderUtils::sanitizeContent($srText));

        return $visible . $screenReader;
    }

    /**
     * Normalize button configuration
     * 
     * @param array $config Raw configuration
     * @return array Normalized configuration
     */
    protected function normalizeConfig(array $config): array
    {
        return array_merge([
            'network' => '',
            'profile' => [],
            'url' => '#',
            'title' => '',
            'icon' => '',
            'label' => '',
            'handle' => '',
            'count' => 0,
            'show_label' => true,
            'show_count' => false,
            'css_classes' => [],
            'attributes' => []
        ], $config);
    }

    /**
     * Build button attributes array
     * 
     * @param array $config Button configuration
     * @param string $shareUrl Share URL
     * @return array Attributes
     */
    protected function buildButtonAttributes(array $config, string $shareUrl): array
    {
        $network = $config['network'];
        $handle = $config['handle'];
        
        $attributes = [
            'class' => RenderUtils::generateButtonClasses($network, $config['css_classes']),
            'href' => $shareUrl
        ];

        // Add accessibility attributes
        $a11yAttrs = RenderUtils::generateA11yAttributes($network, $handle, $config['count']);
        $attributes = array_merge($attributes, $a11yAttrs);

        // Add custom attributes
        if (!empty($config['attributes'])) {
            $attributes = array_merge($attributes, $config['attributes']);
        }

        // Add target for external links
        if (strpos($shareUrl, 'http') === 0 && $shareUrl !== '#') {
            $attributes['target'] = '_blank';
            $attributes['rel'] = 'noopener noreferrer';
        }

        return $attributes;
    }

    /**
     * Build button content (icon + label + count)
     * 
     * @param array $config Button configuration
     * @return string Button content HTML
     */
    protected function buildButtonContent(array $config): string
    {
        $content = '';

        // Icon
        if (!empty($config['icon'])) {
            $content .= $config['icon'];
        } else {
            $content .= $this->getDefaultIcon($config['network']);
        }

        // Label
        if ($config['show_label'] && !empty($config['label'])) {
            $labelText = $config['handle'] ? 
                $config['label'] . ' ' . $config['handle'] : 
                $config['label'];
            
            $content .= sprintf('<span class="hssb-label">%s</span>', 
                RenderUtils::sanitizeContent($labelText));
        }

        // Share count
        if ($config['show_count'] && $config['count'] > 0) {
            $content .= $this->renderShareCount($config['count'], $config['network']);
        }

        return $content;
    }

    /**
     * Generate QR code for WeChat sharing
     * 
     * @param string $url URL to encode
     * @return string QR code data URI
     */
    protected function generateQrCode(string $url): string
    {
        // Try local QR generation first
        if (class_exists('Endroid\\QrCode\\QrCode') && class_exists('Endroid\\QrCode\\Writer\\PngWriter')) {
            try {
                $qr = \Endroid\QrCode\QrCode::create($url)->setSize(200);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qr);
                
                if (method_exists($result, 'getDataUri')) {
                    return $result->getDataUri();
                }
            } catch (\Throwable $e) {
                error_log('ShareButtonRenderer: Local QR generation failed - ' . $e->getMessage());
            }
        }

        // Fallback to Google Charts API
        return 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . rawurlencode($url);
    }

    /**
     * Get default icon for network
     * 
     * @param string $network Network name
     * @return string Default icon HTML
     */
    protected function getDefaultIcon(string $network): string
    {
        $iconMap = [
            'facebook' => 'facebook-alt',
            'twitter' => 'twitter',
            'linkedin' => 'linkedin',
            'pinterest' => 'pinterest',
            'email' => 'email-alt',
            'whatsapp' => 'whatsapp',
            'telegram' => 'format-chat',
            'reddit' => 'reddit',
            'wechat' => 'format-chat',
            'tumblr' => 'format-image',
            'vk' => 'share',
        ];

        $dashicon = $iconMap[$network] ?? 'share';
        return sprintf('<span class="dashicons dashicons-%s"></span>', 
            RenderUtils::sanitizeAttribute($dashicon));
    }

    /**
     * Create renderer with default URL builder
     * 
     * @return ShareButtonRenderer
     */
    public static function createDefault(): self
    {
        return new self(new ShareUrlBuilder());
    }

    /**
     * Create renderer with WordPress-integrated URL builder
     * 
     * @return ShareButtonRenderer
     */
    public static function createWithWordPressIntegration(): self
    {
        return new self(ShareUrlBuilder::createWithWordPressIntegration());
    }
}