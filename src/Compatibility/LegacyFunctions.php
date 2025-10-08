<?php
/**
 * Legacy Functions Compatibility Layer
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Compatibility;

use HtmlSocialShare\Core\Plugin;

/**
 * Provides backward compatibility for legacy function calls
 */
class LegacyFunctions {
    /**
     * Legacy zm_sh_btn function wrapper
     *
     * Maintains backward compatibility with existing shortcode and function calls
     *
     * @param array $atts Attributes
     * @return string HTML output
     */
    public static function zm_sh_btn(array $atts = []): string {
        $plugin = Plugin::getInstance();
        $buttonRenderer = $plugin->getService('buttonRenderer');
        
        if (!$buttonRenderer) {
            return '';
        }
        
        // Handle icon array format
        // Old format: icons='facebook,twitter,linkedin'
        // Or: icons=['facebook' => '1', 'twitter' => '1']
        if (isset($atts['icons'])) {
            if (is_string($atts['icons'])) {
                // Convert comma-separated string to array
                $iconNames = explode(',', $atts['icons']);
                $icons = [];
                foreach ($iconNames as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $icons[$name] = '1';
                    }
                }
                $atts['icons'] = $icons;
            } elseif (is_array($atts['icons'])) {
                // Handle array_flip if needed (legacy format)
                // Check if array has numeric keys (means it was array_flip'd)
                $firstKey = key($atts['icons']);
                if (is_numeric($firstKey)) {
                    $atts['icons'] = array_flip($atts['icons']);
                }
            }
        }
        
        // Map legacy attribute names to new format
        if (isset($atts['iconset_type']) && !isset($atts['type'])) {
            $atts['type'] = $atts['iconset_type'];
        }
        
        return $buttonRenderer->render($atts);
    }
}
