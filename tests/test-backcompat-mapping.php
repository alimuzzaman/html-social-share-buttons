<?php
require __DIR__ . '/../src/bootstrap.php';

$c = require __DIR__ . '/../src/bootstrap.php';

// Seed legacy keys in Settings
$settings = $c->get('settings');
$settings->set('hssb_profiles', [
    '1' => ['network' => 'twitter', 'handle' => '@ex']
]);
$settings->set('hssb_settings', ['show_after_post' => true]);

// older plugin versions stored social links under this key as an associative map
$settings->set('hssb_social_links', [
    'facebook' => ['network' => 'facebook', 'url' => 'https://facebook.com/example'],
    'linkedin' => ['network' => 'linkedin', 'url' => 'https://linkedin.com/in/example'],
]);

$back = $c->get('back_compat');
$canonical = $back->migrate();

echo "Canonical keys after migration:\n";
print_r($canonical);
