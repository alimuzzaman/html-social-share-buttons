<?php
/**
 * Legacy Compatibility Layer for HTML Social Share Buttons
 *
 * This file provides backward compatibility for v2.x functions and APIs
 * while mapping them to the new v3.x architecture.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Legacy zm_sh_btn() function compatibility
 *
 * Maps the legacy zm_sh_btn() function to the new LegacyButtonRenderer
 *
 * @param array $options Legacy options array
 * @return string HTML output for share buttons
 */
if (!function_exists('zm_sh_btn')) {
    function zm_sh_btn($options = array()) {
        try {
            // Get the service container
            $container = html_social_share_get_container();

            // Get the legacy button renderer from the container
            $legacyRenderer = $container->get('legacy_button_renderer');

            // Render the buttons using legacy renderer
            return $legacyRenderer->render($options);

        } catch (\Exception $e) {
            // Log error but don't break the site
            error_log('HTML Social Share Legacy Compatibility Error: ' . $e->getMessage());

            // Return empty string as fallback
            return '';
        }
    }
}

/**
 * Legacy zm_sh_curentPageURL() function compatibility
 *
 * @return string Current page URL
 */
if (!function_exists('zm_sh_curentPageURL')) {
    function zm_sh_curentPageURL() {
        try {
            // Get the service container
            $container = html_social_share_get_container();

            // Get URL utilities from container
            $urlUtils = $container->get('url_utils');

            return $urlUtils->getCurrentUrl();

        } catch (\Exception $e) {
            // Fallback to basic URL detection
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if (isset($_SERVER["SERVER_PORT"]) &&
                $_SERVER["SERVER_PORT"] != "80" &&
                $_SERVER["SERVER_PORT"] != "443") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }
    }
}

/**
 * Legacy shortcode callback compatibility
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
if (!function_exists('zm_sh_shortcode_cb')) {
    function zm_sh_shortcode_cb($atts = array()) {
        return zm_sh_btn($atts);
    }
}

/**
 * Convert legacy options format to new format
 *
 * @param array $legacyOptions Legacy options array
 * @return array New options format
 */
function convert_legacy_options_to_new($legacyOptions = array()) {
    $newOptions = array();

    // Default legacy options for reference
    $legacyDefaults = array(
        "title" => "Share this with your friends",
        "iconset" => "default",
        "use_port" => false,
        "auto_hide_btn" => false,
        "show_in" => array(
            "show_left" => true,
            "show_right" => false,
            "show_before_post" => false,
            "show_after_post" => true,
        ),
        "iconset_type" => "square",
        "icons" => array(
            "facebook" => 1,
            "twitter" => 1,
            "linkedin" => 1,
            "googlepluse" => 1,
            "bookmark" => 1,
            "pinterest" => 1,
            "mail" => 1,
        )
    );

    // Merge with defaults
    $legacyOptions = array_merge($legacyDefaults, $legacyOptions);

    // Convert title
    if (!empty($legacyOptions['title'])) {
        $newOptions['title'] = sanitize_text_field($legacyOptions['title']);
    }

    // Convert iconset
    if (!empty($legacyOptions['iconset'])) {
        $newOptions['iconset'] = sanitize_key($legacyOptions['iconset']);
    }

    // Convert URL if provided
    if (!empty($legacyOptions['url'])) {
        $newOptions['url'] = esc_url_raw($legacyOptions['url']);
    }

    // Convert iconset_type to style
    if (!empty($legacyOptions['iconset_type'])) {
        $newOptions['style'] = sanitize_key($legacyOptions['iconset_type']);
    }

    // Convert class
    if (!empty($legacyOptions['class'])) {
        $newOptions['class'] = sanitize_html_class($legacyOptions['class']);
    }

    // Convert icons array
    if (!empty($legacyOptions['icons']) && is_array($legacyOptions['icons'])) {
        $networks = array();
        foreach ($legacyOptions['icons'] as $network => $enabled) {
            if ($enabled) {
                $networks[] = sanitize_key($network);
            }
        }
        $newOptions['networks'] = $networks;
    }

    // Convert show_on to placement
    if (!empty($legacyOptions['show_on'])) {
        $placementMap = array(
            'show_left' => 'floating-left',
            'show_right' => 'floating-right',
            'show_before_post' => 'before-content',
            'show_after_post' => 'after-content',
            'widget' => 'inline',
            'in_shortcode' => 'inline'
        );

        if (isset($placementMap[$legacyOptions['show_on']])) {
            $newOptions['placement'] = $placementMap[$legacyOptions['show_on']];
        }
    }

    // Handle nofollow
    if (isset($legacyOptions['nofollow']) && $legacyOptions['nofollow']) {
        $newOptions['nofollow'] = true;
    }

    // Handle Google Analytics
    if (isset($legacyOptions['g_analytics']) && $legacyOptions['g_analytics']) {
        $newOptions['analytics'] = true;
    }

    return $newOptions;
}

/**
 * Legacy widget registration compatibility
 */
if (!function_exists('zm_sh_register_widgets')) {
    function zm_sh_register_widgets() {
        // Legacy widget is handled by the new widget system
        // This function exists for backward compatibility only
    }
}

/**
 * Legacy metabox function compatibility
 */
if (!function_exists('zm_sh_metabox_new')) {
    function zm_sh_metabox_new() {
        // Legacy metabox is handled by the new admin system
        // This function exists for backward compatibility only
    }
}

/**
 * Legacy VC integration compatibility
 */
if (!function_exists('zm_sh_integrateWithVC')) {
    function zm_sh_integrateWithVC() {
        // Legacy VC integration is handled by the new integration system
        // This function exists for backward compatibility only
    }
}

/**
 * Legacy schema functions compatibility
 */
if (!function_exists('zm_sh_get_schema')) {
    function zm_sh_get_schema($id) {
        try {
            $container = html_social_share_get_container();
            $schemaManager = $container->get('schema_manager');
            return $schemaManager->getSchema($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('zm_sh_get_schemas')) {
    function zm_sh_get_schemas() {
        try {
            $container = html_social_share_get_container();
            $schemaManager = $container->get('schema_manager');
            return $schemaManager->getAllSchemas();
        } catch (\Exception $e) {
            return array();
        }
    }
}

if (!function_exists('zm_sh_add_schema')) {
    function zm_sh_add_schema($schema) {
        try {
            $container = html_social_share_get_container();
            $schemaManager = $container->get('schema_manager');
            return $schemaManager->addSchema($schema);
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('zm_sh_remove_schema')) {
    function zm_sh_remove_schema($id) {
        try {
            $container = html_social_share_get_container();
            $schemaManager = $container->get('schema_manager');
            return $schemaManager->removeSchema($id);
        } catch (\Exception $e) {
            return false;
        }
    }
}

/**
 * Legacy placeholder filter compatibility
 */
if (!function_exists('zm_sh_placeholder')) {
    function zm_sh_placeholder($item) {
        try {
            $container = html_social_share_get_container();
            $urlProcessor = $container->get('url_processor');
            return $urlProcessor->processPlaceholders($item);
        } catch (\Exception $e) {
            // Basic placeholder replacement as fallback
            $item = str_replace('%%permalink%%', get_permalink(), $item);
            $item = str_replace('%%title%%', get_the_title(), $item);
            $item = str_replace('%%description%%', get_the_excerpt(), $item);

            // Try to get featured image for thumbnail
            if (strpos($item, '%%imageurl%%') !== false) {
                $thumbnail = get_the_post_thumbnail_url();
                if (!$thumbnail) {
                    $thumbnail = '';
                }
                $item = str_replace('%%imageurl%%', $thumbnail, $item);
            }

            return $item;
        }
    }
}

/**
 * Initialize legacy compatibility when plugin loads
 */
add_action('plugins_loaded', function() {
    // Register legacy shortcode if not already registered
    if (!shortcode_exists('zm_sh_btn')) {
        add_shortcode('zm_sh_btn', 'zm_sh_shortcode_cb');
    }

    // Register legacy widget if not already registered
    if (!class_exists('zm_html_share_widget')) {
        // Legacy widget compatibility is handled by the new widget system
    }
}, 20);