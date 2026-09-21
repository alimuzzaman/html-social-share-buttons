<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering\HookedShareUrlResolver;

final class BrowserUrlDescriptorTest extends WP_UnitTestCase {
	public function testDescriptorSharesTheSingleSelectedTemplateWithTheServerUrl(): void {
		$calls = 0;
		$templateFilter = static function ( $template, $networkId, $fallback ) use ( &$calls ) {
			$calls++;

			return $template . '&variant=' . $calls;
		};
		add_filter( ExtensionHooks::SHARE_TEMPLATE, $templateFilter, 10, 3 );

		try {
			$button = $this->buildButton( 'https://example.test/share?url=%%permalink%%' );
		} finally {
			remove_filter( ExtensionHooks::SHARE_TEMPLATE, $templateFilter, 10 );
		}

		$this->assertSame( 1, $calls );
		$this->assertStringContainsString( '&variant=1', $button->url() );
		$this->assertStringContainsString( '&variant=1', $button->browserUrlDescriptor()['template'] );
		$this->assertStringNotContainsString( '&variant=2', $button->browserUrlDescriptor()['template'] );
	}

	public function testCanonicalFinalUrlFilterDisablesTheBrowserDescriptor(): void {
		$filter = static function ( $url ) {
			return $url . '&mutated=1';
		};
		add_filter( ExtensionHooks::SHARE_URL, $filter, 10, 1 );

		try {
			$button = $this->buildButton();
		} finally {
			remove_filter( ExtensionHooks::SHARE_URL, $filter, 10 );
		}

		$this->assertStringContainsString( '&mutated=1', $button->url() );
		$this->assertSame( array(), $button->browserUrlDescriptor() );
	}

	public function testLegacyFinalUrlFilterDisablesTheBrowserDescriptor(): void {
		$filter = static function ( $url ) {
			return $url . '&legacy=1';
		};
		add_filter( 'zm_sh_placeholder', $filter, 10, 1 );

		try {
			$button = $this->buildButton();
		} finally {
			remove_filter( 'zm_sh_placeholder', $filter, 10 );
		}

		$this->assertStringContainsString( '&legacy=1', $button->url() );
		$this->assertSame( array(), $button->browserUrlDescriptor() );
	}

	public function testTemplatesWithoutPermalinkSlotsStayServerOnly(): void {
		$button = $this->buildButton( 'https://example.test/share?title=%%title%%' );

		$this->assertSame( array(), $button->browserUrlDescriptor() );
	}

	private function buildButton( $template = 'https://example.test/share?url=%%permalink%%' ) {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
		$builder = new BuildShareButtons(
			$networks,
			$iconSets,
			new HookedShareUrlResolver( new ResolveShareUrl(), new ExtensionHooks() )
		);
		$result = $builder->build(
			new RenderRequest(
				'default',
				'square',
				RenderPlacement::SHORTCODE,
				'',
				array( 'facebook' ),
				array( 'facebook' => $template ),
				'',
				false,
				array(),
				false,
				'legacy',
				false,
				true
			),
			new ShareContext(
				'https://example.test/post/?source=descriptor',
				'Descriptor title',
				'Descriptor description'
			)
		);

		return $result->buttons()[0];
	}
}
