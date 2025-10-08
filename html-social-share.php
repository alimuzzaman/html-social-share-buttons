<?php
/*
Plugin Name: Html Social share buttons
Plugin URI: http://wordpress.org/plugins/html-social-share-buttons/
Description: Html share button. It show lite share button only with html. It's not using any javascript whats another do. It's load only extra 10-11 kb total on your site.
Author: Alimuzzaman Alim
Version: 2.2.1
Author URI: https://alim.dev
Text Domain: zm-sh
Domain Path: /languages
*/

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Define plugin constants
define('HTML_SOCIAL_SHARE_PLUGIN_FILE', __FILE__);
define('HTML_SOCIAL_SHARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HTML_SOCIAL_SHARE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HTML_SOCIAL_SHARE_VERSION', '3.0.0');

// Additional helper constants for assets and URLs
define('HTML_SOCIAL_SHARE_ASSETS_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/');
define('HTML_SOCIAL_SHARE_ASSETS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/');
define('HTML_SOCIAL_SHARE_BUILD_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'build/');
define('HTML_SOCIAL_SHARE_BUILD_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'build/');
define('HTML_SOCIAL_SHARE_ICONSET_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/iconset/');
define('HTML_SOCIAL_SHARE_ICONSET_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/iconset/');
define('HTML_SOCIAL_SHARE_ADMIN_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/admin/');
define('HTML_SOCIAL_SHARE_CSS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/');
define('HTML_SOCIAL_SHARE_JS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'build/');
define('HTML_SOCIAL_SHARE_IMAGES_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/images/');

// Ensure composer autoloader is loaded from the main plugin directory.
$autoload = __DIR__ . '/vendor/autoload.php';
if (! file_exists($autoload)) {
	// Show admin notice if autoloader missing (for now, just return)
	return;
}

require_once $autoload;

// Initialize the plugin
add_action('plugins_loaded', function() {
	$plugin = \HtmlSocialShare\Core\Plugin::getInstance();
	$plugin->init();
}, 10);

// Register activation/deactivation hooks
register_activation_hook(__FILE__, function() {
	$plugin = \HtmlSocialShare\Core\Plugin::getInstance();
	$plugin->activate();
});

register_deactivation_hook(__FILE__, function() {
	$plugin = \HtmlSocialShare\Core\Plugin::getInstance();
	$plugin->deactivate();
});

// Legacy function wrapper for backward compatibility
if (!function_exists('zm_sh_btn')) {
	/**
	 * Legacy function for rendering social share buttons
	 *
	 * @param array $atts Attributes
	 * @return string HTML output
	 */
	function zm_sh_btn($atts = []) {
		return \HtmlSocialShare\Compatibility\LegacyFunctions::zm_sh_btn($atts);
	}
}
