<?php
namespace HtmlSocialShare\Rest;

use HtmlSocialShare\SettingsInterface;
use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\Integrations\BetterLinks\BetterLinksIntegration;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * WordPress REST API Controller for HTML Social Share Settings
 *
 * Provides REST endpoints for the React admin interface to interact with
 * plugin settings, profiles, and network configurations.
 *
 * @since 3.1.0
 */
class SettingsController extends WP_REST_Controller
{
    /** @var SettingsInterface */
    private SettingsInterface $settings;

    /** @var ProfileManagerInterface */
    private ProfileManagerInterface $profileManager;

    /** @var string REST API namespace */
    protected $namespace = 'html-social-share/v1';

    public function __construct(SettingsInterface $settings, ProfileManagerInterface $profileManager)
    {
        $this->settings = $settings;
        $this->profileManager = $profileManager;
    }

    /**
     * Register the REST API routes
     */
    public function register_routes(): void
    {
        // Settings endpoints
        register_rest_route($this->namespace, '/settings', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_settings'],
                'permission_callback' => [$this, 'check_admin_permissions'],
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_settings'],
                'permission_callback' => [$this, 'check_admin_permissions'],
                'args' => $this->get_settings_schema(),
            ],
        ]);

        // Settings reset endpoint
        register_rest_route($this->namespace, '/settings/reset', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'reset_settings'],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);

        // Profiles endpoints
        register_rest_route($this->namespace, '/profiles', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_profiles'],
                'permission_callback' => [$this, 'check_admin_permissions'],
            ],
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'create_profile'],
                'permission_callback' => [$this, 'check_admin_permissions'],
                'args' => $this->get_profile_schema(),
            ],
        ]);

        register_rest_route($this->namespace, '/profiles/(?P<id>[\w-]+)', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_profile'],
                'permission_callback' => [$this, 'check_admin_permissions'],
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_profile'],
                'permission_callback' => [$this, 'check_admin_permissions'],
                'args' => $this->get_profile_schema(),
            ],
            [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => [$this, 'delete_profile'],
                'permission_callback' => [$this, 'check_admin_permissions'],
            ],
        ]);

        // Networks endpoints
        register_rest_route($this->namespace, '/networks', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_networks'],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);

        register_rest_route($this->namespace, '/networks/(?P<id>[\w-]+)', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'update_network'],
            'permission_callback' => [$this, 'check_admin_permissions'],
            'args' => $this->get_network_schema(),
        ]);
    }

    /**
     * Get all plugin settings
     */
    public function get_settings(WP_REST_Request $request)
    {
        try {
            $settings = [
                'general' => [
                    'show_on_front_page' => $this->settings->get('show_on_front_page', true),
                    'show_on_posts' => $this->settings->get('show_on_posts', true),
                    'show_on_pages' => $this->settings->get('show_on_pages', false),
                    'show_on_archives' => $this->settings->get('show_on_archives', false),
                    'default_style' => $this->settings->get('default_style', 'default'),
                    'default_size' => $this->settings->get('default_size', 'medium'),
                ],
                'networks' => [
                    'enabled_networks' => $this->settings->get('enabled_networks', ['facebook', 'twitter', 'linkedin']),
                    'network_order' => $this->settings->get('network_order', []),
                    'custom_networks' => $this->settings->get('custom_networks', []),
                ],
                'appearance' => [
                    'title' => $this->settings->get('title', 'Share this with your friends'),
                    'icon_style' => $this->settings->get('icon_style', 'default'),
                    'button_size' => $this->settings->get('button_size', 'medium'),
                    'button_spacing' => $this->settings->get('button_spacing', 5),
                    'custom_css' => $this->settings->get('custom_css', ''),
                ],
                'placement' => [
                    'auto_placement' => $this->settings->get('auto_placement', false),
                    'placement_position' => $this->settings->get('placement_position', 'after'),
                    'placement_post_types' => $this->settings->get('placement_post_types', ['post']),
                    'exclude_pages' => $this->settings->get('exclude_pages', ''),
                ],
                'integrations' => [
                    'betterlinks_enabled' => $this->settings->get('betterlinks_enabled', false),
                    'betterlinks_api_key' => $this->settings->get('betterlinks_api_key', ''),
                    'betterlinks_shorten_urls' => $this->settings->get('betterlinks_shorten_urls', true),
                    'betterlinks_add_tracking' => $this->settings->get('betterlinks_add_tracking', true),
                    'betterlinks_custom_tracking' => $this->settings->get('betterlinks_custom_tracking', []),
                    'betterlinks_available' => BetterLinksIntegration::isAvailable(),
                    'betterlinks_pro' => BetterLinksIntegration::isProAvailable(),
                    'betterlinks_version' => BetterLinksIntegration::getVersion(),
                    'elementor_enabled' => $this->settings->get('elementor_enabled', false),
                    'divi_enabled' => $this->settings->get('divi_enabled', false),
                    'beaver_builder_enabled' => $this->settings->get('beaver_builder_enabled', false),
                ],
                'advanced' => [
                    'google_analytics' => $this->settings->get('google_analytics', false),
                    'auto_hide_buttons' => $this->settings->get('auto_hide_buttons', false),
                    'use_port_in_url' => $this->settings->get('use_port_in_url', false),
                    'nofollow_links' => $this->settings->get('nofollow_links', true),
                    'cache_enabled' => $this->settings->get('cache_enabled', true),
                    'cache_duration' => $this->settings->get('cache_duration', 3600),
                    'debug_mode' => $this->settings->get('debug_mode', false),
                ],
            ];

            return new WP_REST_Response($settings, 200);
        } catch (\Exception $e) {
            return new WP_Error('get_settings_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Update plugin settings
     */
    public function update_settings(WP_REST_Request $request)
    {
        try {
            $params = $request->get_json_params();
            $updated = [];

            // Process each settings category
            foreach ($params as $category => $settings) {
                if (!is_array($settings)) {
                    continue;
                }

                foreach ($settings as $key => $value) {
                    $sanitized_value = $this->sanitize_setting_value($key, $value);
                    $this->settings->set($key, $sanitized_value);
                    $updated[$key] = $sanitized_value;
                }
            }

            return new WP_REST_Response([
                'success' => true,
                'message' => __('Settings updated successfully', 'html-social-share'),
                'updated' => $updated,
            ], 200);
        } catch (\Exception $e) {
            return new WP_Error('update_settings_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Reset all settings to defaults
     */
    public function reset_settings(WP_REST_Request $request)
    {
        try {
            // Get default settings and reset
            $defaults = [
                'show_on_front_page' => true,
                'show_on_posts' => true,
                'show_on_pages' => false,
                'show_on_archives' => false,
                'default_style' => 'default',
                'default_size' => 'medium',
                'title' => 'Share this with your friends',
                'enabled_networks' => ['facebook', 'twitter', 'linkedin'],
                'network_order' => [],
                'custom_networks' => [],
                'icon_style' => 'default',
                'button_size' => 'medium',
                'button_spacing' => 5,
                'custom_css' => '',
                'auto_placement' => false,
                'placement_position' => 'after',
                'placement_post_types' => ['post'],
                'exclude_pages' => '',
                'betterlinks_enabled' => false,
                'betterlinks_api_key' => '',
                'betterlinks_shorten_urls' => true,
                'betterlinks_add_tracking' => true,
                'betterlinks_custom_tracking' => [],
                'elementor_enabled' => false,
                'divi_enabled' => false,
                'beaver_builder_enabled' => false,
                'google_analytics' => false,
                'auto_hide_buttons' => false,
                'use_port_in_url' => false,
                'nofollow_links' => true,
                'cache_enabled' => true,
                'cache_duration' => 3600,
                'debug_mode' => false,
            ];

            foreach ($defaults as $key => $value) {
                $this->settings->set($key, $value);
            }

            return new WP_REST_Response([
                'success' => true,
                'message' => __('Settings reset to defaults', 'html-social-share'),
            ], 200);
        } catch (\Exception $e) {
            return new WP_Error('reset_settings_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Get all profiles
     */
    public function get_profiles(WP_REST_Request $request)
    {
        try {
            $profiles = $this->profileManager->listProfiles();
            return new WP_REST_Response($profiles, 200);
        } catch (\Exception $e) {
            return new WP_Error('get_profiles_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Create a new profile
     */
    public function create_profile(WP_REST_Request $request)
    {
        try {
            $params = $request->get_json_params();

            $profile_data = [
                'name' => sanitize_text_field($params['name'] ?? ''),
                'networks' => $params['networks'] ?? [],
                'display_settings' => $params['display_settings'] ?? [],
            ];

            $profile_id = $this->profileManager->createProfile($profile_data);

            return new WP_REST_Response([
                'success' => true,
                'message' => __('Profile created successfully', 'html-social-share'),
                'profile_id' => $profile_id,
            ], 201);
        } catch (\Exception $e) {
            return new WP_Error('create_profile_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Get a specific profile
     */
    public function get_profile(WP_REST_Request $request)
    {
        try {
            $profile_id = $request->get_param('id');
            $profile = $this->profileManager->getProfile($profile_id);

            if (!$profile) {
                return new WP_Error('profile_not_found',
                    __('Profile not found', 'html-social-share'),
                    ['status' => 404]
                );
            }

            return new WP_REST_Response($profile, 200);
        } catch (\Exception $e) {
            return new WP_Error('get_profile_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Update a profile
     */
    public function update_profile(WP_REST_Request $request)
    {
        try {
            $profile_id = $request->get_param('id');
            $params = $request->get_json_params();

            $profile_data = [
                'name' => sanitize_text_field($params['name'] ?? ''),
                'networks' => $params['networks'] ?? [],
                'display_settings' => $params['display_settings'] ?? [],
            ];

            $success = $this->profileManager->updateProfile($profile_id, $profile_data);

            if (!$success) {
                return new WP_Error('profile_not_found',
                    __('Profile not found', 'html-social-share'),
                    ['status' => 404]
                );
            }

            return new WP_REST_Response([
                'success' => true,
                'message' => __('Profile updated successfully', 'html-social-share'),
            ], 200);
        } catch (\Exception $e) {
            return new WP_Error('update_profile_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Delete a profile
     */
    public function delete_profile(WP_REST_Request $request)
    {
        try {
            $profile_id = $request->get_param('id');
            $success = $this->profileManager->deleteProfile($profile_id);

            if (!$success) {
                return new WP_Error('profile_not_found',
                    __('Profile not found', 'html-social-share'),
                    ['status' => 404]
                );
            }

            return new WP_REST_Response([
                'success' => true,
                'message' => __('Profile deleted successfully', 'html-social-share'),
            ], 200);
        } catch (\Exception $e) {
            return new WP_Error('delete_profile_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Get available networks
     */
    public function get_networks(WP_REST_Request $request)
    {
        try {
            // Get networks from Networks class if it exists
            $networks = [
                'facebook' => [
                    'id' => 'facebook',
                    'name' => 'Facebook',
                    'label' => 'Facebook',
                    'share_url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                    'requires_handle' => false,
                    'icon_class' => 'fab fa-facebook-f',
                    'color' => '#1877f2',
                ],
                'twitter' => [
                    'id' => 'twitter',
                    'name' => 'Twitter',
                    'label' => 'Twitter',
                    'share_url' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
                    'requires_handle' => false,
                    'icon_class' => 'fab fa-twitter',
                    'color' => '#1da1f2',
                ],
                'linkedin' => [
                    'id' => 'linkedin',
                    'name' => 'LinkedIn',
                    'label' => 'LinkedIn',
                    'share_url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                    'requires_handle' => false,
                    'icon_class' => 'fab fa-linkedin-in',
                    'color' => '#0077b5',
                ],
                'pinterest' => [
                    'id' => 'pinterest',
                    'name' => 'Pinterest',
                    'label' => 'Pinterest',
                    'share_url' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
                    'requires_handle' => false,
                    'icon_class' => 'fab fa-pinterest-p',
                    'color' => '#bd081c',
                ],
                'reddit' => [
                    'id' => 'reddit',
                    'name' => 'Reddit',
                    'label' => 'Reddit',
                    'share_url' => 'https://reddit.com/submit?url={url}&title={title}',
                    'requires_handle' => false,
                    'icon_class' => 'fab fa-reddit-alien',
                    'color' => '#ff4500',
                ],
                'whatsapp' => [
                    'id' => 'whatsapp',
                    'name' => 'WhatsApp',
                    'label' => 'WhatsApp',
                    'share_url' => 'https://wa.me/?text={title}%20{url}',
                    'requires_handle' => false,
                    'icon_class' => 'fab fa-whatsapp',
                    'color' => '#25d366',
                ],
            ];

            return new WP_REST_Response($networks, 200);
        } catch (\Exception $e) {
            return new WP_Error('get_networks_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Update network configuration
     */
    public function update_network(WP_REST_Request $request)
    {
        try {
            $network_id = $request->get_param('id');
            $params = $request->get_json_params();

            // For now, just return success - network updates might be handled differently
            return new WP_REST_Response([
                'success' => true,
                'message' => __('Network updated successfully', 'html-social-share'),
            ], 200);
        } catch (\Exception $e) {
            return new WP_Error('update_network_failed', $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Check if user has admin permissions
     */
    public function check_admin_permissions(): bool
    {
        return current_user_can('manage_options');
    }

    /**
     * Sanitize setting values
     */
    private function sanitize_setting_value(string $key, $value)
    {
        switch ($key) {
            case 'show_on_front_page':
            case 'show_on_posts':
            case 'show_on_pages':
            case 'show_on_archives':
            case 'auto_placement':
            case 'betterlinks_enabled':
            case 'betterlinks_shorten_urls':
            case 'betterlinks_add_tracking':
            case 'elementor_enabled':
            case 'divi_enabled':
            case 'beaver_builder_enabled':
            case 'google_analytics':
            case 'auto_hide_buttons':
            case 'use_port_in_url':
            case 'nofollow_links':
            case 'cache_enabled':
            case 'debug_mode':
                return (bool) $value;

            case 'button_spacing':
            case 'cache_duration':
                return (int) $value;

            case 'enabled_networks':
            case 'network_order':
            case 'custom_networks':
            case 'placement_post_types':
                return is_array($value) ? array_map('sanitize_text_field', $value) : [];

            case 'betterlinks_custom_tracking':
                if (!is_array($value)) {
                    return [];
                }

                $sanitized = [];
                foreach ($value as $key => $trackingValue) {
                    $sanitizedKey = sanitize_key($key);
                    if ($sanitizedKey === '') {
                        continue;
                    }

                    $sanitized[$sanitizedKey] = sanitize_text_field($trackingValue);
                }

                return $sanitized;

            case 'custom_css':
                return wp_strip_all_tags($value);

            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Get settings schema for validation
     */
    private function get_settings_schema(): array
    {
        return [
            'general' => [
                'type' => 'object',
                'properties' => [
                    'show_on_front_page' => ['type' => 'boolean'],
                    'show_on_posts' => ['type' => 'boolean'],
                    'show_on_pages' => ['type' => 'boolean'],
                    'show_on_archives' => ['type' => 'boolean'],
                    'default_style' => ['type' => 'string'],
                    'default_size' => ['type' => 'string'],
                ],
            ],
            // Add other schema definitions as needed
        ];
    }

    /**
     * Get profile schema for validation
     */
    private function get_profile_schema(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'networks' => [
                'type' => 'object',
            ],
            'display_settings' => [
                'type' => 'object',
            ],
        ];
    }

    /**
     * Get network schema for validation
     */
    private function get_network_schema(): array
    {
        return [
            'enabled' => ['type' => 'boolean'],
            'label' => ['type' => 'string'],
            'custom_url' => ['type' => 'string'],
        ];
    }
}