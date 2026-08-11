<?php

use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;

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
