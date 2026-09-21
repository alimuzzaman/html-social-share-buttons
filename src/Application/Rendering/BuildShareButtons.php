<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderResult;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ResolvedButton;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ResolvedProfileLink;
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
		$profileLinks = array();

		foreach ( $request->networkIds() as $networkId ) {
			if ( ! $this->networks->has( $networkId ) || ! $iconSet->hasIcon( $networkId ) ) {
				continue;
			}

			$network = $this->networks->get( $networkId );
			$templateOverride = isset( $overrides[ $networkId ] ) ? $overrides[ $networkId ] : '';
			$resolved = $this->resolveButton(
				$network,
				$context,
				$templateOverride,
				$request->permalinkOverride(),
				$request->browserUrlEnabled()
			);
			$buttons[] = new ResolvedButton(
				$network,
				$resolved['url'],
				$iconSet->iconFile( $networkId ),
				$resolved['descriptor']
			);
		}

		$requestedProfileLinks = $request->profileLinks();
		foreach ( $this->networks->ids() as $networkId ) {
			if ( ! isset( $requestedProfileLinks[ $networkId ] ) || ! $iconSet->hasIcon( $networkId ) ) {
				continue;
			}

			$profileLinks[] = new ResolvedProfileLink(
				$this->networks->get( $networkId ),
				$requestedProfileLinks[ $networkId ],
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
			$buttons,
			$profileLinks
		);
	}

	private function browserUrlDescriptor( $network, ShareContext $context, $templateOverride ) {
		if ( method_exists( $this->urlResolver, 'browserUrlDescriptor' ) ) {
			return $this->urlResolver->browserUrlDescriptor( $network, $context, $templateOverride );
		}

		$template = is_string( $templateOverride ) && '' !== trim( $templateOverride )
			? $templateOverride
			: $network->defaultShareTemplate();

		return array(
			'permalink_slot' => '%%permalink%%',
			'template'       => str_replace(
				array( '%%title%%', '%%description%%', '%%imageurl%%' ),
				array(
					rawurlencode( $context->title() ),
					rawurlencode( $context->description() ),
					rawurlencode( $context->imageUrl() ),
				),
				$template
			),
		);
	}

	private function resolveButton( $network, ShareContext $context, $templateOverride, $permalinkOverride, $browserUrlEnabled ) {
		if ( $browserUrlEnabled && method_exists( $this->urlResolver, 'resolveWithBrowserDescriptor' ) ) {
			$resolved = $this->urlResolver->resolveWithBrowserDescriptor(
				$network,
				$context,
				$templateOverride,
				$permalinkOverride
			);
			if ( is_array( $resolved ) && isset( $resolved['url'] ) ) {
				return array(
					'url'        => $resolved['url'],
					'descriptor' => isset( $resolved['descriptor'] ) ? $resolved['descriptor'] : array(),
				);
			}
		}

		$url = $this->urlResolver->resolve(
			$network,
			$context,
			$templateOverride,
			$permalinkOverride
		);

		return array(
			'url'        => $url,
			'descriptor' => $browserUrlEnabled ? $this->browserUrlDescriptor( $network, $context, $templateOverride ) : array(),
		);
	}
}
