<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Container;

// Bootstrap class that creates the service container and wires WordPress hooks.
class Bootstrap
{
    private Container $container;
    private string $pluginFile;

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->container = new Container();
        $this->registerServices();
        $this->registerHooks();
        $this->init();
    }

    private function registerServices(): void
    {
        $c = $this->container;

        $c->set('info', function () {
            return new Info();
        });

        $c->set('profile_manager', function ($c) {
            return new ProfileManager($c->get('settings'));
        });

        $c->set('share_renderer', function ($c) {
            return new ShareRenderer($c->get('icon_registry'), $c->get('settings'), $c->get('share_counts'));
        });

        $c->set('icon_registry', function ($c) {
            return new IconRegistry($c->get('settings'));
        });

        $c->set('settings', function () {
            return new Settings();
        });

        $c->set('cache', function () {
            return new Cache();
        });

        $c->set('share_counts', function ($c) {
            return new ShareCounts\ShareCountManager($c->get('cache'), $c->get('settings'));
        });

        $c->set('migration', function ($c) {
            return new Migration($c->get('settings'));
        });

        $c->set('back_compat', function ($c) {
            return new BackCompatShim($c->get('settings'));
        });

        $c->set('admin', function ($c) {
            return new Admin\Admin($c->get('settings'), $c->get('profile_manager'), $c->get('share_renderer'));
        });

        $c->set('content_display', function ($c) {
            return new ContentDisplay(
                $c->get('settings'),
                $c->get('profile_manager'),
                $c->get('share_renderer')
            );
        });

        $c->set('widget', function ($c) {
            return new Widget\Widget($c->get('share_renderer'), $c->get('settings'));
        });

        $c->set('svg_sanitizer', function () {
            return new Svg\Sanitizer();
        });
    }

    private function registerHooks(): void
    {
        // Cron hook
        add_action('hss_refresh_share_counts', [$this, 'handleCron']);

        // AJAX endpoints
        add_action('wp_ajax_hss_refresh_counts', [$this, 'ajaxRefreshCounts']);
        add_action('wp_ajax_hss_flush_share_counts', [$this, 'ajaxFlushCounts']);

        // Frontend assets
        add_action('wp_enqueue_scripts', [$this->container->get('content_display'), 'enqueueFrontendStyles']);

        // Widgets
        add_action('widgets_init', function() {
            register_widget($this->container->get('widget'));
        });

        // Server-side block registration
        add_action('init', function() {
            $shareRenderer = $this->container->get('share_renderer');
            $block = new Blocks\ShareButtons\Block($shareRenderer);
            $block->register();
        });

        // Integrations - directly instantiate our IntegrationLoader
        $integrationLoader = new IntegrationLoader($this->container);
        if (method_exists($integrationLoader, 'init')) {
            $integrationLoader->init();
        }

        // Activation / Deactivation
        register_activation_hook($this->pluginFile, [$this, 'onActivate']);
        register_deactivation_hook($this->pluginFile, [$this, 'onDeactivate']);
    }

    private function init(): void
    {
        // Initialize admin and content display to ensure hooks are registered
        $this->container->get('admin');
        $this->container->get('content_display');
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    // --- Hook handlers ---
    public function handleCron(): void
    {
        $this->container->get('share_counts')->refreshCounts();
    }

    public function ajaxRefreshCounts(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }

        check_ajax_referer('hss_refresh_counts', '_hss_nonce');

        try {
            $this->container->get('share_counts')->refreshCounts();
            wp_send_json_success(['message' => 'Refresh completed']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()], 500);
        }
    }

    public function ajaxFlushCounts(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }

        check_ajax_referer('hss_flush_counts', '_hss_flush_nonce');

        $postIds = isset($_POST['post_ids']) && is_array($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : null;
        $removeDb = !empty($_POST['remove_db']);

        try {
            $this->container->get('share_counts')->flushCache($postIds, $removeDb);
            wp_send_json_success(['message' => 'Flush completed']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()], 500);
        }
    }

    public function onActivate(): void
    {
        $migration = $this->container->get('migration');
        $migration->run();

        if ($this->container->get('share_counts') && method_exists($this->container->get('share_counts'), 'installSchema')) {
            $this->container->get('share_counts')->installSchema();
        }

        if ($this->container->get('share_counts') && method_exists($this->container->get('share_counts'), 'scheduleCron')) {
            $this->container->get('share_counts')->scheduleCron();
        }
    }

    public function onDeactivate(): void
    {
        if ($this->container && method_exists($this->container->get('share_counts'), 'unscheduleCron')) {
            $this->container->get('share_counts')->unscheduleCron();
        }
    }
}

// NOTE: Bootstrap no longer self-instantiates. The main plugin file should
// instantiate the Bootstrap class and retrieve the container via getContainer().
