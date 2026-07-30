<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration;

final class WordPressMigrationStateStore implements MigrationStateStore {
	const OPTION_NAME = 'hssb_schema_version';

	public function currentVersion() {
		return (int) get_option( self::OPTION_NAME, 0 );
	}

	public function saveVersion( $version ) {
		update_option( self::OPTION_NAME, (int) $version, false );
	}
}
