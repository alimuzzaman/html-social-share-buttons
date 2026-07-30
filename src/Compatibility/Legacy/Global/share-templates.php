<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	! class_exists(
		'\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Hook\LegacyExtensionHookBridge'
	)
) {
	$hssb_legacy_autoload = dirname( __DIR__, 4 ) . '/vendor/autoload.php';
	if ( is_readable( $hssb_legacy_autoload ) ) {
		require_once $hssb_legacy_autoload;
	}
	unset( $hssb_legacy_autoload );
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
		'telegram'  => 'https://t.me/share/url?url=%%permalink%%&text=%%title%%',
		'bluesky'   => 'https://bsky.app/intent/compose?text=%%title%%%0A%%permalink%%',
		'mail'      => 'mailto:?subject=%%title%%&body=%%permalink%%',
	);
}

function zm_sh_get_share_templates() {
	$templates = zm_sh_get_default_share_templates();
	$options = array();
	if ( class_exists( '\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime' ) ) {
		try {
			$options =
				\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::settings()
					->stored( array() );
		} catch ( \LogicException $error ) {
			$options = array();
		}
	}
	$custom_templates = isset( $options['share_templates'] ) && is_array( $options['share_templates'] ) ? $options['share_templates'] : array();

	foreach ( $templates as $platform => $default ) {
		if ( isset( $custom_templates[ $platform ] ) && is_string( $custom_templates[ $platform ] ) && '' !== trim( $custom_templates[ $platform ] ) ) {
			$templates[ $platform ] = $custom_templates[ $platform ];
		}
	}

	return \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Hook\LegacyExtensionHookBridge::shareTemplates(
		$templates
	);
}

function zm_sh_get_share_template( $platform, $fallback = '' ) {
	$templates = zm_sh_get_share_templates();
	$template  = isset( $templates[ $platform ] ) ? $templates[ $platform ] : $fallback;

	return \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Hook\LegacyExtensionHookBridge::shareTemplate(
		$template,
		$platform,
		$fallback
	);
}
