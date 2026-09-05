<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend;

/** Final destination treatment shared by links and the admin text preview. */
final class ShareUrlPresentation {
	public static function escape( $networkId, $url ) {
		if ( 'bluesky' === $networkId ) {
			$url = str_ireplace( '%0A', '%20', $url );
		}

		return esc_url( $url );
	}
}
