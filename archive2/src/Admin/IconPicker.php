<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\Networks;
use HtmlSocialShare\IconRegistry;

/**
 * Enhanced Icon Picker Component for HTML Social Share
 * 
 * Provides a visual interface for selecting icons and iconsets
 * with preview capabilities and search functionality.
 * 
 * @since 3.0.0
 */
class IconPicker
{
    /**
     * Available icons and their labels
     * 
     * @var array
     */
    private static $icons = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
        'telegram' => 'Telegram',
        'reddit' => 'Reddit',
        'tumblr' => 'Tumblr',
        'vk' => 'VKontakte'
    ];

    /**
     * Render basic icon picker (backward compatibility)
     * 
     * @param string $fieldName Field name
     * @param string $currentValue Current selected value
     * @param array $attributes Additional attributes
     * @return string HTML output
     */
    public static function render(string $fieldName, string $currentValue = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $fieldName;
        $class = $attributes['class'] ?? 'hssb-icon-picker';
        $showSearch = $attributes['show_search'] ?? false;
        $showPreview = $attributes['show_preview'] ?? true;

        $output = '<div class="' . esc_attr($class) . '" id="picker-' . esc_attr($id) . '">';
        
        // Search box
        if ($showSearch) {
            $output .= '<div class="hssb-picker-search">';
            $output .= '<input type="text" class="hssb-icon-search" placeholder="' . esc_attr__('Search icons...', 'html-social-share') . '">';
            $output .= '<span class="dashicons dashicons-search"></span>';
            $output .= '</div>';
        }

        // Hidden input
        $output .= '<input type="hidden" name="' . esc_attr($fieldName) . '" id="' . esc_attr($id) . '" value="' . esc_attr($currentValue) . '">';

        // Icon grid
        $output .= '<div class="hssb-icon-grid">';
        foreach (self::getAvailableIcons() as $iconKey => $iconData) {
            $selected = ($currentValue === $iconKey) ? 'selected' : '';
            $iconUrl = self::getIconUrl($iconKey);
            $label = is_array($iconData) ? $iconData['label'] : $iconData;

            $output .= '<div class="hssb-icon-option ' . $selected . '" data-icon="' . esc_attr($iconKey) . '" data-label="' . esc_attr(strtolower($label)) . '">';
            $output .= '<div class="hssb-icon-display">';
            if ($iconUrl) {
                $output .= '<img src="' . esc_url($iconUrl) . '" alt="' . esc_attr($label) . '" width="32" height="32">';
            } else {
                $output .= '<span class="dashicons dashicons-' . esc_attr(self::getDashicon($iconKey)) . '"></span>';
            }
            $output .= '</div>';
            $output .= '<span class="hssb-icon-label">' . esc_html($label) . '</span>';
            $output .= '</div>';
        }
        $output .= '</div>';

        // Preview section
        if ($showPreview) {
            $output .= '<div class="hssb-icon-preview">';
            $output .= '<strong>' . esc_html__('Preview:', 'html-social-share') . '</strong>';
            $output .= '<div class="hssb-preview-display">';
            if (!empty($currentValue)) {
                $previewIcon = self::getAvailableIcons()[$currentValue] ?? null;
                if ($previewIcon) {
                    $previewUrl = self::getIconUrl($currentValue);
                    $previewLabel = is_array($previewIcon) ? $previewIcon['label'] : $previewIcon;
                    if ($previewUrl) {
                        $output .= '<img src="' . esc_url($previewUrl) . '" alt="' . esc_attr($previewLabel) . '" width="32" height="32">';
                    } else {
                        $output .= '<span class="dashicons dashicons-' . esc_attr(self::getDashicon($currentValue)) . '"></span>';
                    }
                    $output .= ' <span class="preview-label">' . esc_html($previewLabel) . '</span>';
                }
            } else {
                $output .= '<span class="no-selection">' . esc_html__('No icon selected', 'html-social-share') . '</span>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '<p class="description">' . esc_html__('Click an icon to select it for this profile.', 'html-social-share') . '</p>';
        $output .= '</div>';

        // Add CSS and JavaScript
        $output .= self::getStyles();
        $output .= self::getJavaScript($id);

        return $output;
    }

    /**
     * Render advanced icon picker with iconset selection
     * 
     * @param string $fieldName Field name
     * @param string $currentValue Current selected value
     * @param array $attributes Additional attributes
     * @return string HTML output
     */
    public static function renderAdvanced(string $fieldName, string $currentValue = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $fieldName;
        $class = $attributes['class'] ?? 'hssb-icon-picker-advanced';

        $output = '<div class="' . esc_attr($class) . '" id="picker-' . esc_attr($id) . '">';
        
        // Header with iconset selector
        $output .= '<div class="hssb-picker-header">';
        $output .= '<div class="hssb-iconset-selector">';
        $output .= '<label for="iconset-select-' . esc_attr($id) . '">' . esc_html__('Icon Style:', 'html-social-share') . '</label>';
        $output .= '<select id="iconset-select-' . esc_attr($id) . '" class="hssb-iconset-select">';
        $output .= '<option value="default">' . esc_html__('Default', 'html-social-share') . '</option>';
        $output .= '<option value="square">' . esc_html__('Square', 'html-social-share') . '</option>';
        $output .= '<option value="circle">' . esc_html__('Circle', 'html-social-share') . '</option>';
        $output .= '<option value="minimal">' . esc_html__('Minimal', 'html-social-share') . '</option>';
        $output .= '</select>';
        $output .= '</div>';
        
        $output .= '<div class="hssb-picker-search">';
        $output .= '<input type="text" class="hssb-icon-search" placeholder="' . esc_attr__('Search networks...', 'html-social-share') . '">';
        $output .= '<span class="dashicons dashicons-search"></span>';
        $output .= '</div>';
        $output .= '</div>';

        // Call the basic render method with enhanced attributes
        $enhancedAttributes = array_merge($attributes, [
            'class' => $class . ' hssb-advanced',
            'show_search' => false, // Handled in header
            'show_preview' => true
        ]);

        $output .= self::render($fieldName, $currentValue, $enhancedAttributes);
        $output .= '</div>';

        return $output;
    }

    /**
     * Get available icons from Networks class if available
     * 
     * @return array
     */
    private static function getAvailableIcons(): array
    {
        // Try to get from Networks class if available
        if (class_exists('HtmlSocialShare\Networks')) {
            try {
                return Networks::getAvailableNetworks();
            } catch (Exception $e) {
                // Fall back to static list
            }
        }
        
        return self::$icons;
    }

    /**
     * Get icon URL if available
     * 
     * @param string $iconKey Icon key
     * @return string|null Icon URL or null
     */
    private static function getIconUrl(string $iconKey): ?string
    {
        if (defined('HTML_SOCIAL_SHARE_ICONSET_URL')) {
            $iconPath = HTML_SOCIAL_SHARE_ICONSET_URL . 'default_square/' . $iconKey . '.png';
            return $iconPath;
        }
        
        return null;
    }

    /**
     * Get fallback dashicon for network
     * 
     * @param string $network Network key
     * @return string Dashicon name
     */
    private static function getDashicon(string $network): string
    {
        $dashicons = [
            'facebook' => 'facebook-alt',
            'twitter' => 'twitter',
            'linkedin' => 'linkedin',
            'pinterest' => 'pinterest',
            'email' => 'email-alt',
            'whatsapp' => 'whatsapp',
            'telegram' => 'format-chat',
            'reddit' => 'reddit',
            'tumblr' => 'format-image',
            'vk' => 'share',
        ];

        return $dashicons[$network] ?? 'share';
    }

    /**
     * Get CSS styles for icon picker
     * 
     * @return string CSS
     */
    private static function getStyles(): string
    {
        return '
        <style>
        .hssb-icon-picker {
            border: 1px solid #c3c4c7;
            border-radius: 4px;
            background: #fff;
            padding: 15px;
            margin: 10px 0;
        }

        .hssb-picker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }

        .hssb-iconset-selector select {
            min-width: 120px;
        }

        .hssb-picker-search {
            position: relative;
            max-width: 200px;
            flex: 1;
        }

        .hssb-icon-search {
            width: 100%;
            padding: 6px 30px 6px 10px;
            border: 1px solid #c3c4c7;
            border-radius: 3px;
        }

        .hssb-picker-search .dashicons {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #646970;
            pointer-events: none;
        }

        .hssb-icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
            max-height: 250px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #f0f0f1;
            border-radius: 3px;
        }

        .hssb-icon-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border: 2px solid #f0f0f1;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
            text-align: center;
        }

        .hssb-icon-option:hover {
            border-color: #007cba;
            background: #f6f7f7;
        }

        .hssb-icon-option.selected {
            border-color: #007cba;
            background: #e7f3ff;
            box-shadow: 0 0 0 1px #007cba;
        }

        .hssb-icon-option.hidden {
            display: none;
        }

        .hssb-icon-display {
            width: 32px;
            height: 32px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hssb-icon-display img {
            max-width: 100%;
            max-height: 100%;
        }

        .hssb-icon-display .dashicons {
            font-size: 28px;
            width: 28px;
            height: 28px;
        }

        .hssb-icon-label {
            font-size: 11px;
            font-weight: 500;
            color: #1e1e1e;
        }

        .hssb-icon-preview {
            border-top: 1px solid #f0f0f1;
            padding-top: 10px;
            margin-top: 10px;
        }

        .hssb-preview-display {
            margin-top: 5px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 3px;
            min-height: 40px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hssb-preview-display img,
        .hssb-preview-display .dashicons {
            width: 32px;
            height: 32px;
        }

        .hssb-preview-display .preview-label {
            font-weight: 500;
        }

        .hssb-preview-display .no-selection {
            color: #646970;
            font-style: italic;
        }

        @media screen and (max-width: 782px) {
            .hssb-picker-header {
                flex-direction: column;
                align-items: stretch;
            }

            .hssb-picker-search {
                max-width: none;
            }

            .hssb-icon-grid {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                max-height: 200px;
            }
        }
        </style>';
    }

    /**
     * Get JavaScript for icon picker functionality
     * 
     * @param string $fieldId Field ID
     * @return string JavaScript
     */
    private static function getJavaScript(string $fieldId): string
    {
        return "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const picker = document.getElementById('picker-" . esc_js($fieldId) . "');
            if (!picker) return;

            const hiddenInput = document.getElementById('" . esc_js($fieldId) . "');
            const options = picker.querySelectorAll('.hssb-icon-option');
            const searchInput = picker.querySelector('.hssb-icon-search');
            const iconsetSelect = picker.querySelector('.hssb-iconset-select');
            const previewDisplay = picker.querySelector('.hssb-preview-display');

            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    options.forEach(option => {
                        const label = option.dataset.label || '';
                        const icon = option.dataset.icon || '';
                        const matches = label.includes(query) || icon.includes(query);
                        option.classList.toggle('hidden', !matches);
                    });
                });
            }

            // Icon selection
            options.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    options.forEach(opt => opt.classList.remove('selected'));
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    // Update hidden input
                    hiddenInput.value = this.dataset.icon;
                    
                    // Update preview
                    if (previewDisplay) {
                        const iconDisplay = this.querySelector('.hssb-icon-display').cloneNode(true);
                        const label = this.querySelector('.hssb-icon-label').textContent;
                        previewDisplay.innerHTML = '';
                        previewDisplay.appendChild(iconDisplay);
                        const labelSpan = document.createElement('span');
                        labelSpan.className = 'preview-label';
                        labelSpan.textContent = label;
                        previewDisplay.appendChild(labelSpan);
                    }
                });
            });

            // Iconset change (for advanced picker)
            if (iconsetSelect) {
                iconsetSelect.addEventListener('change', function() {
                    // In a full implementation, this would reload icons with new iconset
                    console.log('Iconset changed to:', this.value);
                });
            }
        });
        </script>";
    }
}