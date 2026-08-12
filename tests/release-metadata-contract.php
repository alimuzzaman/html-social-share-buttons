<?php

$root = dirname( __DIR__ );
$failures = array();

function hssb_contract_match( $pattern, $contents, $label, &$failures ) {
	if ( preg_match( $pattern, $contents, $matches ) ) {
		return $matches[1];
	}
	$failures[] = 'Could not read ' . $label . '.';

	return null;
}

$plugin = file_get_contents( $root . '/html-social-share.php' );
$readme = file_get_contents( $root . '/readme.txt' );
$config = file_get_contents( $root . '/src/Bootstrap/PluginConfig.php' );
$shareBlock = json_decode( file_get_contents( $root . '/block.json' ), true );
$linksBlock = json_decode( file_get_contents( $root . '/blocks/social-links/block.json' ), true );

$versions = array(
	'plugin header' => hssb_contract_match( '/^Version:\s*(\S+)\s*$/m', $plugin, 'plugin header version', $failures ),
	'stable tag'    => hssb_contract_match( '/^Stable tag:\s*(\S+)\s*$/mi', $readme, 'readme stable tag', $failures ),
	'config'        => hssb_contract_match( "/const VERSION = '([^']+)'/", $config, 'PluginConfig version', $failures ),
	'share block'   => isset( $shareBlock['version'] ) ? (string) $shareBlock['version'] : null,
	'links block'   => isset( $linksBlock['version'] ) ? (string) $linksBlock['version'] : null,
);

foreach ( $versions as $label => $version ) {
	if ( $versions['plugin header'] !== $version ) {
		$failures[] = $label . ' version does not match the plugin header.';
	}
}

$requirements = array(
	'wordpress_plugin' => hssb_contract_match( '/^Requires at least:\s*(\S+)\s*$/m', $plugin, 'plugin WordPress requirement', $failures ),
	'wordpress_readme' => hssb_contract_match( '/^Requires at least:\s*(\S+)\s*$/mi', $readme, 'readme WordPress requirement', $failures ),
	'php_plugin'       => hssb_contract_match( '/^Requires PHP:\s*(\S+)\s*$/m', $plugin, 'plugin PHP requirement', $failures ),
	'php_readme'       => hssb_contract_match( '/^Requires PHP:\s*(\S+)\s*$/mi', $readme, 'readme PHP requirement', $failures ),
);

if ( $requirements['wordpress_plugin'] !== $requirements['wordpress_readme'] ) {
	$failures[] = 'WordPress requirements do not match.';
}
if ( $requirements['php_plugin'] !== $requirements['php_readme'] ) {
	$failures[] = 'PHP requirements do not match.';
}
if ( in_array( 'Readme.txt', scandir( $root ), true ) ) {
	$failures[] = 'Distribution readme must use the WordPress-standard lowercase readme.txt name.';
}

if ( $failures ) {
	fwrite( STDERR, "Release metadata contract failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo 'Release metadata contract passed at version ' . $versions['plugin header'] . ".\n";
