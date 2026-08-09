<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use InvalidArgumentException;

final class ResolvedProfileLink {
	private $network;
	private $url;
	private $iconFile;

	public function __construct( Network $network, $url, $iconFile ) {
		$url = trim( (string) $url );
		$iconFile = (string) $iconFile;
		if ( '' === $url ) {
			throw new InvalidArgumentException( 'Resolved profile-link URLs cannot be empty.' );
		}
		if ( '' === $iconFile ) {
			throw new InvalidArgumentException( 'Resolved profile-link icon files cannot be empty.' );
		}

		$this->network = $network;
		$this->url = $url;
		$this->iconFile = $iconFile;
	}

	public function network() {
		return $this->network;
	}

	public function url() {
		return $this->url;
	}

	public function iconFile() {
		return $this->iconFile;
	}
}
