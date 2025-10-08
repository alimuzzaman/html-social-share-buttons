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
 * Handles [zm_sh_btn] shortcode
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
     * Register the shortcode
     */
    public function register(): void {
        add_shortcode('zm_sh_btn', [$this, 'handle']);
    }
    
    /**
     * Handle shortcode rendering
     *
     * @param array|string $atts Shortcode attributes
     * @return string HTML output
     */
    public function handle($atts): string {
        // Normalize attributes
        if (!is_array($atts)) {
            $atts = [];
        }
        
        // Default attributes
        $defaults = [
            'title' => '',
            'iconset' => '',
            'url' => '%%permalink%%',
            'icons' => '',
            'iconset_type' => '',
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
