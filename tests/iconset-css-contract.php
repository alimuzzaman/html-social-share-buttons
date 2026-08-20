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

$responsive_rule = '@media (max-width:600px){.zmshbt.left,.zmshbt.right{position:static!important;display:flex;flex-wrap:wrap;justify-content:center}.zmshbt.left a,.zmshbt.right a{margin:5px!important}}';
$responsive_stylesheets = array(
	'iconset/default/style.css',
	'iconset/flat/style.css',
	'iconset/long_shadow/style.css',
	'iconset/prajin/style.css',
	'assets/iconsets/bootstrap-solid/style.css',
	'assets/iconsets/tabler-outline/style.css',
);

foreach ( $responsive_stylesheets as $relative_path ) {
	$css = file_get_contents( __DIR__ . '/../' . $relative_path );
	if ( false === $css || false === strpos( $css, $responsive_rule ) ) {
		fwrite( STDERR, sprintf( "Missing mobile placement rule: %s\n", $relative_path ) );
		exit( 1 );
	}
	if ( false === strpos( $css, '.zmshbt-profile-separator' ) ) {
		fwrite( STDERR, sprintf( "Missing mixed share/profile separator rule: %s\n", $relative_path ) );
		exit( 1 );
	}
}

echo "All icon-set styles prevent fixed mobile rail collisions.\n";
echo "All icon-set styles distinguish mixed share and profile links.\n";
