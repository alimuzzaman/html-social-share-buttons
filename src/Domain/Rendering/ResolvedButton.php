<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;

final class ResolvedButton {
	private $network;
	private $url;
	private $iconFile;

	public function __construct( Network $network, $url, $iconFile ) {
		$this->network = $network;
		$this->url = (string) $url;
		$this->iconFile = (string) $iconFile;
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
