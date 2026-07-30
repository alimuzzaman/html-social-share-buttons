<?php

final class FrontendAssetContractTest extends WP_UnitTestCase {
	public function testRenderedIconsetsAreCollectedAndEnqueuedOnceByHistoricalHandle(): void {
		$runtime = new zm_social_share();

		$runtime->zm_sh_btn(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'icons' => array( 'facebook' => 1 ),
			)
		);
		$runtime->zm_sh_btn(
			array(
				'iconset' => 'flat',
				'iconset_type' => 'circle',
				'icons' => array( 'x' => 1 ),
			)
		);
		$runtime->zm_sh_btn(
			array(
				'iconset' => 'flat',
				'iconset_type' => 'circle',
				'icons' => array( 'x' => 1 ),
			)
		);

		$runtime->register_styles();
		$styles = wp_styles();

		$this->assertContains( 'social-share-default', $styles->queue );
		$this->assertContains( 'social-share-flat', $styles->queue );
		$this->assertSame(
			$runtime->iconsets->get_iconset( 'default' )->stylesheet_url,
			$styles->registered['social-share-default']->src
		);
		$this->assertSame(
			$runtime->iconsets->get_iconset( 'flat' )->stylesheet_url,
			$styles->registered['social-share-flat']->src
		);
		$this->assertSame( '2.2.4', $styles->registered['social-share-default']->ver );
		$this->assertSame( '2.2.4', $styles->registered['social-share-flat']->ver );
		$this->assertSame(
			1,
			count( array_keys( $styles->queue, 'social-share-flat', true ) )
		);
	}

	public function testInlineIconRulesAreDeduplicatedAndUseLegacyAssetUrls(): void {
		$runtime = new zm_social_share();
		$options = array(
			'iconset' => 'default',
			'iconset_type' => 'square',
			'icons' => array( 'x' => 1 ),
		);

		$runtime->zm_sh_btn( $options );
		$runtime->zm_sh_btn( $options );

		ob_start();
		$runtime->icon_styles();
		$css = (string) ob_get_clean();
		$legacyIconSet = $runtime->iconsets->get_iconset( 'default' );

		$this->assertSame( 1, substr_count( $css, '.zmshbt.default.square .twitter' ) );
		$this->assertStringContainsString(
			"background-image:url('" . $legacyIconSet->url . 'square/twitter.png' . "')",
			$css
		);
		$this->assertStringContainsString( '.zmshbt.left', $css );
		$this->assertStringContainsString( '.zmshbt.right', $css );
	}

	public function testNoRenderedButtonsKeepsTheHistoricalDefaultStylesheetFallback(): void {
		$runtime = new zm_social_share();

		$runtime->register_styles();

		$this->assertContains( 'social-share-default', wp_styles()->queue );
		$this->assertStringEndsWith(
			'/iconset/default/style.css',
			wp_styles()->registered['social-share-default']->src
		);
	}
}
