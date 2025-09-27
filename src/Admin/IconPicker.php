<?php
namespace HtmlSocialShare\Admin;

class IconPicker
{
    private static $icons = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'email' => 'Email'
    ];

    public static function render(string $fieldName, string $currentValue = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $fieldName;
        $class = $attributes['class'] ?? 'hssb-icon-picker';

        $output = '<div class="' . esc_attr($class) . '">';
        $output .= '<input type="hidden" name="' . esc_attr($fieldName) . '" id="' . esc_attr($id) . '" value="' . esc_attr($currentValue) . '">';

        $output .= '<div class="hssb-icon-grid">';
        foreach (self::$icons as $iconKey => $iconLabel) {
            $selected = ($currentValue === $iconKey) ? 'selected' : '';
            $iconUrl = HTML_SOCIAL_SHARE_ICONSET_URL . 'default_square/' . $iconKey . '.png';

            $output .= '<div class="hssb-icon-option ' . $selected . '" data-icon="' . esc_attr($iconKey) . '">';
            $output .= '<img src="' . esc_url($iconUrl) . '" alt="' . esc_attr($iconLabel) . '" width="32" height="32">';
            $output .= '<span class="hssb-icon-label">' . esc_html($iconLabel) . '</span>';
            $output .= '</div>';
        }
        $output .= '</div>';

        $output .= '<p class="description">Click an icon to select it for this profile.</p>';
        $output .= '</div>';

        // Add JavaScript for interaction
        $output .= self::getJavaScript($id);

        return $output;
    }

    private static function getJavaScript(string $fieldId): string
    {
        return "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('" . esc_js($fieldId) . "').closest('.hssb-icon-picker');
            const hiddenInput = document.getElementById('" . esc_js($fieldId) . "');
            const options = container.querySelectorAll('.hssb-icon-option');

            options.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    options.forEach(opt => opt.classList.remove('selected'));
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    // Update hidden input
                    hiddenInput.value = this.getAttribute('data-icon');
                });
            });
        });
        </script>";
    }
}