<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration\WidgetAdapter;

function zm_sh_register_widgets() {
	WidgetAdapter::registerWidget();
}

class zm_html_share_widget extends WidgetAdapter {
}

add_action( 'widgets_init', 'zm_sh_register_widgets' );
