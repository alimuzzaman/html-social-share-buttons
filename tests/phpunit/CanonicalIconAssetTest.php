<?php

use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

final class CanonicalIconAssetTest extends WP_UnitTestCase {
	public function testNewSvgSetsDoNotInheritHistoricalTwitterFilenames(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$registry = ( new ManifestIconSetProvider(
			dirname( __DIR__, 2 ) . '/resources/iconsets'
		) )->createRegistry( $networks );

		$this->assertSame( 'twitter.png', $registry->get( 'default' )->iconFile( 'x' ) );
		$this->assertSame( 'x.svg', $registry->get( 'bootstrap-solid' )->iconFile( 'x' ) );
		$this->assertSame( 'x.svg', $registry->get( 'tabler-outline' )->iconFile( 'x' ) );
	}
	public function testBuiltInAssetsUseOneReleasedSourceTreePerIconSet(): void {
		$root = dirname( __DIR__, 2 );
		$registry = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( ( new BuiltInNetworkProvider() )->createRegistry() );
		$resolver = new IconSetAssetResolver(
			$root,
			'https://example.test'
		);
		$legacyIconSetIds = array( 'default', 'flat', 'long-shadows', 'prajin' );

		foreach ( $registry->all() as $iconSet ) {
			$isLegacy = in_array( $iconSet->id(), $legacyIconSetIds, true );
			$stylesheet = $resolver->stylesheetPath( $iconSet );
			$preview = $resolver->previewPath( $iconSet );

			if ( $isLegacy ) {
				$this->assertStringStartsWith( $root . '/iconset/', $stylesheet );
				$this->assertStringStartsWith( $root . '/iconset/', $preview );
				$this->assertFileDoesNotExist(
					$root . '/assets/iconsets/' . $iconSet->id() . '/style.css',
					'Historical packs must not be duplicated under assets/iconsets.'
				);
			} else {
				$this->assertStringStartsWith( $root . '/assets/iconsets/', $stylesheet );
				$this->assertStringStartsWith( $root . '/assets/iconsets/', $preview );
			}

			$this->assertFileExists( $stylesheet );
			$this->assertFileExists( $preview );
			$this->assertAssetFormat( $preview );

			foreach ( $iconSet->shapes() as $shape ) {
				foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
					$canonical = $resolver->iconPath( $iconSet, $shape, $networkId );
					$this->assertFileExists( $canonical );
					$this->assertAssetFormat( $canonical );
				}
			}
		}
	}

	public function testEveryDeclaredShapeResolvesEachBundledIconAndRenderedAssetUrl(): void {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$registry = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$assets = new IconSetAssetResolver( $root, 'https://example.test/plugin' );
		$facade = new RenderFacade( $networks, $registry, $assets, new ExtensionHooks() );
		$expected = array(
			'default'         => array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			'flat'            => array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			'long-shadows'    => array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			'prajin'          => array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			'bootstrap-solid' => array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			'tabler-outline'  => array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
		);
		$context = new ShareContext(
			'https://example.test/icon-contract/?source=asset',
			'Icon asset contract'
		);

		$this->assertSame( array_keys( $expected ), $registry->ids() );
		foreach ( $registry->all() as $iconSet ) {
			$networkIds = $expected[ $iconSet->id() ];
			$this->assertSame( $networkIds, array_keys( $iconSet->iconFiles() ), $iconSet->id() . ' manifest networks' );

			foreach ( $iconSet->shapes() as $shape ) {
				$outcome = $facade->render(
					array(
						'iconset'      => $iconSet->id(),
						'iconset_type' => $shape,
						'icons'        => $networkIds,
					),
					0,
					$context
				);
				$printed = $outcome->printedIcons();

				$this->assertCount(
					count( $networkIds ),
					$printed,
					$iconSet->id() . '/' . $shape . ' rendered network count'
				);
				foreach ( $networkIds as $networkId ) {
					$key = $iconSet->id() . '_' . $shape . "\0_" . $networkId;
					$path = $assets->iconPath( $iconSet, $shape, $networkId );
					$url = $assets->iconUrl( $iconSet, $shape, $networkId );

					$this->assertArrayHasKey( $key, $printed, $key );
					$this->assertFileExists( $path, $key . ' file' );
					$this->assertSame( $iconSet->iconFile( $networkId ), $printed[ $key ]['image'], $key . ' filename' );
					$this->assertSame( $url, $printed[ $key ]['icon_asset_url'], $key . ' URL' );
					$this->assertStringEndsWith(
						'/' . rawurlencode( $shape ) . '/' . rawurlencode( $iconSet->iconFile( $networkId ) ),
						$url,
						$key . ' URL path'
					);
				}
			}
		}
	}

	public function testNewSvgSetsShipTheirMitLicenses(): void {
		$root = dirname( __DIR__, 2 ) . '/assets/iconsets';

		$this->assertFileExists( $root . '/licenses/bootstrap-icons-MIT.txt' );
		$this->assertFileExists( $root . '/licenses/tabler-icons-MIT.txt' );
		$this->assertStringContainsString(
			'Bootstrap Icons',
			(string) file_get_contents( $root . '/THIRD-PARTY-NOTICES.txt' )
		);
		$this->assertStringContainsString(
			'Tabler Icons',
			(string) file_get_contents( $root . '/THIRD-PARTY-NOTICES.txt' )
		);
	}

	public function testHistoricalXAssetsUseThePinnedSourceAndNoReleasedBirdBytesRemain(): void {
		$root = dirname( __DIR__, 2 );
		$generator = (string) file_get_contents( $root . '/scripts/generate-legacy-x-assets.js' );
		$this->assertStringContainsString( 'bootstrap-icons-v1.13.1', $generator );
		$this->assertStringContainsString( 'twitter-x.svg', $generator );
		$this->assertStringContainsString( '173e37e584ccb49cb87242a2e5444201da2d779cee1b1464732893302975950d', $generator );

		$releasedBirdHashes = array(
			'iconset/default/square/twitter.png'           => '502c078fc4cce000c3a7a811ee49ad0922882022077c77e5ecc93d203b9b07ed',
			'iconset/flat/square/Twitter.png'              => '4d3711c8d6b7b5d7c5896e6d50b975606a576cfd1e48c98b5036220cd2480ca6',
			'iconset/flat/circle/Twitter.png'              => 'c265941759f9ea70d9c43f9a90a6ca66991822e99eeca553a9e7d0b1eb2f6eb4',
			'iconset/long_shadow/square/twitter.png'       => '62dc5675f62b5188ed520ff26f4a922ff7d60451cdb4441ad7f861fe3ebcd282',
			'iconset/long_shadow/circle/twitter.png'       => '721ca8dc64165d526b9f3290d8ecf574030560f3eae8c8825c7d0ad239d0f272',
			'iconset/long_shadow/square/twitter_2.png'     => '51669fb199a610f322d1a9d042b2341080a2177092effebef8ee8526f9a2e006',
			'iconset/long_shadow/circle/twitter_2.png'     => 'a066443b7936c5f1de56f6a2c8f662f012a18b27f8b94f69a047959254683c22',
			'iconset/prajin/square/twitter.png'            => 'e705b92674d71b3e72791af4469870c23abb1ed86b82d63074e059a8dc5e5106',
			'iconset/prajin/circle/twitter.png'            => '2ceb714ad605663e8c00edcb435a50e5889ca1958d2af9c4cd3a5e313d2ec892',
		);

		foreach ( $releasedBirdHashes as $relativePath => $releasedHash ) {
			$this->assertFileExists( $root . '/' . $relativePath );
			$this->assertNotSame( $releasedHash, hash_file( 'sha256', $root . '/' . $relativePath ), $relativePath );
		}
	}

	public function testResolverUsesCanonicalIdsAndEncodedAssetNames(): void {
		$root = dirname( __DIR__, 2 );
		$registry = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( ( new BuiltInNetworkProvider() )->createRegistry() );
		$resolver = new IconSetAssetResolver(
			$root . '/assets/iconsets',
			'https://example.test/assets/iconsets/'
		);

		$this->assertSame(
			'https://example.test/iconset/long_shadow/circle/twitter.png',
			$resolver->iconUrl( $registry->get( 'long-shadows' ), 'circle', 'x' )
		);
	}

	public function testHistoricalPacksRemainAtTheirReleasedUrlsWithoutCanonicalCopies(): void {
		$root = dirname( __DIR__, 2 );
		$registry = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( ( new BuiltInNetworkProvider() )->createRegistry() );
		$resolver = new IconSetAssetResolver(
			$root . '/assets/iconsets',
			'https://example.test/assets/iconsets'
		);
		$historicalSets = array(
			'default'       => 'default',
			'flat'          => 'flat',
			'long-shadows'  => 'long_shadow',
			'prajin'        => 'prajin',
		);

		foreach ( $historicalSets as $id => $directory ) {
			$this->assertDirectoryExists( $root . '/iconset/' . $directory );
			$this->assertFileExists( $root . '/iconset/' . $directory . '/style.css' );
			$this->assertFileExists( $root . '/iconset/' . $directory . '/preview.png' );
			$this->assertDirectoryDoesNotExist( $root . '/assets/iconsets/' . $id );
			$this->assertSame(
				'https://example.test/iconset/' . $directory . '/',
				$resolver->setUrl( $registry->get( $id ) )
			);
		}
	}

	private function assertValidPng( $path ): void {
		$image = getimagesize( $path );

		$this->assertNotFalse( $image, 'Unreadable image metadata for ' . $path . '.' );
		$this->assertSame( IMAGETYPE_PNG, $image[2], 'Unexpected image format for ' . $path . '.' );
		$this->assertTrue( $image[0] > 0 && $image[0] <= 4096, 'Invalid image width for ' . $path . '.' );
		$this->assertTrue( $image[1] > 0 && $image[1] <= 4096, 'Invalid image height for ' . $path . '.' );
	}

	private function assertAssetFormat( $path ): void {
		if ( 'svg' === strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
			$this->assertSafeSvg( $path );
			return;
		}

		$this->assertValidPng( $path );
	}

	private function assertSafeSvg( $path ): void {
		$svg = (string) file_get_contents( $path );
		$lower = strtolower( $svg );

		foreach (
			array(
				'<!doctype',
				'<!entity',
				'<script',
				'<foreignobject',
				'<iframe',
				'<object',
				'<embed',
				'<image',
				'javascript:',
				'data:',
				'@import',
			) as $unsafeToken
		) {
			$this->assertStringNotContainsString( $unsafeToken, $lower, 'Unsafe SVG token in ' . $path . '.' );
		}
		$this->assertDoesNotMatchRegularExpression( '/\\son[a-z]+\\s*=/', $lower );
		$this->assertSame(
			0,
			preg_match( '/(?:href|xlink:href)\\s*=\\s*["\'](?!#)/i', $svg ),
			'SVG references must remain local fragments in ' . $path . '.'
		);

		$previousErrors = libxml_use_internal_errors( true );
		$document = new DOMDocument();
		$loaded = $document->loadXML( $svg, LIBXML_NONET );
		libxml_clear_errors();
		libxml_use_internal_errors( $previousErrors );

		$this->assertTrue( $loaded, 'Malformed SVG XML in ' . $path . '.' );
		$this->assertSame( 'svg', $document->documentElement->localName );
		$this->assertSame( 'http://www.w3.org/2000/svg', $document->documentElement->namespaceURI );
		$viewBox = preg_split( '/\\s+/', trim( $document->documentElement->getAttribute( 'viewBox' ) ) );
		$this->assertCount( 4, $viewBox, 'SVG viewBox must have four values in ' . $path . '.' );
		$this->assertTrue( is_numeric( $viewBox[2] ) && (float) $viewBox[2] > 0 );
		$this->assertTrue( is_numeric( $viewBox[3] ) && (float) $viewBox[3] > 0 );
	}
}
