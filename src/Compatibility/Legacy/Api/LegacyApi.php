<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\Plugin;
use LogicException;

/**
 * Narrow bridge between the historical global API and the canonical kernel.
 *
 * This class deliberately has no factory, WordPress hook registration, option
 * access, or rendering logic.  The canonical bootstrap injects its already
 * constructed Plugin once; legacy symbols then delegate through its public
 * getters.  Keeping the reference here avoids a new public service locator
 * while allowing old global functions to remain callable.
 */
final class LegacyApi {
	private static $plugin;

	private function __construct() {
	}

	public static function register( Plugin $plugin ) {
		self::$plugin = $plugin;
	}

	public static function plugin() {
		if ( ! self::$plugin instanceof Plugin ) {
			throw new LogicException( 'The canonical plugin has not been registered.' );
		}

		return self::$plugin;
	}

	public static function has( $getter ) {
		$plugin = self::plugin();

		return is_string( $getter ) && (
			method_exists( $plugin, $getter ) ||
			( method_exists( $plugin, 'service' ) && null !== $plugin->service( $getter ) )
		);
	}

	public static function service( $getter ) {
		if ( ! self::has( $getter ) ) {
			return null;
		}

		$plugin = self::plugin();
		if ( method_exists( $plugin, $getter ) ) {
			return call_user_func( array( $plugin, $getter ) );
		}

		return $plugin->service( $getter );
	}

	/**
	 * Call an optional canonical controller without making compatibility own it.
	 */
	public static function delegate( $getter, $method, array $arguments = array(), $fallback = null ) {
		$service = self::service( $getter );
		if ( ! is_object( $service ) || ! method_exists( $service, $method ) ) {
			return $fallback;
		}

		return call_user_func_array( array( $service, $method ), $arguments );
	}

	public static function render( array $options, $contextPostId = 0 ) {
		$frontend = self::service( 'frontend' );
		if ( is_object( $frontend ) && method_exists( $frontend, 'render' ) ) {
			return $frontend->render( $options, (int) $contextPostId );
		}

		$renderer = self::service( 'renderer' );
		if ( is_object( $renderer ) && method_exists( $renderer, 'render' ) ) {
			$outcome = $renderer->render( $options, (int) $contextPostId );
			if ( is_object( $outcome ) && method_exists( $outcome, 'html' ) ) {
				return $outcome->html();
			}
		}

		throw new LogicException( 'The canonical renderer is unavailable.' );
	}

	public static function settings() {
		return self::service( 'settings' );
	}

	public static function networks() {
		return self::service( 'networks' );
	}

	public static function iconSets() {
		return self::service( 'iconSets' );
	}

	public static function extensions() {
		return self::service( 'extensions' );
	}

	public static function defaultShareTemplates() {
		$templates = array();
		$networks = self::networks();
		if ( ! is_object( $networks ) || ! method_exists( $networks, 'all' ) ) {
			return $templates;
		}
		foreach ( $networks->all() as $network ) {
			if ( is_object( $network ) && method_exists( $network, 'id' ) ) {
				$templates[ $network->id() ] = $network->defaultShareTemplate();
			}
		}

		return $templates;
	}

	public static function shareTemplates() {
		$templates = self::defaultShareTemplates();
		$settings = self::settings();
		if ( ! is_object( $settings ) || ! method_exists( $settings, 'load' ) ) {
			return $templates;
		}
		$stored = $settings->load();
		if ( ! is_object( $stored ) || ! method_exists( $stored, 'shareTemplates' ) ) {
			return $templates;
		}
		foreach ( (array) $stored->shareTemplates() as $id => $template ) {
			if ( isset( $templates[ $id ] ) && is_string( $template ) && '' !== trim( $template ) ) {
				$templates[ $id ] = $template;
			}
		}

		return $templates;
	}

	public static function storedOptions( array $fallback = array() ) {
		$settings = self::settings();

		return is_object( $settings ) && method_exists( $settings, 'readStored' )
			? $settings->readStored( $fallback )
			: $fallback;
	}

	/**
	 * Present current canonical Settings through the long-lived option shape.
	 */
	public static function legacyOptions( array $fallback = array() ) {
		$settings = self::settings();
		if ( ! is_object( $settings ) || ! method_exists( $settings, 'load' ) ) {
			return self::storedOptions( $fallback );
		}
		$current = $settings->load();
		if ( ! $current instanceof \Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings ) {
			return self::storedOptions( $fallback );
		}

		return ( new LegacyOptionCodec() )->toStored(
			$current,
			self::storedOptions( $fallback )
		);
	}

	/**
	 * Hydrate a historical icon-set value object from canonical data. This is a
	 * value translation only; renderers continue to consume the canonical set.
	 */
	public static function populateLegacyIconSet( $legacy, $id ) {
		$iconSets = self::iconSets();
		$networks = self::networks();
		$assets = self::service( 'assets' );
		if (
			! is_object( $legacy ) ||
			! is_object( $iconSets ) || ! method_exists( $iconSets, 'has' ) || ! $iconSets->has( $id ) ||
			! is_object( $networks ) || ! is_object( $assets )
		) {
			return false;
		}

		$iconSet = $iconSets->get( $id );
		$legacy->id = $iconSet->id();
		$legacy->name = $iconSet->label();
		$legacy->types = $iconSet->shapes();
		$legacy->stylesheet = $iconSet->stylesheet();
		$legacy->preview_img = $iconSet->preview();
		$legacy->__FILE__ = $assets->setPath( $iconSet ) . '/ssb.php';
		$legacy->dir = $assets->setPath( $iconSet );
		$legacy->url = $assets->setUrl( $iconSet );
		$legacy->stylesheet_url = $assets->stylesheetUrl( $iconSet );
		$legacy->preview_img_url = $assets->previewUrl( $iconSet );
		$legacy->preview_img_dir = $assets->previewPath( $iconSet );
		$legacy->icons = array();
		foreach ( $iconSet->iconFiles() as $networkId => $file ) {
			if ( ! $networks->has( $networkId ) ) {
				continue;
			}
			$network = $networks->get( $networkId );
			$legacy->icons[ $networkId ] = array(
				'id' => $networkId,
				/*
				 * The public legacy object advertised X under this longer label.
				 * Rendering consumes the canonical Network and continues to use the
				 * historical `twitter` CSS class; this value adapter only preserves
				 * the old object contract for extensions inspecting ->icons.
				 */
				'name' => self::legacyNetworkLabel( $networkId, $network->label() ),
				'class' => 'x' === $networkId ? 'twitter' : $network->cssClass(),
				'image' => $file,
				'url' => $network->defaultShareTemplate(),
			);
		}

		return true;
	}

	private static function legacyNetworkLabel( $networkId, $label ) {
		$labels = array(
			'x'        => 'X (formerly Twitter)',
			'linkedin' => 'Linkedin',
		);

		return isset( $labels[ $networkId ] ) ? $labels[ $networkId ] : $label;
	}
}
