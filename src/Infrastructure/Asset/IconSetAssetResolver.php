<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use InvalidArgumentException;

final class IconSetAssetResolver {
	private $assetRoot;
	private $assetBaseUrl;

	public function __construct( $assetRoot, $assetBaseUrl ) {
		$this->assetRoot = rtrim( (string) $assetRoot, '/\\' );
		$this->assetBaseUrl = rtrim( (string) $assetBaseUrl, '/' );
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
			rawurlencode( (string) $shape ) . '/' .
				rawurlencode( $iconSet->iconFile( $networkId ) )
		);
	}

	private function path( IconSet $iconSet, $relativePath ) {
		return $this->assetRoot . DIRECTORY_SEPARATOR . $iconSet->id() .
			DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $relativePath );
	}

	private function url( IconSet $iconSet, $relativePath ) {
		return $this->assetBaseUrl . '/' . rawurlencode( $iconSet->id() ) . '/' . $relativePath;
	}

	private function assertShape( IconSet $iconSet, $shape ) {
		if ( ! in_array( (string) $shape, $iconSet->shapes(), true ) ) {
			throw new InvalidArgumentException( 'The icon set does not support this shape.' );
		}
	}
}
