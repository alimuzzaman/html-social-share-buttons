<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Runtime\SocialShareAdapter;

global $zm_sh_iconset_classes;

function zm_sh_btn( $options ) {
	global $zm_sh;

	if ( ! is_object( $zm_sh ) || ! is_array( $options ) ) {
		return '';
	}
	$icons = isset( $options['icons'] ) && is_array( $options['icons'] )
		? $options['icons']
		: array();
	$isList = ! empty( $icons ) && array_keys( $icons ) === range( 0, count( $icons ) - 1 );
	$icons = $isList ? $icons : array_keys( $icons );
	$icons = array_map( 'sanitize_key', $icons );
	$options['icons'] = array_fill_keys( array_filter( $icons ), 'on' );

	return $zm_sh->zm_sh_btn( $options );
}

class zm_social_share extends SocialShareAdapter {
}

add_action(
	'init',
	static function () {
		global $zm_sh;
		$zm_sh = new zm_social_share();
	},
	1
);
