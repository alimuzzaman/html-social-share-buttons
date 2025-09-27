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

    /**
     * Set the current iconset.
     *
     * @param string $iconset
     * @return void
     */
    public function setIconset(string $iconset): void;

    /**
     * Get the current iconset.
     *
     * @return string
     */
    public function getCurrentIconset(): string;

    /**
     * Add a custom icon.
     *
     * @param string $key
     * @param string $svg
     * @param array $meta
     * @return void
     */
    public function addCustomIcon(string $key, string $svg, array $meta = []): void;

    /**
     * Remove a custom icon.
     *
     * @param string $key
     * @return void
     */
    public function removeCustomIcon(string $key): void;

    /**
     * Get all available iconsets.
     *
     * @return array
     */
    public function getAvailableIconsets(): array;
}
