<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

/**
 * Immutable paths and URLs for this plugin installation.
 *
 * Keeping this information in the canonical composition root prevents
 * adapters from recalculating paths from their own source files.  It also
 * gives legacy symbols one authoritative value to alias.
 */
final class PluginPaths {
	private $file;
	private $directory;
	private $url;

	public function __construct( $file, $directory = '', $url = '' ) {
		$this->file = (string) $file;
		$this->directory = '' === (string) $directory
			? dirname( $this->file )
			: (string) $directory;
		$this->directory = rtrim( $this->directory, '/\\' ) . '/';
		$this->url = rtrim( (string) $url, '/' );
		if ( '' !== $this->url ) {
			$this->url .= '/';
		}
	}

	public static function fromPluginFile( $file ) {
		$url = function_exists( 'plugin_dir_url' ) ? plugin_dir_url( $file ) : '';

		return new self( $file, dirname( (string) $file ), $url );
	}

	public function file() {
		return $this->file;
	}

	public function directory() {
		return $this->directory;
	}

	public function url() {
		return $this->url;
	}

	public function assetsDirectory() {
		return $this->directory . 'assets/';
	}

	public function assetsUrl() {
		return $this->url . 'assets/';
	}

	public function iconSetsDirectory() {
		return $this->directory . 'resources/iconsets/';
	}

	public function blockMetadataFile() {
		return $this->directory . 'block.json';
	}

	public function socialLinksBlockMetadataFile() {
		return $this->directory . 'blocks/social-links/block.json';
	}

	public function languageDirectory() {
		return $this->directory . 'languages/';
	}

	public function languageRelativePath() {
		return basename( rtrim( $this->directory, '/\\' ) ) . '/languages';
	}
}
