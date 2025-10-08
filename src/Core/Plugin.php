<?php
/**
 * Plugin Bootstrap
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Core;

use HtmlSocialShare\IconSystem\IconRegistry;
use HtmlSocialShare\Services\UrlBuilder;
use HtmlSocialShare\Options\OptionsManager;
use HtmlSocialShare\Renderers\ButtonRenderer;
use HtmlSocialShare\Renderers\CssGenerator;
use HtmlSocialShare\Services\PlacementManager;

/**
 * Main plugin class that bootstraps everything
 */
class Plugin {
    /**
     * @var Plugin Singleton instance
     */
    private static ?Plugin $instance = null;
    
    /**
     * @var array Service container
     */
    private array $services = [];
    
    /**
     * Get singleton instance
     *
     * @return Plugin
     */
    public static function getInstance(): Plugin {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Private constructor (singleton pattern)
     */
    private function __construct() {
        // Constructor intentionally left empty
        // Initialization happens in init() method
    }
    
    /**
     * Initialize the plugin
     */
    public function init(): void {
        // Register services
        $this->registerServices();
        
        // Register WordPress hooks
        $this->registerHooks();
    }
    
    /**
     * Register all services in the container
     */
    private function registerServices(): void {
        // Core services
        $this->services['iconRegistry'] = new IconRegistry();
        $this->services['optionsManager'] = new OptionsManager();
        
        // URL builder
        $this->services['urlBuilder'] = new UrlBuilder(
            $this->services['iconRegistry']
        );
        
        // Renderers
        $this->services['buttonRenderer'] = new ButtonRenderer(
            $this->services['iconRegistry'],
            $this->services['urlBuilder'],
            $this->services['optionsManager']
        );
        
        $this->services['cssGenerator'] = new CssGenerator(
            $this->services['iconRegistry'],
            $this->services['optionsManager']
        );
        
        // Placement manager
        $this->services['placementManager'] = new PlacementManager(
            $this->services['buttonRenderer'],
            $this->services['optionsManager']
        );
    }
    
    /**
     * Register WordPress hooks
     */
    private function registerHooks(): void {
        // Frontend hooks
        add_action('wp_footer', [$this, 'outputCss'], 5);
        add_action('wp_footer', [$this, 'outputFixedPlacements'], 10);
        add_filter('the_content', [$this, 'filterContent'], 10);
        
        // Allow other plugins to hook in after initialization
        do_action('html_social_share_init', $this);
    }
    
    /**
     * Output CSS in footer
     */
    public function outputCss(): void {
        $cssGenerator = $this->getService('cssGenerator');
        if ($cssGenerator) {
            $cssGenerator->output();
        }
    }
    
    /**
     * Output fixed placements (left/right) in footer
     */
    public function outputFixedPlacements(): void {
        $placementManager = $this->getService('placementManager');
        if ($placementManager) {
            $placementManager->outputFixedPlacements();
        }
    }
    
    /**
     * Filter the_content to add before/after post buttons
     *
     * @param string $content Post content
     * @return string Modified content
     */
    public function filterContent(string $content): string {
        $placementManager = $this->getService('placementManager');
        if ($placementManager) {
            return $placementManager->filterContent($content);
        }
        
        return $content;
    }
    
    /**
     * Get a service from the container
     *
     * @param string $key Service key
     * @return mixed|null Service instance or null
     */
    public function getService(string $key) {
        return $this->services[$key] ?? null;
    }
    
    /**
     * Plugin activation hook
     */
    public function activate(): void {
        // Set default options if not exist
        $optionsManager = $this->getService('optionsManager');
        if ($optionsManager) {
            $existing = $optionsManager->get(null);
            if (empty($existing) || !is_array($existing)) {
                $optionsManager->update($optionsManager->getDefaults());
            }
        }
        
        // Flush rewrite rules if needed
        flush_rewrite_rules();
        
        do_action('html_social_share_activated');
    }
    
    /**
     * Plugin deactivation hook
     */
    public function deactivate(): void {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        do_action('html_social_share_deactivated');
    }
}
