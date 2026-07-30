<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;

final class LegacyNetworkMapper {
	public function cssClass( Network $network ) {
		return 'x' === $network->id() ? 'twitter' : $network->cssClass();
	}
}
