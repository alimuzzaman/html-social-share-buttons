#!/usr/bin/env php
<?php
if (php_sapi_name() !== 'cli') {
	exit("This script must be run from the command line.\n");
}

$argvCopy = $argv;
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

function parse_args(array $argv): array
{
	$parsed = [];

	foreach ($argv as $entry) {
		if (substr($entry, 0, 2) === '--') {
			[$key, $value] = array_pad(explode('=', substr($entry, 2), 2), 2, true);
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

function show_help(): void
{
	echo "Usage: php frontend-output-regression.php <capture|compare> [--wp-root=PATH] [--plugin-path=PATH] [--scenario-file=FILE] [--output=FILE] [--baseline=FILE] [--strict]\n";
	echo "  capture   Generate normalized frontend output for scenarios and write to a fixture file.\n";
	echo "  compare   Compare captured output against baseline fixture and print diff report.\n";
	echo "Examples:\n";
	echo "  php tests/frontend-output-regression.php capture --wp-root=/var/www/html --scenario-file=tests/frontend-output-scenarios.json --output=tests/fixtures/frontend-output-baseline.json\n";
	echo "  php tests/frontend-output-regression.php compare --wp-root=/var/www/html --baseline=tests/fixtures/frontend-output-baseline.json\n";
	exit(0);
}

if ($command === '--help' || $command === '-h' || $command === 'help') {
	show_help();
}

$args = parse_args($argvCopy);
if (!in_array($command, ['capture', 'compare'], true)) {
	echo "Invalid command: {$command}.\n\n";
	show_help();
}

$options = array_merge($defaults, $args);

if (!is_file($options['scenario-file'])) {
	echo "Scenario file not found: {$options['scenario-file']}\n";
	exit(1);
}

if (!is_file($options['plugin-path'])) {
	echo "Plugin entry file not found: {$options['plugin-path']}\n";
	exit(1);
}

if (!is_dir(dirname($options['output']))) {
	mkdir(dirname($options['output']), 0777, true);
}

function bootstrap_wp(array $options): void
{
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

function normalize_output(string $html): string
{
	$html = preg_replace('/\s+/', ' ', $html);
	return trim($html);
}

function render_scenario(array $scenario): array
{
	if (!isset($scenario['options']) || !is_array($scenario['options'])) {
		$scenario['options'] = [];
	}

	$testable = new zm_social_share();
	$options = $testable->options;

	if (is_array($scenario['options'])) {
		foreach ($scenario['options'] as $key => $value) {
			if (is_array($value) && isset($options[$key]) && is_array($options[$key])) {
				$options[$key] = array_replace_recursive($options[$key], $value);
			} else {
				$options[$key] = $value;
			}
		}
	}

	if (!isset($options['show_on'])) {
		$options['show_on'] = 'show_left';
	}

	$output = $testable->zm_sh_btn($options);
	return [
		'output' => normalize_output((string) $output),
		'options' => $options,
	];
}

function load_scenarios(string $path): array
{
	$raw = file_get_contents($path);
	$data = json_decode((string) $raw, true);
	if (!is_array($data) || !isset($data['scenarios']) || !is_array($data['scenarios'])) {
		echo "Invalid scenario schema in {$path}\n";
		exit(1);
	}
	return $data['scenarios'];
}

$GLOBALS['zm_sh_regression_bootstrapped'] = false;
ob_start();
register_shutdown_function(function (): void {
	if (!empty($GLOBALS['zm_sh_regression_bootstrapped'])) {
		return;
	}

	$output = '';
	if (ob_get_level() > 0) {
		$output = (string) ob_get_clean();
	}

	$summary = trim(preg_replace('/\s+/', ' ', strip_tags($output)));
	echo "WordPress bootstrap failed or terminated before regression capture could start.\n";
	if ($summary !== '') {
		echo "Bootstrap output: " . substr($summary, 0, 500) . "\n";
	}
	exit(1);
});

bootstrap_wp($options);

require_once $options['plugin-path'];
do_action('init');
$GLOBALS['zm_sh_regression_bootstrapped'] = true;
if (ob_get_level() > 0) {
	ob_end_clean();
}

$scenarios = load_scenarios($options['scenario-file']);
$results = [];

foreach ($scenarios as $scenario) {
	if (!isset($scenario['name'])) {
		echo "Every scenario must include a name.\n";
		exit(1);
	}
	$results[$scenario['name']] = render_scenario($scenario);
}

if ($command === 'capture') {
	$payload = [
		'generated_at' => gmdate('c'),
		'command' => 'capture',
		'scenarios' => $results,
	];
	file_put_contents($options['output'], json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	echo "Captured " . count($results) . " scenario(s) -> {$options['output']}\n";
	exit(0);
}

if ($command === 'compare') {
	if (!is_file($options['baseline'])) {
		echo "Baseline not found: {$options['baseline']}\n";
		exit(1);
	}
	$baselineRaw = json_decode((string) file_get_contents($options['baseline']), true);
	if (!is_array($baselineRaw) || !isset($baselineRaw['scenarios'])) {
		echo "Invalid baseline schema: {$options['baseline']}\n";
		exit(1);
	}

	$failures = [];
	$baselineMap = $baselineRaw['scenarios'];
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
			echo "- {$name}: {$diff['reason']}\n";
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
