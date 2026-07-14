#!/usr/bin/env php
<?php

$frontend_files = array(
	'actions.php',
	'filters.php',
	'iconsets.php',
	'interfaces.php',
);

$changed = array();
foreach ( $frontend_files as $file ) {
	$diff = shell_exec( 'git diff -- ' . escapeshellarg( $file ) );
	if ( ! is_string( $diff ) || '' === $diff ) {
		continue;
	}

	foreach ( preg_split( '/\R/', $diff ) as $line ) {
		if ( preg_match( '/^(diff --git|index |--- |\+\+\+ |@@ )/', $line ) ) {
			continue;
		}
		if ( preg_match( '/^\+\s*\/\/ phpcs:ignore /', $line ) ) {
			continue;
		}
		if ( preg_match( '/^[+-]/', $line ) ) {
			$changed[] = $file;
			break;
		}
	}
}

if ( ! empty( $changed ) ) {
	echo "Frontend drift surface changed:\n";
	foreach ( $changed as $file ) {
		echo " - {$file}\n";
	}
	exit( 1 );
}

printf( "Frontend drift surface unchanged: %d files checked.\n", count( $frontend_files ) );
