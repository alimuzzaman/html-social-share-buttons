<?php
namespace HtmlSocialShare\Rest;

use HtmlSocialShare\SettingsInterface;
use HtmlSocialShare\ProfileManagerInterface;

/**
 * REST API Service for registering REST endpoints
 *
 * @since 3.1.0
 */
class RestApiService
{
    private SettingsInterface $settings;
    private ProfileManagerInterface $profileManager;
    private SettingsController $settingsController;

    public function __construct(SettingsInterface $settings, ProfileManagerInterface $profileManager)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
        $this->settingsController = new SettingsController($settings, $profileManager);
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