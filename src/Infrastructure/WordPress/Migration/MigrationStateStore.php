<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration;

interface MigrationStateStore {
	public function currentVersion();

	public function saveVersion( $version );
}
