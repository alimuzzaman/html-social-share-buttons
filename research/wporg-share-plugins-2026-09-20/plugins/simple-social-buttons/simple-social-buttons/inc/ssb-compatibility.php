<?php
/**
 * =============== Twenty twenty====================== .
 *
 * @package SimpleSocialButtons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ssb_current_theme = wp_get_theme();

if ( 'twentytwenty' === $ssb_current_theme->template ) {
	add_filter( 'body_class', 'ssb_add_body_class' );
}

/**
 * Add special class.
 *
 * @param array $classes The body classes.
 * @return array The updated body classes.
 */
function ssb_add_body_class( $classes ) {

	$classes[] = 'ssb-twenty-twenty';

	return $classes;
}
