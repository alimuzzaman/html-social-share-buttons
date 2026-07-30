<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function zm_sh_get_excluded_post_identifiers( $excludes ) {
	try {
		return \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::excludedContent()
			->identifiers( $excludes );
	} catch ( \LogicException $error ) {
		return ( new \Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy() )
			->identifiers( $excludes );
	}
}

function zm_sh_post_is_excluded( $post, $excludes ) {
	if ( ! is_object( $post ) || empty( $post->ID ) ) {
		return false;
	}

	try {
		$policy =
			\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::excludedContent();
	} catch ( \LogicException $error ) {
		$policy = new \Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy();
	}

	return $policy->matches(
			$post->ID,
			isset( $post->post_name ) ? $post->post_name : '',
			isset( $post->post_title ) ? $post->post_title : '',
			$excludes
		);
}

function zm_sh_curentPageURL() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	return esc_url_raw( home_url( $request_uri ) );
}
