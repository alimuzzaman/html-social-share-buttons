<?php
namespace HtmlSocialShare\Bootstrap;

use HtmlSocialShare\Container;
use HtmlSocialShare\Telemetry\NullTelemetry;

/**
 * Registers services into the container. Extracted from Bootstrap for separation of concerns.
 */
class ServiceRegistrar
{
    public function register(Container $c): void
    {
        $c->set('info', function () {
            return new \HtmlSocialShare\Info();
        });

        $c->set('profile_manager', function ($c) {
            return new \HtmlSocialShare\ProfileManager($c->get('settings'));
        });

        $c->set('share_renderer', function ($c) {
            return new \HtmlSocialShare\ShareRenderer($c->get('icon_registry'), $c->get('settings'));
        });

        $c->set('icon_registry', function ($c) {
            return new \HtmlSocialShare\IconRegistry($c->get('settings'));
        });

        $c->set('settings', function () {
            return new \HtmlSocialShare\Settings();
        });

        $c->set('cache', function () {
            return new \HtmlSocialShare\WordPressCache();
        });



        $c->set('migration', function ($c) {
            return new \HtmlSocialShare\Migration($c->get('settings'));
        });

        $c->set('back_compat', function ($c) {
            return new \HtmlSocialShare\BackCompatShim($c->get('settings'));
        });

        $c->set('admin', function ($c) {
            return new \HtmlSocialShare\Admin\Admin($c->get('settings'), $c->get('profile_manager'), $c->get('share_renderer'));
        });

        $c->set('content_display', function ($c) {
            return new \HtmlSocialShare\ContentDisplay(
                $c->get('settings'),
                $c->get('profile_manager'),
                $c->get('share_renderer')
            );
        });

        $c->set('widget', function ($c) {
            return new \HtmlSocialShare\Widget\Widget($c->get('share_renderer'), $c->get('settings'));
        });

        $c->set('svg_sanitizer', function () {
            return new \HtmlSocialShare\Svg\Sanitizer();
        });

        // Default telemetry: no-op. Consumers may override this by setting 'telemetry' in tests or when integrating
        $c->set('telemetry', function () {
            return new NullTelemetry();
        });



        // React admin interface
        $c->set('react_admin_interface', function ($c) {
            return new \HtmlSocialShare\Admin\ReactAdminInterface($c->get('settings'));
        });

        // REST API service
        $c->set('rest_api_service', function ($c) {
            return new \HtmlSocialShare\Rest\RestApiService($c->get('settings'), $c->get('profile_manager'));
        });
    }
}
