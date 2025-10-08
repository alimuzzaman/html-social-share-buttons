<?php
namespace HtmlSocialShare\Iconset;

class PreviewGenerator
{
    protected $basePath;
    protected $loader;

    public function __construct(string $basePath, Loader $loader = null)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        $this->loader = $loader ?? new Loader($this->basePath);
    }

    /**
     * Generate a simple HTML preview for a given iconset name.
     * This is intentionally lightweight — it inlines SVG files found in the iconset.
     *
     * @param string $setName
     * @return string HTML fragment
     */
    public function generatePreviewHtml(string $setName): string
    {
        $setDir = $this->basePath . DIRECTORY_SEPARATOR . $setName;
        if (!is_dir($setDir)) {
            return '<!-- iconset not found -->';
        }

        $files = scandir($setDir);
        $items = [];
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            $path = $setDir . DIRECTORY_SEPARATOR . $f;
            if (is_dir($path)) {
                // collect svg files in subdirectories
                $sub = scandir($path);
                foreach ($sub as $s) {
                    if (preg_match('/\.svg$/i', $s)) {
                        $items[$f . '/' . $s] = @file_get_contents($path . DIRECTORY_SEPARATOR . $s);
                    }
                }
            } elseif (preg_match('/\.svg$/i', $f)) {
                $items[$f] = @file_get_contents($path);
            }
        }

        $html = '<div class="iconset-preview">';
        foreach ($items as $name => $svg) {
            $html .= '<div class="icon-preview" data-name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">';
            $html .= $svg;
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }
}
