<?php
// Simple unit-style tests for BackCompatShim
require __DIR__ . '/../../src/bootstrap.php';

$c = require __DIR__ . '/../../src/bootstrap.php';
$settings = $c->get('settings');
$back = $c->get('back_compat');

// Clean any previous state
$settings->set('hssb_options_v1', null);
$settings->set('hssb_profiles', null);
$settings->set('hssb_social_links', null);
$settings->set('hssb_settings', null);

// Test 1: mapping merges profiles + social_links
$settings->set('hssb_profiles', [
    '1' => ['network' => 'twitter', 'handle' => '@ex']
]);
$settings->set('hssb_social_links', [
    'facebook' => ['network' => 'facebook', 'url' => 'https://facebook.com/example']
]);

$canonical = $back->migrate();

if (!isset($canonical['profiles']) || !is_array($canonical['profiles'])) {
    echo "FAIL: profiles key missing\n";
    exit(1);
}

$networks = array_map(function($p){ return $p['network'] ?? null; }, $canonical['profiles']);
if (!in_array('twitter', $networks) || !in_array('facebook', $networks)) {
    echo "FAIL: expected networks not found: " . implode(',', $networks) . "\n";
    exit(1);
}

// Test 2: idempotency / safe re-run (should still contain networks)
$canonical2 = $back->migrate();
$networks2 = array_map(function($p){ return $p['network'] ?? null; }, $canonical2['profiles']);
if (!in_array('twitter', $networks2) || !in_array('facebook', $networks2)) {
    echo "FAIL: after second migrate, networks missing\n";
    exit(1);
}

echo "PASS\n";
exit(0);
