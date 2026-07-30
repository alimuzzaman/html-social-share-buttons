<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;

final class LegacyRegistryAdapter {
	private $canonicalNetworks;

	public function __construct( NetworkRegistry $canonicalNetworks ) {
		$this->canonicalNetworks = $canonicalNetworks;
	}

	public function adapt( $legacyRegistry, $requestedIconSetId = null ) {
		$networks = new NetworkRegistry();
		$identifiers = new LegacyIdentifierMap();
		foreach ( $this->canonicalNetworks->all() as $network ) {
			$identifiers->canonicalNetwork( $network->id() );
			$networks->register( $network );
		}

		$legacyIconSets = $this->legacyIconSets( $legacyRegistry, $requestedIconSetId );
		$definitions = array();

		foreach ( $legacyIconSets as $legacyIconSet ) {
			if ( ! is_object( $legacyIconSet ) || empty( $legacyIconSet->id ) ) {
				continue;
			}

			$iconFiles = array();
			foreach ( (array) $legacyIconSet->icons as $networkId => $legacyIcon ) {
				if ( ! is_array( $legacyIcon ) || empty( $legacyIcon['image'] ) ) {
					continue;
				}

				$legacyNetworkId = sanitize_key( $networkId );
				if ( '' === $legacyNetworkId ) {
					continue;
				}
				$networkId = $identifiers->canonicalNetwork( $legacyNetworkId );
				if ( ! $networks->has( $networkId ) ) {
					$template = isset( $legacyIcon['url'] ) && is_string( $legacyIcon['url'] )
						? $legacyIcon['url']
						: '';
					$networks->register(
						new Network(
							$networkId,
							! empty( $legacyIcon['name'] ) ? $legacyIcon['name'] : $networkId,
							! empty( $legacyIcon['class'] ) && sanitize_html_class( $legacyIcon['class'] )
								? sanitize_html_class( $legacyIcon['class'] )
								: $networkId,
							$template,
							$this->placeholders( $template ),
							false
						)
					);
				}

				$iconFiles[ $networkId ] = basename( (string) $legacyIcon['image'] );
			}

			if ( empty( $iconFiles ) ) {
				continue;
			}

			$legacyId = sanitize_key( $legacyIconSet->id );
			if ( '' === $legacyId ) {
				continue;
			}
			$id = $identifiers->canonicalIconSet( $legacyId );
			$shapes = array();
			foreach ( (array) $legacyIconSet->types as $shape ) {
				$shape = sanitize_key( $shape );
				if ( '' !== $shape ) {
					$shapes[] = $identifiers->canonicalShape( $id, $shape );
				}
			}
			if ( empty( $shapes ) ) {
				$shapes = array( $identifiers->canonicalShape( $id, 'square' ) );
			}
			$definitions[ $id ] = new IconSet(
				$id,
				! empty( $legacyIconSet->name ) ? $legacyIconSet->name : $id,
				! empty( $legacyIconSet->stylesheet ) ? basename( $legacyIconSet->stylesheet ) : 'style.css',
				! empty( $legacyIconSet->preview_img ) ? basename( $legacyIconSet->preview_img ) : 'preview.png',
				$shapes,
				$iconFiles
			);
		}

		$iconSets = new IconSetRegistry( $networks );
		foreach ( $definitions as $definition ) {
			$iconSets->register( $definition );
		}

		return new LegacyRegistryBundle( $networks, $iconSets, $identifiers );
	}

	private function legacyIconSets( $legacyRegistry, $requestedIconSetId ) {
		if (
			null !== $requestedIconSetId &&
			is_object( $legacyRegistry ) &&
			method_exists( $legacyRegistry, 'get_iconset' )
		) {
			$selected = $legacyRegistry->get_iconset( sanitize_key( $requestedIconSetId ) );

			return is_object( $selected ) ? array( $selected ) : array();
		}

		return is_object( $legacyRegistry ) && method_exists( $legacyRegistry, 'get_iconsets' )
			? $legacyRegistry->get_iconsets()
			: array();
	}

	private function placeholders( $template ) {
		preg_match_all( '/%%[a-z]+%%/', (string) $template, $matches );

		return array_values( array_unique( $matches[0] ) );
	}
}
