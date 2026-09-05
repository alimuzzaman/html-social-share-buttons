<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

/** Keep supported literal placeholders intact during WordPress textarea cleanup. */
final class ShareTemplateSanitizer {
	public static function sanitize( $template ) {
		$tokens = array( '%%permalink%%', '%%title%%', '%%description%%', '%%imageurl%%' );
		// A fresh marker avoids collisions even when cleanup joins user text.
		$prefix = 'HSSBTOKEN' . str_replace( '-', '', wp_generate_uuid4() );
		while ( false !== strpos( $template, $prefix ) ) {
			$prefix .= 'X';
		}
		$markers = array();
		foreach ( $tokens as $index => $token ) {
			$markers[] = $prefix . $index . 'END';
		}

		return str_replace(
			$markers,
			$tokens,
			sanitize_textarea_field( str_replace( $tokens, $markers, $template ) )
		);
	}
}
