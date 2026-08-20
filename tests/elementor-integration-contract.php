#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

$registrar = implode(
	'',
	file( __DIR__ . '/../src/Presentation/Integration/Elementor/ElementorRegistrar.php' )
);
$widget = implode(
	'',
	file( __DIR__ . '/../src/Presentation/Integration/Elementor/ElementorShareWidget.php' )
);

$checks = array(
	'add_action( $this->config->elementorHook()' => 'Elementor registration hook',
	"add_action( 'elementor/editor/after_enqueue_scripts'" => 'Elementor editor asset hook',
	'Elementor\\Widget_Base'                 => 'optional Elementor base class',
	'ElementorShareWidget'                    => 'canonical Elementor widget',
	'->renderer->render('                    => 'canonical render facade',
	"'iconset'"                              => 'icon set control',
	"'default' => 'inherit'"                 => 'global icon set default',
	'Clearing all networks hides this widget'  => 'empty selection behavior',
	"class ElementorShareWidget"             => 'Elementor widget class',
);

foreach ( $checks as $needle => $label ) {
	if ( false === strpos( $registrar . $widget, $needle ) ) {
		exit( esc_html( sprintf( 'Elementor integration contract failed: %s\n', $label ) ) );
		exit( 1 );
	}
}

foreach ( array( 'zm_sh_shortcode_cb', 'LegacyRuntime', 'Compatibility\\Legacy', 'global $' ) as $legacy ) {
	if ( false !== strpos( $registrar . $widget, $legacy ) ) {
		exit( esc_html( sprintf( 'Elementor integration contract failed: legacy dependency %s\\n', $legacy ) ) );
	}
}

echo "Elementor integration contract passed.\n";
