<?php // phpcs:ignore
/**
 * Uninstall routine for Simple Social Buttons.
 *
 * Fired when the plugin is deleted from the Plugins screen. Removes all
 * plugin options and (on multisite) per-blog options, but only if the user
 * has enabled "Remove data on uninstall" in Advanced settings.
 *
 * @package SimpleSocialButtons
 * @author  WPBrigade
 * @since   5.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if not a real uninstall (WP core or SDK after-uninstall callback).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) && ! defined( 'SSB_DOING_UNINSTALL' ) ) {
	exit;
}

$ssb_advance_setting = get_option( 'ssb_advanced' );

if ( isset( $ssb_advance_setting['ssb_uninstall_data'] ) && '1' !== $ssb_advance_setting['ssb_uninstall_data'] ) { // phpcs:ignore
	return;
}

/**
 * Option names to delete on uninstall.
 *
 * Includes all SSB options used by the plugin and React admin.
 *
 * @var array<string>
 */
$ssb_unintstall_options = array(
	'ssb_networks',
	'ssb_themes',
	'ssb_positions',
	'ssb_inline',
	'ssb_sidebar',
	'ssb_flyin',
	'ssb_popup',
	'ssb_media',
	'ssb_advanced',
	'ssb_snapchat',
	'ssb_ngg_gallery',
	'ssb_click_to_tweet',
	'ssb_active_time',
	'ssb_follow_twitter_token',
	'ssb_review_dismiss',
	'widget_ssb_widget',
	'ssb_pr_version', // $this->plugin_prefix . 'version'.
	'ssb_share_queue',
);

/**
 * Post meta keys for internal share-count history (removed on uninstall).
 *
 * @var array<string>
 */
$ssb_uninstall_post_meta_keys = array(
	'ssb_share_counts',
	'ssb_share_counts_latest',
	'ssb_legacy_share_counts_migrated',
	'ssb_share_counts_latest_repaired',
);

/**
 * Remove plugin options, internal share queue cron, and internal count post meta for the current site.
 *
 * @param array<string> $options Option names to delete.
 * @param array<string> $post_meta_keys Post meta keys to delete.
 * @return void
 */
function ssb_uninstall_cleanup_current_site( array $options, array $post_meta_keys ) {
	foreach ( $options as $ssb_options ) {
		delete_option( $ssb_options );
	}

	wp_clear_scheduled_hook( 'ssb_flush_internal_share_queue' );

	if ( empty( $post_meta_keys ) ) {
		return;
	}

	global $wpdb;

	$placeholders = implode( ', ', array_fill( 0, count( $post_meta_keys ), '%s' ) );

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ($placeholders)", ...$post_meta_keys ) ); // phpcs:ignore
}

if ( ! is_multisite() ) {
	ssb_uninstall_cleanup_current_site( $ssb_unintstall_options, $ssb_uninstall_post_meta_keys );
} else {

	global $wpdb;
	$ssb_blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // phpcs:ignore

	foreach ( $ssb_blog_ids as $ssb_blog_id ) {

		switch_to_blog( $ssb_blog_id );

		ssb_uninstall_cleanup_current_site( $ssb_unintstall_options, $ssb_uninstall_post_meta_keys );

		restore_current_blog();
	}
}
