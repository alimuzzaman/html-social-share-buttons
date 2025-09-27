<?php
namespace HtmlSocialShare;

interface IconRegistryInterface
{
    /**
     * Register an icon SVG by key.
     *
     * @param string $key
     * @param string $svg
     * @return void
     */
    public function registerIcon(string $key, string $svg): void;

    /**
     * Get the icon SVG for a key, or null if missing.
     *
     * @param string $key
     * @return string|null
     */
    public function getIcon(string $key): ?string;

    /**
     * Check if an icon exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasIcon(string $key): bool;

    /**
     * List registered icon keys.
     *
     * @return string[]
     */
    public function listIcons(): array;
}
