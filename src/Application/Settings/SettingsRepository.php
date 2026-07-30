<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

interface SettingsRepository {
	public function load();

	public function save( Settings $settings );
}
