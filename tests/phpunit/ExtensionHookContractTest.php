<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

final class ExtensionHookContractTest extends WP_UnitTestCase {
	public function testCanonicalHookNamesAreStable(): void {
		$this->assertSame( 'hssb/networks', ExtensionHooks::NETWORKS );
		$this->assertSame( 'hssb/icon_sets', ExtensionHooks::ICON_SETS );
		$this->assertSame( 'hssb/share_templates', ExtensionHooks::SHARE_TEMPLATES );
		$this->assertSame( 'hssb/share_template', ExtensionHooks::SHARE_TEMPLATE );
		$this->assertSame( 'hssb/share_title', ExtensionHooks::SHARE_TITLE );
		$this->assertSame( 'hssb/share_url', ExtensionHooks::SHARE_URL );
		$this->assertSame( 'hssb/settings_schema', ExtensionHooks::SETTINGS_SCHEMA );
	}

	public function testCanonicalShareTemplateRunsBeforeItsLegacyEquivalent(): void {
		$trace = array();
		$canonical = static function ( $template, $platform, $fallback ) use ( &$trace ) {
			$trace[] = array( 'canonical', $platform, $fallback );

			return $template . '|canonical';
		};
		$legacy = static function ( $template, $platform, $fallback ) use ( &$trace ) {
			$trace[] = array( 'legacy', $platform, $fallback );

			return $template . '|legacy';
		};
		add_filter( ExtensionHooks::SHARE_TEMPLATE, $canonical, 10, 3 );
		add_filter( 'zm_sh_share_template', $legacy, 10, 3 );

		try {
			$result = zm_sh_get_share_template( 'community_net', 'fallback' );
		} finally {
			remove_filter( ExtensionHooks::SHARE_TEMPLATE, $canonical, 10 );
			remove_filter( 'zm_sh_share_template', $legacy, 10 );
		}

		$this->assertSame( 'fallback|canonical|legacy', $result );
		$this->assertSame(
			array(
				array( 'canonical', 'community_net', 'fallback' ),
				array( 'legacy', 'community_net', 'fallback' ),
			),
			$trace
		);
	}

	public function testCompatibilityBridgeStopsCrossNamespaceRecursion(): void {
		$canonicalCalls = 0;
		$legacyCalls = 0;
		$canonical = static function ( $template, $platform ) use ( &$canonicalCalls ) {
			$canonicalCalls++;

			return zm_sh_get_share_template( $platform, $template ) . '|canonical';
		};
		$legacy = static function ( $template ) use ( &$legacyCalls ) {
			$legacyCalls++;

			return $template . '|legacy';
		};
		add_filter( ExtensionHooks::SHARE_TEMPLATE, $canonical, 10, 2 );
		add_filter( 'zm_sh_share_template', $legacy, 10, 1 );

		try {
			$result = zm_sh_get_share_template( 'community_net', 'fallback' );
		} finally {
			remove_filter( ExtensionHooks::SHARE_TEMPLATE, $canonical, 10 );
			remove_filter( 'zm_sh_share_template', $legacy, 10 );
		}

		$this->assertSame( 'fallback|canonical|legacy', $result );
		$this->assertSame( 1, $canonicalCalls );
		$this->assertSame( 1, $legacyCalls );
	}

	public function testCanonicalRegistryAndSchemaHooksUseTypedFallbacks(): void {
		$hooks = new ExtensionHooks();
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$register = static function ( $registry ) {
			$registry->register(
				new Network(
					'community',
					'Community',
					'community',
					'https://example.test/share?url=%%permalink%%',
					array( '%%permalink%%' ),
					false
				)
			);

			return $registry;
		};
		add_filter( ExtensionHooks::NETWORKS, $register );

		try {
			$filteredNetworks = $hooks->networks( $networks );
		} finally {
			remove_filter( ExtensionHooks::NETWORKS, $register );
		}

		$this->assertTrue( $filteredNetworks->has( 'community' ) );

		$schema = new SettingsSchema(
			$filteredNetworks->ids(),
			array( 'default' ),
			array( 'square' )
		);
		$invalid = static function () {
			return 'invalid';
		};
		add_filter( ExtensionHooks::SETTINGS_SCHEMA, $invalid );

		try {
			$this->assertSame( $schema, $hooks->settingsSchema( $schema ) );
		} finally {
			remove_filter( ExtensionHooks::SETTINGS_SCHEMA, $invalid );
		}
	}
}
