<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ShareUrlResolver;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

/**
 * Applies canonical extension hooks around the canonical resolver.
 *
 * Legacy hook names are bridged to these filters at the WordPress boundary;
 * Application code deliberately does not know their names or implementations.
 */
final class HookedShareUrlResolver implements ShareUrlResolver {
	private $resolver;
	private $extensions;

	public function __construct( ShareUrlResolver $resolver, ExtensionHooks $extensions ) {
		$this->resolver = $resolver;
		$this->extensions = $extensions;
	}

	public function resolve(
		Network $network,
		ShareContext $context,
		$templateOverride = '',
		$permalinkOverride = ''
	) {
		$fallback = $network->defaultShareTemplate();
		$template = is_string( $templateOverride ) && '' !== trim( $templateOverride )
			? $templateOverride
			: $fallback;
		$template = $this->extensions->shareTemplate( $template, $network->id(), $fallback );
		$url = $this->resolver->resolve( $network, $context, $template, $permalinkOverride );

		return $this->extensions->shareUrl( $url );
	}
}
