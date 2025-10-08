<?php
/**
 * Shortcode Handler
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Frontend;

use HtmlSocialShare\Renderers\ButtonRenderer;
use HtmlSocialShare\Options\OptionsManager;

/**
 * Handles [html_social_share] shortcode (new) and [zm_sh_btn] shortcode (legacy compatibility)
 */
class Shortcode {
    /**
     * @var ButtonRenderer
     */
    private ButtonRenderer $buttonRenderer;
    
    /**
     * @var OptionsManager
     */
    private OptionsManager $optionsManager;
    
    /**
     * Constructor
     *
     * @param ButtonRenderer $buttonRenderer
     * @param OptionsManager $optionsManager
     */
    public function __construct(
        ButtonRenderer $buttonRenderer,
        OptionsManager $optionsManager
    ) {
        $this->buttonRenderer = $buttonRenderer;
        $this->optionsManager = $optionsManager;
    }
    
    /**
     * Register the shortcodes
     */
    public function register(): void {
        // Register new modern shortcode
        add_shortcode('html_social_share', [$this, 'handle']);
        
        // Register legacy shortcode for backward compatibility
        add_shortcode('zm_sh_btn', [$this, 'handleLegacy']);
    }
    
    /**
     * Handle new shortcode rendering [html_social_share]
     *
     * @param array|string $atts Shortcode attributes
     * @return string HTML output
     */
    public function handle($atts): string {
        // Normalize attributes
        if (!is_array($atts)) {
            $atts = [];
        }
        
        // Default attributes for new shortcode
        $defaults = [
            'title' => '',
            'iconset' => '',
            'url' => '%%permalink%%',
            'networks' => '', // Use 'networks' instead of 'icons' for new shortcode
            'type' => '', // Use 'type' instead of 'iconset_type'
            'class' => 'in_shortcode',
        ];
        
        // Merge with WordPress shortcode_atts
        $atts = shortcode_atts($defaults, $atts, 'html_social_share');
        
        // Sanitize inputs
        $atts['title'] = sanitize_text_field($atts['title']);
        $atts['iconset'] = sanitize_key($atts['iconset']);
        $atts['url'] = esc_url_raw($atts['url']);
        $atts['type'] = sanitize_key($atts['type']);
        $atts['class'] = sanitize_html_class($atts['class']);
        
        // Handle networks parameter (new attribute name)
        if (!empty($atts['networks'])) {
            if (is_string($atts['networks'])) {
                // Convert comma-separated string to array
                $networkNames = explode(',', $atts['networks']);
                $networks = [];
                foreach ($networkNames as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $networks[sanitize_key($name)] = '1';
                    }
                }
                $atts['icons'] = $networks; // Map to internal 'icons' key
            }
        } else {
            // Use default icons from options
            $atts['icons'] = $this->optionsManager->get('icons', []);
        }
        
        // Use default iconset if not specified
        if (empty($atts['iconset'])) {
            $atts['iconset'] = $this->optionsManager->get('iconset', 'default');
        }
        
        // Map 'type' to 'iconset_type' for internal use
        if (!empty($atts['type'])) {
            $atts['iconset_type'] = $atts['type'];
        } elseif (empty($atts['iconset_type'])) {
            $atts['iconset_type'] = $this->optionsManager->get('iconset_type', 'square');
        }
        
        // Render buttons
        return $this->buttonRenderer->render($atts);
    }
    
    /**
     * Handle legacy shortcode rendering [zm_sh_btn] - for backward compatibility
     *
     * @param array|string $atts Shortcode attributes
     * @return string HTML output
     */
    public function handleLegacy($atts): string {
        // Normalize attributes
        if (!is_array($atts)) {
            $atts = [];
        }
        
        // Default attributes for legacy shortcode
        $defaults = [
            'title' => '',
            'iconset' => '',
            'url' => '%%permalink%%',
            'icons' => '', // Keep 'icons' for legacy
            'iconset_type' => '', // Keep 'iconset_type' for legacy
            'class' => 'in_shortcode',
        ];
        
        // Merge with WordPress shortcode_atts
        $atts = shortcode_atts($defaults, $atts, 'zm_sh_btn');
        
        // Sanitize inputs
        $atts['title'] = sanitize_text_field($atts['title']);
        $atts['iconset'] = sanitize_key($atts['iconset']);
        $atts['url'] = esc_url_raw($atts['url']);
        $atts['iconset_type'] = sanitize_key($atts['iconset_type']);
        $atts['class'] = sanitize_html_class($atts['class']);
        
        // Handle icons parameter
        if (!empty($atts['icons'])) {
            if (is_string($atts['icons'])) {
                // Convert comma-separated string to array
                $iconNames = explode(',', $atts['icons']);
                $icons = [];
                foreach ($iconNames as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $icons[sanitize_key($name)] = '1';
                    }
                }
                $atts['icons'] = $icons;
            }
        } else {
            // Use default icons from options
            $atts['icons'] = $this->optionsManager->get('icons', []);
        }
        
        // Use default iconset if not specified
        if (empty($atts['iconset'])) {
            $atts['iconset'] = $this->optionsManager->get('iconset', 'default');
        }
        
        // Use default type if not specified
        if (empty($atts['iconset_type'])) {
            $atts['iconset_type'] = $this->optionsManager->get('iconset_type', 'square');
        }
        
        // Render buttons
        return $this->buttonRenderer->render($atts);
    }
}
