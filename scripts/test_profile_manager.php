<?php
require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../src/bootstrap.php';

$pm = $container->get('profile_manager');

$id = $pm->createProfile(['network' => 'twitter', 'handle' => '@example']);
$profile = $pm->getProfile($id);

echo $profile['network'] . "\n";
