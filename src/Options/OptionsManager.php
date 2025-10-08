<?php
/**
 * Options Manager
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Options;

/**
 * Manages plugin options
 */
class OptionsManager {
    /**
     * @var string Option name in database
     */
    private const OPTION_NAME = 'zm_shbt_fld';
    
    /**
     * @var array|null Cached options
     */
    private ?array $options = null;
    
    /**
     * @var array Default options
     */
    private array $defaults = [
        'title' => 'Share this',
        'excludes' => '',
        'g_analytics' => false,
        'auto_hide_btn' => true,
        'use_port' => true,
        'nofollow' => false,
        'iconset' => 'default',
        'iconset_type' => 'square',
        'show_in' => [
            'show_left' => '',
            'show_right' => '',
            'show_before_post' => '',
            'show_after_post' => '',
        ],
        'icons' => [
            'facebook' => '1',
            'twitter' => '1',
            'linkedin' => '1',
            'googlepluse' => '0',
            'pinterest' => '0',
            'mail' => '0',
        ],
    ];
    
    /**
     * Get an option value
     *
     * @param string|null $key Option key, or null to get all options
     * @param mixed $default Default value
     * @return mixed
     */
    public function get(?string $key = null, $default = null) {
        if ($this->options === null) {
            $this->loadOptions();
        }
        
        if ($key === null) {
            return $this->options;
        }
        
        return $this->options[$key] ?? $default;
    }
    
    /**
     * Get all options
     *
     * @return array
     */
    public function getAll(): array {
        if ($this->options === null) {
            $this->loadOptions();
        }
        
        return $this->options;
    }
    
    /**
     * Update options
     *
     * @param array $options Options to update
     * @return bool
     */
    public function update(array $options): bool {
        $sanitized = $this->sanitize($options);
        $result = update_option(self::OPTION_NAME, $sanitized);
        
        if ($result) {
            $this->options = $sanitized;
        }
        
        return $result;
    }
    
    /**
     * Sanitize options
     *
     * @param array $options Raw options
     * @return array Sanitized options
     */
    public function sanitize(array $options): array {
        $sanitized = [];
        
        // Title
        $sanitized['title'] = isset($options['title']) 
            ? sanitize_text_field($options['title']) 
            : $this->defaults['title'];
        
        // Excludes (comma-separated post IDs)
        $sanitized['excludes'] = isset($options['excludes']) 
            ? sanitize_text_field($options['excludes']) 
            : '';
        
        // Boolean options
        $sanitized['g_analytics'] = !empty($options['g_analytics']);
        $sanitized['auto_hide_btn'] = isset($options['auto_hide_btn']) 
            ? !empty($options['auto_hide_btn']) 
            : $this->defaults['auto_hide_btn'];
        $sanitized['use_port'] = isset($options['use_port']) 
            ? !empty($options['use_port']) 
            : $this->defaults['use_port'];
        $sanitized['nofollow'] = !empty($options['nofollow']);
        
        // Iconset and type
        $sanitized['iconset'] = isset($options['iconset']) 
            ? sanitize_key($options['iconset']) 
            : $this->defaults['iconset'];
        $sanitized['iconset_type'] = isset($options['iconset_type']) 
            ? sanitize_key($options['iconset_type']) 
            : $this->defaults['iconset_type'];
        
        // Show in placements
        $sanitized['show_in'] = [];
        if (isset($options['show_in']) && is_array($options['show_in'])) {
            foreach (['show_left', 'show_right', 'show_before_post', 'show_after_post'] as $placement) {
                $sanitized['show_in'][$placement] = isset($options['show_in'][$placement]) 
                    ? sanitize_key($options['show_in'][$placement]) 
                    : '';
            }
        } else {
            $sanitized['show_in'] = $this->defaults['show_in'];
        }
        
        // Icons (enabled networks)
        $sanitized['icons'] = [];
        if (isset($options['icons'])) {
            // Handle array format
            if (is_array($options['icons'])) {
                foreach ($options['icons'] as $network => $enabled) {
                    $sanitized['icons'][sanitize_key($network)] = !empty($enabled) ? '1' : '0';
                }
            }
            // Handle comma-separated string format
            elseif (is_string($options['icons'])) {
                $networks = explode(',', $options['icons']);
                foreach ($networks as $network) {
                    $network = trim($network);
                    if (!empty($network)) {
                        $sanitized['icons'][sanitize_key($network)] = '1';
                    }
                }
            }
        }
        
        // Ensure at least default icons if none specified
        if (empty($sanitized['icons'])) {
            $sanitized['icons'] = $this->defaults['icons'];
        }
        
        return $sanitized;
    }
    
    /**
     * Get default options
     *
     * @return array
     */
    public function getDefaults(): array {
        return $this->defaults;
    }
    
    /**
     * Load options from database
     */
    private function loadOptions(): void {
        $stored = get_option(self::OPTION_NAME, []);
        
        if (!is_array($stored)) {
            $stored = [];
        }
        
        // Merge with defaults
        $this->options = wp_parse_args($stored, $this->defaults);
    }
    
    /**
     * Parse excludes string to array
     *
     * @param string $excludes Comma-separated post IDs
     * @return array Array of post IDs
     */
    public function parseExcludes(string $excludes): array {
        if (empty($excludes)) {
            return [];
        }
        
        $ids = explode(',', $excludes);
        $ids = array_map('trim', $ids);
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids);
        
        return array_values($ids);
    }
    
    /**
     * Check if current post is excluded
     *
     * @param int|null $postId Post ID (null for current post)
     * @return bool
     */
    public function isPostExcluded(?int $postId = null): bool {
        if ($postId === null) {
            $postId = get_the_ID();
        }
        
        if (!$postId) {
            return false;
        }
        
        $excludes = $this->parseExcludes($this->get('excludes', ''));
        return in_array($postId, $excludes, true);
    }
}
