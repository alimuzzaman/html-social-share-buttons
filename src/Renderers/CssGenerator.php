<?php
/**
 * CSS Generator
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Renderers;

use HtmlSocialShare\IconSystem\IconRegistry;
use HtmlSocialShare\Options\OptionsManager;

/**
 * Generates CSS for social share buttons
 */
class CssGenerator {
    /**
     * @var IconRegistry
     */
    private IconRegistry $iconRegistry;
    
    /**
     * @var OptionsManager
     */
    private OptionsManager $optionsManager;
    
    /**
     * @var array CSS rules to output
     */
    private array $cssRules = [];
    
    /**
     * Constructor
     *
     * @param IconRegistry $iconRegistry
     * @param OptionsManager $optionsManager
     */
    public function __construct(
        IconRegistry $iconRegistry,
        OptionsManager $optionsManager
    ) {
        $this->iconRegistry = $iconRegistry;
        $this->optionsManager = $optionsManager;
    }
    
    /**
     * Generate CSS for an iconset
     *
     * @param string $iconsetId Iconset ID
     * @param string $type Type (square/circle)
     * @param array $enabledIcons Array of enabled icon IDs
     * @return string CSS rules
     */
    public function generateIconCss(
        string $iconsetId,
        string $type,
        array $enabledIcons = []
    ): string {
        $iconset = $this->iconRegistry->getIconset($iconsetId, $type);
        
        if (!$iconset) {
            return '';
        }
        
        $css = [];
        
        // Base icon styles
        $css[] = ".zmshbt.{$iconsetId}.{$type} a {";
        $css[] = "    width: 32px;";
        $css[] = "    height: 32px;";
        $css[] = "    display: block;";
        $css[] = "    background-size: cover;";
        $css[] = "    margin: 10px;";
        $css[] = "}";
        $css[] = "";
        
        // Inline/widget styles
        $css[] = ".zmshbt.{$iconsetId}.in_widget a,";
        $css[] = ".zmshbt.{$iconsetId}.in_shortcode a {";
        $css[] = "    display: inline-block;";
        $css[] = "    margin: 5px;";
        $css[] = "}";
        $css[] = "";
        
        // Icon background images
        foreach ($iconset->icons as $icon) {
            // Skip if we have a list of enabled icons and this one isn't in it
            if (!empty($enabledIcons) && !in_array($icon->id, $enabledIcons, true)) {
                continue;
            }
            
            $imageUrl = HTML_SOCIAL_SHARE_ICONSET_URL . $iconsetId . '_' . $type . '/' . $icon->image;
            $css[] = ".zmshbt.{$iconsetId}.{$type} a.{$icon->class} {";
            $css[] = "    background-image: url('{$imageUrl}');";
            $css[] = "}";
            $css[] = "";
        }
        
        // Hover effects
        $css[] = ".zmshbt.{$iconsetId} a:hover,";
        $css[] = ".zmshbt.{$iconsetId} a:active {";
        $css[] = "    transform: scale(1.5);";
        $css[] = "    transition: all .25s linear;";
        $css[] = "}";
        
        return implode("\n", $css);
    }
    
    /**
     * Generate positioning CSS
     *
     * @param array $options Options array
     * @return string CSS rules
     */
    public function generatePositioningCss(array $options = []): string {
        $autoHide = $options['auto_hide_btn'] ?? $this->optionsManager->get('auto_hide_btn', true);
        $iconset = $options['iconset'] ?? $this->optionsManager->get('iconset', 'default');
        
        $css = [];
        
        // Fixed positioning base styles
        $css[] = ".zmshbt.left,";
        $css[] = ".zmshbt.right {";
        $css[] = "    top: 30%;";
        $css[] = "    z-index: 9999;";
        $css[] = "    position: fixed;";
        $css[] = "    transition: all .25s linear .5s;";
        $css[] = "}";
        $css[] = "";
        
        // Left placement
        if ($autoHide) {
            $css[] = ".zmshbt.left {";
            $css[] = "    left: -25px;";
            $css[] = "}";
            $css[] = "";
            $css[] = ".zmshbt.left:hover {";
            $css[] = "    left: 0;";
            $css[] = "}";
        } else {
            $css[] = ".zmshbt.left {";
            $css[] = "    left: 0;";
            $css[] = "}";
        }
        $css[] = "";
        
        // Right placement
        if ($autoHide) {
            $css[] = ".zmshbt.right {";
            $css[] = "    right: -25px;";
            $css[] = "}";
            $css[] = "";
            $css[] = ".zmshbt.right:hover {";
            $css[] = "    right: 0;";
            $css[] = "}";
        } else {
            $css[] = ".zmshbt.right {";
            $css[] = "    right: 0;";
            $css[] = "}";
        }
        
        return implode("\n", $css);
    }
    
    /**
     * Generate all CSS and output to footer
     *
     * @param array $options Options for CSS generation
     */
    public function output(array $options = []): void {
        $iconsetId = $options['iconset'] ?? $this->optionsManager->get('iconset', 'default');
        $type = $options['iconset_type'] ?? $this->optionsManager->get('iconset_type', 'square');
        $icons = $options['icons'] ?? $this->optionsManager->get('icons', []);
        
        // Get enabled icon IDs
        $enabledIcons = [];
        if (is_array($icons)) {
            foreach ($icons as $icon => $enabled) {
                if (!empty($enabled) && $enabled !== '0') {
                    $enabledIcons[] = $icon;
                }
            }
        }
        
        // Generate CSS
        $iconCss = $this->generateIconCss($iconsetId, $type, $enabledIcons);
        $positioningCss = $this->generatePositioningCss($options);
        
        // Output CSS
        if (!empty($iconCss) || !empty($positioningCss)) {
            echo "<style id='html-social-share-css'>\n";
            if (!empty($iconCss)) {
                echo $iconCss . "\n";
            }
            if (!empty($positioningCss)) {
                echo $positioningCss . "\n";
            }
            echo "</style>\n";
        }
    }
    
    /**
     * Add CSS rule to be output later
     *
     * @param string $css CSS rule
     */
    public function addRule(string $css): void {
        $this->cssRules[] = $css;
    }
    
    /**
     * Get all collected CSS rules
     *
     * @return string
     */
    public function getRules(): string {
        return implode("\n", $this->cssRules);
    }
}
