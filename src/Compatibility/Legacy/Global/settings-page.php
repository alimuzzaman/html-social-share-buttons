<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		if ( is_admin() ) {
			new zm_sh_settings();
		}
	},
	20
);

/**
 * Historical global settings controller.
 *
 * Public callbacks remain here for third-party compatibility. Their implementation
 * is delegated to namespaced modules under Compatibility\Legacy\Admin.
 */
class zm_sh_settings {
	private $options;
	private $settings_service;
	private $assets;
	private $ajax;

	function __construct() {
		global $zm_sh, $zm_sh_default_options;

		$pluginRoot = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::pluginRoot();
		$this->settings_service = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::settings();
		$this->options = $this->settings_service->runtime( $zm_sh_default_options );

		$content = new \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Admin\LegacyExcludedContentLookup();
		$this->assets = new \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Admin\LegacySettingsAssetEnqueuer(
			$pluginRoot,
			$zm_sh->iconsets,
			$content
		);
		$this->ajax = new \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Admin\LegacySettingsAjaxController(
			$this->settings_service,
			$content
		);

		add_action( 'admin_menu', array( $this, 'reg_admn_menu' ) );
		add_action( 'admin_init', array( $this, 'zm_reg_sett' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
		add_action( 'wp_ajax_zm_sh_save_settings', array( $this, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_zm_sh_search_content', array( $this, 'ajax_search_content' ) );
	}

	function reg_admn_menu() {
		add_submenu_page(
			'options-general.php',
			__( 'Html Social Share', 'html-social-share-buttons' ),
			__( 'Html Social Share', 'html-social-share-buttons' ),
			'manage_options',
			'zm_shbt_opt',
			array( $this, 'zm_sh_opt' )
		);
	}

	function admin_scripts( $hook ) {
		$this->assets->enqueue( $hook, (array) $this->options );
	}

	function ajax_search_content() {
		$this->ajax->search();
	}

	function ajax_save_settings() {
		$this->ajax->save();
	}

	/**
	 * Kept as a no-op because the historical public method was callable.
	 */
	function add_new() {
	}

	/**
	 * Kept as a no-op because the historical public method was callable.
	 */
	function show_all() {
	}

	function zm_sh_opt() {
		?>
		<div class="wrap zmsh-settings-wrap">
			<div class="zm_settings_page_header">
				<h1 class="zm_options_page_heading"><?php esc_html_e( 'Html Social Share button', 'html-social-share-buttons' ); ?></h1>
				<p class="zm_settings_page_subtitle"><?php esc_html_e( 'Configure share buttons, placement, and output format from a single settings page.', 'html-social-share-buttons' ); ?></p>
			</div>
			<form id="zm-social-share-settings" class="zm_settings" method="post" action="options.php">
				<?php settings_fields( 'zm_shbt_opt' ); ?>
				<div id="zmsh-react-settings-root">
					<div class="zm_settings_loader zm_settings_loader--html" role="status" aria-live="polite">
						<span class="zm_settings_loader_spinner" aria-hidden="true"></span>
						<span><?php esc_html_e( 'Loading settings...', 'html-social-share-buttons' ); ?></span>
					</div>
				</div>
				<?php submit_button(); ?>
				<p class="desin_by">
					Designed By Hakan Ertan <a target="_blank" href="https://www.tonicons.com/" rel="follow">www.tonicons.com</a>
				</p>
			</form>
		</div>
		<?php
	}

	function zm_reg_sett() {
		register_setting( 'zm_shbt_opt', 'zm_shbt_fld', array( $this, 'sanitize' ) );
	}

	function sanitize( $input ) {
		return is_array( $input )
			? $this->settings_service->sanitize( $input )
			: array();
	}
}
