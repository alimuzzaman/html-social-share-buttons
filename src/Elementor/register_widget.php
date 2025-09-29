<?php
/**
 * Register the Elementor widget for HTML Social Share Buttons
 * 
 * Handles widget registration with proper error handling and validation.
 * 
 * @since 3.0.0
 */

use HtmlSocialShare\Utils\SecurityUtils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register Elementor widget with error handling
 * 
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager
 * @return void
 */
function register_html_social_share_elementor_widget($widgets_manager)
{
    try {
        // Validate Elementor is available
        if (!class_exists('\Elementor\Widgets_Manager')) {
            error_log('HTML Social Share: Elementor Widgets_Manager class not found');
            return;
        }

        // Validate our widget class exists
        if (!class_exists('\HtmlSocialShare\Elementor\ShareButtonsWidget')) {
            require_once __DIR__ . '/ShareButtonsWidget.php';
            
            if (!class_exists('\HtmlSocialShare\Elementor\ShareButtonsWidget')) {
                error_log('HTML Social Share: ShareButtonsWidget class could not be loaded');
                return;
            }
        }

        // Register the widget with error handling
        $widget = new \HtmlSocialShare\Elementor\ShareButtonsWidget();
        $widgets_manager->register($widget);

        // Log successful registration in debug mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('HTML Social Share: Elementor widget registered successfully');
        }

    } catch (\Throwable $e) {
        // Log registration error
        error_log('HTML Social Share: Error registering Elementor widget - ' . $e->getMessage());
        
        // Show admin notice in debug mode
        if (defined('WP_DEBUG') && WP_DEBUG && is_admin()) {
            add_action('admin_notices', function() use ($e) {
                $message = sprintf(
                    /* translators: %s: Error message */
                    __('HTML Social Share: Failed to register Elementor widget. Error: %s', 'html-social-share'),
                    SecurityUtils::escapeHtml($e->getMessage())
                );
                printf('<div class="notice notice-error"><p>%s</p></div>', $message);
            });
        }
    }
}

/**
 * Check if Elementor requirements are met
 * 
 * @return bool True if requirements are met
 */
function html_social_share_elementor_requirements_met()
{
    // Check if Elementor is active
    if (!did_action('elementor/loaded')) {
        return false;
    }

    // Check minimum Elementor version
    if (!class_exists('\Elementor\Plugin')) {
        return false;
    }

    $elementor_version = \Elementor\Plugin::$instance->get_version();
    $minimum_version = '3.0.0';
    
    if (version_compare($elementor_version, $minimum_version, '<')) {
        // Log version compatibility issue
        error_log(sprintf(
            'HTML Social Share: Elementor version %s is below minimum required version %s',
            $elementor_version,
            $minimum_version
        ));
        return false;
    }

    return true;
}

// Hook into Elementor widget registration with requirements check
add_action('elementor/widgets/register', function($widgets_manager) {
    if (html_social_share_elementor_requirements_met()) {
        register_html_social_share_elementor_widget($widgets_manager);
    }
});

// Add compatibility notice for older Elementor versions
add_action('admin_notices', function() {
    if (!html_social_share_elementor_requirements_met() && did_action('elementor/loaded')) {
        $message = __(
            'HTML Social Share: Your Elementor version is not compatible. Please update to Elementor 3.0.0 or higher.',
            'html-social-share'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%s</p></div>', 
               SecurityUtils::escapeHtml($message));
    }
});

// Clean up widget registration on plugin deactivation
register_deactivation_hook(plugin_basename(__FILE__), function() {
    // Clear any cached widget data
    if (function_exists('wp_cache_delete')) {
        wp_cache_delete('elementor_widgets', 'elementor');
    }
});
