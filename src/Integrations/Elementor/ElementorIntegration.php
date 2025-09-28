<?php
namespace HtmlSocialShare\Integrations\Elementor;

use HtmlSocialShare\ShareRendererInterface;
use HtmlSocialShare\Container;

/**
 * Elementor Integration Loader
 * 
 * @since 3.0.0
 */
class ElementorIntegration
{
    /**
     * Service container
     * 
     * @var Container
     */
    private $container;

    /**
     * Constructor
     * 
     * @param Container $container Service container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Initialize the Elementor integration
     * 
     * @return void
     */
    public function init(): void
    {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Register hooks
        add_action('elementor/widgets/register', [$this, 'registerWidgets']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueueEditorAssets']);
    }

    /**
     * Register Elementor widgets
     * 
     * @param \Elementor\Widgets_Manager $widgetsManager
     * @return void
     */
    public function registerWidgets($widgetsManager): void
    {
        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }

        // Get share renderer from container
        $shareRenderer = $this->container->get(ShareRendererInterface::class);
        
        // Create widget instance with dependency injection
        $widget = new ShareButtonsWidget([], null, $shareRenderer);
        
        // Register the widget
        $widgetsManager->register($widget);
    }

    /**
     * Enqueue editor assets
     * 
     * @return void
     */
    public function enqueueEditorAssets(): void
    {
        $assetsUrl = plugin_dir_url(dirname(dirname(__DIR__))) . 'assets/';
        
        wp_enqueue_style(
            'html-social-share-elementor-editor',
            $assetsUrl . 'admin.css',
            [],
            '3.0.0'
        );
    }

    /**
     * Check if Elementor is available
     * 
     * @return bool
     */
    public static function isAvailable(): bool
    {
        return (
            class_exists('\Elementor\Plugin') && 
            did_action('elementor/loaded')
        );
    }

    /**
     * Get integration info
     * 
     * @return array
     */
    public static function getIntegrationInfo(): array
    {
        return [
            'name' => 'Elementor',
            'description' => __('Adds HTML Social Share Buttons widget to Elementor page builder.', 'html-social-share'),
            'version' => '3.0.0',
            'available' => self::isAvailable(),
            'required_plugin' => 'Elementor',
            'required_version' => '3.0.0',
        ];
    }
}