<?php
namespace HtmlSocialShare\Iconset;

class Loader
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * List available iconsets by folder name
     * @return array
     */
    public function listIconsets(): array
    {
        $dir = $this->basePath;
        if (!is_dir($dir)) {
            return [];
        }

        $items = scandir($dir);
        $sets = [];
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            if (is_dir($dir . DIRECTORY_SEPARATOR . $it)) {
                $sets[] = $it;
            }
        }

        return $sets;
    }
}
