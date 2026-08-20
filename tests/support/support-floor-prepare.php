<?php

/* First request: persist representative legacy data before the assertion boot. */
$postId = wp_insert_post(
	array(
		'post_title'   => 'HSSB support-floor fixture',
		'post_content' => 'Support-floor content',
		'post_status'  => 'publish',
		'post_type'    => 'post',
	)
);
if ( ! $postId || is_wp_error( $postId ) ) {
	throw new RuntimeException( 'Could not create the support-floor post.' );
}

update_post_meta( $postId, '_zm_sh_disable_share', 'on' );
update_option( 'hssb_support_floor_post_id', $postId );
update_option(
	'zm_shbt_fld',
	array(
		'title'                  => 'Support-floor title',
		'iconset'                => 'default',
		'show_in'                => array( 'show_after_post' => '1' ),
		'show_after_post'        => 'square',
		'icons'                  => array( 'facebook' => '1', 'x' => '1' ),
		'profile_links'          => array( 'facebook' => 'https://www.facebook.com/example' ),
		'support_floor_extension' => 'retained',
	)
);
