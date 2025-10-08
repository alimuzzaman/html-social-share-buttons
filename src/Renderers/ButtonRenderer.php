<?php
/**
 * Button Renderer
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Renderers;

use HtmlSocialShare\IconSystem\IconRegistry;
use HtmlSocialShare\Services\UrlBuilder;
use HtmlSocialShare\Options\OptionsManager;

/**
 * Renders HTML for social share buttons
 */
class ButtonRenderer {
    /**
     * @var IconRegistry
     */
    private IconRegistry $iconRegistry;
    
    /**
     * @var UrlBuilder
     */
    private UrlBuilder $urlBuilder;
    
    /**
     * @var OptionsManager
     */
    private OptionsManager $optionsManager;
    
    /**
     * Constructor
     *
     * @param IconRegistry $iconRegistry
     * @param UrlBuilder $urlBuilder
     * @param OptionsManager $optionsManager
     */
    public function __construct(
        IconRegistry $iconRegistry,
        UrlBuilder $urlBuilder,
        OptionsManager $optionsManager
    ) {
        $this->iconRegistry = $iconRegistry;
        $this->urlBuilder = $urlBuilder;
        $this->optionsManager = $optionsManager;
    }
    
    /**
     * Render social share buttons
     *
     * @param array $options Rendering options
     * @return string HTML output
     */
    public function render(array $options = []): string {
        try {
            // Merge with defaults from options manager
            $iconsetId = $options['iconset'] ?? $this->optionsManager->get('iconset', 'default');
            $type = $options['iconset_type'] ?? $this->optionsManager->get('iconset_type', 'square');
            $class = $options['class'] ?? 'in_shortcode';
            $icons = $options['icons'] ?? $this->optionsManager->get('icons', []);
            $nofollow = $options['nofollow'] ?? $this->optionsManager->get('nofollow', false);
            
            // Get iconset
            $iconset = $this->iconRegistry->getIconset($iconsetId, $type);
            
            if (!$iconset) {
                return '';
            }
            
            // Parse icons if string (comma-separated)
            if (is_string($icons)) {
                $iconNames = explode(',', $icons);
                $icons = [];
                foreach ($iconNames as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $icons[$name] = '1';
                    }
                }
            }
            
            // Filter to only enabled icons
            $enabledIcons = [];
            if (is_array($icons)) {
                foreach ($icons as $iconId => $enabled) {
                    if (!empty($enabled) && $enabled !== '0') {
                        $enabledIcons[] = $iconId;
                    }
                }
            }
            
            // If no icons enabled, return empty
            if (empty($enabledIcons)) {
                return '';
            }
            
            // Build wrapper div classes
            $wrapperClasses = [
                'zmshbt',
                esc_attr($class),
                esc_attr($iconsetId),
                esc_attr($type),
            ];
            
            $html = [];
            $html[] = '<div class="' . implode(' ', $wrapperClasses) . '">';
            
            // Generate context for URL building
            $context = [
                'permalink' => $options['url'] ?? '',
                'title' => $options['title'] ?? '',
                'image' => $options['image'] ?? '',
            ];
            
            // Render each icon
            foreach ($enabledIcons as $iconId) {
                $iconHtml = $this->renderIcon($iconId, $iconsetId, $type, $nofollow, $context);
                if (!empty($iconHtml)) {
                    $html[] = $iconHtml;
                }
            }
            
            $html[] = '</div>';
            
            $output = implode("\n", $html);
            
            // Allow filtering of output
            $output = apply_filters('html_social_share_output', $output, $options);
            
            return $output;
            
        } catch (\Exception $e) {
            // Log error if WP_DEBUG is enabled
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('HTML Social Share - ButtonRenderer error: ' . $e->getMessage());
            }
            return '';
        }
    }
    
    /**
     * Render a single icon
     *
     * @param string $iconId Icon ID
     * @param string $iconsetId Iconset ID
     * @param string $type Type
     * @param bool $nofollow Add nofollow attribute
     * @param array $context Context for URL building
     * @return string HTML for icon
     */
    public function renderIcon(
        string $iconId,
        string $iconsetId,
        string $type,
        bool $nofollow = false,
        array $context = []
    ): string {
        $icon = $this->iconRegistry->getIcon($iconsetId, $type, $iconId);
        
        if (!$icon) {
            return '';
        }
        
        // Build URL
        $url = $this->urlBuilder->buildUrl($iconId, $iconsetId, $type, $context);
        
        if (empty($url)) {
            return '';
        }
        
        // Build attributes
        $attributes = [
            'class' => esc_attr($icon->class),
            'target' => '_blank',
            'href' => esc_url($url),
        ];
        
        // Add nofollow if enabled
        if ($nofollow) {
            $attributes['rel'] = 'nofollow';
        }
        
        // Build attribute string
        $attrString = [];
        foreach ($attributes as $key => $value) {
            $attrString[] = $key . '="' . $value . '"';
        }
        
        return '<a ' . implode(' ', $attrString) . '></a>';
    }
    
    /**
     * Render buttons for a specific placement
     *
     * @param string $placement Placement (left, right, before_post, after_post, widget)
     * @param array $options Additional options
     * @return string HTML output
     */
    public function renderForPlacement(string $placement, array $options = []): string {
        // Map placement to class
        $classMap = [
            'left' => 'left',
            'right' => 'right',
            'before_post' => 'in_shortcode',
            'after_post' => 'in_shortcode',
            'widget' => 'in_widget',
            'shortcode' => 'in_shortcode',
        ];
        
        $class = $classMap[$placement] ?? 'in_shortcode';
        $options['class'] = $class;
        
        return $this->render($options);
    }
}
