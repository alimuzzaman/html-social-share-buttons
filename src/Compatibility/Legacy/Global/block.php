<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;

function zm_sh_register_block() {
	LegacyRuntime::block()->registerBlock();
}

function zm_sh_get_builder_iconset_assets() {
	return LegacyRuntime::block()->builderIconSetAssets();
}

function zm_sh_render_block( $attributes ) {
	return LegacyRuntime::block()->render( $attributes );
}

LegacyRuntime::block()->register();
