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
		$template = $this->selectedTemplate( $network, $templateOverride );
		$url = $this->resolver->resolve( $network, $context, $template, $permalinkOverride );

		return $this->extensions->shareUrl( $url );
	}

	/**
	 * Resolve one link and, when safe, derive its browser descriptor from the
	 * exact template used for that link. This keeps stateful template filters
	 * from being called a second time during descriptor generation.
	 */
	public function resolveWithBrowserDescriptor(
		Network $network,
		ShareContext $context,
		$templateOverride = '',
		$permalinkOverride = ''
	) {
		$template = $this->selectedTemplate( $network, $templateOverride );
		$hasExternalShareUrlFilters = $this->hasExternalShareUrlFilters();
		$url = $this->resolver->resolve( $network, $context, $template, $permalinkOverride );
		$url = $this->extensions->shareUrl( $url );

		return array(
			'url'        => $url,
			'descriptor' => $hasExternalShareUrlFilters
				? array()
				: $this->descriptorFromTemplate( $template, $context ),
		);
	}

	/**
	 * Build a browser descriptor only when the final URL hook cannot mutate it.
	 * The per-network template hook is applied once here, while the final URL
	 * hook causes a safe server-only fallback because it receives a full URL.
	 */
	public function browserUrlDescriptor( Network $network, ShareContext $context, $templateOverride = '' ) {
		if ( $this->hasExternalShareUrlFilters() ) {
			return array();
		}

		$template = $this->selectedTemplate( $network, $templateOverride );

		return $this->descriptorFromTemplate( $template, $context );
	}

	private function selectedTemplate( Network $network, $templateOverride ) {
		$fallback = $network->defaultShareTemplate();
		$template = is_string( $templateOverride ) && '' !== trim( $templateOverride )
			? $templateOverride
			: $fallback;
		$template = $this->extensions->shareTemplate( $template, $network->id(), $fallback );
		if ( 'bluesky' === $network->id() ) {
			$template = str_ireplace( '%0A', '%20', $template );
		}

		return $template;
	}

	private function descriptorFromTemplate( $template, ShareContext $context ) {
		if ( ! is_string( $template ) || false === strpos( $template, '%%permalink%%' ) ) {
			return array();
		}

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

	private function hasExternalShareUrlFilters() {
		return $this->hasExternalCallbacks( ExtensionHooks::SHARE_URL, true )
			|| $this->hasExternalCallbacks( 'zm' . '_sh_placeholder' );
	}

	private function hasExternalCallbacks( $hookName, $ignoreLegacyBridge = false ) {
		global $wp_filter;
		if ( ! isset( $wp_filter[ $hookName ] ) ) {
			return false;
		}

		$hook = $wp_filter[ $hookName ];
		$callbacks = isset( $hook->callbacks ) && is_array( $hook->callbacks )
			? $hook->callbacks
			: ( is_array( $hook ) ? $hook : array() );
		foreach ( $callbacks as $priorityCallbacks ) {
			foreach ( (array) $priorityCallbacks as $callback ) {
				$function = isset( $callback['function'] ) ? $callback['function'] : null;
				if (
					$ignoreLegacyBridge &&
					is_array( $function ) &&
					isset( $function[0], $function[1] ) &&
					'bridgeUrl' === $function[1] &&
					false !== strpos( (string) $function[0], 'LegacyHooks' )
				) {
					continue;
				}
				return true;
			}
		}

		return false;
	}
}
