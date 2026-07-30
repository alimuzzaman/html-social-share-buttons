<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings;

final class LegacyTruthiness {
	public static function isTruthy( $value ) {
		return (bool) $value;
	}
}
