<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyIconSetAssetMap;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;

final class IconSetRegistryTest extends WP_UnitTestCase {
	public function testBuiltInIconSetsLoadFromExplicitManifests(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$provider = new ManifestIconSetProvider( dirname( __DIR__, 2 ) . '/resources/iconsets' );
		$registry = $provider->createRegistry( $networks );

		$this->assertSame(
			array(
				'default',
				'flat',
				'long-shadows',
				'prajin',
				'bootstrap-solid',
				'tabler-outline',
			),
			$registry->ids()
		);
		$this->assertSame( array( 'square' ), $registry->get( 'default' )->shapes() );
		$this->assertSame( array( 'square', 'circle' ), $registry->get( 'flat' )->shapes() );
		$this->assertSame( 'x.png', $registry->get( 'flat' )->iconFile( 'x' ) );
		$this->assertSame(
			'Twitter.png',
			( new LegacyIconSetAssetMap() )->iconFile( $registry->get( 'flat' ), 'x' )
		);
		$this->assertSame(
			'long_shadow',
			( new LegacyIconSetAssetMap() )->directory( $registry->get( 'long-shadows' ) )
		);
		$this->assertFalse( $registry->get( 'prajin' )->hasIcon( 'mail' ) );
		foreach ( array( 'bootstrap-solid', 'tabler-outline' ) as $iconSetId ) {
			$this->assertSame( array( 'square', 'circle' ), $registry->get( $iconSetId )->shapes() );
			$this->assertSame(
				array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
				array_keys( $registry->get( $iconSetId )->iconFiles() )
			);
		}
	}

	public function testIconSetsCannotReferenceUnknownNetworks(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$registry = new IconSetRegistry( $networks );

		$this->expectException( InvalidArgumentException::class );
		$registry->register(
			new IconSet(
				'example',
				'Example',
				'style.css',
				'preview.png',
				array( 'square' ),
				array( 'unknown' => 'unknown.svg' )
			)
		);
	}

	public function testManifestProviderDoesNotDiscoverArbitraryFiles(): void {
		$temporaryDirectory = sys_get_temp_dir() . '/hssb-iconsets-' . uniqid( '', true );
		mkdir( $temporaryDirectory );
		copy( dirname( __DIR__, 2 ) . '/resources/iconsets/default.php', $temporaryDirectory . '/default.php' );

		try {
			$provider = new ManifestIconSetProvider( $temporaryDirectory );
			$this->expectException( RuntimeException::class );
			$provider->createRegistry( ( new BuiltInNetworkProvider() )->createRegistry() );
		} finally {
			unlink( $temporaryDirectory . '/default.php' );
			rmdir( $temporaryDirectory );
		}
	}
}
