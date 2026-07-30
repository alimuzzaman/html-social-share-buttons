<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

interface SettingsStateStore extends SettingsRepository {
	public function readStored( $fallback = array() );

	public function replace( Settings $settings, array $storageBase );

	public function replaceStored( array $stored );
}
