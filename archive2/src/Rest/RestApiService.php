<?php
namespace HtmlSocialShare\Rest;

use HtmlSocialShare\SettingsInterface;
use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\IconRegistryInterface;

/**
 * REST API Service for registering REST endpoints
 *
 * @since 3.1.0
 */
class RestApiService
{
    private SettingsInterface $settings;
    private ProfileManagerInterface $profileManager;
    private IconRegistryInterface $iconRegistry;
    private SettingsController $settingsController;

    public function __construct(SettingsInterface $settings, ProfileManagerInterface $profileManager, IconRegistryInterface $iconRegistry)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->iconRegistry = $iconRegistry;
        $this->settingsController = new SettingsController($settings, $profileManager, $iconRegistry);
    }

    /**
     * Initialize REST API hooks
     */
    public function init(): void
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register all REST API routes
     */
    public function register_routes(): void
    {
        $this->settingsController->register_routes();
    }
}