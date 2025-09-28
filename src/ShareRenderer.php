<?php
namespace HtmlSocialShare;

class ShareRenderer implements ShareRendererInterface
{
    private IconRegistryInterface $iconRegistry;
    private $settings;
    private $shareCountManager = null;

    public function __construct(IconRegistryInterface $iconRegistry, $settings = null, $shareCountManager = null)
    {
        $this->iconRegistry = $iconRegistry;
        $this->settings = $settings;
        $this->shareCountManager = $shareCountManager;
    }

    public function setIconset(string $iconset): void
    {
        if (method_exists($this->iconRegistry, 'setIconset')) {
            $this->iconRegistry->setIconset($iconset);
        }
    }

    public function setShareCountManager($manager): void
    {
        $this->shareCountManager = $manager;
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
        $enabled = $this->settings ? ($this->settings->get('share_counts_enabled', false) ?: false) : false;
        if ($enabled && $this->shareCountManager) {
            $postId = (int) (get_the_ID() ?: 0);
            if ($postId > 0) {
                $count = $this->shareCountManager->getCountForPostNetwork($postId, $network);
                // Visible badge (aria-hidden) and separate screen-reader-only text
                $visible = '<span class="hssb-count" aria-hidden="true">' . $this->formatCount($count) . '</span>';
                $srText = sprintf('%s shares on %s', number_format_i18n($count), ucfirst($label));
                $sr = '<span class="hss-sr">' . esc_html($srText) . '</span>';
                $countHtml = $visible . $sr;
            }
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
