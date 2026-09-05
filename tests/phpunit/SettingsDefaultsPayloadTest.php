<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginPaths;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ExcludedContentLookup;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\IconSetPayloadBuilder;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsAjaxController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPayloadBuilder;

final class SettingsDefaultsPayloadTest extends WP_UnitTestCase {
	private function services( $optionName = 'hssb_payload_test' ) {
		$root = dirname( __DIR__, 2 );
		$codec = new OptionSettingsCodec();
		$store = new OptionSettingsRepository( $optionName, $codec );
		$config = new PluginConfig( new PluginPaths( $root . '/html-social-share.php' ) );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
		$icons = new IconSetPayloadBuilder( $iconSets, $networks, new IconSetAssetResolver( $root, 'https://example.test/plugin' ) );
		$content = new ExcludedContentLookup( new ExcludedContentPolicy() );
		$sanitizer = new SettingsRequestSanitizer( new SettingsSchema( $networks->ids(), $iconSets->ids(), array( 'square', 'circle' ) ) );

		return array(
			$store,
			new SettingsPayloadBuilder( $store, $content, $icons, $networks, $root . '/html-social-share.php', $config, $codec ),
			new SettingsAjaxController( $store, $sanitizer, new OptionSettingsRequestMapper(), $content, $icons, $config ),
			$codec,
		);
	}

	public function testVisibleSettingsMatchLoadedValuesAndUnchangedSave(): void {
		list( $store, $payload, $controller, $codec ) = $this->services();
		$cases = array(
			'missing' => null,
			'empty' => array(),
			'malformed' => 'legacy invalid scalar',
			'partial' => array( 'icons' => array( 'twitter' => 'false' ), 'extension' => array( 'opaque' ) ),
			'full' => $codec->encode( SettingsDefaults::create(), array( 'g_analytics' => true, 'use_port' => true ) ),
		);
		foreach ( $cases as $name => $raw ) {
			delete_option( 'hssb_payload_test' );
			if ( 'missing' !== $name ) {
				update_option( 'hssb_payload_test', $raw );
			}
			$before = $store->load();
			$visible = $payload->build()['options'];
			$this->assertSame( $before->title(), $visible['title'], $name );
			$this->assertSame( $before->iconSetId(), $visible['iconset'], $name );
			$this->assertSame( $before->networkStates(), $visible['icons'], $name );
			$this->assertSame( $before->autoHideEnabled(), $visible['auto_hide_btn'], $name );
			$this->assertSame( $before->defaultIconShape(), $visible['iconset_type'], $name );
			$this->assertSame( $before->buttonAppearance(), $visible['button_appearance'], $name );
			$this->assertSame( $before->noFollow(), $visible['nofollow'], $name );
			$this->assertSame( $before->showForCurrentUser(), $visible['show_for_current_user'], $name );
			$this->assertSame( $before->showForLoggedInUser(), $visible['show_for_logged_in_user'], $name );
			$this->assertSame( $before->showForLoggedOutUser(), $visible['show_for_logged_out_user'], $name );
			$this->assertSame( $before->profileLinks(), $visible['profile_links'], $name );
			$this->assertSame( $before->shareTemplates(), $visible['share_templates'], $name );
			$this->assertSame( $before->excludedContent(), $visible['excludes'], $name );
			$decoded = $codec->decode( $visible );
			$this->assertSame( $before->placements(), $decoded->placements(), $name );
			$this->assertSame( $before->placementShapes(), $decoded->placementShapes(), $name );
			$this->assertSame( $before->profileLinkPlacements(), $decoded->profileLinkPlacements(), $name );
			$this->assertSame( $raw, $store->readStored( null ), 'Viewing writes nothing: ' . $name );
			// Submit rendered controls, not opaque data carried in localization.
			$form = array_intersect_key( $visible, array_flip( array(
				'title', 'iconset', 'button_appearance', 'excludes', 'show_in',
				'show_left', 'show_right', 'show_before_post', 'show_after_post',
				'icons', 'share_templates', 'profile_links', 'profile_link_placements',
				'auto_hide_btn', 'nofollow', 'show_for_current_user',
				'show_for_logged_in_user', 'show_for_logged_out_user',
			) ) );
			// Browser checkboxes submit enabled values only.
			$form['icons'] = array_filter( $form['icons'] );
			foreach ( array( 'auto_hide_btn', 'nofollow' ) as $checkbox ) {
				if ( empty( $form[ $checkbox ] ) ) {
					unset( $form[ $checkbox ] );
				}
			}
			$controller->persist( $form );
			$after = $store->load();
			$this->assertSame( $before->placements(), $after->placements(), $name );
			$this->assertSame( $before->placementShapes(), $after->placementShapes(), $name );
			$this->assertSame( array_filter( $before->networkStates() ), array_filter( $after->networkStates() ), $name );
			$this->assertSame( $before->title(), $after->title(), $name );
			$this->assertSame( $before->iconSetId(), $after->iconSetId(), $name );
			$this->assertSame( $before->buttonAppearance(), $after->buttonAppearance(), $name );
			if ( is_array( $raw ) && isset( $raw['extension'] ) ) {
				$this->assertSame( $raw['extension'], $store->readStored()['extension'] );
			}
		}
	}

	public function testSettingsApiPreservesRetiredFieldsWhileLegacySanitizerStillWritesThem(): void {
		list( $store, $payload, $controller ) = $this->services();
		foreach ( array( array(), array( 'g_analytics' => null, 'use_port' => false ), array( 'g_analytics' => array( 'opaque' ), 'use_port' => 'false' ) ) as $original ) {
			update_option( 'hssb_payload_test', $original );
			$input = array( 'title' => 'Saved', 'g_analytics' => '1', 'use_port' => '1' );
			$sanitized = $controller->sanitizeAdmin( $input );
			foreach ( array( 'g_analytics', 'use_port' ) as $key ) {
				$this->assertSame( array_key_exists( $key, $original ), array_key_exists( $key, $sanitized ) );
				if ( array_key_exists( $key, $original ) ) {
					$this->assertSame( $original[ $key ], $sanitized[ $key ] );
				}
			}
			$legacy = $controller->sanitize( $input );
			$this->assertTrue( $legacy['g_analytics'] );
			$this->assertTrue( $legacy['use_port'] );
		}
	}
	public function testRegisteredSanitizerDistinguishesSettingsFormFromProgrammaticWrites(): void {
		list( $store, $payload, $ajax ) = $this->services();
		$root = dirname( __DIR__, 2 );
		$config = new PluginConfig( new PluginPaths( $root . '/html-social-share.php' ) );
		$page = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPageController(
			$ajax,
			new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsAssetEnqueuer( $root, $root . '/html-social-share.php', $payload, $config ),
			$config
		);
		$originalPost = $_POST;
		$input = array( 'title' => 'Saved', 'g_analytics' => true, 'use_port' => true );
		try {
			update_option( 'hssb_payload_test', array( 'g_analytics' => 'opaque', 'use_port' => null ) );
			$_POST = array( 'option_page' => $config->settingsGroup(), 'action' => 'update' );
			$saved = $page->sanitizeAdmin( $input );
			$this->assertSame( 'opaque', $saved['g_analytics'] );
			$this->assertNull( $saved['use_port'] );
			$_POST = array();
			$programmatic = $page->sanitizeAdmin( $input );
			$this->assertTrue( $programmatic['g_analytics'] );
			$this->assertTrue( $programmatic['use_port'] );
		} finally {
			$_POST = $originalPost;
		}
	}

	public function testRegisteredSettingsApiPersistsUnchangedFormsThroughWordPress(): void {
		$root = dirname( __DIR__, 2 );
		$config = new PluginConfig( new PluginPaths( $root . '/html-social-share.php' ) );
		$option = $config->optionName();
		list( $store, $payload, $ajax, $codec ) = $this->services( $option );
		$page = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPageController(
			$ajax,
			new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsAssetEnqueuer( $root, $root . '/html-social-share.php', $payload, $config ),
			$config
		);
		$filter = 'sanitize_option_' . $option;
		$originalFilter = isset( $GLOBALS['wp_filter'][ $filter ] ) ? clone $GLOBALS['wp_filter'][ $filter ] : null;
		$originalPost = $_POST;
		$registrationGlobals = array();
		foreach ( array( 'new_allowed_options', 'new_whitelist_options', 'wp_registered_settings' ) as $key ) {
			$registrationGlobals[ $key ] = array( array_key_exists( $key, $GLOBALS ), isset( $GLOBALS[ $key ] ) ? $GLOBALS[ $key ] : null );
		}
		$cases = array(
			'missing' => null,
			'empty' => array(),
			'malformed' => 'invalid scalar',
			'sparse' => array( 'icons' => array( 'facebook' => '1' ), 'extension' => array( 'opaque', false ), 'g_analytics' => null, 'use_port' => false ),
			'opaque-retired' => array( 'g_analytics' => array( 'legacy' ), 'use_port' => 'false', 'show_in' => array( 'extension-placement' => 'opaque' ) ),
			'full' => $codec->encode( SettingsDefaults::create(), array() ) + array( 'g_analytics' => true, 'use_port' => true ),
		);
		try {
			foreach ( $cases as $name => $raw ) {
				// Seed the pre-upgrade bytes before registering this case's callback.
				remove_all_filters( $filter );
				delete_option( $option );
				if ( 'missing' !== $name ) {
					add_option( $option, $raw );
				}
				$page->registerSettings();
				$this->assertNotFalse( has_filter( $filter, array( $page, 'sanitizeAdmin' ) ) );
				$calls = 0;
				add_filter( $filter, static function ( $value ) use ( &$calls ) { ++$calls; return $value; }, 999 );
				$before = $store->load();
				$visible = $payload->build()['options'];
				$this->assertSame( $raw, $store->readStored( null ), $name . ': viewing does not write' );
				$form = array_intersect_key( $visible, array_flip( array(
					'title', 'iconset', 'button_appearance', 'excludes', 'show_left', 'show_right',
					'show_before_post', 'show_after_post', 'auto_hide_btn', 'nofollow',
					'show_for_current_user', 'show_for_logged_in_user', 'show_for_logged_out_user',
				) ) );
				foreach ( array( 'icons', 'share_templates', 'profile_links' ) as $field ) {
					$form[ $field ] = array_intersect_key( isset( $visible[ $field ] ) ? $visible[ $field ] : array(), array_flip( ( new BuiltInNetworkProvider() )->createRegistry()->ids() ) );
				}
				foreach ( array( 'show_in', 'profile_link_placements' ) as $field ) {
					$form[ $field ] = array_intersect_key( isset( $visible[ $field ] ) ? $visible[ $field ] : array(), array_flip( array( 'show_left', 'show_right', 'show_before_post', 'show_after_post' ) ) );
				}
				$form['icons'] = array_filter( $form['icons'] );
				foreach ( array( 'auto_hide_btn', 'nofollow' ) as $checkbox ) {
					if ( empty( $form[ $checkbox ] ) ) { unset( $form[ $checkbox ] ); }
				}
				// options.php passes the unslashed submitted option to update_option().
				$_POST = array( 'option_page' => $config->settingsGroup(), 'action' => 'update', $option => wp_slash( $form ) );
				update_option( $option, wp_unslash( $_POST[ $option ] ) );
				$this->assertSame( 'missing' === $name ? 2 : 1, $calls, $name . ': actual WordPress sanitation count' );
				$after = $store->load();
				foreach ( array( 'title', 'iconSetId', 'defaultIconShape', 'placements', 'placementShapes', 'shareTemplates', 'excludedContent', 'analyticsEnabled', 'autoHideEnabled', 'preserveUrlPort', 'noFollow', 'profileLinks', 'profileLinkPlacements', 'showForCurrentUser', 'showForLoggedInUser', 'showForLoggedOutUser', 'buttonAppearance' ) as $accessor ) {
					$this->assertSame( $before->$accessor(), $after->$accessor(), $name . ': ' . $accessor );
				}
				$this->assertSame( array_filter( $before->networkStates() ), array_filter( $after->networkStates() ), $name . ': enabled networks' );
				$saved = $store->readStored();
				foreach ( array( 'g_analytics', 'use_port', 'extension' ) as $key ) {
					$present = is_array( $raw ) && array_key_exists( $key, $raw );
					$this->assertSame( $present, array_key_exists( $key, $saved ), $name . ': ' . $key );
					if ( $present ) { $this->assertSame( $raw[ $key ], $saved[ $key ] ); }
				}
				if ( 'opaque-retired' === $name ) {
					$this->assertSame( 'opaque', $saved['show_in']['extension-placement'] );
				}
			}
		} finally {
			$_POST = $originalPost;
			remove_all_filters( $filter );
			if ( null !== $originalFilter ) { $GLOBALS['wp_filter'][ $filter ] = $originalFilter; }
			foreach ( $registrationGlobals as $key => $snapshot ) {
				if ( $snapshot[0] ) { $GLOBALS[ $key ] = $snapshot[1]; } else { unset( $GLOBALS[ $key ] ); }
			}
		}
	}

}
