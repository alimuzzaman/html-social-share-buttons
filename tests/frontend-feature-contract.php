#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$registry = (string) file_get_contents( $root . '/src/Domain/Frontend/FrontendFeatureRegistry.php' );
$payload = (string) file_get_contents( $root . '/src/Presentation/Admin/SettingsPayloadBuilder.php' );
$components = (string) file_get_contents( $root . '/src/js/admin/settings/components.js' );
$renderer = (string) file_get_contents( $root . '/src/js/admin/settings/settings-renderer.js' );
$baseline = json_decode( (string) file_get_contents( $root . '/tests/fixtures/frontend-feature-baseline.json' ), true );
$expected = isset( $baseline['browser_url'] ) && is_array( $baseline['browser_url'] ) ? $baseline['browser_url'] : array();

foreach ( array( 'id', 'setting', 'requires_frontend_js' ) as $key ) {
	if ( false === strpos( $registry, "'$key'" ) ) {
		echo "Frontend feature contract failed: registry is missing $key.\n";
		exit( 1 );
	}
}
if ( 'browser_url' !== (string) ( $expected['id'] ?? '' ) || 'use_browser_url' !== (string) ( $expected['setting'] ?? '' ) || true !== (bool) ( $expected['requires_frontend_js'] ?? false ) ) {
	echo "Frontend feature contract failed: fixture does not describe browser_url.\n";
	exit( 1 );
}
if (
	false === strpos( $payload, 'FrontendFeatureRegistry::all()' ) ||
	false === strpos( $components, 'function FrontendJsBadge' ) ||
	false === strpos( $components, 'frontendJsFeature: feature' ) ||
	false === strpos( $components, 'frontendJsFeature: props.frontendJsFeature' ) ||
	false === strpos( $renderer, 'frontendJsFeature' )
) {
	echo "Frontend feature contract failed: shared admin warning wiring is missing.\n";
	exit( 1 );
}

echo "Frontend feature contract passed.\n";
