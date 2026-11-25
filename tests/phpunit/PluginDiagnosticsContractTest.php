<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ExcludedContentLookup;

final class PluginDiagnosticsContractTest extends WP_UnitTestCase {
	public function testCorePluginPathsProduceZeroPluginOwnedPhpWarningsNoticesOrDeprecations(): void {
		$pluginRoot = realpath( dirname( __DIR__, 2 ) );
		$pluginRoot = false !== $pluginRoot ? $pluginRoot . DIRECTORY_SEPARATOR : dirname( __DIR__, 2 ) . DIRECTORY_SEPARATOR;

		$previousReporting = error_reporting( E_ALL );
		set_error_handler(
			static function ( $severity, $message, $file, $line ) use ( $pluginRoot ) {
				$frames   = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
				$frames[] = array( 'file' => $file );
				foreach ( $frames as $frame ) {
					$path = isset( $frame['file'] ) ? (string) $frame['file'] : '';
					if ( 0 === strpos( $path, $pluginRoot ) && false === strpos( $path, $pluginRoot . 'vendor/' ) ) {
						throw new ErrorException( $message, 0, $severity, $file, $line );
					}
				}

				return false;
			}
		);

		$postId = self::factory()->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Diagnostics Target & Title',
				'post_content' => 'Diagnostics body content',
			)
		);

		try {
			$plugin   = LegacyApi::plugin();
			$renderer = $plugin->renderer();

			$outcome = $renderer->render(
				array(
					'iconset'              => 'bootstrap-solid',
					'icons'                => array( 'facebook', 'x', 'pinterest', 'copy', 'native', 'mail' ),
					'browser_url_eligible' => true,
					'browser_url_source'   => 'automatic_page',
					'placement'            => RenderPlacement::FLOATING_LEFT,
					'class'                => 'left',
				),
				0,
				new ShareContext( 'https://example.test/diag/?a=1&b=2', 'Tom & Jerry', 'Desc', 'https://example.test/img.jpg' )
			);
			$this->assertStringContainsString( 'zmshbt', $outcome->html() );

			$admin = $plugin->admin();
			ob_start();
			$admin->enqueueAssets( 'settings_page_zm_shbt_opt' );
			ob_end_clean();

			$sanitized = $admin->sanitizeAdmin(
				array(
					'title'        => 'Diagnostics Title',
					'iconset'      => 'flat',
					'iconset_type' => 'circle',
					'show_in'      => array( 'show_left' => '1', 'show_after_post' => '1' ),
					'icons'        => array( 'facebook' => '1', 'x' => '1', 'mail' => '1' ),
					'excludes'     => $postId . ', missing-custom',
				)
			);
			$this->assertSame( 'circle', $sanitized['iconset_type'] );

			$shortcodeOut = do_shortcode( '[zm_sh_btn icons="facebook,copy,native"]' );
			$this->assertStringContainsString( 'zmshbt', $shortcodeOut );

			$blockOut = $plugin->block()->renderShareBlock( array( 'icons' => array( 'facebook', 'x' ) ) );
			$this->assertStringContainsString( 'zmshbt', $blockOut );

			ob_start();
			$plugin->metabox()->render( get_post( $postId ) );
			$metaboxHtml = (string) ob_get_clean();
			$this->assertStringContainsString( 'zm_sh_mtbox', $metaboxHtml );

			$legacy = new zm_social_share();
			$this->assertIsArray( $legacy->options );
		} finally {
			wp_dequeue_script( 'zm_sh_admin_scripts' );
			wp_deregister_script( 'zm_sh_admin_scripts' );
			wp_dequeue_style( 'zm_sh_admin_styles' );
			wp_deregister_style( 'zm_sh_admin_styles' );
			wp_dequeue_style( 'hssb-button-appearance' );
			wp_deregister_style( 'hssb-button-appearance' );
			restore_error_handler();
			error_reporting( $previousReporting );
		}
	}

	public function testExcludedContentLookupResolvesNumericIdsAndTitlesWithoutCtype(): void {
		$pageId = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Privacy Notice',
				'post_name'   => 'privacy-notice',
			)
		);
		$lookup = new ExcludedContentLookup( new ExcludedContentPolicy() );
		$result = $lookup->resolve( $pageId . ', unknown-entry, 12a, Privacy Notice' );

		$this->assertSame( array( 'unknown-entry', '12a' ), $result['custom'] );
		$this->assertCount( 2, $result['items'] );
		$this->assertSame( (string) $pageId, $result['items'][0]['id'] );
		$this->assertSame( (string) $pageId, $result['items'][1]['id'] );
	}
}
