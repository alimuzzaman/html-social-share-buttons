<?php
namespace HtmlSocialShare;

class ShareRenderer implements ShareRendererInterface
{
    private IconRegistryInterface $iconRegistry;

    public function __construct(IconRegistryInterface $iconRegistry)
    {
        $this->iconRegistry = $iconRegistry;
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

        $output = sprintf('<a class="hssb-share hssb-%s" href="%s" title="Share on %s">%s<span class="hssb-label">%s</span></a>',
            $label, $shareUrl, ucfirst($label), $icon, $handle ? ' ' . $handle : '');

        error_log("HSS Debug: Rendered output for '{$network}': " . substr($output, 0, 100) . '...');
        return $output;
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
        if (empty($profile['url_template'])) {
            return '#';
        }

        $template = $profile['url_template'];

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
