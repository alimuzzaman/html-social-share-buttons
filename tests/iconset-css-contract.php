<?php

/**
 * The canonical icon-set ID and rendered public class are lowercase `prajin`.
 * Keep the historical uppercase selectors too, because older custom markup may
 * carry them, but do not let the canonical frontend render without dimensions.
 */
$stylesheet = file_get_contents( __DIR__ . '/../iconset/prajin/style.css' );

if ( false === $stylesheet ) {
	fwrite( STDERR, "Could not read the Prajin stylesheet.\n" );
	exit( 1 );
}

foreach ( array(
	'.zmshbt.prajin a',
	'.zmshbt.prajin.in_shortcode a',
	'.zmshbt.prajin.circle a',
) as $selector ) {
	if ( false === strpos( $stylesheet, $selector ) ) {
		fwrite( STDERR, sprintf( "Missing canonical Prajin selector: %s\n", $selector ) );
		exit( 1 );
	}
}

echo "Prajin canonical stylesheet selectors are present.\n";
