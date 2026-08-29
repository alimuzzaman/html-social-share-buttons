<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;

final class BuildShareButtonsTest extends WP_UnitTestCase {
	private $builder;

	protected function setUp(): void {
		parent::setUp();
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider(
			dirname( __DIR__, 2 ) . '/resources/iconsets'
		) )->createRegistry( $networks );
		$this->builder = new BuildShareButtons( $networks, $iconSets, new ResolveShareUrl() );
	}

	public function testCanonicalResultResolvesNetworksUrlsAssetsAndRelTokens(): void {
		$request = new RenderRequest(
			'flat',
			'circle',
			RenderPlacement::BLOCK,
			'Share this',
			array( 'facebook', 'x', 'unknown', 'facebook' ),
			array(
				'x' => 'https://example.test/share?url=%%permalink%%&title=%%title%%',
			),
			'',
			true
		);
		$context = new ShareContext(
			'https://example.test/post/?a=1',
			'Title & more',
			'Description',
			'https://example.test/image.jpg'
		);

		$result = $this->builder->build( $request, $context );

		$this->assertSame( 'flat', $result->iconSet()->id() );
		$this->assertSame( 'circle', $result->shape() );
		$this->assertSame( RenderPlacement::BLOCK, $result->placement() );
		$this->assertSame( 'Share this', $result->heading() );
		$this->assertSame( array( 'nofollow', 'noopener', 'noreferrer' ), $result->relTokens() );
		$this->assertCount( 2, $result->buttons() );
		$this->assertSame( 'facebook', $result->buttons()[0]->network()->id() );
		$this->assertSame( 'Facebook.png', $result->buttons()[0]->iconFile() );
		$this->assertSame( 'x', $result->buttons()[1]->network()->cssClass() );
		$this->assertSame( 'Twitter.png', $result->buttons()[1]->iconFile() );
		$this->assertSame(
			'https://example.test/share?url=https%3A%2F%2Fexample.test%2Fpost%2F%3Fa%3D1&title=Title%20%26%20more',
			$result->buttons()[1]->url()
		);
	}

	public function testUnknownIconSetAndUnsupportedShapeUseCanonicalFallbacks(): void {
		$request = new RenderRequest(
			'missing',
			'circle',
			RenderPlacement::SHORTCODE,
			'',
			array( 'facebook', 'mail' )
		);

		$result = $this->builder->build(
			$request,
			new ShareContext( 'https://example.test/', 'Example' )
		);

		$this->assertSame( 'default', $result->iconSet()->id() );
		$this->assertSame( 'square', $result->shape() );
		$this->assertCount( 2, $result->buttons() );
	}

	public function testIconSetCapabilityFiltersUnavailableNetworks(): void {
		$request = new RenderRequest(
			'prajin',
			'square',
			RenderPlacement::WIDGET,
			'',
			array( 'facebook', 'unknown' )
		);

		$result = $this->builder->build(
			$request,
			new ShareContext( 'https://example.test/', 'Example' )
		);

		$this->assertCount( 1, $result->buttons() );
		$this->assertSame( 'facebook', $result->buttons()[0]->network()->id() );
	}

	public function testExplicitPermalinkOverrideIsEncodedWithoutLegacyPlaceholderCoercion(): void {
		$request = new RenderRequest(
			'default',
			'square',
			RenderPlacement::PHP_API,
			'',
			array( 'facebook' ),
			array(),
			'https://other.example/path'
		);

		$result = $this->builder->build(
			$request,
			new ShareContext( 'https://example.test/', 'Example' )
		);

		$this->assertSame(
			'https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fother.example%2Fpath',
			$result->buttons()[0]->url()
		);
	}
}
