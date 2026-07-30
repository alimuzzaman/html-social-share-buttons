<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ShareUrlResolver;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyRegistryBundle;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

final class LegacyShareUrlResolver implements ShareUrlResolver {
	private $registry;

	public function __construct( LegacyRegistryBundle $registry ) {
		$this->registry = $registry;
	}

	public function resolve(
		Network $network,
		ShareContext $context,
		$templateOverride = '',
		$permalinkOverride = ''
	) {
		$fallback = is_string( $templateOverride ) && '' !== trim( $templateOverride )
			? $templateOverride
			: $network->defaultShareTemplate();
		$template = zm_sh_get_share_template(
			$this->registry->legacyNetworkId( $network->id() ),
			$fallback
		);

		if ( is_string( $permalinkOverride ) && '' !== $permalinkOverride ) {
			$template = str_replace(
				'%%permalink%%',
				rawurlencode( esc_url_raw( $permalinkOverride ) ),
				$template
			);
		}

		return \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Hook\LegacyExtensionHookBridge::shareUrl(
			$template
		);
	}
}
