<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;

final class ResolvedButton {
	private $network;
	private $url;
	private $iconFile;
	private $browserUrlDescriptor;

	public function __construct( Network $network, $url, $iconFile, $browserUrlDescriptor = array() ) {
		$this->network = $network;
		$this->url = (string) $url;
		$this->iconFile = (string) $iconFile;
		$this->browserUrlDescriptor = is_array( $browserUrlDescriptor ) && $this->isValidBrowserDescriptor( $browserUrlDescriptor )
			? $browserUrlDescriptor
			: ( is_string( $browserUrlDescriptor ) && '' !== $browserUrlDescriptor
				&& false !== strpos( $browserUrlDescriptor, '%%permalink%%' )
				? array(
					'permalink_slot' => '%%permalink%%',
					'template'       => $browserUrlDescriptor,
				)
				: array() );
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

	public function browserUrlDescriptor() {
		return $this->browserUrlDescriptor;
	}

	private function isValidBrowserDescriptor( array $descriptor ) {
		return isset( $descriptor['template'], $descriptor['permalink_slot'] )
			&& is_string( $descriptor['template'] )
			&& '' !== $descriptor['template']
			&& '%%permalink%%' === $descriptor['permalink_slot']
			&& false !== strpos( $descriptor['template'], $descriptor['permalink_slot'] );
	}
}
