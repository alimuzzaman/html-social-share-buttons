<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyIconSetAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;

final class LegacyRegistryAdapterTest extends WP_UnitTestCase {
	public function testBuiltInIconsetsExposeReleasedHistoricalAssetsWithoutDefinitionShims(): void {
		global $zm_sh;

		$plugin = LegacyApi::plugin();
		$canonical = $plugin->iconSets()->get( 'default' );
		$legacy = $zm_sh->iconsets->get_iconset( 'default' );

		$this->assertSame(
			rtrim( $plugin->assets()->setPath( $canonical ), '/\\' ),
			rtrim( $legacy->dir, '/\\' )
		);
		$this->assertFileExists( $legacy->preview_img_dir );
		$this->assertSame( $plugin->assets()->setUrl( $canonical ), $legacy->url );
		$this->assertFileExists( $plugin->assets()->stylesheetPath( $canonical ) );
	}

	public function testBuiltInLegacyObjectsAreValueAdaptersOverCanonicalDefinitions(): void {
		global $zm_sh;

		$canonical = LegacyApi::plugin()->iconSets()->get( 'flat' );
		$legacy = $zm_sh->iconsets->get_iconset( 'flat' );

		$this->assertSame( $canonical->id(), $legacy->id );
		$this->assertSame( $canonical->label(), $legacy->name );
		$this->assertSame( $canonical->shapes(), $legacy->types );
		$this->assertSame( $canonical->stylesheet(), $legacy->stylesheet );
		$this->assertSame( $canonical->preview(), $legacy->preview_img );
		$this->assertSame( array_keys( $canonical->iconFiles() ), array_keys( $legacy->icons ) );
		$this->assertSame( 'Twitter.png', $legacy->icons['x']['image'] );
		$this->assertSame( 'twitter', $legacy->icons['x']['class'] );
		$this->assertSame( 'X (formerly Twitter)', $legacy->icons['x']['name'] );
	}

	public function testThirdPartyLegacyIconsetRegistersValidatedCanonicalDefinitions(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = new IconSetRegistry( $networks );
		$adapter = new LegacyIconSetAdapter();
		$legacy = (object) array(
			'id' => 'community',
			'name' => 'Community',
			'stylesheet' => 'community.css',
			'preview_img' => 'community-preview.png',
			'types' => array( 'square', 'circle' ),
			'icons' => array(
				'mastodon' => array(
					'name' => 'Mastodon',
					'class' => 'mastodon',
					'image' => 'mastodon.svg',
					'url' => 'https://example.social/share?text=%%title%%&url=%%permalink%%',
				),
			),
		);

		$this->assertTrue( $adapter->register( $legacy, $iconSets, $networks ) );
		$this->assertTrue( $networks->has( 'mastodon' ) );
		$this->assertSame(
			array( '%%title%%', '%%permalink%%' ),
			$networks->get( 'mastodon' )->placeholders()
		);
		$this->assertTrue( $iconSets->has( 'community' ) );
		$this->assertSame( 'mastodon.svg', $iconSets->get( 'community' )->iconFile( 'mastodon' ) );
	}

	public function testThirdPartyDuplicateRegistrationDoesNotMutateAnImmutableCanonicalRegistry(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = new IconSetRegistry( $networks );
		$adapter = new LegacyIconSetAdapter();
		$first = (object) array(
			'id' => 'replacement',
			'name' => 'First',
			'types' => array( 'square' ),
			'icons' => array( 'facebook' => array( 'image' => 'first.png' ) ),
		);
		$second = clone $first;
		$second->name = 'Second';
		$second->icons = array( 'facebook' => array( 'image' => 'second.png' ) );

		$this->assertTrue( $adapter->register( $first, $iconSets, $networks ) );
		$this->assertFalse( $adapter->register( $second, $iconSets, $networks ) );
		$this->assertSame( 'First', $iconSets->get( 'replacement' )->label() );
		$this->assertSame( 'first.png', $iconSets->get( 'replacement' )->iconFile( 'facebook' ) );
	}

	public function testLegacyUnderscoreIdentifiersAreNormalizedAtTheCompatibilityBoundary(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = new IconSetRegistry( $networks );
		$legacy = (object) array(
			'id' => 'community_pack',
			'name' => 'Community pack',
			'types' => array( 'round_shape' ),
			'icons' => array(
				'social_net' => array(
					'name' => 'Social net',
					'class' => 'social_net',
					'image' => 'social_net.svg',
					'url' => 'https://example.test/share?url=%%permalink%%',
				),
			),
		);

		$this->assertTrue( ( new LegacyIconSetAdapter() )->register( $legacy, $iconSets, $networks ) );
		$this->assertTrue( $iconSets->has( 'community-pack' ) );
		$this->assertSame( array( 'round-shape' ), $iconSets->get( 'community-pack' )->shapes() );
		$this->assertTrue( $networks->has( 'social-net' ) );
		$this->assertSame( 'social_net', $networks->get( 'social-net' )->cssClass() );
	}
}
