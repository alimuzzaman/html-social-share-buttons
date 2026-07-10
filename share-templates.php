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
function zm_sh_get_share_templates() {
	$templates = array(
		'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
		'x'         => 'https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%',
		'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=%%permalink%%',
		'pinterest' => 'https://www.pinterest.com/pin/create/button/?url=%%permalink%%&media=%%imageurl%%&description=%%title%%',
		'mail'      => 'mailto:?subject=%%title%%&body=%%permalink%%',
	);

	return apply_filters( 'zm_sh_share_templates', $templates );
}

function zm_sh_get_share_template( $platform, $fallback = '' ) {
	$templates = zm_sh_get_share_templates();
	$template  = isset( $templates[ $platform ] ) ? $templates[ $platform ] : $fallback;

	return apply_filters( 'zm_sh_share_template', $template, $platform, $fallback );
}
