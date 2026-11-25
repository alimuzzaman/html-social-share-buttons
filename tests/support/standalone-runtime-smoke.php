<?php

/**
 * Shared standalone runtime smoke test for decoupled plugin layers under strict
 * E_ALL error handling. Used both for multi-version PHP 7.0-8.5 runtime checks
 * and hardened `-d disable_functions=...` runtime verification.
 *
 * Must remain strictly PHP 7.0-compatible.
 */

if ( ! function_exists( 'hssb_run_standalone_runtime_smoke' ) ) {
	function hssb_run_standalone_runtime_smoke( $root ) {
		$root = rtrim( (string) $root, '/\\' );

		set_error_handler(
			static function ( $severity, $message, $file, $line ) {
				throw new ErrorException( $message, 0, $severity, $file, $line );
			}
		);

		require_once $root . '/vendor/autoload.php';

		if ( ! function_exists( 'apply_filters' ) ) {
			function apply_filters( $tag, $value ) {
				unset( $tag );

				return $value;
			}
		}
		if ( ! function_exists( 'sanitize_key' ) ) {
			function sanitize_key( $key ) {
				return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
			}
		}
		if ( ! function_exists( 'sanitize_text_field' ) ) {
			function sanitize_text_field( $str ) {
				return is_scalar( $str ) ? trim( (string) preg_replace( '/<[^>]*>/', '', (string) $str ) ) : '';
			}
		}
		if ( ! function_exists( 'sanitize_textarea_field' ) ) {
			function sanitize_textarea_field( $str ) {
				return is_scalar( $str ) ? trim( (string) $str ) : '';
			}
		}
		if ( ! function_exists( 'esc_attr' ) ) {
			function esc_attr( $text ) {
				return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
			}
		}
		if ( ! function_exists( 'esc_html' ) ) {
			function esc_html( $text ) {
				return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
			}
		}
		if ( ! function_exists( 'esc_url' ) ) {
			function esc_url( $url ) {
				return str_replace( '&', '&#038;', (string) $url );
			}
		}
		if ( ! function_exists( 'esc_url_raw' ) ) {
			function esc_url_raw( $url ) {
				return trim( (string) $url );
			}
		}
		if ( ! function_exists( '__' ) ) {
			function __( $text ) {
				return $text;
			}
		}
		if ( ! function_exists( 'esc_attr__' ) ) {
			function esc_attr__( $text ) {
				return esc_attr( (string) $text );
			}
		}
		if ( ! function_exists( 'esc_html__' ) ) {
			function esc_html__( $text ) {
				return esc_html( (string) $text );
			}
		}
		if ( ! function_exists( 'is_email' ) ) {
			function is_email( $email ) {
				return false !== filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : false;
			}
		}
		if ( ! function_exists( 'wp_parse_url' ) ) {
			function wp_parse_url( $url, $component = -1 ) {
				return parse_url( $url, $component );
			}
		}
		if ( ! function_exists( 'wp_json_encode' ) ) {
			function wp_json_encode( $data, $options = 0, $depth = 512 ) {
				return json_encode( $data, $options, $depth );
			}
		}

		try {
			$networks = ( new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider() )->createRegistry();
			$iconSets = ( new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
			$resolver = new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver( $root . '/assets/iconsets', 'https://example.test/assets/iconsets' );

			$defaultSet = $iconSets->get( \Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy::NEW_DEFAULT_ID );
			if ( '' === $resolver->stylesheetUrl( $defaultSet ) ) {
				throw new RuntimeException( 'IconSetAssetResolver returned empty stylesheet URL.' );
			}

			$codec     = new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec();
			$mapper    = new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper();
			$schema    = new \Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema( $networks->ids(), $iconSets->ids(), array( 'square', 'circle' ) );
			$sanitized = ( new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer( $schema ) )->sanitize(
				$mapper->toCanonical(
					array(
						'title'         => 'Standalone runtime title',
						'iconset'       => 'flat',
						'iconset_type'  => 'circle',
						'icons'         => array( 'facebook' => '1', 'x' => '1', 'copy' => '1', 'native' => '1', 'mail' => '1' ),
						'show_in'       => array( 'show_left' => '1', 'show_after_post' => '1' ),
						'profile_links' => array( 'facebook' => 'https://www.facebook.com/example', 'mail' => 'mailto:hello@example.com' ),
						'excludes'      => '42, sample-page',
					)
				)
			);
			$stored = $mapper->toStoredReplacement( $sanitized, array( 'title' => 'Standalone runtime title', 'iconset' => 'flat', 'iconset_type' => 'circle' ), array() );
			if ( 'flat' !== $stored['iconset'] || 'circle' !== $stored['iconset_type'] ) {
				throw new RuntimeException( 'OptionSettingsRequestMapper standalone verification failed.' );
			}

			$encoded = $codec->encode( $sanitized, array( 'custom_ext' => 'keep' ) );
			$decoded = $codec->decode( $encoded );
			if ( 'keep' !== $encoded['custom_ext'] || 'Standalone runtime title' !== $decoded->title() ) {
				throw new RuntimeException( 'OptionSettingsCodec standalone round-trip failed.' );
			}

			$policy = new \Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy();
			if ( ! $policy->matches( 42, 'other', 'Title', '42, sample-page' ) || $policy->matches( 99, 'other', 'Title', '42, sample-page' ) ) {
				throw new RuntimeException( 'ExcludedContentPolicy standalone verification failed.' );
			}

			$builder = new \Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons(
				$networks,
				$iconSets,
				new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering\HookedShareUrlResolver(
					new \Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl(),
					new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks()
				)
			);
			$requestMapper = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderRequestMapper();
			$htmlRenderer  = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\HtmlRenderer();
			$request       = $requestMapper->map(
				array(
					'title'                => 'Share now',
					'iconset'              => 'flat',
					'iconset_type'         => 'circle',
					'icons'                => array( 'facebook' => '1', 'x' => '1', 'copy' => '1', 'native' => '1', 'mail' => '1' ),
					'browser_url_eligible' => true,
					'browser_url_source'   => 'automatic_page',
				)
			);
			$result = $builder->build(
				$request,
				new \Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext( 'https://example.test/post/?a=1&b=2', 'Tom & Jerry', 'Desc', '' )
			);
			$html = $htmlRenderer->render( $request, $result );
			if ( false === strpos( $html, 'data-hssb-native-share' ) || false === strpos( $html, 'data-hssb-copy-link' ) ) {
				throw new RuntimeException( 'HtmlRenderer failed to produce expected output in standalone smoke.' );
			}

			$composer = new \Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer();
			$composed = $composer->compose(
				'<p>Body</p>',
				\Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults::create(),
				static function () {
					return '[SHARE]';
				},
				true
			);
			if ( false === strpos( $composed, '[SHARE]' ) ) {
				throw new RuntimeException( 'ContentPlacementComposer failed in standalone smoke.' );
			}

			$floating = ( new \Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner() )->enabled(
				\Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults::create()
			);
			if ( empty( $floating ) ) {
				throw new RuntimeException( 'FloatingPlacementPlanner returned no placements in standalone smoke.' );
			}
		} finally {
			restore_error_handler();
		}
	}
}
