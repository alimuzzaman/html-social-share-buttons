<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyIconSetAssetMap;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;

final class CanonicalIconAssetTest extends WP_UnitTestCase {
	public function testCanonicalAssetsExistAndMatchTheReleasedVisualFiles(): void {
		$root = dirname( __DIR__, 2 );
		$registry = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( ( new BuiltInNetworkProvider() )->createRegistry() );
		$resolver = new IconSetAssetResolver(
			$root . '/assets/iconsets',
			'https://example.test/assets/iconsets'
		);
		$legacyMap = new LegacyIconSetAssetMap();

		foreach ( $registry->all() as $iconSet ) {
			$legacyDirectory = $root . '/iconset/' . $legacyMap->directory( $iconSet );
			$this->assertFileExists( $resolver->stylesheetPath( $iconSet ) );
			$this->assertFileExists( $resolver->previewPath( $iconSet ) );
			$this->assertSame(
				hash_file( 'sha256', $legacyDirectory . '/style.css' ),
				hash_file( 'sha256', $resolver->stylesheetPath( $iconSet ) )
			);
			$this->assertSame(
				hash_file( 'sha256', $legacyDirectory . '/preview.png' ),
				hash_file( 'sha256', $resolver->previewPath( $iconSet ) )
			);
			$this->assertValidPng( $resolver->previewPath( $iconSet ) );

			foreach ( $iconSet->shapes() as $shape ) {
				foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
					$canonical = $resolver->iconPath( $iconSet, $shape, $networkId );
					$legacy = $legacyDirectory . '/' . $shape . '/' .
						$legacyMap->iconFile( $iconSet, $networkId );
					$this->assertFileExists( $canonical );
					$this->assertSame(
						hash_file( 'sha256', $legacy ),
						hash_file( 'sha256', $canonical ),
						$iconSet->id() . '/' . $shape . '/' . $networkId
					);
					if ( 'svg' === strtolower( pathinfo( $canonical, PATHINFO_EXTENSION ) ) ) {
						$this->assertSafeSvg( $canonical );
					} else {
						$this->assertValidPng( $canonical );
					}
				}
			}
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
			'https://example.test/assets/iconsets/long-shadows/circle/x.png',
			$resolver->iconUrl( $registry->get( 'long-shadows' ), 'circle', 'x' )
		);
	}

	private function assertValidPng( $path ): void {
		$image = getimagesize( $path );

		$this->assertNotFalse( $image, 'Unreadable image metadata for ' . $path . '.' );
		$this->assertSame( IMAGETYPE_PNG, $image[2], 'Unexpected image format for ' . $path . '.' );
		$this->assertTrue( $image[0] > 0 && $image[0] <= 4096, 'Invalid image width for ' . $path . '.' );
		$this->assertTrue( $image[1] > 0 && $image[1] <= 4096, 'Invalid image height for ' . $path . '.' );
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
