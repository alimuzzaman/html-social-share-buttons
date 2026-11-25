<?php

/**
 * Full WordPress + plugin runtime exercise meant to be invoked directly under:
 *   php -d disable_functions=<shared_host> tests/support/disabled-functions-wp-runtime.php --wp-root=...
 *
 * Bypasses WP-CLI (which requires proc_open/posix/ini_set) so WordPress and the
 * plugin boot and execute under real host disable_functions constraints.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 1 );
}

require_once __DIR__ . '/disabled-functions-profiles.php';

foreach ( array( 'exec', 'shell_exec', 'proc_open', 'curl_exec', 'ini_alter', 'mail', 'set_time_limit', 'readlink', 'chmod' ) as $mustBeDisabled ) {
	if ( function_exists( $mustBeDisabled ) ) {
		fwrite( STDERR, "Expected {$mustBeDisabled}() to be disabled when running disabled-functions-wp-runtime.php.\n" );
		exit( 1 );
	}
}

$wpRoot = getenv( 'WP_ROOT' ) ? getenv( 'WP_ROOT' ) : '/var/www/html';
if ( isset( $argv ) && is_array( $argv ) ) {
	foreach ( $argv as $arg ) {
		if ( 0 === strpos( $arg, '--wp-root=' ) ) {
			$wpRoot = substr( $arg, 10 );
		}
	}
}
$wpRoot = rtrim( (string) $wpRoot, '/\\' );
if ( ! is_file( $wpRoot . '/wp-load.php' ) ) {
	fwrite( STDERR, "Could not locate wp-load.php at {$wpRoot}.\n" );
	exit( 1 );
}

if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
	$_SERVER['HTTP_HOST'] = 'localhost';
}
if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
	$_SERVER['REQUEST_URI'] = '/';
}
if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
	$_SERVER['SERVER_NAME'] = 'localhost';
}

require_once $wpRoot . '/wp-load.php';

$candidateRoot = ( defined( 'WP_PLUGIN_DIR' ) && is_dir( WP_PLUGIN_DIR . '/html-social-share-buttons' ) )
	? WP_PLUGIN_DIR . '/html-social-share-buttons'
	: dirname( __DIR__, 2 );
$pluginRoot = realpath( $candidateRoot );
$pluginRoot = false !== $pluginRoot ? $pluginRoot . DIRECTORY_SEPARATOR : rtrim( $candidateRoot, '/\\' ) . DIRECTORY_SEPARATOR;

$previousReporting = error_reporting( E_ALL );
set_error_handler(
	static function ( $severity, $message, $file, $line ) use ( $pluginRoot ) {
		$frames   = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
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

$failures = array();
$require  = static function ( $condition, $message ) use ( &$failures ) {
	if ( ! $condition ) {
		$failures[] = $message;
	}
};

try {
	$plugin = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();
	$require( is_object( $plugin ), 'LegacyApi::plugin() unavailable under disable_functions' );

	// 1. Admin payload builder + settings sanitization + asset enqueuing.
	$admin = $plugin->admin();
	ob_start();
	$admin->enqueueAssets( 'settings_page_zm_shbt_opt' );
	ob_end_clean();
	$require(
		isset( wp_scripts()->registered['zm_sh_admin_scripts']->extra['data'] ),
		'SettingsPayloadBuilder did not localize admin settings under disable_functions'
	);

	$sanitized = $admin->sanitizeAdmin(
		array(
			'title'        => 'Disabled-functions probe',
			'iconset'      => 'flat',
			'iconset_type' => 'circle',
			'show_in'      => array( 'show_left' => '1' ),
			'icons'        => array( 'facebook' => '1', 'x' => '1' ),
		)
	);
	$require(
		is_array( $sanitized ) && 'circle' === $sanitized['iconset_type'],
		'Admin sanitization failed under disable_functions'
	);

	// 2. Excluded-content lookup (regex ID check instead of ctype_digit).
	$lookup   = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ExcludedContentLookup(
		new \Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy()
	);
	$resolved = $lookup->resolve( '1, non-existent-slug' );
	$require( is_array( $resolved ) && array_key_exists( 'items', $resolved ), 'ExcludedContentLookup::resolve failed' );

	// 3. Frontend rendering, shortcodes, blocks, widget, metabox, legacy facades.
	$shortcodeOut = do_shortcode( '[zm_sh_btn icons="facebook,copy,native"]' );
	$require( false !== strpos( $shortcodeOut, 'zmshbt' ), 'Shortcode rendering failed under disable_functions' );

	$blockOut = $plugin->block()->renderShareBlock( array( 'icons' => array( 'facebook', 'x' ) ) );
	$require( false !== strpos( $blockOut, 'zmshbt' ), 'Block rendering failed under disable_functions' );

	$postId = wp_insert_post(
		array(
			'post_title'   => 'Disabled Functions Probe',
			'post_content' => 'Probe body',
			'post_status'  => 'publish',
			'post_type'    => 'post',
		)
	);
	if ( is_int( $postId ) && $postId > 0 ) {
		$prevQuery     = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		$prevMainQuery = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;
		$prevPost      = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		try {
			$post = get_post( $postId );
			ob_start();
			$plugin->metabox()->render( $post );
			$metaboxOut = (string) ob_get_clean();
			$require( false !== strpos( $metaboxOut, 'zm_sh_mtbox' ), 'Metabox render failed under disable_functions' );

			$GLOBALS['wp_query']     = new WP_Query( array( 'p' => $postId ) );
			$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
			$GLOBALS['post']         = $post;
			$filtered = $plugin->frontend()->filterContentWithOptions(
				'<p>Hello</p>',
				array(
					'iconset' => 'default',
					'icons'   => array( 'facebook' => '1' ),
					'show_in' => array( 'show_after_post' => '1' ),
				)
			);
			$require( false !== strpos( $filtered, 'zmshbt' ), 'Frontend filterContentWithOptions failed under disable_functions' );
		} finally {
			$GLOBALS['wp_query']     = $prevQuery;
			$GLOBALS['wp_the_query'] = $prevMainQuery;
			$GLOBALS['post']         = $prevPost;
			wp_delete_post( $postId, true );
		}
	}

	$legacyObj = new zm_social_share();
	$require( is_array( $legacyObj->options ), 'Legacy zm_social_share failed under disable_functions' );

	$footerHtml = $plugin->frontend()->footerWithOptions(
		array(
			'iconset' => 'default',
			'icons'   => array( 'facebook' => '1' ),
			'show_in' => array( 'show_left' => '1' ),
		),
		$plugin->frontend()->createAssetCollector()
	);
	$require( false !== strpos( $footerHtml, 'zmshbt' ), 'Frontend footerWithOptions failed under disable_functions' );
} finally {
	restore_error_handler();
	error_reporting( $previousReporting );
}

if ( $failures ) {
	fwrite( STDERR, "WordPress disabled-functions runtime failed:\n - " . implode( "\n - ", $failures ) . "\n" );
	exit( 1 );
}

echo 'WordPress disabled-functions runtime passed on PHP ' . PHP_VERSION . " / WP " . get_bloginfo( 'version' ) . ".\n";
