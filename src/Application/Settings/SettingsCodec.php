<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

interface SettingsCodec {
	public function decode( array $stored );

	public function encode( Settings $settings, array $original );
}
