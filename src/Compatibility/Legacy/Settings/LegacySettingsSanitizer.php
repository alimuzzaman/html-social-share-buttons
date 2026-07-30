<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;

final class LegacySettingsSanitizer {
	private $canonical;
	private $mapper;

	public function __construct(
		SettingsRequestSanitizer $canonical,
		LegacySettingsRequestMapper $mapper
	) {
		$this->canonical = $canonical;
		$this->mapper = $mapper;
	}

	public function sanitize( array $input ) {
		$settings = $this->canonical->sanitize( $this->mapper->toCanonical( $input ) );

		return $this->mapper->toLegacySubmission( $settings, $input );
	}
}
