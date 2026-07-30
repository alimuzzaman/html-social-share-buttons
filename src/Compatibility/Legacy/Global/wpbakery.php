<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;

function zm_sh_integrateWithVC() {
	LegacyRuntime::wpBakery()->integrate();
}

LegacyRuntime::wpBakery()->register();
