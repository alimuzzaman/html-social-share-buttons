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

$horizontal_wrappers = array( 'in_widget', 'in_shortcode', 'in_block', 'in_elementor', 'in_php_function' );
$class_prefixes = array(
	'iconset/default/style.css'               => array( '.zmshbt.default.' ),
	'iconset/flat/style.css'                  => array( '.zmshbt.flat.' ),
	'iconset/long_shadow/style.css'           => array( '.zmshbt.long-shadows.' ),
	'iconset/prajin/style.css'                => array( '.zmshbt.prajin.', '.zmshbt.Prajin.' ),
	'assets/iconsets/bootstrap-solid/style.css' => array( '.zmshbt.bootstrap-solid.' ),
	'assets/iconsets/tabler-outline/style.css'  => array( '.zmshbt.tabler-outline.' ),
);
foreach ( $class_prefixes as $relative_path => $prefixes ) {
	$css = file_get_contents( __DIR__ . '/../' . $relative_path );
	foreach ( $prefixes as $prefix ) {
		foreach ( $horizontal_wrappers as $wrapper ) {
			$selector = $prefix . $wrapper . ' a';
			if ( false === $css || false === strpos( $css, $selector ) ) {
				fwrite( STDERR, sprintf( "Missing horizontal wrapper selector %s in %s\n", $selector, $relative_path ) );
				exit( 1 );
			}
		}
	}
}
echo "All icon-set styles enforce horizontal layout across every inline wrapper.\n";

$native_css = file_get_contents( __DIR__ . '/../assets/frontend/native-share.css' );
if ( false === $native_css || false === strpos( $native_css, '.zmshbt [hidden]{display:none!important}' ) ) {
	fwrite( STDERR, "Missing [hidden] specificity rule in assets/frontend/native-share.css\n" );
	exit( 1 );
}
echo "Native share stylesheet preserves [hidden] display:none specificity.\n";
