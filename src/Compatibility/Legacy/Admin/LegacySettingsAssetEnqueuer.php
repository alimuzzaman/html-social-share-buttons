<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Admin;

final class LegacySettingsAssetEnqueuer {
	private $pluginRoot;
	private $iconsets;
	private $content;

	public function __construct(
		$pluginRoot,
		$iconsets,
		LegacyExcludedContentLookup $content
	) {
		$this->pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
		$this->iconsets = $iconsets;
		$this->content = $content;
	}

	public function enqueue( $hook, array $options ) {
		if ( 'settings_page_zm_shbt_opt' === $hook ) {
			$this->enqueueSettingsPage( $options );

			return;
		}

		if ( 'widgets.php' === $hook ) {
			wp_enqueue_style(
				'zm_sh_admin_styles_scripts',
				plugins_url( 'assets/admin-widget.css', $this->pluginFile() ),
				array(),
				'2.2.4'
			);

			return;
		}

		if (
			in_array( $hook, array( 'post.php', 'post-new.php' ), true ) &&
			function_exists( 'vc_map' )
		) {
			$this->enqueueWpBakeryAssets();
		}
	}

	private function enqueueSettingsPage( array $options ) {
		$adminStylePath = $this->pluginRoot . '/assets/admin.css';
		$adminStyleVersion = file_exists( $adminStylePath ) ? filemtime( $adminStylePath ) : '2.2.8';
		wp_enqueue_style(
			'zm_sh_admin_styles',
			plugins_url( 'assets/admin.css', $this->pluginFile() ),
			array(),
			$adminStyleVersion
		);

		$tokens = $this->getAdminColorSchemeTokens();
		wp_add_inline_style(
			'zm_sh_admin_styles',
			sprintf(
				'.zmsh-settings-wrap{--zmsh-accent:%1$s;--zmsh-accent-strong:%2$s;--zmsh-accent-light:%3$s;}',
				esc_html( $tokens['accent'] ),
				esc_html( $tokens['accent_strong'] ),
				esc_html( $tokens['accent_light'] )
			)
		);

		$asset = $this->assetMetadata( 'admin-react' );
		$dependencies = array_values(
			array_unique(
				array_merge(
					array( 'jquery', 'wp-components', 'wp-element' ),
					$asset['dependencies']
				)
			)
		);
		wp_enqueue_script(
			'zm_sh_admin_scripts',
			plugins_url( 'build/admin-react.js', $this->pluginFile() ),
			$dependencies,
			$asset['version'],
			true
		);

		wp_localize_script(
			'zm_sh_admin_scripts',
			'zm_sh_react_settings',
			$this->buildSettingsPayload( $options )
		);
	}

	private function enqueueWpBakeryAssets() {
		$asset = $this->assetMetadata( 'vc-scripts' );
		$dependencies = array_values(
			array_unique( array_merge( array( 'jquery' ), $asset['dependencies'] ) )
		);
		wp_enqueue_script(
			'zm_sh_vc_admin_scripts',
			plugins_url( 'build/vc-scripts.js', $this->pluginFile() ),
			$dependencies,
			$asset['version'],
			true
		);
		wp_localize_script(
			'zm_sh_vc_admin_scripts',
			'zm_sh',
			array( 'nonce' => wp_create_nonce( 'zm_sh_admin' ) )
		);
	}

	private function buildSettingsPayload( array $options ) {
		$defaultedOptions = wp_parse_args(
			$options,
			array(
				'title'           => __( 'Share this with your friends', 'html-social-share-buttons' ),
				'iconset'         => 'default',
				'show_in'         => array(
					'show_left'        => 0,
					'show_right'       => 0,
					'show_before_post' => 0,
					'show_after_post'  => 0,
				),
				'excludes'        => '',
				'iconset_type'    => 'square',
				'icons'           => array(),
				'g_analytics'     => 0,
				'auto_hide_btn'   => 0,
				'use_port'        => 0,
				'nofollow'        => 0,
				'profile_links'   => array(),
				'share_templates' => function_exists( 'zm_sh_get_share_templates' )
					? zm_sh_get_share_templates()
					: array(),
			)
		);
		if (
			isset( $defaultedOptions['icons']['twitter'] ) &&
			! isset( $defaultedOptions['icons']['x'] )
		) {
			$defaultedOptions['icons']['x'] = $defaultedOptions['icons']['twitter'];
		}
		if (
			isset( $defaultedOptions['profile_links']['twitter'] ) &&
			! isset( $defaultedOptions['profile_links']['x'] )
		) {
			$defaultedOptions['profile_links']['x'] =
				$defaultedOptions['profile_links']['twitter'];
			unset( $defaultedOptions['profile_links']['twitter'] );
		}

		$excluded = $this->content->resolve( $defaultedOptions['excludes'] );
		$templateDefaults = function_exists( 'zm_sh_get_default_share_templates' )
			? zm_sh_get_default_share_templates()
			: array();
		$templateOverrides = isset( $options['share_templates'] ) && is_array( $options['share_templates'] )
			? $options['share_templates']
			: array();

		return array(
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'nonce'                    => wp_create_nonce( 'zm_sh_admin' ),
			'assets_img'               => zm_sh_url_assets_img,
			'iconsets'                 => ( new LegacyIconSetPayloadBuilder( $this->iconsets ) )->build(),
			'options'                  => $defaultedOptions,
			'share_template_defaults'  => $templateDefaults,
			'share_template_overrides' => $templateOverrides,
			'exclude_items'            => $excluded['items'],
			'exclude_custom'           => $excluded['custom'],
			'defaultIconset'           => 'default',
			'strings'                  => array(
				'loading'   => __( 'Loading settings...', 'html-social-share-buttons' ),
				'saving'    => __( 'Saving...', 'html-social-share-buttons' ),
				'saved'     => __( 'Settings saved.', 'html-social-share-buttons' ),
				'saveError' => __( 'Settings could not be saved. Try again.', 'html-social-share-buttons' ),
			),
		);
	}

	private function assetMetadata( $entry ) {
		$path = $this->pluginRoot . '/build/' . $entry . '.asset.php';
		$asset = file_exists( $path ) ? require $path : array();

		return array(
			'dependencies' => isset( $asset['dependencies'] ) && is_array( $asset['dependencies'] )
				? $asset['dependencies']
				: array(),
			'version'      => isset( $asset['version'] ) ? $asset['version'] : '2.2.8',
		);
	}

	private function getAdminColorSchemeTokens() {
		global $_wp_admin_css_colors;

		$tokens = array(
			'accent'        => '#2271b1',
			'accent_strong' => '#135e96',
			'accent_light'  => '#72aee6',
		);
		$scheme = get_user_option( 'admin_color' );
		if ( empty( $_wp_admin_css_colors[ $scheme ] ) ) {
			$scheme = 'modern';
		}

		$colors = ! empty( $_wp_admin_css_colors[ $scheme ]->colors )
			? $_wp_admin_css_colors[ $scheme ]->colors
			: array();
		foreach (
			array(
				'accent'        => 2,
				'accent_strong' => 1,
				'accent_light'  => 3,
			) as $token => $index
		) {
			if ( isset( $colors[ $index ] ) && sanitize_hex_color( $colors[ $index ] ) ) {
				$tokens[ $token ] = sanitize_hex_color( $colors[ $index ] );
			}
		}

		return $tokens;
	}

	private function pluginFile() {
		return $this->pluginRoot . '/html-social-share.php';
	}
}
