<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * SSB Help Page Content - React Version.
 *
 * This file renders the Help page using a React component.
 *
 * @since 7.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the logs class to get system info.
require_once SSB_PLUGIN_DIR . 'classes/class-ssb-logs.php';

$ssb_settings_api = new Ssb_Settings_Structure();
echo '<div class="wrap">';
if ( class_exists( 'Ssb_Pro_React_Admin' ) || ! class_exists( 'Simple_Social_Buttons_Pro' ) ) {
	Ssb_Settings_Structure::ssb_banner_content();
}
echo '</div>';

// Get system info. Added prefix later 7.0.0.
$ssb_sys_info = Ssb_Logs_Info::ssb_get_sysinfo();

// Enqueue WordPress dependencies first.
wp_enqueue_script( 'wp-element' );
wp_enqueue_script( 'wp-i18n' );
wp_enqueue_style( 'wp-components' );

// Enqueue necessary scripts and styles for React component.
wp_enqueue_script( 'ssb-help-react', plugins_url( '../build/help.js', __FILE__ ), array( 'wp-element', 'wp-i18n' ), SSB_VERSION, true );
wp_enqueue_style( 'ssb-help-style', plugins_url( '../build/help.css', __FILE__ ), array(), SSB_VERSION );

// Localize script for React component.
wp_localize_script(
	'ssb-help-react',
	'ssbHelpData',
	array(
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'adminUrl' => admin_url(),
		'isPro'    => class_exists( 'Simple_Social_Buttons_Pro' ),
		'sysInfo'  => $ssb_sys_info,
	)
);
?>

<!-- React Root Element -->
<div id="ssb-help-root"></div>

<script>
	/**
	 * Initialize React component after DOM is ready.
	 */
	document.addEventListener('DOMContentLoaded', function() {
		if (window.wp && window.wp.element && window.ssbHelpComponent) {
			const { render, Fragment } = window.wp.element;
			const rootElement = document.getElementById('ssb-help-root');
			
			if (rootElement) {
				render(
					window.wp.element.createElement(window.ssbHelpComponent),
					rootElement
				);
			}
		} else {
			console.log('Missing dependencies:', {
				wp: typeof window.wp,
				element: window.wp ? typeof window.wp.element : 'undefined',
				component: typeof window.ssbHelpComponent
			});
		}
	});
</script>
