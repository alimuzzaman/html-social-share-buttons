<?php

// Run in the disposable candidate installation, after the compatibility probe.
$pluginRoot = realpath( WP_PLUGIN_DIR . '/html-social-share-buttons' ) . DIRECTORY_SEPARATOR;
$previousReporting = error_reporting( E_ALL );
set_error_handler(
	static function ( $severity, $message, $file, $line ) use ( $pluginRoot ) {
		$frames = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		$frames[] = array( 'file' => $file );
		foreach ( $frames as $frame ) {
			$path = isset( $frame['file'] ) ? (string) $frame['file'] : '';
			if ( 0 === strpos( $path, $pluginRoot ) && false === strpos( $path, $pluginRoot . 'vendor/' ) ) {
				throw new ErrorException( $message, 0, $severity, $file, $line );
			}
		}
		return false;
	}
);
try {
	$plugin   = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();
	$renderer = $plugin->renderer();
	foreach ( array( array( 'native' ), array( 'native', 'copy' ), array( 'facebook', 'pinterest', 'mail' ) ) as $ids ) {
		$outcome = $renderer->render(
			array( 'icons' => $ids, 'iconset' => 'bootstrap-solid', 'url' => 'https://example.test/runtime/?a=1&b=2' ),
			0,
			new \Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext( 'https://example.test/', 'PHP runtime title' )
		);
		if ( '' === $outcome->html() ) {
			throw new RuntimeException( 'Runtime diagnostic probe rendered no output.' );
		}
	}
	$pageOutcome = $renderer->render(
		array(
			'class'                => 'left',
			'placement'            => \Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement::FLOATING_LEFT,
			'icons'                => array( 'facebook', 'pinterest', 'mail' ),
			'iconset'              => 'bootstrap-solid',
			'browser_url_eligible' => true,
			'browser_url_source'   => 'automatic_page',
		),
		0
	);
	if ( '' === $pageOutcome->html() ) {
		throw new RuntimeException( 'Runtime diagnostic page-level probe rendered no output.' );
	}

	// Admin payload builder & sanitization paths under E_ALL.
	$admin = $plugin->admin();
	ob_start();
	$admin->enqueueAssets( 'settings_page_zm_shbt_opt' );
	ob_end_clean();
	$sanitized = $admin->sanitizeAdmin(
		array(
			'title'        => 'Diagnostics title',
			'iconset'      => 'flat',
			'iconset_type' => 'circle',
			'show_in'      => array( 'show_left' => '1', 'show_after_post' => '1' ),
			'icons'        => array( 'facebook' => '1', 'x' => '1', 'mail' => '1' ),
			'excludes'     => '1, sample-slug',
		)
	);
	if ( ! is_array( $sanitized ) || 'circle' !== $sanitized['iconset_type'] ) {
		throw new RuntimeException( 'Runtime diagnostic admin sanitization failed.' );
	}

	// Excluded-content lookup (regex ID handling & search).
	$lookup   = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ExcludedContentLookup(
		new \Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy()
	);
	$resolved = $lookup->resolve( '1, missing-slug' );
	if ( ! is_array( $resolved ) || ! array_key_exists( 'items', $resolved ) ) {
		throw new RuntimeException( 'Runtime diagnostic ExcludedContentLookup::resolve failed.' );
	}
	$lookup->search( 'Hello' );

	// Shortcodes, blocks, widget, automatic placement, metabox, and legacy facades.
	$frontend = $plugin->frontend();
	$frontend->footerWithOptions(
		array(
			'iconset' => 'default',
			'icons'   => array( 'facebook' => '1' ),
			'show_in' => array( 'show_left' => '1' ),
		),
		$frontend->createAssetCollector()
	);

	if ( false === strpos( do_shortcode( '[zm_sh_btn icons="facebook,copy,native"]' ), 'zmshbt' ) ) {
		throw new RuntimeException( 'Runtime diagnostic shortcode check failed.' );
	}
	if ( false === strpos( do_blocks( '<!-- wp:html-social-share/social-share {"icons":["facebook"]} /-->' ), 'zmshbt' ) ) {
		throw new RuntimeException( 'Runtime diagnostic block check failed.' );
	}

	$widget = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget\ShareWidget(
		$renderer,
		$plugin->settings(),
		$plugin->iconSets(),
		$plugin->networks(),
		$frontend->assets(),
		$plugin->config()
	);
	$widget->update(
		array( 'title' => 'Diag widget', 'icons' => array( 'facebook' => '1' ), 'iconset' => 'default', 'iconset_type' => 'square' ),
		array()
	);

	$diagPostId = wp_insert_post(
		array( 'post_title' => 'Diag Post', 'post_content' => 'Body', 'post_status' => 'publish', 'post_type' => 'post' )
	);
	if ( is_int( $diagPostId ) && $diagPostId > 0 ) {
		$prevQuery     = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		$prevMainQuery = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;
		$prevPost      = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		try {
			$diagPost = get_post( $diagPostId );
			ob_start();
			$plugin->metabox()->render( $diagPost );
			ob_end_clean();

			$GLOBALS['wp_query']             = new WP_Query( array( 'p' => $diagPostId ) );
			$GLOBALS['wp_the_query']         = $GLOBALS['wp_query'];
			$GLOBALS['wp_query']->in_the_loop = true;
			$GLOBALS['post']                 = $diagPost;
			$frontend->detectExclusion();
			$frontend->filterContent( '<p>Content check</p>' );
			$filteredDiag = $frontend->filterContentWithOptions(
				'<p>Legacy filter check</p>',
				array(
					'iconset' => 'default',
					'icons'   => array( 'facebook' => '1' ),
					'show_in' => array( 'show_after_post' => '1' ),
				)
			);
			if ( false === strpos( $filteredDiag, 'zmshbt' ) ) {
				throw new RuntimeException( 'Runtime diagnostic filterContentWithOptions produced no buttons.' );
			}
		} finally {
			$GLOBALS['wp_query']     = $prevQuery;
			$GLOBALS['wp_the_query'] = $prevMainQuery;
			$GLOBALS['post']         = $prevPost;
			$frontend->detectExclusion();
			wp_delete_post( $diagPostId, true );
		}
	}

	$legacyShare = new zm_social_share();
	$legacyForm  = new zm_form();
	ob_start();
	$legacyForm->select_iconset( 'iconset', 'Icon set' );
	ob_end_clean();
	$legacySchema = zm_sh_get_schemas();
	if ( empty( $legacyShare->options ) || ! is_array( $legacySchema ) ) {
		throw new RuntimeException( 'Runtime diagnostic legacy API check failed.' );
	}
	zm_sh_curentPageURL();
} finally {
	restore_error_handler();
	error_reporting( $previousReporting );
}
echo 'Plugin-owned E_ALL runtime diagnostic probe passed on PHP ' . PHP_VERSION . ".\n";
