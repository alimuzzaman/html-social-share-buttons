<?php
namespace HtmlSocialShare;

interface BackCompatInterface
{
    /**
     * Migrate legacy options to canonical schema.
     *
     * @return array Canonical options array
     */
    public function migrate(): array;

    /**
     * Map a single legacy option key to a canonical path.
     * Returns null if no mapping exists.
     *
     * @param string $key Legacy option key
     * @return string|null Canonical dot-notated path or null when no mapping exists
     */
    public function mapLegacy(string $key): ?string;
}
