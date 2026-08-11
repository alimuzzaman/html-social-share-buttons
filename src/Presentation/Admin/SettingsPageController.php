<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;

final class SettingsPageController {
	private $ajax;
	private $assets;
	private $config;

	public function __construct(
		SettingsAjaxController $ajax,
		SettingsAssetEnqueuer $assets,
		PluginConfig $config
	) {
		$this->ajax = $ajax;
		$this->assets = $assets;
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( 'admin_menu', array( $this, 'registerMenu' ) );
		add_action( 'admin_init', array( $this, 'registerSettings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAssets' ), 20 );
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->config->paths()->file() ),
			array( $this, 'pluginActionLinks' )
		);
		$this->ajax->registerHooks();
	}

	public function registerMenu() {
		add_submenu_page(
			'options-general.php',
			__( 'Html Social Share', 'html-social-share-buttons' ),
			__( 'Html Social Share', 'html-social-share-buttons' ),
			'manage_options',
			$this->config->settingsPage(),
			array( $this, 'renderPage' )
		);
	}

	public function registerSettings() {
		register_setting(
			$this->config->settingsGroup(),
			$this->config->optionName(),
			array( $this, 'sanitize' )
		);
	}

	public function enqueueAssets( $hook ) {
		$this->assets->enqueue( $hook );
	}

	public function sanitize( $input ) {
		return is_array( $input ) ? $this->ajax->sanitize( $input ) : array();
	}

	public function pluginActionLinks( $links ) {
		$links = is_array( $links ) ? $links : array();
		$settingsLink = '<a href="options-general.php?page=' .
			esc_attr( $this->config->settingsPage() ) . '">' .
			__( 'Settings', 'html-social-share-buttons' ) .
			'</a>';
		array_unshift( $links, $settingsLink );

		return $links;
	}

	/** Historical public callback aliases; the canonical controller retains ownership. */
	public function searchContent() {
		return $this->ajax->search();
	}

	public function saveSettings() {
		return $this->ajax->save();
	}

	public function iconSet() {
		return $this->ajax->iconSet();
	}

	public function iconSetPreview() {
		return $this->ajax->iconSetPreview();
	}

	public function iconSetDetails() {
		return $this->ajax->iconSetDetails();
	}

	public function renderPage() {
		?>
		<div class="wrap zmsh-settings-wrap">
			<div class="zm_settings_page_header">
				<h1 class="zm_options_page_heading"><?php esc_html_e( 'Html Social Share button', 'html-social-share-buttons' ); ?></h1>
				<p class="zm_settings_page_subtitle"><?php esc_html_e( 'Configure share buttons, placement, and output format from a single settings page.', 'html-social-share-buttons' ); ?></p>
			</div>
			<form id="zm-social-share-settings" class="zm_settings" method="post" action="options.php">
				<?php settings_fields( $this->config->settingsGroup() ); ?>
				<div id="zmsh-react-settings-root">
					<div class="zm_settings_loader zm_settings_loader--html" role="status" aria-live="polite">
						<span class="zm_settings_loader_spinner" aria-hidden="true"></span>
						<span><?php esc_html_e( 'Loading settings...', 'html-social-share-buttons' ); ?></span>
					</div>
				</div>
				<?php submit_button(); ?>
				<p class="desin_by">
					<?php esc_html_e( 'Designed By Hakan Ertan', 'html-social-share-buttons' ); ?>
					<a target="_blank" href="https://www.tonicons.com/" rel="follow">www.tonicons.com</a>
				</p>
			</form>
		</div>
		<?php
	}
}
