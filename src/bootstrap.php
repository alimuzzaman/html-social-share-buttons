<?php
// Minimal bootstrap for local development and tests
// Loads composer's autoloader and returns a simple service container placeholder.

if (! file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Composer dependencies not installed yet.
    return null;
}

$loader = require __DIR__ . '/../vendor/autoload.php';

// Boot a very small container and register basic services.
use HtmlSocialShare\Container;

$container = new Container();

$container->set('info', function () {
    return new HtmlSocialShare\Info();
});

$container->set('profile_manager', function () {
    return new HtmlSocialShare\ProfileManager();
});

$container->set('share_renderer', function () {
    return new HtmlSocialShare\ShareRenderer();
});

$container->set('icon_registry', function () {
    return new HtmlSocialShare\IconRegistry();
});

$container->set('settings', function () {
    return new HtmlSocialShare\Settings();
});

$container->set('cache', function () {
    return new HtmlSocialShare\Cache();
});

$container->set('back_compat', function ($c) {
    return new HtmlSocialShare\BackCompatShim($c->get('settings'));
});

return $container;
