<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyRegistryAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;

final class LegacyRegistryAdapterTest extends WP_UnitTestCase {
	public function testBuiltInIconsetsRetainHistoricalPublicDefinitionPaths(): void {
		global $zm_sh;

		$default = $zm_sh->iconsets->get_iconset( 'default' );

		$this->assertStringEndsWith( '/iconset/default/ssb.php', $default->__FILE__ );
		$this->assertFileExists( $default->__FILE__ );
		$this->assertStringEndsWith( '/iconset/default/', $default->url );
	}

	public function testBuiltInLegacyObjectsAreHydratedFromTheCanonicalRegistry(): void {
		global $zm_sh;

		$canonical = LegacyRuntime::plugin()->iconSets()->get( 'flat' );
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

	public function testLegacyIconsetAndCustomNetworkBecomeValidatedRuntimeDefinitions(): void {
		$iconSet = (object) array(
			'id' => 'community',
			'name' => 'Community',
			'stylesheet' => 'community.css',
			'preview_img' => 'community-preview.png',
			'types' => array( 'square', 'circle' ),
			'icons' => array(
				'mastodon' => array(
					'id' => 'mastodon',
					'name' => 'Mastodon',
					'class' => 'mastodon',
					'image' => 'mastodon.svg',
					'url' => 'https://example.social/share?text=%%title%%&url=%%permalink%%',
				),
			),
		);
		$registry = new LegacyRegistryAdapterStub( (object) array( 'community' => $iconSet ) );
		$adapter = new LegacyRegistryAdapter(
			( new BuiltInNetworkProvider() )->createRegistry()
		);

		$bundle = $adapter->adapt( $registry );

		$this->assertTrue( $bundle->networks()->has( 'mastodon' ) );
		$this->assertSame(
			array( '%%title%%', '%%permalink%%' ),
			$bundle->networks()->get( 'mastodon' )->placeholders()
		);
		$this->assertTrue( $bundle->iconSets()->has( 'community' ) );
		$this->assertSame(
			'mastodon.svg',
			$bundle->iconSets()->get( 'community' )->iconFile( 'mastodon' )
		);
	}

	public function testLaterLegacyDefinitionsReplaceTheSameIconsetIdBeforeValidation(): void {
		$first = (object) array(
			'id' => 'replacement',
			'name' => 'First',
			'types' => array( 'square' ),
			'icons' => array(
				'facebook' => array( 'image' => 'first.png' ),
			),
		);
		$second = clone $first;
		$second->name = 'Second';
		$second->icons = array(
			'facebook' => array( 'image' => 'second.png' ),
		);
		$registry = new LegacyRegistryAdapterStub( array( $first, $second ) );
		$adapter = new LegacyRegistryAdapter(
			( new BuiltInNetworkProvider() )->createRegistry()
		);

		$bundle = $adapter->adapt( $registry );

		$this->assertSame( 'Second', $bundle->iconSets()->get( 'replacement' )->label() );
		$this->assertSame( 'second.png', $bundle->iconSets()->get( 'replacement' )->iconFile( 'facebook' ) );
	}

	public function testSelectedLegacyCustomNetworkMayUseHistoricalEmptyUrlAndTypes(): void {
		$iconSet = (object) array(
			'id' => 'community',
			'name' => '',
			'types' => array(),
			'icons' => array(
				'community' => array(
					'name' => '',
					'class' => '',
					'image' => 'community.svg',
					'url' => '',
				),
			),
		);
		$registry = new LegacyRegistryAdapterStub(
			array( 'community' => $iconSet )
		);

		$bundle = ( new LegacyRegistryAdapter(
			( new BuiltInNetworkProvider() )->createRegistry()
		) )->adapt( $registry, 'community' );

		$this->assertSame( '', $bundle->networks()->get( 'community' )->defaultShareTemplate() );
		$this->assertSame( array( 'square' ), $bundle->iconSets()->get( 'community' )->shapes() );
	}

	public function testLegacySafeUnderscoreIdentifiersMapWithoutWeakeningCanonicalIds(): void {
		$iconSet = (object) array(
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
		$bundle = ( new LegacyRegistryAdapter(
			( new BuiltInNetworkProvider() )->createRegistry()
		) )->adapt(
			new LegacyRegistryAdapterStub( array( 'community_pack' => $iconSet ) ),
			'community_pack'
		);
		$canonicalIconSet = $bundle->canonicalIconSetId( 'community_pack' );
		$canonicalNetwork = $bundle->canonicalNetworkId( 'social_net' );
		$canonicalShape = $bundle->canonicalShapeId( $canonicalIconSet, 'round_shape' );

		$this->assertMatchesRegularExpression( '/^[a-z][a-z0-9-]*$/', $canonicalIconSet );
		$this->assertMatchesRegularExpression( '/^[a-z][a-z0-9-]*$/', $canonicalNetwork );
		$this->assertMatchesRegularExpression( '/^[a-z][a-z0-9-]*$/', $canonicalShape );
		$this->assertSame( 'community_pack', $bundle->legacyIconSetId( $canonicalIconSet ) );
		$this->assertSame( 'social_net', $bundle->legacyNetworkId( $canonicalNetwork ) );
		$this->assertSame( 'round_shape', $bundle->legacyShapeId( $canonicalIconSet, $canonicalShape ) );
		$this->assertTrue( $bundle->iconSets()->has( $canonicalIconSet ) );
		$this->assertTrue( $bundle->networks()->has( $canonicalNetwork ) );
	}
}

final class LegacyRegistryAdapterStub {
	private $iconSets;

	public function __construct( $iconSets ) {
		$this->iconSets = $iconSets;
	}

	public function get_iconsets() {
		return $this->iconSets;
	}

	public function get_iconset( $id ) {
		if ( is_object( $this->iconSets ) ) {
			return isset( $this->iconSets->{$id} ) ? $this->iconSets->{$id} : false;
		}

		return isset( $this->iconSets[ $id ] ) ? $this->iconSets[ $id ] : false;
	}
}
