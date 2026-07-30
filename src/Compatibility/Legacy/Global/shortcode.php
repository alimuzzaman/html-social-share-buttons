<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;

function zm_sh_shortcode_cb( $attributes ) {
	return LegacyRuntime::shortcode()->render( $attributes );
}

function zm_sh_get_builder_iconset( $iconSet = 'inherit' ) {
	return LegacyRuntime::shortcode()->resolveBuilderIconSet( $iconSet );
}

function zm_sh_get_builder_iconset_options() {
	return LegacyRuntime::shortcode()->builderIconSetOptions();
}

LegacyRuntime::shortcode()->register();
