<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Container;
use HtmlSocialShare\Bootstrap\ServiceRegistrar;
use HtmlSocialShare\Bootstrap\HookRegistrar;
use HtmlSocialShare\Telemetry\TelemetryInterface;

// Bootstrap class that creates the service container and wires WordPress hooks.
class Bootstrap
{
    private Container $container;
    private string $pluginFile;

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->container = new Container();

        // Delegate service registration to ServiceRegistrar for better SOLID separation
        $serviceRegistrar = new ServiceRegistrar();
        $serviceRegistrar->register($this->container);

        // Delegate hook registration to HookRegistrar. Bootstrap remains the activation handler.
        $hookRegistrar = new HookRegistrar();
        $hookRegistrar->register($this->container, $this->pluginFile, $this);

        $this->init();
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

        // Telemetry / logging hook for activation
        try {
            $telemetry = $this->container->get('telemetry');
            if ($telemetry instanceof TelemetryInterface) {
                $telemetry->track('plugin_activated', ['plugin_file' => $this->pluginFile, 'time' => time()]);
            }
        } catch (\Exception $e) {
            // Do not prevent activation if telemetry fails; swallow errors
        }

        // Provide a WordPress hook so external integrations can react to activation
        do_action('hss_activated', $this->container);
    }

    public function onDeactivate(): void
    {
        if ($this->container && method_exists($this->container->get('share_counts'), 'unscheduleCron')) {
            $this->container->get('share_counts')->unscheduleCron();
        }

        // Telemetry / logging hook for deactivation
        try {
            $telemetry = $this->container->get('telemetry');
            if ($telemetry instanceof TelemetryInterface) {
                $telemetry->track('plugin_deactivated', ['plugin_file' => $this->pluginFile, 'time' => time()]);
            }
        } catch (\Exception $e) {
            // Swallow telemetry errors on deactivation
        }

        // Provide a WordPress hook so external integrations can react to deactivation
        do_action('hss_deactivated', $this->container);
    }
}

// NOTE: Bootstrap no longer self-instantiates. The main plugin file should
// instantiate the Bootstrap class and retrieve the container via getContainer().
