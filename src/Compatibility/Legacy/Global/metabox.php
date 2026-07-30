<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration\MetaboxAdapter;

function zm_sh_metabox_new() {
	new zm_sh_metabox();
}

class zm_sh_metabox extends MetaboxAdapter {
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'zm_sh_metabox_new' );
	add_action( 'load-post-new.php', 'zm_sh_metabox_new' );
}
