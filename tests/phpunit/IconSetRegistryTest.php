<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;
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
		$this->assertSame( 'Twitter.png', $registry->get( 'flat' )->iconFile( 'x' ) );
		$this->assertSame( 'iconset/long_shadow', $registry->get( 'long-shadows' )->assetPath() );
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

	public function testNewSelectionsHideLegacyDefaultWithoutRemovingItFromTheRegistry(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$registry = ( new ManifestIconSetProvider( dirname( __DIR__, 2 ) . '/resources/iconsets' ) )
			->createRegistry( $networks );

		$freshIds = array_map(
			static function ( IconSet $iconSet ) {
				return $iconSet->id();
			},
			IconSetSelectionPolicy::choices( $registry )
		);
		$legacyIds = array_map(
			static function ( IconSet $iconSet ) {
				return $iconSet->id();
			},
			IconSetSelectionPolicy::choices( $registry, 'default' )
		);

		$this->assertSame( 'bootstrap-solid', $freshIds[0] );
		$this->assertNotContains( 'default', $freshIds );
		$this->assertContains( 'default', $legacyIds );
		$this->assertTrue( $registry->has( 'default' ) );
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
