<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use InvalidArgumentException;

final class LegacyIconSetAssetMap {
	public function directory( IconSet $iconSet ) {
		return 'long-shadows' === $iconSet->id() ? 'long_shadow' : $iconSet->id();
	}

	public function iconFile( IconSet $iconSet, $networkId ) {
		$networkId = (string) $networkId;
		if ( ! $iconSet->hasIcon( $networkId ) ) {
			throw new InvalidArgumentException( 'The icon set does not contain this network.' );
		}

		if ( 'flat' === $iconSet->id() ) {
			$files = array(
				'facebook'  => 'Facebook.png',
				'x'         => 'Twitter.png',
				'linkedin'  => 'Linkedin.png',
				'pinterest' => 'Pinterest.png',
				'mail'      => 'Mail.png',
			);
			if ( isset( $files[ $networkId ] ) ) {
				return $files[ $networkId ];
			}
		}

		return 'x' === $networkId ? 'twitter.png' : $iconSet->iconFile( $networkId );
	}
}
