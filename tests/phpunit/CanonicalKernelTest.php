<?php

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\HookRegistrar;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginFactory;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;

final class CanonicalKernelTest extends WP_UnitTestCase {
	public function testCanonicalInstallationConstantsAreDefinedByTheEntryPoint(): void {
		$this->assertTrue( defined( 'HSSB_PLUGIN_FILE' ) );
		$this->assertTrue( defined( 'HSSB_PLUGIN_DIR' ) );
		$this->assertTrue( defined( 'HSSB_PLUGIN_URL' ) );
		$this->assertTrue( defined( 'HSSB_ASSETS_URL' ) );
		$this->assertSame( dirname( HSSB_PLUGIN_FILE ) . '/', HSSB_PLUGIN_DIR );
		$this->assertSame( HSSB_PLUGIN_URL . 'assets/', HSSB_ASSETS_URL );
	}

	public function testKernelOwnsAndIdempotentlyRegistersInjectedSubscribers(): void {
		$subscriber = new class() {
			public $registrations = 0;

			public function registerHooks() {
				$this->registrations++;
			}
		};
		$settings = new class() implements \Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository {
			public function load() {
				return SettingsDefaults::create();
			}

			public function save( Settings $settings ) {
				return $settings;
			}
		};

		$plugin = ( new PluginFactory() )->create(
			dirname( __DIR__, 2 ),
			$settings,
			array( $subscriber )
		);
		$plugin->boot();
		$plugin->boot();

		$this->assertTrue( $plugin->isBooted() );
		$this->assertSame( 1, $subscriber->registrations );
		$this->assertTrue( $plugin->hooks()->isRegistered() );
		$this->assertSame( HSSB_PLUGIN_FILE, $plugin->paths()->file() );
		$this->assertSame( 'zm_shbt_fld', $plugin->config()->optionName() );
		$this->assertSame( 'html-social-share/social-share', $plugin->config()->shareBlockName() );
	}

	public function testHookRegistrarRejectsServicesWithoutAnExplicitRegistrationMethod(): void {
		$this->expectException( InvalidArgumentException::class );
		new HookRegistrar( array( new stdClass() ) );
	}

	public function testCanonicalSettingsControllerOwnsThePluginActionSettingsLink(): void {
		$hook = 'plugin_action_links_' . plugin_basename( HSSB_PLUGIN_FILE );

		$this->assertNotFalse( has_filter( $hook ) );
		$this->assertSame(
			array(
				'<a href="options-general.php?page=zm_shbt_opt">Settings</a>',
				'Deactivate',
			),
			apply_filters( $hook, array( 'Deactivate' ) )
		);
	}
}
