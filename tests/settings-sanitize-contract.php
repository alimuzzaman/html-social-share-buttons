#!/usr/bin/env php
<?php
define('ABSPATH', __DIR__ . '/../');

function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
	return true;
}

function sanitize_textarea_field($value) {
	return trim($value);
}

require_once __DIR__ . '/../share-templates.php';
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
	'share_templates' => [
		'facebook' => 'https://example.com/share?u=%%permalink%%',
		'x' => 'https://x.com/intent/tweet?url=%%permalink%%',
		'unknown' => 'https://example.com/ignored',
	],
];

$expected = [
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
	'g_analytics' => true,
	'auto_hide_btn' => true,
	'use_port' => true,
	'nofollow' => true,
	'share_templates' => [
		'facebook' => 'https://example.com/share?u=%%permalink%%',
		'x' => 'https://x.com/intent/tweet?url=%%permalink%%',
	],
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

$unchecked_input = [
	'title' => 'Share this with your friends',
	'iconset' => 'default',
	'excludes' => 'about,42,Sample page',
	'show_in' => [],
	'show_left' => 'circle',
	'show_right' => 'square',
	'show_before_post' => 'circle',
	'show_after_post' => 'square',
	'icons' => [],
];

$unchecked_expected = $unchecked_input;
unset( $unchecked_expected['show_in'] );
$unchecked_actual = $settings->sanitize($unchecked_input);

if ($unchecked_actual !== $unchecked_expected) {
	echo "Unchecked settings sanitize contract failed.\n";
	echo "Expected:\n";
	var_export($unchecked_expected);
	echo "\nActual:\n";
	var_export($unchecked_actual);
	echo "\n";
	exit(1);
}

echo "Settings sanitize contract passed.\n";
