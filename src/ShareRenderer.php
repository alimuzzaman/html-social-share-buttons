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

    public function render(string $network, array $profile): string
    {
        // Debug: Log render call
        error_log("HSS Debug: Rendering network '{$network}' with profile: " . json_encode($profile));

        $label = htmlspecialchars($network, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $handle = isset($profile['handle']) ? htmlspecialchars($profile['handle'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
        $url = '#';

        $icon = $this->iconRegistry->getIcon($network);
        if (!$icon) {
            $icon = '<span class="dashicons dashicons-share"></span>';
            error_log("HSS Debug: No icon found for network '{$network}', using fallback");
        }

        $output = sprintf('<a class="hssb-share hssb-%s" href="%s" title="Share on %s">%s<span class="hssb-label">%s</span></a>',
            $label, $url, ucfirst($label), $icon, $handle ? ' ' . $handle : '');

        error_log("HSS Debug: Rendered output for '{$network}': " . substr($output, 0, 100) . '...');
        return $output;
    }
}
