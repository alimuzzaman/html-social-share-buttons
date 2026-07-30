<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;

interface ShareUrlResolver {
	public function resolve(
		Network $network,
		ShareContext $context,
		$templateOverride = '',
		$permalinkOverride = ''
	);
}
