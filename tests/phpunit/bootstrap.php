<?php

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $value ) {
		return trim( (string) $value );
	}
}

require_once dirname( __DIR__, 2 ) . '/share-templates.php';
require_once dirname( __DIR__, 2 ) . '/settings_page.php';
