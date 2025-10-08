<?php
/**
 * Iconset Data Structure
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\IconSystem;

/**
 * Represents an iconset (e.g., 'default_square')
 */
class Iconset {
    /**
     * @var string Iconset ID (e.g., 'default')
     */
    public string $id;
    
    /**
     * @var string Display name
     */
    public string $name;
    
    /**
     * @var string Type (square or circle)
     */
    public string $type;
    
    /**
     * @var Icon[] Array of Icon objects
     */
    public array $icons = [];
    
    /**
     * @var string Path to CSS file
     */
    public string $cssPath = '';
    
    /**
     * @var string Path to preview image
     */
    public string $previewImage = '';
    
    /**
     * Constructor
     *
     * @param string $id Iconset ID
     * @param string $type Type (square/circle)
     * @param array $data Iconset data
     */
    public function __construct(string $id, string $type, array $data = []) {
        $this->id = $id;
        $this->type = $type;
        $this->name = $data['name'] ?? ucfirst($id);
        $this->cssPath = $data['cssPath'] ?? '';
        $this->previewImage = $data['previewImage'] ?? '';
        
        // Convert icon data to Icon objects
        if (isset($data['icons']) && is_array($data['icons'])) {
            foreach ($data['icons'] as $iconData) {
                if (is_array($iconData)) {
                    $this->icons[] = new Icon($iconData);
                }
            }
        }
    }
    
    /**
     * Get icon by network ID
     *
     * @param string $network Network ID (e.g., 'facebook')
     * @return Icon|null
     */
    public function getIcon(string $network): ?Icon {
        foreach ($this->icons as $icon) {
            if ($icon->id === $network) {
                return $icon;
            }
        }
        return null;
    }
    
    /**
     * Get combined ID (iconset_type)
     *
     * @return string
     */
    public function getCombinedId(): string {
        return $this->id . '_' . $this->type;
    }
}
