<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;

/**
 * The only option/request shape translation retained for the old API.
 */
final class LegacyOptionCodec {
	private $storage;
	private $request;

	/**
	 * @param OptionSettingsCodec|null         $storage Optional storage codec.
	 * @param OptionSettingsRequestMapper|null $request Optional request mapper.
	 */
	public function __construct(
		$storage = null,
		$request = null
	) {
		$this->storage = $storage ? $storage : new OptionSettingsCodec();
		$this->request = $request ? $request : new OptionSettingsRequestMapper();
	}

	public function fromStored( array $stored ) {
		return $this->storage->fromArray( $stored );
	}

	public function toStored( Settings $settings, array $original = array() ) {
		return $this->storage->toArray( $settings, $original );
	}

	public function toCanonicalRequest( array $input ) {
		return $this->request->toCanonical( $input );
	}

	public function toLegacySubmission( Settings $settings, array $input ) {
		return $this->request->toStoredSubmission( $settings, $input );
	}
}
