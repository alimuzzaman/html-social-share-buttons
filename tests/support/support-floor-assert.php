<?php

/* Second request: prove the stored compatibility surface after normal boot. */
$failures = array();
$require = function ( $condition, $message ) use ( &$failures ) {
	if ( ! $condition ) {
		$failures[] = $message;
	}
};

$pluginData = get_plugin_data( WP_PLUGIN_DIR . '/html-social-share-buttons/html-social-share.php' );
$require( isset( $pluginData['Version'] ) && '3.1.0' === $pluginData['Version'], 'active version is not 3.1.0' );
$require( function_exists( 'zm_sh_btn' ), 'legacy PHP facade is missing' );
$require( class_exists( 'zm_sh_iconset' ), 'legacy icon-set class is missing' );
$require( shortcode_exists( 'zm_sh_btn' ), 'legacy shortcode is missing' );
$require( shortcode_exists( 'html-social-share-buttons' ), 'descriptive shortcode is missing' );

$legacy = do_shortcode( '[zm_sh_btn icons="facebook,x"]' );
$descriptive = do_shortcode( '[html-social-share-buttons icons="facebook"]' );
$require( false !== strpos( $legacy, 'aria-label=' ), 'legacy shortcode lacks accessible share links' );
$require( false !== strpos( $legacy, 'https://www.facebook.com/example' ), 'stored profile link was not inherited' );
$require( false !== strpos( $descriptive, 'facebook' ), 'descriptive shortcode did not render' );

$blocks = WP_Block_Type_Registry::get_instance();
$require( $blocks->is_registered( 'html-social-share/social-share' ), 'Social Share block is not registered' );
$require( $blocks->is_registered( 'html-social-share/social-links' ), 'Social Links block is not registered' );
$shareBlock = do_blocks( '<!-- wp:html-social-share/social-share {"icons":["facebook"]} /-->' );
$linksBlock = do_blocks( '<!-- wp:html-social-share/social-links /-->' );
$require( false !== strpos( $shareBlock, 'zmshbt' ), 'stored Social Share block did not render' );
$require( false !== strpos( $linksBlock, 'zmshbt-profile-link' ), 'stored Social Links block did not render' );

$postId = (int) get_option( 'hssb_support_floor_post_id' );
$GLOBALS['post'] = get_post( $postId );
$GLOBALS['wp_query']->queried_object = $GLOBALS['post'];
$GLOBALS['wp_query']->queried_object_id = $postId;
setup_postdata( $GLOBALS['post'] );
\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->frontend()->detectExclusion();
$filteredContent = apply_filters( 'the_content', 'Support-floor body' );
$require( false === strpos( $filteredContent, 'zmshbt' ), 'disabled meta did not suppress automatic placement' );
wp_reset_postdata();

$stored = get_option( 'zm_shbt_fld' );
$require( is_array( $stored ) && array_key_exists( 'support_floor_extension', $stored ), 'legacy extension key was dropped' );

if ( $failures ) {
	throw new RuntimeException( implode( '; ', $failures ) );
}

echo "Support-floor functional smoke passed.\n";
