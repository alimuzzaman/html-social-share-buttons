<?php

/**
 * Shared helpers for capturing the legacy frontend renderer contract.
 *
 * The context is deterministic so fixtures do not depend on the Sandbox URL,
 * database content, or the current request.
 */

function hssb_test_frontend_home_url( $url, $path ) {
	$path = (string) $path;

	return 'https://example.test' . ( '' === $path ? '' : '/' . ltrim( $path, '/' ) );
}

function hssb_test_frontend_title() {
	return 'Frontend Contract Title';
}

function hssb_test_frontend_plugins_url( $url ) {
	$url = preg_replace( '#^https?://[^/]+#', 'https://example.test', (string) $url );

	/*
	 * The WordPress test suite mounts the plugin outside wp-content/plugins,
	 * which makes plugins_url() include the absolute host path. Normalize that
	 * harness-only difference without changing the URL suffix under the plugin.
	 */
	return preg_replace(
		'#/wp-content/plugins/.*/html-social-share-buttons/#',
		'/wp-content/plugins/html-social-share-buttons/',
		$url
	);
}

function hssb_test_prepare_frontend_context() {
	$_SERVER['REQUEST_URI'] = '/frontend-contract/?preview=true';
	$GLOBALS['post']        = null;

	remove_filter( 'home_url', 'hssb_test_frontend_home_url', PHP_INT_MAX );
	remove_filter( 'plugins_url', 'hssb_test_frontend_plugins_url', PHP_INT_MAX );
	remove_filter( 'zm_sh_title', 'hssb_test_frontend_title', PHP_INT_MAX );
	add_filter( 'home_url', 'hssb_test_frontend_home_url', PHP_INT_MAX, 2 );
	add_filter( 'plugins_url', 'hssb_test_frontend_plugins_url', PHP_INT_MAX );
	add_filter( 'zm_sh_title', 'hssb_test_frontend_title', PHP_INT_MAX );
}

function hssb_test_normalize_frontend_output( $html ) {
	return str_replace( array( "\r\n", "\r" ), "\n", (string) $html );
}

function hssb_test_load_frontend_scenarios( $path ) {
	$data = json_decode( (string) file_get_contents( $path ), true );
	if ( ! is_array( $data ) || ! isset( $data['scenarios'] ) || ! is_array( $data['scenarios'] ) ) {
		throw new RuntimeException( sprintf( 'Invalid scenario schema in %s', $path ) );
	}

	$names = array();
	foreach ( $data['scenarios'] as $scenario ) {
		if ( ! is_array( $scenario ) || empty( $scenario['name'] ) || ! is_string( $scenario['name'] ) ) {
			throw new RuntimeException( 'Every frontend scenario must have a non-empty string name.' );
		}
		if ( isset( $names[ $scenario['name'] ] ) ) {
			throw new RuntimeException( sprintf( 'Duplicate frontend scenario name: %s', $scenario['name'] ) );
		}
		$names[ $scenario['name'] ] = true;
	}

	return $data['scenarios'];
}

function hssb_test_render_frontend_scenario( $scenario ) {
	$renderer = new zm_social_share();
	$schema   = json_decode(
		(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/settings-schema-baseline.json' ),
		true
	);
	$options  = isset( $schema['default_options'] ) && is_array( $schema['default_options'] )
		? $schema['default_options']
		: array();
	$replace  = isset( $scenario['replace'] ) && is_array( $scenario['replace'] ) ? $scenario['replace'] : array();
	$overrides = isset( $scenario['options'] ) && is_array( $scenario['options'] ) ? $scenario['options'] : array();

	foreach ( $overrides as $key => $value ) {
		if ( in_array( $key, $replace, true ) ) {
			$options[ $key ] = $value;
		} elseif ( is_array( $value ) && isset( $options[ $key ] ) && is_array( $options[ $key ] ) ) {
			$options[ $key ] = array_replace_recursive( $options[ $key ], $value );
		} else {
			$options[ $key ] = $value;
		}
	}

	if ( ! isset( $options['show_on'] ) ) {
		$options['show_on'] = 'show_left';
	}

	$renderer->options = $options;
	$entrypoint = isset( $scenario['entrypoint'] ) ? $scenario['entrypoint'] : 'renderer';
	$output = hssb_test_render_frontend_entrypoint( $entrypoint, $renderer, $scenario, $options );

	return array(
		'output'  => hssb_test_normalize_frontend_output( $output ),
		'options' => $options,
	);
}

function hssb_test_render_frontend_entrypoint( $entrypoint, $renderer, $scenario, $options ) {
	$previousRenderer = isset( $GLOBALS['zm_sh'] ) ? $GLOBALS['zm_sh'] : null;
	$GLOBALS['zm_sh']  = $renderer;

	switch ( $entrypoint ) {
		case 'shortcode':
			$output = zm_sh_shortcode_cb(
				isset( $scenario['attributes'] ) && is_array( $scenario['attributes'] )
					? $scenario['attributes']
					: array()
			);
			break;

		case 'block':
			$output = zm_sh_render_block(
				isset( $scenario['attributes'] ) && is_array( $scenario['attributes'] )
					? $scenario['attributes']
					: array()
			);
			break;

		case 'content':
			$previousQuery = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
			$query = is_object( $previousQuery )
				? $GLOBALS['wp_query']
				: new WP_Query();
			$wasSingular = $query->is_singular;
			$query->is_singular = true;
			$GLOBALS['wp_query'] = $query;
			$output = $renderer->filter_the_content(
				isset( $scenario['content'] ) ? (string) $scenario['content'] : 'Frontend contract content.'
			);
			$query->is_singular = $wasSingular;
			$GLOBALS['wp_query'] = $previousQuery;
			break;

		case 'footer':
			ob_start();
			$renderer->footer();
			$output = (string) ob_get_clean();
			break;

		case 'renderer':
		default:
			$output = $renderer->zm_sh_btn( $options );
			break;
	}

	$GLOBALS['zm_sh'] = $previousRenderer;

	return $output;
}

function hssb_test_capture_frontend_scenarios( $scenarios ) {
	$results = array();

	foreach ( $scenarios as $scenario ) {
		$rendered = hssb_test_render_frontend_scenario( $scenario );
		$results[ $scenario['name'] ] = array(
			'output' => $rendered['output'],
		);
	}

	return $results;
}
