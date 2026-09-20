<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * SSB Import Export Page Content - React Version.
 *
 * This file replaces the jQuery-based import/export with a React component
 * while maintaining the same functionality.
 *
 * @since 6.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ssb_settings_api = new Ssb_Settings_Structure();
echo '<div class="wrap">';
if ( class_exists( 'Ssb_Pro_React_Admin' ) || ! class_exists( 'Simple_Social_Buttons_Pro' ) ) {
	Ssb_Settings_Structure::ssb_banner_content();
}
echo '</div>';

// Enqueue necessary scripts and styles for React component.
wp_enqueue_script( 'ssb-import-export-react', plugins_url( '../build/import-export.js', __FILE__ ), array( 'wp-element' ), SSB_VERSION, true );
wp_enqueue_style( 'ssb-import-export-style', plugins_url( '../build/import-export.css', __FILE__ ), array(), SSB_VERSION );

// Localize script for React component.
wp_localize_script(
	'ssb-import-export-react',
	'ssbImportExportData',
	array(
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
		'importNonce' => wp_create_nonce( 'ssb-import-security-check' ),
		'exportNonce' => wp_create_nonce( 'ssb-export-security-check' ),
		'adminUrl'    => admin_url(),
	)
);
?>

<!-- React Root Element -->
<div id="ssb-import-export-root"></div>

<script>
	/**
	 * Initialize React component after DOM is ready
	 */
	document.addEventListener('DOMContentLoaded', function() {
		if (window.wp && window.wp.element && window.ssbImportExportComponent) {
			const { render } = window.wp.element;
			const rootElement = document.getElementById('ssb-import-export-root');
			
			if (rootElement) {
				render(
					window.wp.element.createElement(window.ssbImportExportComponent),
					rootElement
				);
			}
		}
	});
</script>
