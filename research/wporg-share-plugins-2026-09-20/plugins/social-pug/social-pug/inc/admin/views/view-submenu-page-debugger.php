<?php

	/**
	 * Get all system versions
	 *
	 */
	global $wp_version;

	$php_version  = phpversion();
	$curl_version = ( function_exists( 'curl_version' ) ? curl_version() : 'Not installed' );
	$curl_version = ( is_array( $curl_version ) ? $curl_version['version'] : $curl_version );
	$dpsp_version = DPSP_VERSION;

	/**
	 * Get all plugins and active plugins
	 *
	 */
	$plugins        = get_plugins();
	$active_plugins = [];

	foreach ( $plugins as $key => $plugin ) {
		if ( is_plugin_active( $key ) ) {
			$active_plugins[ $key ]['Name'] = $plugin['Name'];
		}
	}

	/**
	 * Get all Hubbub cron jobs
	 *
	 */
	$cron_jobs = [];

	if ( false !== wp_get_schedule( 'dpsp_cron_get_posts_networks_share_count' ) ) {
		$cron_jobs[] = 'dpsp_cron_get_posts_networks_share_count';
	}

	if ( false !== wp_get_schedule( 'dpsp_cron_get_posts_networks_share_count', [ '2x_hourly' ] ) ) {
		$cron_jobs[] = 'dpsp_cron_get_posts_networks_share_count - 2x_hourly';
	}

	if ( false !== wp_get_schedule( 'dpsp_cron_get_posts_networks_share_count', [ 'daily' ] ) ) {
		$cron_jobs[] = 'dpsp_cron_get_posts_networks_share_count - daily';
	}

	if ( false !== wp_get_schedule( 'dpsp_cron_get_posts_networks_share_count', [ 'weekly' ] ) ) {
		$cron_jobs[] = 'dpsp_cron_get_posts_networks_share_count - weekly';
	}

?>

<div class="dpsp-page-wrapper dpsp-page-content wrap">

	<h1 class="dpsp-page-title"><?php esc_html_e( 'System Status', 'social-pug' ); ?></h1>

<textarea readonly style="width: 100%; min-height: 600px;">
System Versions:
---------------------------------------------------------------------------------------------------&#13;
PHP Version: <?php echo esc_html( $php_version ); ?> &#13;
cURL Version: <?php echo esc_html( $curl_version ); ?> &#13;
WP Version: <?php echo esc_html( $wp_version ); ?> &#13;
Hubbub Version: <?php echo esc_html( $dpsp_version ); ?> &#13;
&#13;
All Plugins:
---------------------------------------------------------------------------------------------------&#13;
<?php
	if ( ! empty( $plugins ) ) {
		foreach ( $plugins as $key => $plugin ) {
			echo esc_attr( $plugin['Name'] ) . ' (' . esc_attr( $key ) . ')&#13;';
		}
	} else {
		echo 'None&#13;';
	}
?>
&#13;
Active Plugins:
---------------------------------------------------------------------------------------------------&#13;
<?php
	if ( ! empty( $active_plugins ) ) {
		foreach ( $active_plugins as $key => $plugin ) {
			echo esc_attr( $plugin['Name'] ) . ' (' . esc_attr( $key ) . ')&#13;';
		}
	} else {
		echo 'None&#13;';
	}

?>
&#13;
Hubbub Cron Jobs:
---------------------------------------------------------------------------------------------------&#13;
<?php
	if ( ! empty( $cron_jobs ) ) {
		foreach ( $cron_jobs as $cron_job ) {
			echo esc_attr( $cron_job ) . '&#13;';
		}
	} else {
		echo 'None&#13;';
	}
?>
&#13;
Serial response:
---------------------------------------------------------------------------------------------------&#13;
<?php echo esc_attr( $serial_response ); ?>
&#13;&#13;
Saved serial status:
---------------------------------------------------------------------------------------------------&#13;
<?php echo esc_attr( $serial_status_db ); ?>
&#13;&#13;
Request serial status:
---------------------------------------------------------------------------------------------------&#13;
<?php echo esc_attr( $serial_status_request ); ?>
</textarea>

</div>
