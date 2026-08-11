<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;

/**
 * Builds the old browser payload from canonical icon-set and network metadata.
 */
final class IconSetPayloadBuilder {
	private $iconSets;
	private $networks;
	private $assets;

	public function __construct(
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		IconSetAssetResolver $assets
	) {
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
	}

	public function settingsPayload() {
		$payload = array();
		foreach ( $this->iconSets->all() as $iconSet ) {
			$payload[] = $this->settingsIconSet( $iconSet );
		}

		return $payload;
	}

	public function legacyObjectPayload( $id ) {
		$iconSet = $this->iconSets->get( $id );
		$icons = array();
		foreach ( $iconSet->iconFiles() as $networkId => $fileName ) {
			$icons[ $networkId ] = array(
				'id'    => $networkId,
				'name'  => $this->networkLabel( $networkId ),
				'image' => $fileName,
				'url'   => '',
			);
		}

		return array(
			'id'              => $iconSet->id(),
			'name'            => $this->iconSetLabel( $iconSet->id(), $iconSet->label() ),
			'types'           => $iconSet->shapes(),
			'icons'           => $icons,
			'stylesheet'      => $iconSet->stylesheet(),
			'preview_img'     => $iconSet->preview(),
			'stylesheet_url'  => $this->assets->stylesheetUrl( $iconSet ),
			'preview_img_url' => $this->assets->previewUrl( $iconSet ),
		);
	}

	public function iconNames( $id ) {
		$iconSet = $this->iconSets->get( $id );
		$names = array();
		foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
			$names[ $networkId ] = $this->networkLabel( $networkId );
		}

		return $names;
	}

	public function previewUrl( $id ) {
		return $this->assets->previewUrl( $this->iconSets->get( $id ) );
	}

	private function settingsIconSet( IconSet $iconSet ) {
		$icons = array();
		$shapes = $iconSet->shapes();
		$defaultShape = reset( $shapes );
		foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
			$previewUrls = array();
			foreach ( $shapes as $shape ) {
				$previewUrls[ $shape ] = esc_url_raw(
					$this->assets->iconUrl( $iconSet, $shape, $networkId )
				);
			}
			$icons[] = array(
				'id'           => $networkId,
				'name'         => esc_html( $this->networkLabel( $networkId ) ),
				'preview_url'  => $previewUrls[ $defaultShape ],
				'preview_urls' => $previewUrls,
			);
		}

		return array(
			'id'          => esc_attr( $iconSet->id() ),
			'name'        => esc_html( $this->iconSetLabel( $iconSet->id(), $iconSet->label() ) ),
			'preview_img' => esc_url_raw( $this->assets->previewUrl( $iconSet ) ),
			'types'       => $shapes,
			'icons'       => $icons,
		);
	}

	private function networkLabel( $networkId ) {
		switch ( (string) $networkId ) {
			case 'facebook':
				return __( 'Facebook', 'html-social-share-buttons' );
			case 'x':
				return __( 'X (formerly Twitter)', 'html-social-share-buttons' );
			case 'linkedin':
				return __( 'LinkedIn', 'html-social-share-buttons' );
			case 'pinterest':
				return __( 'Pinterest', 'html-social-share-buttons' );
			case 'telegram':
				return __( 'Telegram', 'html-social-share-buttons' );
			case 'bluesky':
				return __( 'Bluesky', 'html-social-share-buttons' );
			case 'mail':
				return __( 'Email', 'html-social-share-buttons' );
			default:
				return $this->networks->get( $networkId )->label();
		}
	}

	private function iconSetLabel( $id, $fallback ) {
		switch ( (string) $id ) {
			case 'default':
				return __( 'Default', 'html-social-share-buttons' );
			case 'flat':
				return __( 'Flat', 'html-social-share-buttons' );
			case 'long-shadows':
				return __( 'Long Shadows', 'html-social-share-buttons' );
			case 'prajin':
				return __( 'Prajin', 'html-social-share-buttons' );
			case 'bootstrap-solid':
				return __( 'Bootstrap Solid', 'html-social-share-buttons' );
			case 'tabler-outline':
				return __( 'Tabler Outline', 'html-social-share-buttons' );
			default:
				return (string) $fallback;
		}
	}
}
