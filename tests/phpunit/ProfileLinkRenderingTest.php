<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network\LegacyNetworkMapper;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyHtmlRenderer;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyRenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;

final class ProfileLinkRenderingTest extends WP_UnitTestCase {
	private $builder;
	private $renderer;

	protected function setUp(): void {
		parent::setUp();
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider(
			dirname( __DIR__, 2 ) . '/resources/iconsets'
		) )->createRegistry( $networks );
		$this->builder = new BuildShareButtons( $networks, $iconSets, new ResolveShareUrl() );
		$this->renderer = new LegacyHtmlRenderer( new LegacyNetworkMapper() );
	}

	public function testCanonicalBuilderUsesRegistryOrderAndDoesNotRequireShareToggle(): void {
		$request = new RenderRequest(
			'default',
			'square',
			RenderPlacement::PHP_API,
			'',
			array( 'x' ),
			array(),
			'',
			true,
			array(
				'mail' => 'mailto:hello@example.com',
				'facebook' => 'https://facebook.com/example',
				'unknown' => 'https://example.com/profile',
			)
		);

		$result = $this->builder->build(
			$request,
			new ShareContext( 'https://example.test/post', 'Example' )
		);

		$this->assertSame( array( 'x' ), $this->networkIds( $result->buttons() ) );
		$this->assertSame( array( 'facebook', 'mail' ), $this->networkIds( $result->profileLinks() ) );
		$this->assertSame( 'facebook.png', $result->profileLinks()[0]->iconFile() );
		$this->assertSame( 'mailto:hello@example.com', $result->profileLinks()[1]->url() );
	}

	public function testCanonicalRequestRejectsUnsafeProfileDestinations(): void {
		$this->expectException( InvalidArgumentException::class );

		new RenderRequest(
			'default',
			'square',
			RenderPlacement::PHP_API,
			'',
			array(),
			array(),
			'',
			false,
			array( 'facebook' => 'http://facebook.com/example' )
		);
	}

	public function testRendererAppendsAccessibleProfileLinksWithoutChangingShareMarkup(): void {
		$request = new RenderRequest(
			'default',
			'square',
			RenderPlacement::PHP_API,
			'',
			array( 'facebook' ),
			array(),
			'',
			true,
			array(
				'facebook' => 'https://facebook.com/example',
				'mail' => 'mailto:hello@example.com',
			)
		);
		$result = $this->builder->build(
			$request,
			new ShareContext( 'https://example.test/post', 'Example' )
		);
		$html = $this->renderer->render( $request, $result );

		$this->assertStringContainsString(
			"<a class='facebook' target='_blank' href='https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fexample.test%2Fpost' rel='nofollow noopener noreferrer'></a>\n",
			$html
		);
		$this->assertStringContainsString(
			"class='facebook zmshbt-profile-link' data-zmshbt-kind='profile' target='_blank' rel='nofollow noopener noreferrer' href='https://facebook.com/example' aria-label='Visit our Facebook profile'",
			$html
		);
		$this->assertStringContainsString(
			"class='mail zmshbt-profile-link' data-zmshbt-kind='profile' href='mailto:hello@example.com' aria-label='Contact us by email'",
			$html
		);
		$this->assertSame( 1, substr_count( $html, "target='_blank' href='https://www.facebook.com" ) );
	}

	public function testAbsentProfilesPreserveTheHistoricalHtmlExactly(): void {
		$request = new RenderRequest(
			'default',
			'square',
			RenderPlacement::PHP_API,
			'',
			array()
		);
		$result = $this->builder->build(
			$request,
			new ShareContext( 'https://example.test/post', 'Example' )
		);

		$this->assertSame(
			"<div class='zmshbt in_php_function default square'></div>",
			$this->renderer->render( $request, $result )
		);
	}

	public function testCompatibilityFacadeCollectsProfileOnlyIconCssAndRejectsUnsafeUrls(): void {
		global $zm_sh;
		$facade = new LegacyRenderFacade( ( new BuiltInNetworkProvider() )->createRegistry() );
		$outcome = $facade->render(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'icons' => array(),
				'profile_links' => array(
					'facebook' => 'https://facebook.com/example',
					'x' => 'javascript:alert(1)',
				),
			),
			$zm_sh->iconsets
		);

		$this->assertStringContainsString( 'https://facebook.com/example', $outcome->html() );
		$this->assertStringNotContainsString( 'javascript:', $outcome->html() );
		$this->assertSame( array( "default_square\0_facebook" ), array_keys( $outcome->printedIcons() ) );
	}

	public function testAnalyticsExcludesProfilesOnlyWhenTheyAreConfigured(): void {
		$runtime = new zm_social_share();
		$runtime->options = array(
			'g_analytics' => true,
			'profile_links' => array( 'facebook' => 'https://facebook.com/example' ),
			'show_in' => array(),
		);
		ob_start();
		$runtime->footer();
		$withProfiles = (string) ob_get_clean();

		$this->assertStringContainsString(
			"jQuery('.zmshbt a:not(.zmshbt-profile-link)').on('click'",
			$withProfiles
		);

		$runtime = new zm_social_share();
		$runtime->options = array(
			'g_analytics' => true,
			'show_in' => array(),
		);
		ob_start();
		$runtime->footer();
		$withoutProfiles = (string) ob_get_clean();

		$this->assertStringContainsString( "jQuery('.zmshbt a').on('click'", $withoutProfiles );
		$this->assertStringNotContainsString( 'a:not(.zmshbt-profile-link)', $withoutProfiles );
	}

	public function testGlobalProfilesAreInheritedByShortcodeStyleRenderOverrides(): void {
		global $zm_sh;

		$runtime = new zm_social_share();
		$runtime->options = array(
			'profile_links' => array( 'facebook' => 'https://facebook.com/example' ),
		);
		$zm_sh = $runtime;

		$html = $runtime->zm_sh_btn(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'icons' => array( 'x' => 'on' ),
				'class' => 'in_shortcode',
			)
		);

		$this->assertStringContainsString( 'https://facebook.com/example', $html );
		$this->assertStringContainsString( 'zmshbt-profile-link', $html );

		$shortcodeHtml = zm_sh_shortcode_cb( array( 'icons' => 'x' ) );
		$blockHtml = zm_sh_render_block( array( 'icons' => array( 'x' ) ) );
		$this->assertStringContainsString( 'https://facebook.com/example', $shortcodeHtml );
		$this->assertStringContainsString( 'https://facebook.com/example', $blockHtml );
	}

	private function networkIds( array $resolvedLinks ) {
		return array_map(
			static function ( $resolvedLink ) {
				return $resolvedLink->network()->id();
			},
			$resolvedLinks
		);
	}
}
