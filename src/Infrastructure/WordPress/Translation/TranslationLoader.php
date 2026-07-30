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
		return load_plugin_textdomain(
			$this->textDomain,
			false,
			$this->relativeLanguagePath()
		);
	}

	public function relativeLanguagePath() {
		return dirname( plugin_basename( $this->pluginFile ) ) . '/languages';
	}
}
