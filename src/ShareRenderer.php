<?php
namespace HtmlSocialShare;

class ShareRenderer implements ShareRendererInterface
{
    private IconRegistryInterface $iconRegistry;
    private $settings;

    public function __construct(IconRegistryInterface $iconRegistry, $settings = null)
    {
        $this->iconRegistry = $iconRegistry;
        $this->settings = $settings;
    }

    public function setIconset(string $iconset): void
    {
        if (method_exists($this->iconRegistry, 'setIconset')) {
            $this->iconRegistry->setIconset($iconset);
        }
    }



    public function render(string $network, array $profile, string $url = '#', string $title = ''): string
    {
        // Debug: Log render call
        error_log("HSS Debug: Rendering network '{$network}' with profile: " . json_encode($profile));

        $label = htmlspecialchars($network, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $handle = isset($profile['handle']) ? htmlspecialchars($profile['handle'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';

        // Use provided URL or generate from template
        $shareUrl = ($url !== '#') ? $url : $this->generateShareUrl($profile, $url, $title);

        $icon = $this->iconRegistry->getIcon($network);
        if (!$icon) {
            $icon = '<span class="dashicons dashicons-share"></span>';
            error_log("HSS Debug: No icon found for network '{$network}', using fallback");
        }

        $countHtml = '';

        // Special-case WeChat: render a QR code for privacy-friendly sharing
        if ($network === 'wechat') {
            $iconHtml = $icon;

            // Prefer local QR generation if endroid/qr-code is available
            $qrDataUri = null;
            if (class_exists('Endroid\\QrCode\\QrCode') && class_exists('Endroid\\QrCode\\Writer\\PngWriter')) {
                try {
                    $qr = \Endroid\QrCode\QrCode::create($shareUrl)->setSize(200);
                    $writer = new \Endroid\QrCode\Writer\PngWriter();
                    $result = $writer->write($qr);
                    if (method_exists($result, 'getDataUri')) {
                        $qrDataUri = $result->getDataUri();
                    }
                } catch (\Throwable $e) {
                    error_log('HSS WeChat QR: local QR generation failed: ' . $e->getMessage());
                    $qrDataUri = null;
                }
            }

            // Fallback to Google Chart API when local generation is unavailable
            if ($qrDataUri === null) {
                $qrDataUri = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . rawurlencode($shareUrl);
            }

            $button = '<div class="hssb-share hssb-wechat" role="group" aria-label="Share on WeChat">';
            $button .= sprintf(
                '<button class="hssb-wechat-btn" type="button" aria-expanded="false" aria-controls="wechat-desc-%d">%s<span class="hssb-label">%s</span></button>',
                crc32($shareUrl), $iconHtml, esc_html($label)
            );
            $button .= sprintf('<div id="wechat-desc-%d" class="hssb-wechat-qr" style="display:none">', crc32($shareUrl));
            $button .= sprintf('<img src="%s" alt="QR code to share on WeChat" width="200" height="200">', esc_attr($qrDataUri));
            $button .= '</div></div>';

            // Note: front-end JS toggles visibility of the .hssb-wechat-qr element on click for progressive enhancement
            return $button;
        }

        $output = sprintf('<a class="hssb-share hssb-%s" href="%s" title="Share on %s" aria-label="Share on %s%s">%s<span class="hssb-label">%s</span>%s</a>',
            $label, $shareUrl, ucfirst($label), ucfirst($label), $handle ? ' with ' . $handle : '', $icon, $handle ? ' ' . $handle : '', $countHtml);

        error_log("HSS Debug: Rendered output for '{$network}': " . substr($output, 0, 100) . '...');
        return $output;
    }

    private function formatCount(int $count): string
    {
        if ($count < 1000) {
            return (string) $count;
        }
        if ($count < 1000000) {
            return round($count / 1000, 1) . 'K';
        }
        return round($count / 1000000, 1) . 'M';
    }

    /**
     * Generate share URL from profile template
     *
     * @param array $profile Profile data
     * @param string $url The URL to share
     * @param string $title The title to share
     * @return string Generated share URL
     */
    private function generateShareUrl(array $profile, string $url, string $title = ''): string
    {
        $template = $profile['url_template'] ?? '';

        // If profile doesn't contain a url_template, attempt to resolve from Networks registry
        if (empty($template)) {
            $networkKey = $profile['network'] ?? '';
            if ($networkKey) {
                $available = Networks::getAvailableNetworks();
                if (isset($available[$networkKey]['url_template'])) {
                    $template = $available[$networkKey]['url_template'];
                }
            }
        }

        if (empty($template)) {
            return '#';
        }

        // Replace placeholders
        $replacements = [
            '{url}' => urlencode($url),
            '{title}' => urlencode($title),
            '{encoded_url}' => urlencode($url),
            '{encoded_title}' => urlencode($title),
        ];

        $shareUrl = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Apply filters for integrations like BetterLinks
        $network = $profile['network'] ?? '';
        return apply_filters('hss_share_url', $shareUrl, $network, $url, $title, $profile);
    }
}
