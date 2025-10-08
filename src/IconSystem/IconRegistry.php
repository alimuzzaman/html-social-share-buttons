<?php
/**
 * Icon Registry
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\IconSystem;

/**
 * Central registry for all iconsets
 */
class IconRegistry {
    /**
     * @var array<string, Iconset> Cached iconsets
     */
    private array $iconsets = [];
    
    /**
     * @var array Default icon definitions
     */
    private array $defaultIcons = [
        'facebook' => [
            'id' => 'facebook',
            'name' => 'Facebook',
            'class' => 'facebook',
            'image' => 'facebook.png',
            'url' => 'http://www.facebook.com/sharer.php?u=%%permalink%%&t=%%title%%',
        ],
        'twitter' => [
            'id' => 'twitter',
            'name' => 'Twitter',
            'class' => 'twitter',
            'image' => 'twitter.png',
            'url' => 'http://twitter.com/share?url=%%permalink%%&text=%%title%%',
        ],
        'linkedin' => [
            'id' => 'linkedin',
            'name' => 'LinkedIn',
            'class' => 'linkedin',
            'image' => 'linkedin.png',
            'url' => 'http://www.linkedin.com/shareArticle?url=%%permalink%%&title=%%title%%',
        ],
        'pinterest' => [
            'id' => 'pinterest',
            'name' => 'Pinterest',
            'class' => 'pinterest',
            'image' => 'pinterest.png',
            'url' => 'http://pinterest.com/pin/create/button/?url=%%permalink%%&description=%%title%%&media=%%image%%',
        ],
        'googlepluse' => [
            'id' => 'googlepluse',
            'name' => 'Google Plus',
            'class' => 'googlepluse',
            'image' => 'googlepluse.png',
            'url' => 'https://plus.google.com/share?url=%%permalink%%',
        ],
        'mail' => [
            'id' => 'mail',
            'name' => 'Email',
            'class' => 'mail',
            'image' => 'mail.png',
            'url' => 'mailto:?subject=%%title%%&body=%%permalink%%',
        ],
    ];
    
    /**
     * Get an iconset by ID and type
     *
     * @param string $iconsetId Iconset ID (e.g., 'default')
     * @param string $type Type (square or circle)
     * @return Iconset|null
     */
    public function getIconset(string $iconsetId, string $type): ?Iconset {
        $cacheKey = $iconsetId . '_' . $type;
        
        if (isset($this->iconsets[$cacheKey])) {
            return $this->iconsets[$cacheKey];
        }
        
        // Try to load iconset
        $iconset = $this->loadIconset($iconsetId, $type);
        
        if ($iconset) {
            $this->iconsets[$cacheKey] = $iconset;
            return $iconset;
        }
        
        // Fallback to default if requested iconset doesn't exist
        if ($iconsetId !== 'default') {
            return $this->getIconset('default', $type);
        }
        
        return null;
    }
    
    /**
     * Get a specific icon from an iconset
     *
     * @param string $iconsetId Iconset ID
     * @param string $type Type
     * @param string $network Network ID
     * @return Icon|null
     */
    public function getIcon(string $iconsetId, string $type, string $network): ?Icon {
        $iconset = $this->getIconset($iconsetId, $type);
        
        if (!$iconset) {
            return null;
        }
        
        return $iconset->getIcon($network);
    }
    
    /**
     * Get all available iconsets
     *
     * @return array Array of iconset IDs
     */
    public function getAvailableIconsets(): array {
        return ['default', 'flat', 'long_shadow', 'prajin'];
    }
    
    /**
     * Check if an iconset exists
     *
     * @param string $iconsetId Iconset ID
     * @return bool
     */
    public function iconsetExists(string $iconsetId): bool {
        return in_array($iconsetId, $this->getAvailableIconsets(), true);
    }
    
    /**
     * Load an iconset from storage
     *
     * @param string $iconsetId Iconset ID
     * @param string $type Type
     * @return Iconset|null
     */
    private function loadIconset(string $iconsetId, string $type): ?Iconset {
        // For Phase 1, we'll use hardcoded data structure
        // Future: Load from JSON metadata files
        
        $iconsetData = [
            'name' => ucfirst(str_replace('_', ' ', $iconsetId)),
            'cssPath' => HTML_SOCIAL_SHARE_BUILD_URL . 'iconsets/' . $iconsetId . '_' . $type . '.css',
            'previewImage' => HTML_SOCIAL_SHARE_ASSETS_URL . 'iconset/' . $iconsetId . '_' . $type . '/preview.png',
            'icons' => $this->defaultIcons,
        ];
        
        // Allow filtering of icon data
        $iconsetData = apply_filters(
            'html_social_share_iconset_data',
            $iconsetData,
            $iconsetId,
            $type
        );
        
        return new Iconset($iconsetId, $type, $iconsetData);
    }
    
    /**
     * Get default icon definitions
     *
     * @return array
     */
    public function getDefaultIcons(): array {
        return $this->defaultIcons;
    }
}
