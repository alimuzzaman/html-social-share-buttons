<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use InvalidArgumentException;

final class IconSetAssetResolver {
	private $assetRoot;
	private $assetBaseUrl;
	private $pluginFile;
	private $externalLocations = array();

	public function __construct( $assetRoot, $assetBaseUrl, $pluginFile = '' ) {
		$this->assetRoot = rtrim( (string) $assetRoot, '/\\' );
		$this->assetBaseUrl = rtrim( (string) $assetBaseUrl, '/' );
		$this->pluginFile = (string) $pluginFile;
	}

	public function stylesheetPath( IconSet $iconSet ) {
		return $this->path( $iconSet, $iconSet->stylesheet() );
	}

	public function stylesheetUrl( IconSet $iconSet ) {
		return $this->url( $iconSet, $iconSet->stylesheet() );
	}

	public function previewPath( IconSet $iconSet ) {
		return $this->path( $iconSet, $iconSet->preview() );
	}

	public function previewUrl( IconSet $iconSet ) {
		return $this->url( $iconSet, $iconSet->preview() );
	}

	public function iconPath( IconSet $iconSet, $shape, $networkId ) {
		$this->assertShape( $iconSet, $shape );

		return $this->path(
			$iconSet,
			(string) $shape . '/' . $iconSet->iconFile( $networkId )
		);
	}

	public function iconUrl( IconSet $iconSet, $shape, $networkId ) {
		$this->assertShape( $iconSet, $shape );

		return $this->url(
			$iconSet,
			(string) $shape . '/' . $iconSet->iconFile( $networkId )
		);
	}

	/**
	 * Public directory URL for integrations that generate the historical
	 * background-image rule contract.
	 */
	public function setUrl( IconSet $iconSet ) {
		return $this->setRootUrl( $iconSet ) . '/';
	}

	public function setPath( IconSet $iconSet ) {
		return $this->setRootPath( $iconSet );
	}

	/**
	 * Register a third-party icon set whose released files do not live below
	 * this plugin. Both values are deliberately required and validated together
	 * so a legacy add-on cannot redirect only filesystem or public asset lookup.
	 */
	public function registerExternalLocation( $id, $directory, $url ) {
		$id = (string) $id;
		$directory = rtrim( (string) $directory, '/\\' );
		$url = rtrim( (string) $url, '/' );
		if (
			! preg_match( '/^[a-z][a-z0-9-]*$/', $id ) ||
			'' === $directory ||
			0 !== strpos( $directory, '/' ) ||
			false !== strpos( $directory, "\0" ) ||
			false !== strpos( $directory, '/../' ) ||
			! $this->validExternalUrl( $url )
		) {
			return false;
		}

		$this->externalLocations[ $id ] = array(
			'directory' => $directory,
			'url'       => $url,
		);

		return true;
	}

	private function path( IconSet $iconSet, $relativePath ) {
		return $this->setRootPath( $iconSet ) . DIRECTORY_SEPARATOR .
			str_replace( '/', DIRECTORY_SEPARATOR, $relativePath );
	}

	private function url( IconSet $iconSet, $relativePath ) {
		return $this->setRootUrl( $iconSet ) . '/' . $this->encodePath( $relativePath );
	}

	private function setRootPath( IconSet $iconSet ) {
		$id = $iconSet->id();
		if ( isset( $this->externalLocations[ $id ] ) ) {
			return $this->externalLocations[ $id ]['directory'];
		}
		$assetPath = $iconSet->assetPath();
		if ( '' === $assetPath ) {
			return $this->assetRoot . DIRECTORY_SEPARATOR . $iconSet->id();
		}

		return $this->pluginRootPath() . DIRECTORY_SEPARATOR .
			str_replace( '/', DIRECTORY_SEPARATOR, $assetPath );
	}

	private function setRootUrl( IconSet $iconSet ) {
		$id = $iconSet->id();
		if ( isset( $this->externalLocations[ $id ] ) ) {
			return $this->externalLocations[ $id ]['url'];
		}
		$assetPath = $iconSet->assetPath();
		if ( '' !== $this->pluginFile && function_exists( 'plugins_url' ) ) {
			$relativePath = '' === $assetPath ? $iconSet->id() : $assetPath;

			return rtrim( plugins_url( $relativePath, $this->pluginFile ), '/' );
		}
		if ( '' === $assetPath ) {
			return $this->assetBaseUrl . '/' . rawurlencode( $iconSet->id() );
		}

		return $this->pluginRootUrl() . '/' . $this->encodePath( $assetPath );
	}

	/**
	 * Until all consumers pass the plugin root, preserve the old
	 * assets/iconsets constructor convention. New manifests deliberately point
	 * historical packs at their released iconset/ directories.
	 */
	private function pluginRootPath() {
		if (
			'iconsets' === basename( $this->assetRoot ) &&
			'assets' === basename( dirname( $this->assetRoot ) )
		) {
			return dirname( $this->assetRoot, 2 );
		}

		return $this->assetRoot;
	}

	private function pluginRootUrl() {
		$needle = '/assets/iconsets';
		if ( substr( $this->assetBaseUrl, -strlen( $needle ) ) === $needle ) {
			return substr( $this->assetBaseUrl, 0, -strlen( $needle ) );
		}

		return $this->assetBaseUrl;
	}

	private function encodePath( $path ) {
		$segments = explode( '/', (string) $path );
		foreach ( $segments as $index => $segment ) {
			$segments[ $index ] = rawurlencode( $segment );
		}

		return implode( '/', $segments );
	}

	private function assertShape( IconSet $iconSet, $shape ) {
		if ( ! in_array( (string) $shape, $iconSet->shapes(), true ) ) {
			throw new InvalidArgumentException( 'The icon set does not support this shape.' );
		}
	}

	private function validExternalUrl( $url ) {
		$parts = wp_parse_url( $url );

		return is_array( $parts ) &&
			isset( $parts['scheme'], $parts['host'] ) &&
			in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true );
	}
}
