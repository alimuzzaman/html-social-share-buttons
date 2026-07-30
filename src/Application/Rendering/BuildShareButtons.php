<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderResult;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ResolvedButton;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

final class BuildShareButtons {
	private $networks;
	private $iconSets;
	private $urlResolver;

	public function __construct(
		NetworkRegistry $networks,
		IconSetRegistry $iconSets,
		ShareUrlResolver $urlResolver
	) {
		$this->networks = $networks;
		$this->iconSets = $iconSets;
		$this->urlResolver = $urlResolver;
	}

	public function build( RenderRequest $request, ShareContext $context ) {
		$iconSetId = $this->iconSets->has( $request->iconSetId() )
			? $request->iconSetId()
			: 'default';
		$iconSet = $this->iconSets->get( $iconSetId );
		$shape = in_array( $request->shape(), $iconSet->shapes(), true )
			? $request->shape()
			: $iconSet->shapes()[0];
		$overrides = $request->templateOverrides();
		$buttons = array();

		foreach ( $request->networkIds() as $networkId ) {
			if ( ! $this->networks->has( $networkId ) || ! $iconSet->hasIcon( $networkId ) ) {
				continue;
			}

			$network = $this->networks->get( $networkId );
			$buttons[] = new ResolvedButton(
				$network,
				$this->urlResolver->resolve(
					$network,
					$context,
					isset( $overrides[ $networkId ] ) ? $overrides[ $networkId ] : '',
					$request->permalinkOverride()
				),
				$iconSet->iconFile( $networkId )
			);
		}

		$relTokens = array( 'noopener', 'noreferrer' );
		if ( $request->noFollow() ) {
			array_unshift( $relTokens, 'nofollow' );
		}

		return new RenderResult(
			$iconSet,
			$shape,
			$request->placement(),
			$request->heading(),
			$relTokens,
			$buttons
		);
	}
}
