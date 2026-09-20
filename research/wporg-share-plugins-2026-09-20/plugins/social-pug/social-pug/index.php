<?php
/**
 * Plugin Name:         Hubbub Lite
 * Plugin URI:          https://morehubbub.com/
 * Description:         Hubbub is a suite of website growth tools to help you add customizable social sharing & follow buttons, update missing social media metadata easily, and increase email newsletter signups.
 * Version:             1.36.3.1

 * Requires at least:   5.3
 * Requires PHP:        7.2.24
 * Author:              NerdPress
 * Text Domain:         social-pug
 * Author URI:          https://morehubbub.com/
 * License:             GPL2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Oops, this file cannot be loaded on its own.' );
}

require_once __DIR__ . '/inc/functions-requirements.php';

if ( ! mv_grow_is_compatible() ) {
	add_action( 'admin_notices', 'mv_grow_incompatible_notice' );
	add_action( 'admin_head', 'mv_grow_throw_warnings' );
	return false;
}

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/constants.php';

/**
 * Returns plugin activation path. Here for backwards compatibility.
 *
 * @return string
 */
function mv_grow_get_activation_path() {
	return __FILE__;
}

if ( strpos( get_stylesheet(), '-trellis' ) ) {
	\Social_Pug::get_instance();
} else {
	add_action('init', [ 'Social_Pug', 'get_instance' ], 0 );
}
