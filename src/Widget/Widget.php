<?php
namespace HtmlSocialShare\Widget;

use HtmlSocialShare\ShareRendererInterface;
use HtmlSocialShare\SettingsInterface;

class Widget extends \WP_Widget
{
    private $shareRenderer;
    private $settings;

    public function __construct(ShareRendererInterface $shareRenderer, SettingsInterface $settings)
    {
        $this->shareRenderer = $shareRenderer;
        $this->settings = $settings;

        parent::__construct(
            'html_social_share_widget', // Base ID
            'HTML Social Share', // Name
            [
                'description' => 'Display social share buttons'
            ]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title']) && (!isset($instance['show_title']) || $instance['show_title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $networks = !empty($instance['networks']) ? $instance['networks'] : [];

        if (!empty($networks)) {
            echo '<div class="hssb-widget-buttons" role="group" aria-label="Social sharing buttons">';
            foreach ($networks as $network) {
                $profile = ['handle' => '', 'network' => $network]; // Use default profile
                $html = $this->shareRenderer->render($network, $profile);
                // Add accessibility attributes to the rendered HTML
                $html = str_replace('<a ', '<a aria-label="Share on ' . ucfirst($network) . '" ', $html);
                echo $html . ' ';
            }
            echo '</div>';
        }

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $networks = !empty($instance['networks']) ? $instance['networks'] : [];
        $show_title = isset($instance['show_title']) ? (bool) $instance['show_title'] : true;
        $icon_set = !empty($instance['icon_set']) ? $instance['icon_set'] : 'default';
        $button_style = !empty($instance['button_style']) ? $instance['button_style'] : 'default';

        // Title field
        echo '<p>';
        echo '<label for="' . $this->get_field_id('title') . '">Title:</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '">';
        echo '</p>';

        // Show title checkbox
        echo '<p>';
        echo '<input class="checkbox" type="checkbox" ' . checked($show_title, true, false) . ' id="' . $this->get_field_id('show_title') . '" name="' . $this->get_field_name('show_title') . '" />';
        echo '<label for="' . $this->get_field_id('show_title') . '">Display widget title</label>';
        echo '</p>';

        // Icon set
        echo '<p>';
        echo '<label for="' . $this->get_field_id('icon_set') . '">Icon Set:</label>';
        echo '<select class="widefat" id="' . $this->get_field_id('icon_set') . '" name="' . $this->get_field_name('icon_set') . '">';
        echo '<option value="default" ' . selected($icon_set, 'default', false) . '>Default</option>';
        echo '<option value="square" ' . selected($icon_set, 'square', false) . '>Square</option>';
        echo '<option value="circle" ' . selected($icon_set, 'circle', false) . '>Circle</option>';
        echo '</select>';
        echo '</p>';

        // Button style
        echo '<p>';
        echo '<label for="' . $this->get_field_id('button_style') . '">Button Style:</label>';
        echo '<select class="widefat" id="' . $this->get_field_id('button_style') . '" name="' . $this->get_field_name('button_style') . '">';
        echo '<option value="default" ' . selected($button_style, 'default', false) . '>Default</option>';
        echo '<option value="minimal" ' . selected($button_style, 'minimal', false) . '>Minimal</option>';
        echo '<option value="colored" ' . selected($button_style, 'colored', false) . '>Colored</option>';
        echo '</select>';
        echo '</p>';

        // Networks selection
        $available_networks = ['facebook', 'twitter', 'linkedin', 'pinterest', 'email'];
        echo '<p>';
        echo '<label>Networks to display:</label><br>';
        foreach ($available_networks as $network) {
            $checked = in_array($network, $networks) ? 'checked' : '';
            echo '<input type="checkbox" id="' . $this->get_field_id('networks') . '_' . $network . '" name="' . $this->get_field_name('networks') . '[]" value="' . esc_attr($network) . '" ' . $checked . '>';
            echo '<label for="' . $this->get_field_id('networks') . '_' . $network . '">' . esc_html(ucfirst($network)) . '</label><br>';
        }
        echo '</p>';

        // Preview
        echo '<div style="margin-top: 10px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">';
        echo '<strong>Preview:</strong><br>';
        $preview_networks = !empty($networks) ? $networks : ['facebook', 'twitter'];
        echo '<div class="hssb-widget-preview" style="margin-top: 5px;">';
        foreach ($preview_networks as $network) {
            $profile = ['handle' => '', 'network' => $network];
            $html = $this->shareRenderer->render($network, $profile);
            echo $html . ' ';
        }
        echo '</div>';
        echo '<small>Preview shows selected networks with default styling.</small>';
        echo '</div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];

        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_title'] = isset($new_instance['show_title']) ? (bool) $new_instance['show_title'] : false;
        $instance['icon_set'] = (!empty($new_instance['icon_set'])) ? sanitize_text_field($new_instance['icon_set']) : 'default';
        $instance['button_style'] = (!empty($new_instance['button_style'])) ? sanitize_text_field($new_instance['button_style']) : 'default';
        $instance['networks'] = (!empty($new_instance['networks']) && is_array($new_instance['networks'])) ? array_map('sanitize_text_field', $new_instance['networks']) : [];

        return $instance;
    }
}