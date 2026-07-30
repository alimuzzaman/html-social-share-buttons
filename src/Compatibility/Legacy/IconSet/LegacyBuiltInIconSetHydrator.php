<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet;

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network\LegacyNetworkMapper;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use RuntimeException;

final class LegacyBuiltInIconSetHydrator {
	private $iconSets;
	private $networks;
	private $assets;
	private $networkMapper;
	private $canonicalAssets;

	public function __construct(
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		LegacyIconSetAssetMap $assets,
		LegacyNetworkMapper $networkMapper,
		IconSetAssetResolver $canonicalAssets
	) {
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
		$this->networkMapper = $networkMapper;
		$this->canonicalAssets = $canonicalAssets;
	}

	public function hydrate( $legacyIconSet, $iconSetId, $definitionFile ) {
		$iconSet = $this->iconSets->get( $iconSetId );
		$icons = array();
		$this->assertCanonicalAssetsExist( $iconSet );

		foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
			$network = $this->networks->get( $networkId );
			$icons[ $networkId ] = array(
				'id'    => $networkId,
				'name'  => $this->legacyLabel( $networkId, $network->label() ),
				'class' => $this->networkMapper->cssClass( $network ),
				'image' => $this->assets->iconFile( $iconSet, $networkId ),
				'url'   => $network->defaultShareTemplate(),
			);
		}

		$legacyIconSet->id = $iconSet->id();
		$legacyIconSet->name = $iconSet->label();
		$legacyIconSet->stylesheet = $iconSet->stylesheet();
		$legacyIconSet->preview_img = $iconSet->preview();
		$legacyIconSet->types = $iconSet->shapes();
		$legacyIconSet->icons = $icons;
		$legacyIconSet->__FILE__ = $definitionFile;
	}

	private function assertCanonicalAssetsExist( $iconSet ) {
		$paths = array(
			$this->canonicalAssets->stylesheetPath( $iconSet ),
			$this->canonicalAssets->previewPath( $iconSet ),
		);
		foreach ( $iconSet->shapes() as $shape ) {
			foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
				$paths[] = $this->canonicalAssets->iconPath( $iconSet, $shape, $networkId );
			}
		}

		foreach ( $paths as $path ) {
			if ( ! is_file( $path ) ) {
				throw new RuntimeException( 'A canonical built-in icon-set asset is missing.' );
			}
		}
	}

	private function legacyLabel( $networkId, $canonicalLabel ) {
		$overrides = array(
			'x'        => 'X (formerly Twitter)',
			'linkedin' => 'Linkedin',
		);

		return isset( $overrides[ $networkId ] )
			? $overrides[ $networkId ]
			: $canonicalLabel;
	}
}
