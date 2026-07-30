<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsStateStore;

final class LegacySettingsService {
	private $store;
	private $sanitizer;

	public function __construct(
		SettingsStateStore $store,
		LegacySettingsSanitizer $sanitizer
	) {
		$this->store = $store;
		$this->sanitizer = $sanitizer;
	}

	public function sanitize( array $input ) {
		return $this->sanitizer->sanitize( $input );
	}

	public function stored( $fallback = array() ) {
		return $this->store->readStored( $fallback );
	}

	public function runtime( $fallback = array() ) {
		$stored = $this->stored( $fallback );
		if (
			is_array( $stored ) &&
			isset( $stored['icons'] ) &&
			is_array( $stored['icons'] ) &&
			isset( $stored['icons']['twitter'] )
		) {
			$stored['icons']['x'] = $stored['icons']['twitter'];
			unset( $stored['icons']['twitter'] );
		}

		return $stored;
	}

	public function save( array $input ) {
		$sanitized = $this->sanitize( $input );

		return $this->store->replaceStored( $sanitized );
	}
}
