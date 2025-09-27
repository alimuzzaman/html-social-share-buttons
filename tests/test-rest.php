<?php
require __DIR__ . '/../src/bootstrap.php';

$c = require __DIR__ . '/../src/bootstrap.php';

$api = new HtmlSocialShare\Rest\Api();
$controller = new HtmlSocialShare\Rest\Controller($c->get('profile_manager'));

$api->register('GET', '/profiles', function() use ($controller) { return $controller->listProfiles(); });
$api->register('POST', '/profiles', function($p) use ($controller) { return $controller->createProfile($p); });

$res1 = $api->dispatch('GET', '/profiles');
if ($res1['status'] !== 200) {
    echo "FAIL GET /profiles -> status " . $res1['status'] . "\n";
    exit(1);
}

$res2 = $api->dispatch('POST', '/profiles', ['network' => 'twitter', 'handle' => '@api']);
if ($res2['status'] !== 201) {
    echo "FAIL POST /profiles -> status " . $res2['status'] . "\n";
    exit(1);
}

echo "REST tests PASS\n";
exit(0);
