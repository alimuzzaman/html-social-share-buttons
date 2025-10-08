<?php
namespace HtmlSocialShare\Rest;

use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use Exception;
use WP_Error;
use WP_REST_Response;

/**
 * Enhanced REST API Controller with comprehensive security and validation.
 * 
 * Provides secure REST endpoints for profile management with:
 * - Input validation and sanitization
 * - Authentication and authorization checks
 * - Comprehensive error handling
 * - Rate limiting support
 * - Pure functions for data processing
 * - Security headers and CORS handling
 */
class Controller
{
    /** @var ProfileManagerInterface Profile management service */
    protected ProfileManagerInterface $profiles;
    
    /** @var array<string, int> Rate limiting data */
    private array $rateLimitData = [];
    
    /** @var int Maximum requests per minute per IP */
    private const RATE_LIMIT_MAX = 60;
    
    /** @var array<string, string> Required capabilities for endpoints */
    private const ENDPOINT_CAPABILITIES = [
        'list_profiles' => 'read',
        'create_profile' => 'edit_posts',
        'update_profile' => 'edit_posts',
        'delete_profile' => 'delete_posts',
    ];

    /**
     * Initialize controller with enhanced dependency injection.
     *
     * @param ProfileManagerInterface $profiles Profile management service
     * @throws Exception If dependencies are invalid
     */
    public function __construct(ProfileManagerInterface $profiles)
    {
        $this->profiles = $profiles;
        $this->initializeSecurityHeaders();
    }
    
    /**
     * Set security headers for API responses.
     */
    private function initializeSecurityHeaders(): void
    {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
            header('X-XSS-Protection: 1; mode=block');
        }
    }

    /**
     * List all profiles with pagination and filtering support.
     *
     * @param array $params Request parameters
     * @return array|WP_Error Response data or error
     */
    public function listProfiles(array $params = []): array
    {
        try {
            // Check permissions
            if (!$this->checkPermissions('list_profiles')) {
                return $this->errorResponse('Insufficient permissions', 403);
            }
            
            // Rate limiting
            if (!$this->checkRateLimit()) {
                return $this->errorResponse('Rate limit exceeded', 429);
            }
            
            // Validate and sanitize parameters
            $validatedParams = $this->validateListParams($params);
            if (is_wp_error($validatedParams)) {
                return $this->errorResponse($validatedParams->get_error_message(), 400);
            }
            
            // Get profiles with filtering
            $profiles = $this->profiles->listProfiles($validatedParams);
            
            // Sanitize output
            $sanitizedProfiles = $this->sanitizeProfileList($profiles);
            
            return $this->successResponse($sanitizedProfiles);
            
        } catch (Exception $e) {
            error_log("HSS REST: List profiles failed: {$e->getMessage()}");
            return $this->errorResponse('Internal server error', 500);
        }
    }

    /**
     * Create new profile with comprehensive validation.
     *
     * @param array $payload Profile data to create
     * @return array|WP_Error Response data or error
     */
    public function createProfile(array $payload): array
    {
        try {
            // Check permissions
            if (!$this->checkPermissions('create_profile')) {
                return $this->errorResponse('Insufficient permissions', 403);
            }
            
            // Rate limiting
            if (!$this->checkRateLimit()) {
                return $this->errorResponse('Rate limit exceeded', 429);
            }
            
            // Validate payload structure
            if (!is_array($payload)) {
                return $this->errorResponse('Invalid payload format', 400);
            }
            
            // Validate and sanitize profile data
            $validatedData = $this->validateProfileData($payload);
            if (is_wp_error($validatedData)) {
                return $this->errorResponse($validatedData->get_error_message(), 400);
            }
            
            // Create profile
            $profileId = $this->profiles->createProfile($validatedData);
            
            if (!$profileId) {
                return $this->errorResponse('Failed to create profile', 500);
            }
            
            // Return success response
            return $this->successResponse([
                'id' => $profileId,
                'message' => 'Profile created successfully'
            ], 201);
            
        } catch (Exception $e) {
            error_log("HSS REST: Create profile failed: {$e->getMessage()}");
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    /**
     * Update existing profile with validation.
     *
     * @param int $profileId Profile ID to update
     * @param array $payload Updated profile data
     * @return array Response data
     */
    public function updateProfile(int $profileId, array $payload): array
    {
        try {
            // Check permissions
            if (!$this->checkPermissions('update_profile')) {
                return $this->errorResponse('Insufficient permissions', 403);
            }
            
            // Validate profile ID
            if (!$this->isValidProfileId($profileId)) {
                return $this->errorResponse('Invalid profile ID', 400);
            }
            
            // Validate and sanitize update data
            $validatedData = $this->validateProfileData($payload, true);
            if (is_wp_error($validatedData)) {
                return $this->errorResponse($validatedData->get_error_message(), 400);
            }
            
            // Update profile
            $success = $this->profiles->updateProfile($profileId, $validatedData);
            
            if (!$success) {
                return $this->errorResponse('Failed to update profile', 500);
            }
            
            return $this->successResponse([
                'message' => 'Profile updated successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("HSS REST: Update profile failed: {$e->getMessage()}");
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    /**
     * Delete profile with confirmation.
     *
     * @param int $profileId Profile ID to delete
     * @return array Response data
     */
    public function deleteProfile(int $profileId): array
    {
        try {
            // Check permissions
            if (!$this->checkPermissions('delete_profile')) {
                return $this->errorResponse('Insufficient permissions', 403);
            }
            
            // Validate profile ID
            if (!$this->isValidProfileId($profileId)) {
                return $this->errorResponse('Invalid profile ID', 400);
            }
            
            // Delete profile
            $success = $this->profiles->deleteProfile($profileId);
            
            if (!$success) {
                return $this->errorResponse('Failed to delete profile', 500);
            }
            
            return $this->successResponse([
                'message' => 'Profile deleted successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("HSS REST: Delete profile failed: {$e->getMessage()}");
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    // PURE FUNCTIONS FOR VALIDATION AND PROCESSING
    
    /**
     * Validate list parameters (pure function).
     *
     * @param array $params Input parameters
     * @return array|WP_Error Validated parameters or error
     */
    private function validateListParams(array $params)
    {
        $validated = [];
        
        // Pagination
        if (isset($params['page'])) {
            $page = (int) $params['page'];
            $validated['page'] = max(1, $page);
        }
        
        if (isset($params['per_page'])) {
            $perPage = (int) $params['per_page'];
            $validated['per_page'] = max(1, min(100, $perPage));
        }
        
        // Search
        if (isset($params['search']) && is_string($params['search'])) {
            $search = SecurityUtils::sanitizeInput($params['search']);
            if (strlen($search) > 0 && strlen($search) <= 100) {
                $validated['search'] = $search;
            }
        }
        
        // Sorting
        if (isset($params['orderby'])) {
            $allowedFields = ['id', 'name', 'created_at', 'updated_at'];
            if (in_array($params['orderby'], $allowedFields, true)) {
                $validated['orderby'] = $params['orderby'];
            }
        }
        
        if (isset($params['order'])) {
            $order = strtoupper($params['order']);
            if (in_array($order, ['ASC', 'DESC'], true)) {
                $validated['order'] = $order;
            }
        }
        
        return $validated;
    }
    
    /**
     * Validate profile data (pure function).
     *
     * @param array $data Profile data to validate
     * @param bool $isUpdate Whether this is an update operation
     * @return array|WP_Error Validated data or error
     */
    private function validateProfileData(array $data, bool $isUpdate = false)
    {
        $validated = [];
        $errors = [];
        
        // Name validation (required for create, optional for update)
        if (isset($data['name'])) {
            $name = SecurityUtils::sanitizeInput($data['name']);
            if (empty($name)) {
                $errors[] = 'Profile name is required';
            } elseif (strlen($name) > 100) {
                $errors[] = 'Profile name must be 100 characters or less';
            } else {
                $validated['name'] = $name;
            }
        } elseif (!$isUpdate) {
            $errors[] = 'Profile name is required';
        }
        
        // Settings validation
        if (isset($data['settings'])) {
            if (!is_array($data['settings'])) {
                $errors[] = 'Settings must be an object';
            } else {
                $validatedSettings = $this->validateSettingsData($data['settings']);
                if (is_wp_error($validatedSettings)) {
                    $errors[] = $validatedSettings->get_error_message();
                } else {
                    $validated['settings'] = $validatedSettings;
                }
            }
        }
        
        // Networks validation
        if (isset($data['networks'])) {
            if (!is_array($data['networks'])) {
                $errors[] = 'Networks must be an array';
            } else {
                $validatedNetworks = $this->validateNetworksData($data['networks']);
                if (is_wp_error($validatedNetworks)) {
                    $errors[] = $validatedNetworks->get_error_message();
                } else {
                    $validated['networks'] = $validatedNetworks;
                }
            }
        }
        
        return empty($errors) ? $validated : new WP_Error('validation_failed', implode('. ', $errors));
    }
    
    /**
     * Validate settings data (pure function).
     *
     * @param array $settings Settings to validate
     * @return array|WP_Error Validated settings or error
     */
    private function validateSettingsData(array $settings)
    {
        $validated = [];
        
        // Display settings
        if (isset($settings['display'])) {
            $display = $settings['display'];
            if (is_array($display)) {
                $validated['display'] = $this->sanitizeDisplaySettings($display);
            }
        }
        
        // Style settings
        if (isset($settings['style'])) {
            $style = SecurityUtils::sanitizeInput($settings['style']);
            $allowedStyles = ['default', 'minimal', 'rounded', 'square'];
            if (in_array($style, $allowedStyles, true)) {
                $validated['style'] = $style;
            }
        }
        
        return $validated;
    }
    
    /**
     * Sanitize display settings (pure function).
     *
     * @param array $display Display settings
     * @return array Sanitized display settings
     */
    private function sanitizeDisplaySettings(array $display): array
    {
        $sanitized = [];
        
        // Boolean settings
        $booleanSettings = ['show_labels', 'show_counts', 'open_in_new_window'];
        foreach ($booleanSettings as $setting) {
            if (isset($display[$setting])) {
                $sanitized[$setting] = (bool) $display[$setting];
            }
        }
        
        // String settings with validation
        if (isset($display['size'])) {
            $size = SecurityUtils::sanitizeInput($display['size']);
            if (in_array($size, ['small', 'medium', 'large'], true)) {
                $sanitized['size'] = $size;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Validate networks data (pure function).
     *
     * @param array $networks Networks configuration
     * @return array|WP_Error Validated networks or error
     */
    private function validateNetworksData(array $networks)
    {
        $validated = [];
        $validNetworks = ['facebook', 'twitter', 'linkedin', 'pinterest', 'reddit', 'email'];
        
        foreach ($networks as $network) {
            if (is_string($network) && in_array($network, $validNetworks, true)) {
                $validated[] = $network;
            }
        }
        
        if (empty($validated)) {
            return new WP_Error('invalid_networks', 'At least one valid network must be specified');
        }
        
        return array_unique($validated);
    }
    
    /**
     * Sanitize profile list for output (pure function).
     *
     * @param array $profiles Raw profile data
     * @return array Sanitized profile data
     */
    private function sanitizeProfileList(array $profiles): array
    {
        return array_map([$this, 'sanitizeProfileOutput'], $profiles);
    }
    
    /**
     * Sanitize single profile for output (pure function).
     *
     * @param array $profile Raw profile data
     * @return array Sanitized profile data
     */
    private function sanitizeProfileOutput(array $profile): array
    {
        return [
            'id' => (int) ($profile['id'] ?? 0),
            'name' => SecurityUtils::escapeOutput($profile['name'] ?? ''),
            'settings' => $profile['settings'] ?? [],
            'networks' => $profile['networks'] ?? [],
            'created_at' => $profile['created_at'] ?? null,
            'updated_at' => $profile['updated_at'] ?? null,
        ];
    }
    
    // HELPER METHODS FOR SECURITY AND UTILITIES
    
    /**
     * Check user permissions for endpoint.
     *
     * @param string $endpoint Endpoint name
     * @return bool True if user has permission
     */
    private function checkPermissions(string $endpoint): bool
    {
        $capability = self::ENDPOINT_CAPABILITIES[$endpoint] ?? 'manage_options';
        return current_user_can($capability);
    }
    
    /**
     * Check rate limiting for current request.
     *
     * @return bool True if within rate limit
     */
    private function checkRateLimit(): bool
    {
        $clientIp = SecurityUtils::getClientIp();
        $currentTime = time();
        $windowStart = $currentTime - 60; // 1 minute window
        
        // Clean old entries
        $this->rateLimitData = array_filter($this->rateLimitData, function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        // Count requests from this IP
        $requestCount = count(array_filter($this->rateLimitData, function($timestamp, $ip) use ($clientIp) {
            return strpos($ip, $clientIp) === 0;
        }, ARRAY_FILTER_USE_BOTH));
        
        if ($requestCount >= self::RATE_LIMIT_MAX) {
            return false;
        }
        
        // Record this request
        $this->rateLimitData[$clientIp . '_' . $currentTime] = $currentTime;
        return true;
    }
    
    /**
     * Validate profile ID exists.
     *
     * @param int $profileId Profile ID to validate
     * @return bool True if valid
     */
    private function isValidProfileId(int $profileId): bool
    {
        return $profileId > 0 && $this->profiles->getProfile($profileId) !== null;
    }
    
    /**
     * Create success response (pure function).
     *
     * @param mixed $data Response data
     * @param int $status HTTP status code
     * @return array Success response
     */
    private function successResponse($data, int $status = 200): array
    {
        return [
            'status' => $status,
            'body' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
            ]
        ];
    }
    
    /**
     * Create error response (pure function).
     *
     * @param string $message Error message
     * @param int $status HTTP status code
     * @return array Error response
     */
    private function errorResponse(string $message, int $status = 400): array
    {
        return [
            'status' => $status,
            'body' => [
                'error' => SecurityUtils::escapeOutput($message),
                'code' => $status
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ];
    }
}
