<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use RuntimeException;
use stdClass;

final class OptionSettingsRepository implements SettingsStateStore {
	private $optionName;
	private $codec;

	public function __construct( $optionName, SettingsCodec $codec ) {
		$optionName = (string) $optionName;
		if ( '' === $optionName ) {
			throw new RuntimeException( 'The settings option name cannot be empty.' );
		}

		$this->optionName = $optionName;
		$this->codec = $codec;
	}

	public function load() {
		$missing = new stdClass();
		$stored = get_option( $this->optionName, $missing );

		if ( $missing === $stored ) {
			return SettingsDefaults::create();
		}

		return $this->codec->decode( is_array( $stored ) ? $stored : array() );
	}

	public function readStored( $fallback = array() ) {
		return get_option( $this->optionName, $fallback );
	}

	public function save( Settings $settings ) {
		$stored = get_option( $this->optionName, array() );
		$original = is_array( $stored ) ? $stored : array();

		return $this->replace( $settings, $original );
	}

	public function replace( Settings $settings, array $storageBase ) {
		$encoded = $this->codec->encode( $settings, $storageBase );

		return $this->replaceStored( $encoded );
	}

	public function replaceStored( array $stored ) {
		update_option( $this->optionName, $stored );

		return $stored;
	}
}
