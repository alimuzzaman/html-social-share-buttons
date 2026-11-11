<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Frontend\FrontendFeatureRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

final class NativeShareTest extends WP_UnitTestCase {
	public function testNativeAssetsAndTitleUseTheResolvedPostContext(): void {
		$outcome = LegacyApi::plugin()->renderer()->render(
			array( 'icons' => array( 'native' ), 'iconset' => 'bootstrap-solid', 'url' => 'https://example.test/custom/?a=1&b=2' ),
			0,
			new ShareContext( 'https://example.test/ignored/', 'Loop title & café' )
		);
		$this->assertSame( array( FrontendFeatureRegistry::NATIVE_SHARE ), $outcome->frontendFeatures() );
		$this->assertStringContainsString( "data-hssb-native-title='Loop title &amp; café'", $outcome->html() );
		$this->assertStringContainsString( "data-hssb-native-url='https://example.test/custom/?a=1&#038;b=2'", $outcome->html() );
		$this->assertStringNotContainsString( 'data-hssb-copy-link', $outcome->html() );
	}

	public function testNativeAndCopyKeepOnlyOneVisibleFallback(): void {
		$outcome = LegacyApi::plugin()->renderer()->render(
			array( 'icons' => array( 'native', 'copy' ), 'iconset' => 'bootstrap-solid', 'url' => 'https://example.test/' )
		);
		$this->assertSame( array( FrontendFeatureRegistry::COPY_LINK, FrontendFeatureRegistry::NATIVE_SHARE ), $outcome->frontendFeatures() );
		$this->assertStringContainsString( "<span class='native' hidden style='display:none'", $outcome->html() );
		$this->assertSame( 1, substr_count( $outcome->html(), "data-hssb-copy-link='1'" ) );
	}

	public function testFinalUrlHookRemainsAuthoritative(): void {
		$callback = static function () { return 'https://example.test/filtered/'; };
		add_filter( 'zm_sh_placeholder', $callback );
		try {
			$outcome = LegacyApi::plugin()->renderer()->render(
				array( 'icons' => array( 'native' ), 'iconset' => 'bootstrap-solid', 'url' => 'https://example.test/original/' )
			);
			$this->assertStringContainsString( "data-hssb-native-url='https://example.test/filtered/'", $outcome->html() );
			$this->assertStringNotContainsString( 'data-hssb-browser-descriptor', $outcome->html() );
		} finally {
			remove_filter( 'zm_sh_placeholder', $callback );
		}
	}
}
