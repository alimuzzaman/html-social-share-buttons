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
			'strings'                  => $this->interfaceStrings(),
		);
	}

	private function interfaceStrings() {
		return array(
			'loading' => __( 'Loading settings...', 'html-social-share-buttons' ),
			'saving' => __( 'Saving...', 'html-social-share-buttons' ),
			'saved' => __( 'Settings saved.', 'html-social-share-buttons' ),
			'saveError' => __( 'Settings could not be saved. Try again.', 'html-social-share-buttons' ),
			'saveChanges' => __( 'Save Changes', 'html-social-share-buttons' ),
			'postTitle' => __( 'Post title', 'html-social-share-buttons' ),
			'postTitleDescription' => __( 'The title of the shared post', 'html-social-share-buttons' ),
			'permalink' => __( 'Permalink', 'html-social-share-buttons' ),
			'permalinkDescription' => __( 'The canonical post URL', 'html-social-share-buttons' ),
			'featuredImageUrl' => __( 'Featured image URL', 'html-social-share-buttons' ),
			'featuredImageDescription' => __( "The post's featured image", 'html-social-share-buttons' ),
			'defaultTitle' => __( 'Share this with your friends', 'html-social-share-buttons' ),
			'enabled' => __( 'Enabled', 'html-social-share-buttons' ),
			'disabled' => __( 'Disabled', 'html-social-share-buttons' ),
			'buttonShape' => __( 'Button shape', 'html-social-share-buttons' ),
			'header' => __( 'Header', 'html-social-share-buttons' ),
			'headerDescription' => __( 'Set the text shown with the share buttons and choose pages where buttons should stay hidden.', 'html-social-share-buttons' ),
			'enterTitle' => __( 'Enter a title', 'html-social-share-buttons' ),
			'excludeContent' => __( 'Exclude pages or posts', 'html-social-share-buttons' ),
			'excludeHelp' => __( 'Search published pages and posts, or press Enter to add a custom value.', 'html-social-share-buttons' ),
			'excludePlaceholder' => __( 'Search pages, posts, or add a custom value', 'html-social-share-buttons' ),
			'iconStyle' => __( 'Icon Style', 'html-social-share-buttons' ),
			'iconStyleDescription' => __( 'Choose the icon pack used for every placement and generated code snippet.', 'html-social-share-buttons' ),
			'buttonStyle' => __( 'Button style', 'html-social-share-buttons' ),
			'preview' => __( 'Preview', 'html-social-share-buttons' ),
			'displayPlacement' => __( 'Display placement', 'html-social-share-buttons' ),
			'displayPlacementDescription' => __( 'Turn each placement on or off and pick its shape.', 'html-social-share-buttons' ),
			'leftSide' => __( 'Left side', 'html-social-share-buttons' ),
			'leftSideDescription' => __( 'A vertical rail on the left edge of the screen.', 'html-social-share-buttons' ),
			'beforePost' => __( 'Before post', 'html-social-share-buttons' ),
			'beforePostDescription' => __( 'A row of buttons placed above post content.', 'html-social-share-buttons' ),
			'rightSide' => __( 'Right side', 'html-social-share-buttons' ),
			'rightSideDescription' => __( 'A vertical rail on the right edge of the screen.', 'html-social-share-buttons' ),
			'afterPost' => __( 'After post', 'html-social-share-buttons' ),
			'afterPostDescription' => __( 'A row of buttons placed below post content.', 'html-social-share-buttons' ),
			'socialNetworks' => __( 'Social Networks', 'html-social-share-buttons' ),
			'socialNetworksDescription' => __( 'Select the share buttons that should be available in the output.', 'html-social-share-buttons' ),
			'shareByEmail' => __( 'Share the current page by email.', 'html-social-share-buttons' ),
			/* translators: %s is a social network name. */
			'shareOnNetwork' => __( 'Share the current page on %s.', 'html-social-share-buttons' ),
			/* translators: %s is a social network name. */
			'shareTemplate' => __( '%s share template', 'html-social-share-buttons' ),
			'restoreDefaults' => __( 'Restore defaults', 'html-social-share-buttons' ),
			'shareUrl' => __( 'Share URL', 'html-social-share-buttons' ),
			'shareParameters' => __( 'Share parameters', 'html-social-share-buttons' ),
			'parameterNamesManaged' => __( 'Parameter names are managed automatically', 'html-social-share-buttons' ),
			'parameter' => __( 'Parameter', 'html-social-share-buttons' ),
			/* translators: 1: social network name, 2: parameter name. */
			'parameterValueLabel' => __( '%1$s %2$s value', 'html-social-share-buttons' ),
			'customTemplateSaved' => __( 'Custom template saved for this platform.', 'html-social-share-buttons' ),
			'canonicalTemplateUsed' => __( 'Using the canonical template shown as the placeholder.', 'html-social-share-buttons' ),
			'socialProfileLinks' => __( 'Social profile links', 'html-social-share-buttons' ),
			'profileLinksDescription' => __( 'Add direct profile or contact destinations beside the share buttons. Leave a field empty to hide it.', 'html-social-share-buttons' ),
			'emailDestination' => __( 'Email destination', 'html-social-share-buttons' ),
			/* translators: %s is a social network name. */
			'profileUrl' => __( '%s profile URL', 'html-social-share-buttons' ),
			'emailDestinationHelp' => __( 'Use one mailto: address without subject or body parameters.', 'html-social-share-buttons' ),
			'httpsLinksOnly' => __( 'HTTPS links only.', 'html-social-share-buttons' ),
			'advancedOptions' => __( 'Advanced options', 'html-social-share-buttons' ),
			'advancedOptionsDescription' => __( 'Fine tune tracking, behavior, and link output.', 'html-social-share-buttons' ),
			'googleAnalytics' => __( 'Google Social analytics', 'html-social-share-buttons' ),
			'autoHide' => __( 'Auto hide button', 'html-social-share-buttons' ),
			'usePort' => __( 'Use port on the url.', 'html-social-share-buttons' ),
			'noFollow' => __( 'No follow social link', 'html-social-share-buttons' ),
			'codeGenerator' => __( 'Code generator', 'html-social-share-buttons' ),
			'codeGeneratorDescription' => __( 'Generate embed code from the same icon set and selected networks.', 'html-social-share-buttons' ),
			'getPhpCode' => __( 'Get PHP Code', 'html-social-share-buttons' ),
			'getShortcode' => __( 'Get Shortcode', 'html-social-share-buttons' ),
			'close' => __( 'Close', 'html-social-share-buttons' ),
			'insertPlaceholder' => __( 'Insert share parameter placeholder', 'html-social-share-buttons' ),
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
