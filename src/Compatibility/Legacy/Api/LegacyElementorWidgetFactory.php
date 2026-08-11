<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

/**
 * Supplies the historical Elementor class name only when Elementor is loaded.
 * The canonical Elementor implementation never imports or names this alias.
 */
final class LegacyElementorWidgetFactory {
	private function __construct() {
	}

	public static function register( $widgetsManager ) {
		self::alias();

		return LegacyApi::delegate( 'elementor', 'register', array( $widgetsManager ) );
	}

	private static function alias() {
		if (
			class_exists( 'ZM_SH_Elementor_Share_Widget', false ) ||
			! class_exists( '\\Elementor\\Widget_Base' )
		) {
			return;
		}

		$class = 'Alimuzzaman\\HtmlSocialShareButtons\\Presentation\\Integration\\Elementor\\ElementorShareWidget';
		if ( class_exists( $class ) ) {
			class_alias( $class, 'ZM_SH_Elementor_Share_Widget' );
		}
	}
}
