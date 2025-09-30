<?php
namespace HtmlSocialShare;

use HtmlSocialShare\ShareRendererInterface;

/**
 * Integration Loader Class
 *
 * Handles loading and registering of WordPress page builder and plugin integrations
 * for the HTML Social Share Buttons plugin.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class IntegrationLoader
{
    /**
     * Container instance for dependency injection
     *
     * @var Container
     */
    private $container;

    /**
     * Share renderer instance
     *
     * @var ShareRendererInterface
     */
    private $shareRenderer;

    /**
     * Array of registered integrations
     *
     * @var array
     */
    private $integrations = [];

    /**
     * Constructor
     *
     * @param Container $container DI container instance
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->shareRenderer = $container->get('share_renderer');
    }

    /**
     * Initialize all integrations
     *
     * @return void
     */
    public function init()
    {
        $this->registerIntegrations();
        $this->loadIntegrations();
    }

    /**
     * Register all available integrations
     *
     * @return void
     */
    private function registerIntegrations()
    {
        // Page Builder Integrations
        $this->registerIntegration('elementor', 'Elementor', [$this, 'loadElementorIntegration']);
        $this->registerIntegration('wpbakery', 'WPBakery', [$this, 'loadWPBakeryIntegration']);
        $this->registerIntegration('divi', 'Divi', [$this, 'loadDiviIntegration']);
        $this->registerIntegration('beaver_builder', 'Beaver Builder', [$this, 'loadBeaverBuilderIntegration']);

        // Plugin Integrations
        $this->registerIntegration('woocommerce', 'WooCommerce', [$this, 'loadWooCommerceIntegration']);
        $this->registerIntegration('bbpress', 'bbPress', [$this, 'loadbbPressIntegration']);
        $this->registerIntegration('buddypress', 'BuddyPress', [$this, 'loadBuddyPressIntegration']);
        $this->registerIntegration('betterlinks', 'BetterLinks', [$this, 'loadBetterLinksIntegration']);
    }

    /**
     * Register a single integration
     *
     * @param string $slug Integration slug
     * @param string $name Integration display name
     * @param callable $loader Callback function to load the integration
     * @return void
     */
    private function registerIntegration($slug, $name, $loader)
    {
        $this->integrations[$slug] = [
            'name' => $name,
            'loader' => $loader,
            'loaded' => false
        ];
    }

    /**
     * Load all registered integrations
     *
     * @return void
     */
    private function loadIntegrations()
    {
        foreach ($this->integrations as $slug => $integration) {
            $this->loadIntegration($slug);
        }
    }

    /**
     * Load a specific integration if its dependencies are met
     *
     * @param string $slug Integration slug
     * @return void
     */
    private function loadIntegration($slug)
    {
        if (!isset($this->integrations[$slug])) {
            return;
        }

        $integration = $this->integrations[$slug];

        // Check if integration dependencies are met
        if ($this->checkIntegrationDependencies($slug)) {
            // Call the loader function
            call_user_func($integration['loader']);

            // Mark as loaded
            $this->integrations[$slug]['loaded'] = true;

            // Log successful loading
            error_log("HTML Social Share: Loaded integration '{$integration['name']}'");
        }
    }

    /**
     * Check if integration dependencies are met
     *
     * @param string $slug Integration slug
     * @return bool True if dependencies are met
     */
    private function checkIntegrationDependencies($slug)
    {
        switch ($slug) {
            case 'elementor':
                return defined('ELEMENTOR_VERSION');

            case 'wpbakery':
                return defined('WPB_VC_VERSION');

            case 'divi':
                return class_exists('ET_Builder_Module');

            case 'beaver_builder':
                return class_exists('FLBuilder');

            case 'woocommerce':
                return class_exists('WooCommerce');

            case 'bbpress':
                return class_exists('bbPress');

            case 'buddypress':
                return class_exists('BuddyPress');

            case 'betterlinks':
                return class_exists('BetterLinks');

            default:
                return false;
        }
    }

    /**
     * Load Elementor integration
     *
     * @return void
     */
    public function loadElementorIntegration()
    {
        add_action('init', function() {
            $integration = new \HtmlSocialShare\Integrations\Elementor\ElementorIntegration($this->container);
            $integration->init();
        });
    }

    /**
     * Load WPBakery integration
     *
     * @return void
     */
    public function loadWPBakeryIntegration()
    {
        add_action('vc_before_init', function() {
            \HtmlSocialShare\Integrations\WPBakery\ShareButtonsElement::register($this->shareRenderer);
        });
    }

    /**
     * Load Divi integration
     *
     * @return void
     */
    public function loadDiviIntegration()
    {
        add_action('et_builder_ready', function() {
            \HtmlSocialShare\Integrations\Divi\ShareButtonsModule::register($this->shareRenderer);
        });
    }

    /**
     * Load Beaver Builder integration
     *
     * @return void
     */
    public function loadBeaverBuilderIntegration()
    {
        add_action('init', function() {
            \HtmlSocialShare\Integrations\BeaverBuilder\ShareButtonsModule::register($this->shareRenderer);
        });
    }

    /**
     * Load WooCommerce integration
     *
     * @return void
     */
    public function loadWooCommerceIntegration()
    {
        add_action('init', function() {
            \HtmlSocialShare\Integrations\WooCommerce\ShareButtonsIntegration::register($this->shareRenderer);
        });
    }

    /**
     * Load bbPress integration
     *
     * @return void
     */
    public function loadbbPressIntegration()
    {
        add_action('init', function() {
            \HtmlSocialShare\Integrations\bbPress\ShareButtonsIntegration::register($this->shareRenderer);
        });
    }

    /**
     * Load BuddyPress integration
     *
     * @return void
     */
    public function loadBuddyPressIntegration()
    {
        add_action('bp_init', function() {
            \HtmlSocialShare\Integrations\BuddyPress\ShareButtonsIntegration::register($this->shareRenderer);
        });
    }

    /**
     * Load BetterLinks integration
     *
     * @return void
     */
    public function loadBetterLinksIntegration()
    {
        add_action('init', function() {
            $integration = new \HtmlSocialShare\Integrations\BetterLinks\BetterLinksIntegration();
            $settingsService = $this->container->get('settings');
            $settings = [
                'betterlinks_enabled' => (bool) $settingsService->get('betterlinks_enabled', false),
                'betterlinks_api_key' => (string) $settingsService->get('betterlinks_api_key', ''),
                'betterlinks_shorten_urls' => (bool) $settingsService->get('betterlinks_shorten_urls', true),
                'betterlinks_add_tracking' => (bool) $settingsService->get('betterlinks_add_tracking', true),
                'betterlinks_custom_tracking' => (array) $settingsService->get('betterlinks_custom_tracking', []),
            ];

            $urlFilter = new \HtmlSocialShare\Integrations\BetterLinks\BetterLinksUrlFilter($integration, $settings);
            $urlFilter->register();
        });
    }

    /**
     * Get list of loaded integrations
     *
     * @return array Array of loaded integration names
     */
    public function getLoadedIntegrations()
    {
        $loaded = [];
        foreach ($this->integrations as $slug => $integration) {
            if ($integration['loaded']) {
                $loaded[] = $integration['name'];
            }
        }
        return $loaded;
    }

    /**
     * Get list of available integrations (whether loaded or not)
     *
     * @return array Array of all registered integrations with status
     */
    public function getAvailableIntegrations()
    {
        $available = [];
        foreach ($this->integrations as $slug => $integration) {
            $available[$slug] = [
                'name' => $integration['name'],
                'loaded' => $integration['loaded'],
                'available' => $this->checkIntegrationDependencies($slug)
            ];
        }
        return $available;
    }
}