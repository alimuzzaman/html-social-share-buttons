<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering\HookedShareUrlResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering\ShareContextFactory;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\HtmlRenderer;

/**
 * Single canonical rendering entrypoint for shortcode, block, builder and
 * public-PHP adapters. It accepts their established option shape at the
 * presentation edge, then works only with canonical registries and values.
 */
final class RenderFacade {
	private $networks;
	private $assets;
	private $mapper;
	private $renderer;
	private $builder;
	private $contexts;
	private $extensions;

	public function __construct(
		NetworkRegistry $networks,
		IconSetRegistry $iconSets,
		IconSetAssetResolver $assets,
		ExtensionHooks $extensions = null,
		ShareContextFactory $contexts = null,
		RenderRequestMapper $mapper = null,
		HtmlRenderer $renderer = null
	) {
		$this->networks = $networks;
		$this->assets = $assets;
		$this->extensions = $extensions ? $extensions : new ExtensionHooks();
		$this->mapper = $mapper ? $mapper : new RenderRequestMapper();
		$this->renderer = $renderer ? $renderer : new HtmlRenderer();
		$this->contexts = $contexts ? $contexts : new ShareContextFactory( null, $this->extensions );
		$this->builder = new BuildShareButtons(
			$networks,
			$iconSets,
			new HookedShareUrlResolver( new ResolveShareUrl(), $this->extensions )
		);
	}

	public function render( array $options, $contextPostId = 0, ShareContext $context = null ) {
		$options = $this->normalizeOptions( $options, $contextPostId );
		$request = $this->mapper->map( $options );
		$context = $context ? $context : $this->contexts->create( $contextPostId );
		$result = $this->builder->build( $request, $context );
		$iconSet = $result->iconSet();

		return new RenderOutcome(
			$this->renderer->render(
				$request,
				$result,
				$this->wrapperClass( $options ),
				$this->iconSetClass( $request, $iconSet->id() ),
				$result->shape()
			),
			array( $iconSet->id() => $this->assets->stylesheetUrl( $iconSet ) ),
			$this->printedIcons( $result )
		);
	}

	private function normalizeOptions( array $options, $contextPostId ) {
		$url = isset( $options['url'] ) && is_scalar( $options['url'] )
			? trim( (string) $options['url'] )
			: '';
		$options['url'] = $this->usesCurrentPostPermalink( $url )
			? $this->contexts->permalink( $contextPostId )
			: $this->decodeUrl( $url );
		$options['share_templates'] = $this->filteredTemplates( $options );

		return $options;
	}

	private function filteredTemplates( array $options ) {
		$submitted = isset( $options['share_templates'] ) && is_array( $options['share_templates'] )
			? $options['share_templates']
			: array();
		$templates = array();
		foreach ( $this->networks->all() as $network ) {
			$id = $network->id();
			if ( 'x' === $id && isset( $submitted['twitter'] ) && ! isset( $submitted['x'] ) ) {
				$templates[ $id ] = $submitted['twitter'];
			} elseif ( isset( $submitted[ $id ] ) ) {
				$templates[ $id ] = $submitted[ $id ];
			} else {
				$templates[ $id ] = $network->defaultShareTemplate();
			}
		}
		$templates = $this->extensions->shareTemplates( $templates );

		return is_array( $templates ) ? $templates : array();
	}

	private function printedIcons( $result ) {
		$iconSet = $result->iconSet();
		$printed = array();
		foreach ( array_merge( $result->buttons(), $result->profileLinks() ) as $button ) {
			$network = $button->network();
			$networkId = $network->id();
			$printed[ $iconSet->id() . '_' . $result->shape() . "\0_" . $networkId ] = array(
				'id'             => $networkId,
				'name'           => $network->label(),
				'class'          => $this->renderer->cssClass( $network ),
				'image'          => $button->iconFile(),
				'url'            => $network->defaultShareTemplate(),
				'iconset_id'     => $iconSet->id(),
				'iconset_url'    => $this->assets->setUrl( $iconSet ),
				'iconset_type'   => $result->shape(),
				'icon_asset_url' => $this->assets->iconUrl( $iconSet, $result->shape(), $networkId ),
			);
		}

		return $printed;
	}

	private function wrapperClass( array $options ) {
		$class = isset( $options['class'] ) && is_scalar( $options['class'] )
			? (string) $options['class']
			: 'left';

		return function_exists( 'sanitize_html_class' ) ? sanitize_html_class( $class ) : $class;
	}

	/**
	 * Historical markup retained a valid requested icon-set slug even when the
	 * renderer selected Default as the usable icon-set fallback. Stylesheets and
	 * icon URLs must follow the resolved set; the wrapper class is public HTML.
	 */
	private function iconSetClass( RenderRequest $request, $resolvedIconSetId ) {
		$requested = $request->iconSetId();

		return '' !== $requested ? $requested : $resolvedIconSetId;
	}

	private function usesCurrentPostPermalink( $url ) {
		$url = $this->decodeUrl( $url );
		return in_array(
			$url,
			array( '', '%%permalink%%', 'http://%%permalink%%', 'https://%%permalink%%' ),
			true
		);
	}

	private function decodeUrl( $url ) {
		$url = (string) $url;
		for ( $attempt = 0; $attempt < 2; $attempt++ ) {
			$decoded = rawurldecode( $url );
			if ( $decoded === $url ) {
				break;
			}
			$url = $decoded;
		}

		return $url;
	}
}
