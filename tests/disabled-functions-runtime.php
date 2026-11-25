#!/usr/bin/env php
<?php

require_once __DIR__ . '/support/disabled-functions-profiles.php';
require_once __DIR__ . '/support/standalone-runtime-smoke.php';

$isWorker = in_array( '--worker', isset( $argv ) ? $argv : array(), true );

if ( ! $isWorker ) {
	$disabled = implode( ',', hssb_disabled_functions_hardened_host() );
	$command  = escapeshellarg( PHP_BINARY ) .
		' -d ' . escapeshellarg( 'disable_functions=' . $disabled ) .
		' -d error_reporting=' . E_ALL .
		' -d display_errors=1 ' .
		escapeshellarg( __FILE__ ) .
		' --worker 2>&1';

	$output = array();
	$status = 1;
	exec( $command, $output, $status );
	$text = implode( "\n", $output );
	if ( '' !== $text ) {
		echo $text . "\n";
	}
	if ( 0 !== $status || false === strpos( $text, 'Disabled-functions standalone runtime passed' ) ) {
		fwrite( STDERR, "Disabled-functions standalone runtime failed (exit {$status}).\n" );
		exit( 1 );
	}
	exit( 0 );
}

foreach ( array( 'exec', 'shell_exec', 'proc_open', 'curl_exec', 'scandir', 'error_log', 'ini_get', 'lstat', 'filemtime', 'ctype_digit', 'mb_substr', 'glob', 'mail' ) as $fn ) {
	if ( function_exists( $fn ) ) {
		fwrite( STDERR, "Expected {$fn}() to be disabled in worker runtime.\n" );
		exit( 1 );
	}
}

hssb_run_standalone_runtime_smoke( dirname( __DIR__ ) );

echo 'Disabled-functions standalone runtime passed on PHP ' . PHP_VERSION . ' (' . count( hssb_disabled_functions_hardened_host() ) . " functions disabled).\n";
