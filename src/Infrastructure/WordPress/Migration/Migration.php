<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration;

interface Migration {
	public function version();

	public function up();
}
