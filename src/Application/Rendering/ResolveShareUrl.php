<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

final class ResolveShareUrl implements ShareUrlResolver {
	public function resolve(
		Network $network,
		ShareContext $context,
		$templateOverride = '',
		$permalinkOverride = ''
	) {
		$template = is_string( $templateOverride ) && '' !== trim( $templateOverride )
			? $templateOverride
			: $network->defaultShareTemplate();
		$permalink = is_string( $permalinkOverride ) && '' !== $permalinkOverride
			? $permalinkOverride
			: $context->permalink();

		return str_replace(
			array( '%%permalink%%', '%%title%%', '%%description%%', '%%imageurl%%' ),
			array(
				rawurlencode( $permalink ),
				rawurlencode( $context->title() ),
				rawurlencode( $context->description() ),
				rawurlencode( $context->imageUrl() ),
			),
			$template
		);
	}
}
