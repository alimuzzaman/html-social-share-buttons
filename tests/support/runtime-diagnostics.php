<?php

// Run in the disposable candidate installation, after the compatibility probe.
$pluginRoot = realpath( WP_PLUGIN_DIR . '/html-social-share-buttons' ) . DIRECTORY_SEPARATOR;
$previousReporting = error_reporting( E_ALL );
set_error_handler(
	static function ( $severity, $message, $file, $line ) use ( $pluginRoot ) {
		$frames = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		$frames[] = array( 'file' => $file );
		foreach ( $frames as $frame ) {
			$path = isset( $frame['file'] ) ? (string) $frame['file'] : '';
			if ( 0 === strpos( $path, $pluginRoot ) && false === strpos( $path, $pluginRoot . 'vendor/' ) ) {
				throw new ErrorException( $message, 0, $severity, $file, $line );
			}
		}
		return false;
	}
);
try {
	$renderer = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->renderer();
	foreach ( array( array( 'native' ), array( 'native', 'copy' ), array( 'facebook', 'pinterest' ) ) as $ids ) {
		$outcome = $renderer->render(
			array( 'icons' => $ids, 'iconset' => 'bootstrap-solid', 'url' => 'https://example.test/runtime/?a=1&b=2' ),
			0,
			new \Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext( 'https://example.test/', 'PHP runtime title' )
		);
		if ( '' === $outcome->html() ) {
			throw new RuntimeException( 'Runtime diagnostic probe rendered no output.' );
		}
	}
} finally {
	restore_error_handler();
	error_reporting( $previousReporting );
}
echo "Plugin-owned E_ALL runtime diagnostic probe passed on PHP " . PHP_VERSION . ".\n";
