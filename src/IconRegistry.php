<?php
namespace HtmlSocialShare;

class IconRegistry implements IconRegistryInterface
{
    private array $icons = [];

    public function __construct()
    {
        // register a tiny placeholder SVG for twitter as a default
        $this->icons['twitter'] = '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M22 5.92c-.69.31-1.43.52-2.21.61.8-.48 1.42-1.24 1.71-2.15-.75.45-1.58.78-2.46.96A4.12 4.12 0 0 0 12 9.59c0 .32.04.63.11.93C8.04 10.33 4.5 8.4 2.22 5.19c-.35.6-.55 1.3-.55 2.05 0 1.42.72 2.67 1.82 3.4-.66-.02-1.28-.2-1.82-.5v.05c0 1.98 1.41 3.63 3.28 4.01-.34.09-.7.14-1.07.14-.26 0-.51-.03-.76-.07.51 1.58 2.01 2.73 3.78 2.76A8.28 8.28 0 0 1 2 19.54a11.64 11.64 0 0 0 6.29 1.84c7.55 0 11.69-6.25 11.69-11.67v-.53c.8-.57 1.5-1.28 2.05-2.09-.74.33-1.53.56-2.35.66z"/></svg>';
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
