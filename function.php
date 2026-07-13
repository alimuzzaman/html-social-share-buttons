<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function zm_sh_get_excluded_post_identifiers( $excludes ) {
	$identifiers = array_map( 'trim', explode( ',', (string) $excludes ) );

	return array_values( array_filter( $identifiers, 'strlen' ) );
}

function zm_sh_post_is_excluded( $post, $excludes ) {
	if ( ! is_object( $post ) || empty( $post->ID ) ) {
		return false;
	}

	$candidates = array_filter(
		array(
			(string) $post->ID,
			isset( $post->post_name ) ? trim( (string) $post->post_name ) : '',
			isset( $post->post_title ) ? trim( (string) $post->post_title ) : '',
		),
		'strlen'
	);

	foreach ( zm_sh_get_excluded_post_identifiers( $excludes ) as $identifier ) {
		foreach ( $candidates as $candidate ) {
			if ( 0 === strcasecmp( $identifier, $candidate ) ) {
				return true;
			}
		}
	}

	return false;
}

function zm_sh_curentPageURL() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	return esc_url_raw( home_url( $request_uri ) );
}
