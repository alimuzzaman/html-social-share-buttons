#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$paths = array( $root . '/html-social-share.php', $root . '/src' );
$failures = array();
$typeTokens = array( T_STRING, T_ARRAY, T_CALLABLE );
foreach ( array( 'T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED', 'T_NAME_RELATIVE' ) as $tokenName ) {
	if ( defined( $tokenName ) ) {
		$typeTokens[] = constant( $tokenName );
	}
}

function hssb_previous_meaningful_token( array $tokens, $index ) {
	for ( $position = $index - 1; $position >= 0; $position-- ) {
		if ( ! is_array( $tokens[ $position ] ) || T_WHITESPACE !== $tokens[ $position ][0] ) {
			return $tokens[ $position ];
		}
	}

	return null;
}

function hssb_next_meaningful_token( array $tokens, $index ) {
	for ( $position = $index + 1, $count = count( $tokens ); $position < $count; $position++ ) {
		if ( ! is_array( $tokens[ $position ] ) || T_WHITESPACE !== $tokens[ $position ][0] ) {
			return array( $position, $tokens[ $position ] );
		}
	}

	return array( $index, null );
}

foreach ( $paths as $path ) {
	$files = array();
	if ( is_file( $path ) ) {
		$files[] = new SplFileInfo( $path );
	} else {
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS )
		);
	}

	foreach ( $files as $file ) {
		if ( 'php' !== strtolower( $file->getExtension() ) ) {
			continue;
		}
		$contents = (string) file_get_contents( $file->getPathname() );
		$tokens = token_get_all( $contents );
		foreach ( $tokens as $index => $token ) {
			if ( ! is_array( $token ) || T_VARIABLE !== $token[0] ) {
				continue;
			}
			$previous = hssb_previous_meaningful_token( $tokens, $index );
			if ( ! is_array( $previous ) || ! in_array( $previous[0], $typeTokens, true ) ) {
				continue;
			}
			list( $equalsIndex, $equals ) = hssb_next_meaningful_token( $tokens, $index );
			list( , $default ) = hssb_next_meaningful_token( $tokens, $equalsIndex );
			if ( '=' !== $equals || ! is_array( $default ) || T_STRING !== $default[0] || 'null' !== strtolower( $default[1] ) ) {
				continue;
			}
			$failures[] = str_replace( $root . '/', '', $file->getPathname() ) . ':' . $token[2] .
				' uses an implicitly nullable typed parameter';
		}
	}
}

if ( $failures ) {
	fwrite( STDERR, "PHP compatibility contract failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "PHP compatibility contract passed: no implicitly nullable typed parameters.\n";
