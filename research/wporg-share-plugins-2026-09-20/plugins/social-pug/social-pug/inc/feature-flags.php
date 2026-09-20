<?php
/**
 * The feature flag manifest for Grow Social.
 *
 * Do not put this in the autoloader.
 * Start all feature flag slugs with the short plugin name.
 *
 * @see \Mediavine\Grow\register_flags
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

return [ /*
	'grow_test_flag' => [
		'name'        => 'Test Flag', // User-friendly reference
		'description' => 'A test feature flag to show how the system works, sorta.',
		'visible'     => false, // Does not appear in Dashboard without entering key
		'on_enable'   => '\Mediavine\Create\enable_test_flag', // Callback
		'on_disable'  => '\Mediavine\Create\disable_test_flag', // Callback
	], */
];
