#!/usr/bin/env php
<?php

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
}

if (php_sapi_name() !== 'cli') {
	exit("This script must be run from the command line.\n");
}

$wpCliArguments = isset( $args ) && is_array( $args ) ? $args : null;
$argvCopy = is_array( $wpCliArguments ) ? array_merge( array( __FILE__ ), $wpCliArguments ) : $argv;
array_shift($argvCopy);
$command = strtolower($argvCopy[0] ?? 'capture');
array_shift($argvCopy);

$defaults = [
	'wp-root' => getenv('WP_ROOT') ?: '',
	'plugin-path' => __DIR__ . '/../html-social-share.php',
	'scenario-file' => __DIR__ . '/frontend-output-scenarios.json',
	'output' => __DIR__ . '/fixtures/frontend-output-baseline.json',
	'baseline' => __DIR__ . '/fixtures/frontend-output-baseline.json',
	'strict' => false,
];

function regression_fail(string $format, ...$values)
{
	exit(esc_html(vsprintf($format, $values)));
}

function parse_args(array $argv): array
{
	$parsed = [];

	foreach ($argv as $entry) {
		if (substr($entry, 0, 2) === '--') {
			list( $key, $value ) = array_pad(explode('=', substr($entry, 2), 2), 2, true);
			if ($value === true || $value === '') {
				$parsed[$key] = true;
			} else {
				$parsed[$key] = $value;
			}
		} elseif (strlen(trim($entry)) > 0) {
			$parsed[] = $entry;
		}
	}

	return $parsed;
}

function show_help()
{
	echo "Usage: php frontend-output-regression.php <capture|compare> [--wp-root=PATH] [--plugin-path=PATH] [--scenario-file=FILE] [--output=FILE] [--baseline=FILE] [--strict]\n";
	echo "  capture   Generate deterministic frontend output for scenarios and write to a fixture file.\n";
	echo "  compare   Compare captured output against baseline fixture and print diff report.\n";
	echo "Examples:\n";
	echo "  php tests/frontend-output-regression.php capture --wp-root=/var/www/html --scenario-file=tests/frontend-output-scenarios.json --output=tests/fixtures/frontend-output-baseline.json\n";
	echo "  php tests/frontend-output-regression.php compare --wp-root=/var/www/html --baseline=tests/fixtures/frontend-output-baseline.json\n";
	echo "  wp eval-file tests/frontend-output-regression.php compare strict\n";
	exit(0);
}

if ($command === '--help' || $command === '-h' || $command === 'help') {
	show_help();
}

$args = parse_args($argvCopy);
if ( in_array( 'strict', $args, true ) ) {
	$args['strict'] = true;
}
$options = array_merge($defaults, $args);

function bootstrap_wp(array $options)
{
	if ( defined( 'ABSPATH' ) && function_exists( 'do_action' ) ) {
		return;
	}

	if (!empty($options['wp-root']) && is_file(rtrim($options['wp-root'], '/') . '/wp-load.php')) {
		require_once rtrim($options['wp-root'], '/') . '/wp-load.php';
		return;
	}

	$fallbacks = [
		dirname(__DIR__),
		dirname(__DIR__) . '/..',
		dirname(__DIR__) . '/../..',
		'./',
	];

	foreach ($fallbacks as $path) {
		$candidate = realpath($path) . '/wp-load.php';
		if ($candidate && is_file($candidate)) {
			require_once $candidate;
			return;
		}
	}

	echo "wp-load.php not found. Set --wp-root=/path/to/wordpress.\n";
	exit(1);
}

$GLOBALS['zm_sh_regression_bootstrapped'] = false;
ob_start();
register_shutdown_function(function () {
	if (!empty($GLOBALS['zm_sh_regression_bootstrapped'])) {
		return;
	}

	$output = '';
	if (ob_get_level() > 0) {
		$output = (string) ob_get_clean();
	}

	echo "WordPress bootstrap failed or terminated before regression capture could start.\n";
	exit(1);
});

bootstrap_wp($options);

require_once __DIR__ . '/cli-helpers.php';
require_once __DIR__ . '/support/frontend-output-contract.php';

if (!in_array($command, ['capture', 'compare'], true)) {
	regression_fail("Invalid command: %s.\n\n", $command);
	show_help();
}

if (!is_file($options['scenario-file'])) {
	regression_fail("Scenario file not found: %s\n", $options['scenario-file']);
}

if (!is_file($options['plugin-path'])) {
	regression_fail("Plugin entry file not found: %s\n", $options['plugin-path']);
}

require_once $options['plugin-path'];
if ( empty( $GLOBALS['zm_sh'] ) || ! is_object( $GLOBALS['zm_sh'] ) ) {
	do_action('init');
}
$GLOBALS['zm_sh_regression_bootstrapped'] = true;
if (ob_get_level() > 0) {
	ob_end_clean();
}

require_once ABSPATH . 'wp-admin/includes/file.php';
WP_Filesystem();
global $wp_filesystem;

if (!is_dir(dirname($options['output']))) {
	wp_mkdir_p(dirname($options['output']));
}

try {
	$scenarios = hssb_test_load_frontend_scenarios($options['scenario-file']);
} catch ( RuntimeException $error ) {
	regression_fail("%s\n", $error->getMessage());
}
hssb_test_prepare_frontend_context();
$results = hssb_test_capture_frontend_scenarios($scenarios);

if ($command === 'capture') {
	$payload = [
		'format_version' => 1,
		'command'        => 'capture',
		'scenarios' => $results,
	];
	if (!$wp_filesystem->put_contents($options['output'], wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FS_CHMOD_FILE)) {
		regression_fail("Could not write capture output: %s\n", $options['output']);
	}
	echo "Captured " . count($results) . " scenario(s) -> " . esc_html($options['output']) . "\n";
	exit(0);
}

if ($command === 'compare') {
	if (!is_file($options['baseline'])) {
		regression_fail("Baseline not found: %s\n", $options['baseline']);
		exit(1);
	}
	$baselineRaw = json_decode(implode('', file($options['baseline'])), true);
	if (!is_array($baselineRaw) || !isset($baselineRaw['scenarios'])) {
		regression_fail("Invalid baseline schema: %s\n", $options['baseline']);
		exit(1);
	}
	if ( empty( $baselineRaw['scenarios'] ) ) {
		regression_fail("Baseline contains no frontend scenarios: %s\n", $options['baseline']);
	}

	$failures = [];
	$baselineMap = $baselineRaw['scenarios'];
	if ( array_keys( $baselineMap ) !== array_keys( $results ) ) {
		$failures['scenario-catalog'] = [
			'expected' => array_keys( $baselineMap ),
			'actual' => array_keys( $results ),
			'reason' => 'baseline and scenario catalog differ',
		];
	}
	foreach ($results as $name => $current) {
		if (!isset($baselineMap[$name]['output'])) {
			$failures[$name] = [
				'expected' => null,
				'actual' => $current['output'],
				'reason' => 'missing baseline scenario',
			];
			continue;
		}

		if ($baselineMap[$name]['output'] !== $current['output']) {
			$failures[$name] = [
				'expected' => $baselineMap[$name]['output'],
				'actual' => $current['output'],
				'reason' => 'content mismatch',
			];
		}
	}

	if ($failures) {
		echo "\nFrontend regression failed for " . count($failures) . " scenario(s):\n";
		foreach ($failures as $name => $diff) {
			echo '- ' . esc_html($name) . ': ' . esc_html($diff['reason']) . "\n";
		}
		if (($options['strict'] === 'true') || ($options['strict'] === true)) {
			echo "Strict mode: failing.\n";
			exit(1);
		}
		echo "Non-strict mode: differences reported but not failing.\n";
		exit(0);
	}

	echo "Frontend regression passed: " . count($results) . " scenario(s).\n";
	exit(0);
}
