<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Container;
use HtmlSocialShare\Bootstrap\ServiceRegistrar;
use HtmlSocialShare\Bootstrap\HookRegistrar;
use HtmlSocialShare\Telemetry\TelemetryInterface;
use HtmlSocialShare\Utils\DataUtils;

/**
 * Bootstrap class that creates the service container and wires WordPress hooks.
 *
 * Handles plugin initialization, service registration, and lifecycle events
 * with proper error handling and telemetry integration.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class Bootstrap
{
    private Container $container;
    private string $pluginFile;
    private bool $initialized = false;

    public function __construct(string $pluginFile)
    {
        if (!self::isValidPluginFile($pluginFile)) {
            throw new \InvalidArgumentException('Invalid plugin file path provided');
        }

        $this->pluginFile = $pluginFile;
        $this->container = new Container();

        try {
            $this->registerServices();
            $this->registerHooks();
            $this->init();
        } catch (\Exception $e) {
            // Log initialization error but don't prevent WordPress from loading
            error_log('HTML Social Share Bootstrap Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Register all services in the container
     */
    private function registerServices(): void
    {
        $serviceRegistrar = new ServiceRegistrar();
        $serviceRegistrar->register($this->container);
    }

    /**
     * Register all WordPress hooks
     */
    private function registerHooks(): void
    {
        $hookRegistrar = new HookRegistrar();
        $hookRegistrar->register($this->container, $this->pluginFile, $this);
    }

    /**
     * Initialize required services
     */
    private function init(): void
    {
        if ($this->initialized) {
            return;
        }

        try {
            // Initialize admin and content display to ensure hooks are registered
            $this->container->get('admin');
            $this->container->get('content_display');

            // Initialize REST API service
            $this->container->get('rest_api_service')->init();

            $this->initialized = true;

            // Fire initialization complete action
            do_action('hss_bootstrap_initialized', $this->container);
        } catch (\Exception $e) {
            error_log('HTML Social Share Init Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    // --- Hook handlers with enhanced error handling ---





    public function ajaxFlushCounts(): void
    {
        if (!$this->validateAjaxRequest('hss_flush_counts', '_hss_flush_nonce')) {
            return;
        }

        $requestData = self::sanitizeFlushRequest($_POST);

        try {
            $shareCountManager = $this->container->get('share_counts');
            if (method_exists($shareCountManager, 'flushCache')) {
                $shareCountManager->flushCache($requestData['post_ids'], $requestData['remove_db']);
                wp_send_json_success(['message' => 'Flush completed']);
            } else {
                wp_send_json_error(['message' => 'Share count manager not available'], 500);
            }
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()], 500);
        }
    }

    public function onActivate(): void
    {
        try {
            $this->runMigrations();

            $this->trackTelemetryEvent('plugin_activated', [
                'plugin_file' => $this->pluginFile,
                'time' => time()
            ]);

            // Provide a WordPress hook so external integrations can react to activation
            do_action('hss_activated', $this->container);
        } catch (\Exception $e) {
            error_log('HSS Activation Error: ' . $e->getMessage());
            // Don't prevent activation, but log the error
        }
    }

    public function onDeactivate(): void
    {
        try {
            $this->trackTelemetryEvent('plugin_deactivated', [
                'plugin_file' => $this->pluginFile,
                'time' => time()
            ]);

            // Provide a WordPress hook so external integrations can react to deactivation
            do_action('hss_deactivated', $this->container);
        } catch (\Exception $e) {
            error_log('HSS Deactivation Error: ' . $e->getMessage());
        }
    }

    // --- Private helper methods with side effects ---

    private function runMigrations(): void
    {
        $migration = $this->container->get('migration');
        if (method_exists($migration, 'run')) {
            $migration->run();
        }
    }







    private function validateAjaxRequest(string $action, string $nonceField): bool
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
            return false;
        }

        if (!check_ajax_referer($action, $nonceField, false)) {
            wp_send_json_error(['message' => 'invalid nonce'], 403);
            return false;
        }

        return true;
    }

    private function trackTelemetryEvent(string $event, array $data = []): void
    {
        try {
            $telemetry = $this->container->get('telemetry');
            if ($telemetry instanceof TelemetryInterface) {
                $telemetry->track($event, $data);
            }
        } catch (\Exception $e) {
            // Swallow telemetry errors - they shouldn't prevent normal operation
        }
    }

    // --- Pure helper functions ---

    /**
     * Pure function to validate plugin file path
     *
     * @param string $pluginFile Plugin file path
     * @return bool True if valid
     */
    public static function isValidPluginFile(string $pluginFile): bool
    {
        return !empty($pluginFile) &&
               is_string($pluginFile) &&
               strlen($pluginFile) > 4 &&
               str_ends_with($pluginFile, '.php');
    }

    /**
     * Pure function to sanitize flush request data
     *
     * @param array $postData POST data from request
     * @return array Sanitized request data
     */
    public static function sanitizeFlushRequest(array $postData): array
    {
        $postIds = null;
        if (isset($postData['post_ids']) && is_array($postData['post_ids'])) {
            $postIds = array_map('intval', $postData['post_ids']);
            $postIds = array_filter($postIds, function($id) { return $id > 0; });
            $postIds = empty($postIds) ? null : array_values($postIds);
        }

        $removeDb = !empty($postData['remove_db']);

        return [
            'post_ids' => $postIds,
            'remove_db' => $removeDb,
        ];
    }

    /**
     * Pure function to validate initialization requirements
     *
     * @param Container $container Service container
     * @return array Validation result with success flag and errors
     */
    public static function validateInitializationRequirements(Container $container): array
    {
        $errors = [];
        $requiredServices = ['settings', 'admin', 'content_display', 'share_renderer'];

        foreach ($requiredServices as $service) {
            try {
                $container->get($service);
            } catch (\Exception $e) {
                $errors[] = "Required service '{$service}' not available: " . $e->getMessage();
            }
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
        ];
    }
}

// NOTE: Bootstrap no longer self-instantiates. The main plugin file should
// instantiate the Bootstrap class and retrieve the container via getContainer().
