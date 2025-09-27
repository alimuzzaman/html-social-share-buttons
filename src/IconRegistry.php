<?php
namespace HtmlSocialShare;

class IconRegistry implements IconRegistryInterface
{
    private array $icons = [];
    private string $currentIconset = 'default_square';

    private string $basePath;
    private string $baseUrl;

    public function __construct($settings = null, string $basePath = null, string $baseUrl = null)
    {
        $this->basePath = $basePath ?: plugin_dir_path(dirname(__DIR__));
        $this->baseUrl = $baseUrl ?: plugins_url('', dirname(__DIR__));
        if ($settings) {
            $iconset = $settings->get('iconset', 'default');
            // Map iconset to path
            $this->currentIconset = $this->mapIconsetToPath($iconset);
        }
        $this->loadIcons();
    }

    private function mapIconsetToPath(string $iconset): string
    {
        $mappings = [
            'default' => 'default_square',
            'square' => 'flat_square',
            'circle' => 'flat_circle',
            'minimal' => 'prajin_square' // Using prajin as minimal
        ];
        return $mappings[$iconset] ?? 'default_square';
    }

    public function setIconset(string $iconset): void
    {
        $this->currentIconset = $iconset;
        $this->loadIcons();
    }

    private function loadIcons(): void
    {
        $this->icons = [];

        $iconsetPath = $this->basePath . 'assets/iconset/' . $this->currentIconset . '/';

        $networkMappings = [
            'facebook' => 'facebook.png',
            'twitter' => 'twitter.png',
            'linkedin' => 'linkedin.png',
            'pinterest' => 'pinterest.png',
            'email' => 'mail.png'
        ];

        foreach ($networkMappings as $network => $filename) {
            $filePath = $iconsetPath . $filename;
            if (file_exists($filePath)) {
                $iconUrl = $this->baseUrl . '/assets/iconset/' . $this->currentIconset . '/' . $filename;
                $this->icons[$network] = sprintf('<img src="%s" alt="%s icon" width="24" height="24" />', esc_url($iconUrl), esc_attr($network));
            } else {
                // Fallback to dashicon
                $this->icons[$network] = sprintf('<span class="dashicons dashicons-%s"></span>', esc_attr($network === 'email' ? 'email' : 'share'));
            }
        }
    }

    public function registerIcon(string $key, string $svg): void
    {
        $this->icons[$key] = $svg;
    }

    public function getIcon(string $key): ?string
    {
        return $this->icons[$key] ?? null;
    }

    public function hasIcon(string $key): bool
    {
        return array_key_exists($key, $this->icons);
    }

    public function listIcons(): array
    {
        return array_keys($this->icons);
    }
}
