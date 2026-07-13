#!/usr/bin/env php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

$source = file_get_contents( __DIR__ . '/../elementor-integration.php' );

$checks = array(
	"add_action( 'elementor/widgets/register'" => 'Elementor registration hook',
	'Elementor\\Widget_Base'                 => 'optional Elementor base class',
	'zm_sh_shortcode_cb'                      => 'existing shortcode delegation',
	'zm_sh_get_builder_iconset'                => 'configured icon set delegation',
	"'iconset'"                              => 'icon set control',
	"'default' => 'inherit'"                 => 'global icon set default',
	'Clearing all networks hides this widget'  => 'empty selection behavior',
	"class ZM_SH_Elementor_Share_Widget"     => 'Elementor widget class',
);

foreach ( $checks as $needle => $label ) {
	if ( false === strpos( $source, $needle ) ) {
		echo "Elementor integration contract failed: {$label}\n";
		exit( 1 );
	}
}

echo "Elementor integration contract passed.\n";
