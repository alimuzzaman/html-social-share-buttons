<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical share URL template for each built-in platform.
 *
 * Placeholders are encoded when the link is rendered. Integrations can replace
 * the complete map or one resolved template without changing an icon set.
 */
function zm_sh_get_default_share_templates() {
	return array(
		'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
		'x'         => 'https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%',
		'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=%%permalink%%',
		'pinterest' => 'https://www.pinterest.com/pin/create/button/?url=%%permalink%%&media=%%imageurl%%&description=%%title%%',
		'mail'      => 'mailto:?subject=%%title%%&body=%%permalink%%',
	);
}

function zm_sh_get_share_templates() {
	$templates = zm_sh_get_default_share_templates();
	$options = function_exists( 'get_option' ) ? get_option( 'zm_shbt_fld', array() ) : array();
	$custom_templates = isset( $options['share_templates'] ) && is_array( $options['share_templates'] ) ? $options['share_templates'] : array();

	foreach ( $templates as $platform => $default ) {
		if ( isset( $custom_templates[ $platform ] ) && is_string( $custom_templates[ $platform ] ) && '' !== trim( $custom_templates[ $platform ] ) ) {
			$templates[ $platform ] = $custom_templates[ $platform ];
		}
	}

	return apply_filters( 'zm_sh_share_templates', $templates );
}

function zm_sh_get_share_template( $platform, $fallback = '' ) {
	$templates = zm_sh_get_share_templates();
	$template  = isset( $templates[ $platform ] ) ? $templates[ $platform ] : $fallback;

	return apply_filters( 'zm_sh_share_template', $template, $platform, $fallback );
}
