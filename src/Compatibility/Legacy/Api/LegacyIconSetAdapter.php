<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;

/**
 * Imports a third-party legacy icon-set object once at registration time.
 * Built-ins are canonical manifests and must never pass through this adapter.
 */
final class LegacyIconSetAdapter {
	private $adapted = array();

	public function register(
		$legacyIconSet,
		IconSetRegistry $registry,
		NetworkRegistry $networks = null,
		IconSetAssetResolver $assets = null
	) {
		if ( ! is_object( $legacyIconSet ) || empty( $legacyIconSet->id ) ) {
			return false;
		}

		$id = $this->canonicalId( $legacyIconSet->id );
		if ( '' === $id || isset( $this->adapted[ $id ] ) || $registry->has( $id ) ) {
			return false;
		}

		$icons = array();
		foreach ( (array) $legacyIconSet->icons as $networkId => $icon ) {
			if ( ! is_array( $icon ) || empty( $icon['image'] ) ) {
				continue;
			}
			$networkId = $this->canonicalId( $networkId );
			if ( '' !== $networkId ) {
				if ( ! $this->ensureNetwork( $networkId, $icon, $networks ) ) {
					continue;
				}
				$icons[ $networkId ] = basename( (string) $icon['image'] );
			}
		}
		if ( empty( $icons ) ) {
			return false;
		}

		$shapes = array();
		foreach ( (array) $legacyIconSet->types as $shape ) {
			$shape = $this->canonicalId( $shape );
			if ( '' !== $shape ) {
				$shapes[ $shape ] = $shape;
			}
		}
		if ( empty( $shapes ) ) {
			$shapes['square'] = 'square';
		}

		$registry->register(
			new IconSet(
				$id,
				! empty( $legacyIconSet->name ) ? $legacyIconSet->name : $id,
				! empty( $legacyIconSet->stylesheet ) ? basename( $legacyIconSet->stylesheet ) : 'style.css',
				! empty( $legacyIconSet->preview_img ) ? basename( $legacyIconSet->preview_img ) : 'preview.png',
				array_values( $shapes ),
				$icons
			)
		);
		$this->adapted[ $id ] = true;
		if ( $assets instanceof IconSetAssetResolver ) {
			$assets->registerExternalLocation(
				$id,
				isset( $legacyIconSet->dir ) ? $legacyIconSet->dir : '',
				isset( $legacyIconSet->url ) ? $legacyIconSet->url : ''
			);
		}

		return true;
	}

	public function hasAdapted( $id ) {
		return ! empty( $this->adapted[ (string) $id ] );
	}

	private function ensureNetwork( $id, array $icon, $networks ) {
		if ( ! $networks instanceof NetworkRegistry ) {
			return true;
		}
		if ( $networks->has( $id ) ) {
			return true;
		}

		$template = isset( $icon['url'] ) && is_string( $icon['url'] ) ? $icon['url'] : '';
		preg_match_all( '/%%[a-z]+%%/', $template, $matches );
		$networks->register(
			new Network(
				$id,
				isset( $icon['name'] ) ? $icon['name'] : $id,
				isset( $icon['class'] ) ? $icon['class'] : $id,
				$template,
				array_values( array_unique( $matches[0] ) ),
				false
			)
		);

		return true;
	}

	/**
	 * WordPress' legacy safe keys permit underscores, while canonical domain
	 * identifiers deliberately use URL-style slugs. Normalize only at this
	 * compatibility boundary so the domain validation remains strict.
	 */
	private function canonicalId( $value ) {
		return str_replace( '_', '-', sanitize_key( $value ) );
	}
}
