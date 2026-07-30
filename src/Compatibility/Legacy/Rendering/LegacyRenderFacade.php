<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyRegistryAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network\LegacyNetworkMapper;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

final class LegacyRenderFacade {
	private $canonicalNetworks;
	private $requestMapper;
	private $htmlRenderer;

	public function __construct( NetworkRegistry $canonicalNetworks ) {
		$this->canonicalNetworks = $canonicalNetworks;
		$this->requestMapper = new LegacyRenderRequestMapper();
		$this->htmlRenderer = new LegacyHtmlRenderer( new LegacyNetworkMapper() );
	}

	public function render( array $options, $legacyIconSets ) {
		$requestedIconSetId = sanitize_key(
			isset( $options['iconset'] ) && is_scalar( $options['iconset'] )
				? $options['iconset']
				: 'default'
		);
		$bundle = ( new LegacyRegistryAdapter( $this->canonicalNetworks ) )->adapt(
			$legacyIconSets,
			$requestedIconSetId
		);
		$canonicalOptions = $this->canonicalOptions( $options, $bundle, $requestedIconSetId );
		$request = $this->requestMapper->map( $canonicalOptions, $bundle->networks() );
		$result = ( new BuildShareButtons(
			$bundle->networks(),
			$bundle->iconSets(),
			new LegacyShareUrlResolver( $bundle )
		) )->build(
			$request,
			new ShareContext( '', '' )
		);
		$legacyIconSetId = $bundle->legacyIconSetId( $result->iconSet()->id() );
		$legacyShape = $bundle->legacyShapeId(
			$result->iconSet()->id(),
			$result->shape()
		);

		return new LegacyRenderOutcome(
			$this->htmlRenderer->render(
				$request,
				$result,
				$this->iconClasses( $legacyIconSets, $legacyIconSetId, $bundle ),
				$this->wrapperClass( $options ),
				$requestedIconSetId,
				$legacyShape
			),
			$this->stylesheets( $legacyIconSets, $legacyIconSetId ),
			$this->printedIcons( $legacyIconSets, $result, $bundle )
		);
	}

	private function canonicalOptions( array $options, $bundle, $legacyIconSetId ) {
		$canonical = $options;
		$canonicalIconSetId = $bundle->canonicalIconSetId( $legacyIconSetId );
		$canonical['iconset'] = $canonicalIconSetId;

		if ( isset( $canonical['icons'] ) && is_array( $canonical['icons'] ) ) {
			$keys = array_keys( $canonical['icons'] );
			$isList = ! empty( $keys ) && $keys === range( 0, count( $keys ) - 1 );
			if ( ! $isList ) {
				$icons = array();
				foreach ( $canonical['icons'] as $networkId => $enabled ) {
					$networkId = (string) $networkId;
					if ( '' === $networkId || sanitize_key( $networkId ) !== $networkId ) {
						continue;
					}
					if ( 'twitter' === $networkId ) {
						$networkId = 'x';
					}
					$icons[ $bundle->canonicalNetworkId( $networkId ) ] = $enabled;
				}
				$canonical['icons'] = $icons;
			}
		}

		foreach (
			array( 'iconset_type', 'show_left', 'show_right', 'show_before_post', 'show_after_post' )
			as $shapeField
		) {
			if ( isset( $canonical[ $shapeField ] ) && is_scalar( $canonical[ $shapeField ] ) ) {
				$shape = sanitize_key( $canonical[ $shapeField ] );
				if ( '' !== $shape ) {
					$canonical[ $shapeField ] = $bundle->canonicalShapeId(
						$canonicalIconSetId,
						$shape
					);
				}
			}
		}

		return $canonical;
	}

	private function iconClasses( $legacyIconSets, $iconSetId, $bundle ) {
		$iconSet = $this->legacyIconSet( $legacyIconSets, $iconSetId );
		$classes = array();
		if ( ! $iconSet ) {
			return $classes;
		}

		foreach ( (array) $iconSet->icons as $networkId => $icon ) {
			if ( is_array( $icon ) && isset( $icon['class'] ) ) {
				$networkId = sanitize_key( $networkId );
				$classes[ $bundle->canonicalNetworkId( $networkId ) ] =
					sanitize_html_class( $icon['class'] );
			}
		}

		return $classes;
	}

	private function wrapperClass( array $options ) {
		return sanitize_html_class(
			isset( $options['class'] ) && is_scalar( $options['class'] ) && $options['class']
				? $options['class']
				: 'left'
		);
	}

	private function stylesheets( $legacyIconSets, $iconSetId ) {
		$iconSet = $this->legacyIconSet( $legacyIconSets, $iconSetId );
		if ( ! $iconSet ) {
			return array();
		}

		return array(
			$iconSet->id => $iconSet->url . $iconSet->stylesheet,
		);
	}

	private function printedIcons( $legacyIconSets, $result, $bundle ) {
		$legacyIconSetId = $bundle->legacyIconSetId( $result->iconSet()->id() );
		$legacyShape = $bundle->legacyShapeId(
			$result->iconSet()->id(),
			$result->shape()
		);
		$iconSet = $this->legacyIconSet( $legacyIconSets, $legacyIconSetId );
		if ( ! $iconSet ) {
			return array();
		}

		$printed = array();
		foreach ( $result->buttons() as $button ) {
			$networkId = $bundle->legacyNetworkId( $button->network()->id() );
			if ( ! isset( $iconSet->icons[ $networkId ] ) || ! is_array( $iconSet->icons[ $networkId ] ) ) {
				continue;
			}

			$icon = $iconSet->icons[ $networkId ];
			$icon['iconset_id'] = $iconSet->id;
			$icon['iconset_url'] = $iconSet->url;
			$icon['iconset_type'] = $legacyShape;
			$printed[ $iconSet->id . '_' . $legacyShape . "\0_" . $networkId ] = $icon;
		}

		return $printed;
	}

	private function legacyIconSet( $legacyIconSets, $iconSetId ) {
		if ( ! is_object( $legacyIconSets ) || ! method_exists( $legacyIconSets, 'get_iconset' ) ) {
			return null;
		}

		$iconSet = $legacyIconSets->get_iconset( $iconSetId );

		return is_object( $iconSet ) ? $iconSet : null;
	}
}
