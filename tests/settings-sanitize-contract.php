#!/usr/bin/env php
<?php
define('ABSPATH', __DIR__ . '/../');

function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
	return true;
}

require_once __DIR__ . '/../settings_page.php';

$reflection = new ReflectionClass('zm_sh_settings');
$settings = $reflection->newInstanceWithoutConstructor();

$input = [
	'title' => 'Share this with your friends',
	'iconset' => 'default',
	'excludes' => '1,2,3',
	'show_in' => [
		'show_left' => '1',
		'show_after_post' => '1',
	],
	'show_left' => 'circle',
	'show_right' => 'square',
	'show_before_post' => 'circle',
	'show_after_post' => 'square',
	'icons' => [
		'facebook' => '1',
		'x' => '1',
		'linkedin' => '1',
	],
	'g_analytics' => '1',
	'auto_hide_btn' => '1',
	'use_port' => '1',
	'nofollow' => '1',
];

$expected = [
	'title' => 'Share this with your friends',
	'iconset' => 'default',
	'excludes' => '1,2,3',
	'show_in' => [
		'show_left' => 'circle',
		'show_after_post' => 'square',
	],
	'show_left' => 'circle',
	'show_right' => 'square',
	'show_before_post' => 'circle',
	'show_after_post' => 'square',
	'icons' => [
		'facebook' => '1',
		'x' => '1',
		'linkedin' => '1',
	],
	'g_analytics' => true,
	'auto_hide_btn' => true,
	'use_port' => true,
	'nofollow' => true,
];

$actual = $settings->sanitize($input);

if ($actual !== $expected) {
	echo "Settings sanitize contract failed.\n";
	echo "Expected:\n";
	var_export($expected);
	echo "\nActual:\n";
	var_export($actual);
	echo "\n";
	exit(1);
}

echo "Settings sanitize contract passed.\n";
