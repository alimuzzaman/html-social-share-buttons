<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\Plugin;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginFactory;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings\LegacySettingsMapper;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings\LegacySettingsRequestMapper;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings\LegacySettingsSanitizer;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings\LegacySettingsService;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyRenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyBuiltInIconSetHydrator;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyIconSetAssetMap;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network\LegacyNetworkMapper;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration\ShortcodeAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration\BlockAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration\WpBakeryAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;
use LogicException;

final class LegacyRuntime {
	private static $plugin;
	private static $renderer;
	private static $shortcode;
	private static $block;
	private static $wpBakery;
	private static $settings;
	private static $builtInIconSets;
	private static $pluginRoot;

	private function __construct() {
	}

	public static function boot( $pluginRoot ) {
		if ( self::$plugin instanceof Plugin ) {
			return self::$plugin;
		}

		self::$pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
		$settingsMapper = new LegacySettingsMapper();
		$settingsStore = new OptionSettingsRepository( 'zm_shbt_fld', $settingsMapper );
		self::$plugin = ( new PluginFactory() )->create( self::$pluginRoot, $settingsStore );
		self::$settings = new LegacySettingsService(
			$settingsStore,
			new LegacySettingsSanitizer(
				new SettingsRequestSanitizer(
					self::$plugin->extensions()->settingsSchema(
						new SettingsSchema(
						self::$plugin->networks()->ids(),
						self::$plugin->iconSets()->ids(),
						self::iconShapes()
						)
					)
				),
				new LegacySettingsRequestMapper()
			)
		);
		self::$plugin->boot();

		return self::$plugin;
	}

	public static function plugin() {
		if ( ! self::$plugin instanceof Plugin ) {
			throw new LogicException( 'The compatibility runtime has not booted.' );
		}

		return self::$plugin;
	}

	public static function renderer() {
		if ( ! self::$renderer instanceof LegacyRenderFacade ) {
			self::$renderer = new LegacyRenderFacade( self::plugin()->networks() );
		}

		return self::$renderer;
	}

	public static function shortcode() {
		if ( ! self::$shortcode instanceof ShortcodeAdapter ) {
			self::$shortcode = new ShortcodeAdapter();
		}

		return self::$shortcode;
	}

	public static function block() {
		if ( ! self::$block instanceof BlockAdapter ) {
			self::$block = new BlockAdapter( self::$pluginRoot );
		}

		return self::$block;
	}

	public static function wpBakery() {
		if ( ! self::$wpBakery instanceof WpBakeryAdapter ) {
			self::$wpBakery = new WpBakeryAdapter( self::$pluginRoot );
		}

		return self::$wpBakery;
	}

	public static function settings() {
		if ( ! self::$settings instanceof LegacySettingsService ) {
			throw new LogicException( 'The compatibility runtime has not booted.' );
		}

		return self::$settings;
	}

	public static function builtInIconSets() {
		if ( ! self::$builtInIconSets instanceof LegacyBuiltInIconSetHydrator ) {
			self::$builtInIconSets = new LegacyBuiltInIconSetHydrator(
				self::plugin()->iconSets(),
				self::plugin()->networks(),
				new LegacyIconSetAssetMap(),
				new LegacyNetworkMapper(),
				self::plugin()->assets()
			);
		}

		return self::$builtInIconSets;
	}

	public static function excludedContent() {
		return self::plugin()->excludedContent();
	}

	public static function contentPlacement() {
		return self::plugin()->contentPlacement();
	}

	public static function floatingPlacement() {
		return self::plugin()->floatingPlacement();
	}

	public static function pluginRoot() {
		if ( ! is_string( self::$pluginRoot ) || '' === self::$pluginRoot ) {
			throw new LogicException( 'The compatibility runtime has not booted.' );
		}

		return self::$pluginRoot;
	}

	private static function iconShapes() {
		$shapes = array();
		foreach ( self::$plugin->iconSets()->all() as $iconSet ) {
			foreach ( $iconSet->shapes() as $shape ) {
				$shapes[ $shape ] = $shape;
			}
		}

		return array_values( $shapes );
	}
}
