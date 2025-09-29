<?php
namespace HtmlSocialShare\Bootstrap;

use HtmlSocialShare\Container;

/**
 * Wires WordPress hooks. Extracted from Bootstrap so that Bootstrap only composes collaborators.
 */
class HookRegistrar
{
    /**
     * Register all WordPress hooks. The activation handler is any object with onActivate/onDeactivate methods (Bootstrap).
     */
    public function register(Container $container, string $pluginFile, $activationHandler): void
    {




        // Frontend assets
        add_action('wp_enqueue_scripts', [$container->get('content_display'), 'enqueueFrontendStyles']);

        // Widgets
        add_action('widgets_init', function() use ($container) {
            register_widget($container->get('widget'));
        });

        // Server-side block registration
        add_action('init', function() use ($container) {
            $shareRenderer = $container->get('share_renderer');
            $block = new \HtmlSocialShare\Blocks\ShareButtons\Block($shareRenderer);
            $block->register();
        });

        // Integrations - directly instantiate our IntegrationLoader
        $integrationLoader = new \HtmlSocialShare\IntegrationLoader($container);
        if (method_exists($integrationLoader, 'init')) {
            $integrationLoader->init();
        }



        // React admin interface
        add_action('admin_init', function() use ($container) {
            $reactAdmin = $container->get('react_admin_interface');
            $reactAdmin->init();
        });

        // AJAX endpoints for React admin
        add_action('wp_ajax_hss_get_posts_with_counts', function() use ($container) {
            $reactAdmin = $container->get('react_admin_interface');
            $reactAdmin->ajaxGetPostsWithCounts();
        });

        // Activation / Deactivation
        register_activation_hook($pluginFile, [$activationHandler, 'onActivate']);
        register_deactivation_hook($pluginFile, [$activationHandler, 'onDeactivate']);
    }
}
