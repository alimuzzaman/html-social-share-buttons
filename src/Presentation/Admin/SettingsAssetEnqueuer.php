<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;

final class SettingsAssetEnqueuer {
	private $pluginRoot;
	private $pluginFile;
	private $payloads;
	private $config;

	public function __construct(
		$pluginRoot,
		$pluginFile,
		SettingsPayloadBuilder $payloads,
		PluginConfig $config
	) {
		$this->pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
		$this->pluginFile = (string) $pluginFile;
		$this->payloads = $payloads;
		$this->config = $config;
	}

	public function enqueue( $hook ) {
		if ( $this->config->settingsScreenHook() === $hook ) {
			$this->enqueueSettingsPage();

			return;
		}

		if ( 'widgets.php' === $hook ) {
			wp_enqueue_style(
				$this->config->adminWidgetStyleHandle(),
				plugins_url( 'assets/admin-widget.css', $this->pluginFile ),
				array(),
				$this->config->version()
			);

			return;
		}

		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && function_exists( 'vc_map' ) ) {
			$this->enqueueWpBakeryAssets();
		}
	}

	private function enqueueSettingsPage() {
		$adminStylePath = $this->pluginRoot . '/assets/admin.css';
		$adminStyleVersion = file_exists( $adminStylePath ) ? filemtime( $adminStylePath ) : $this->config->version();
		wp_enqueue_style(
			$this->config->adminSettingsStyleHandle(),
			plugins_url( 'assets/admin.css', $this->pluginFile ),
			array(),
			$adminStyleVersion
		);

		$tokens = $this->adminColorSchemeTokens();
		wp_add_inline_style(
			$this->config->adminSettingsStyleHandle(),
			sprintf(
				'.zmsh-settings-wrap{--zmsh-accent:%1$s;--zmsh-accent-strong:%2$s;--zmsh-accent-light:%3$s;}',
				esc_html( $tokens['accent'] ),
				esc_html( $tokens['accent_strong'] ),
				esc_html( $tokens['accent_light'] )
			)
		);

		$asset = $this->assetMetadata( 'admin-react' );
		$dependencies = array_values(
			array_unique( array_merge( array( 'jquery', 'wp-components', 'wp-element' ), $asset['dependencies'] ) )
		);
		wp_enqueue_script(
			$this->config->adminSettingsScriptHandle(),
			plugins_url( 'build/admin-react.js', $this->pluginFile ),
			$dependencies,
			$asset['version'],
			true
		);
		wp_localize_script(
			$this->config->adminSettingsScriptHandle(),
			$this->config->adminSettingsObject(),
			$this->payloads->build()
		);
	}

	private function enqueueWpBakeryAssets() {
		$asset = $this->assetMetadata( 'vc-scripts' );
		$dependencies = array_values( array_unique( array_merge( array( 'jquery' ), $asset['dependencies'] ) ) );
		wp_enqueue_script(
			$this->config->adminWpBakeryScriptHandle(),
			plugins_url( 'build/vc-scripts.js', $this->pluginFile ),
			$dependencies,
			$asset['version'],
			true
		);
		wp_localize_script(
			$this->config->adminWpBakeryScriptHandle(),
			$this->config->adminWpBakeryObject(),
			array(
				'nonce'           => wp_create_nonce( $this->config->adminNonceAction() ),
				'elementorWidget' => $this->config->elementorWidgetName(),
				'legacyIconsets'  => array(
					'default' => __( 'Default (legacy)', 'html-social-share-buttons' ),
				),
			)
		);
	}

	private function assetMetadata( $entry ) {
		$path = $this->pluginRoot . '/build/' . $entry . '.asset.php';
		$asset = file_exists( $path ) ? require $path : array();

		return array(
			'dependencies' => isset( $asset['dependencies'] ) && is_array( $asset['dependencies'] )
				? $asset['dependencies']
				: array(),
			'version'      => isset( $asset['version'] ) ? $asset['version'] : $this->config->version(),
		);
	}

	private function adminColorSchemeTokens() {
		return array(
			'accent'        => '#2271b1',
			'accent_strong' => '#135e96',
			'accent_light'  => '#72aee6',
		);
	}
}
