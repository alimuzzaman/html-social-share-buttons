<?php
/** Router for the disposable CI WordPress HTTP server. */
$root = getenv( 'WP_ROOT' );
$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$file = realpath( $root . '/' . ltrim( rawurldecode( $path ), '/' ) );
if ( false !== $file && 0 === strpos( $file, realpath( $root ) . DIRECTORY_SEPARATOR ) && is_file( $file ) ) {
	return false;
}
require $root . '/index.php';
