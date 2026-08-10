#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

$source = (string) file_get_contents( __DIR__ . '/../html-social-share.php' );
$required = array(
	"if ( ! is_readable( \$hssb_autoload ) )" => 'missing dependency branch',
	"register_activation_hook( __FILE__, 'hssb_fail_missing_autoloader_activation' )" => 'activation failure hook',
	"add_action( 'admin_notices', 'hssb_missing_autoloader_admin_notice' )" => 'single-site administrator notice',
	"add_action( 'network_admin_notices', 'hssb_missing_autoloader_admin_notice' )" => 'network administrator notice',
	'Install a packaged release, or run Composer' => 'actionable remediation',
);

foreach ( $required as $needle => $label ) {
	if ( false === strpos( $source, $needle ) ) {
		echo 'Missing-autoloader contract failed: ' . esc_html( $label ) . ".\n";
		exit( 1 );
	}
}

$autoloadBranch = substr(
	$source,
	strpos( $source, 'if ( ! is_readable( $hssb_autoload ) )' ),
	strpos( $source, 'require_once $hssb_autoload;' ) - strpos( $source, 'if ( ! is_readable( $hssb_autoload ) )' )
);
if ( false === strpos( $autoloadBranch, 'return;' ) ) {
	echo "Missing-autoloader contract failed: bootstrap does not stop before runtime boot.\n";
	exit( 1 );
}

echo "Missing-autoloader contract passed.\n";
