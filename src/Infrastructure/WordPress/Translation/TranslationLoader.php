<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation;

final class TranslationLoader {
	private $pluginFile;
	private $textDomain;

	public function __construct( $pluginFile, $textDomain ) {
		$this->pluginFile = (string) $pluginFile;
		$this->textDomain = (string) $textDomain;
	}

	public function load() {
		return $this->loadDomain( $this->textDomain );
	}

	/**
	 * Load an additional catalog from this plugin's language directory.
	 *
	 * Compatibility policy stays with the caller; this service only resolves
	 * the plugin-relative language path for a supplied domain.
	 */
	public function loadDomain( $textDomain ) {
		return load_plugin_textdomain(
			(string) $textDomain,
			false,
			$this->relativeLanguagePath()
		);
	}

	public function textDomain() {
		return $this->textDomain;
	}

	public function relativeLanguagePath() {
		return dirname( plugin_basename( $this->pluginFile ) ) . '/languages';
	}
}
