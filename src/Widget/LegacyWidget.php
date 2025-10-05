<?php
/**
 * Legacy Widget
 *
 * WordPress widget for legacy v2.x compatibility.
 * Provides the same interface as zm_html_share_widget from v2.x
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\Widget;

use HtmlSocialShare\Frontend\LegacyButtonRenderer;
use HtmlSocialShare\Settings;

class LegacyWidget extends \WP_Widget
{
    private LegacyButtonRenderer $legacyRenderer;
    private Settings $settings;

    public function __construct(
        LegacyButtonRenderer $legacyRenderer,
        Settings $settings
    ) {
        $this->legacyRenderer = $legacyRenderer;
        $this->settings = $settings;

        $widget_ops = [
            'description' => __(
                "Html share button. It show lite share button only with html. It's not using any javascript whats anothers do.",
                'html-social-share'
            )
        ];

        parent::__construct(
            'html_share_button_widget',
            __('Html share button widget', 'html-social-share'),
            $widget_ops
        );
    }

    /**
     * Widget output
     *
     * @param array $args Widget arguments
     * @param array $instance Widget instance settings
     */
    public function widget($args, $instance)
    {
        // Check if excluded
        global $post;
        if (!empty($post->ID)) {
            $excludes = $this->settings->get('excludes', '');
            $excludes = array_map('trim', explode(',', $excludes));
            
            if (in_array($post->ID, $excludes, true)) {
                return;
            }

            $disableShare = get_post_meta($post->ID, '_zm_sh_disable_share', true);
            if ($disableShare === 'on') {
                return;
            }
        }

        echo $args['before_widget'];

        // Widget title
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        // Prepare options for legacy renderer
        $options = [
            'class' => 'in_widget',
            'show_on' => 'widget',
            'iconset' => !empty($instance['iconset']) ? sanitize_key($instance['iconset']) : 'default',
            'iconset_type' => !empty($instance['iconset_type']) ? sanitize_key($instance['iconset_type']) : 'square',
            'icons' => !empty($instance['icons']) ? $instance['icons'] : []
        ];

        // Ensure icons are in the right format (array with 1/0 values)
        if (is_array($options['icons'])) {
            foreach ($options['icons'] as $key => $value) {
                $options['icons'][$key] = (int) $value;
            }
        }

        // Render buttons
        echo $this->legacyRenderer->render($options);

        echo $args['after_widget'];
    }

    /**
     * Update widget settings
     *
     * @param array $new_instance New settings
     * @param array $old_instance Previous settings
     * @return array Updated settings
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        
        $instance['title'] = !empty($new_instance['title']) 
            ? sanitize_text_field($new_instance['title']) 
            : '';
        
        $instance['iconset'] = !empty($new_instance['iconset']) 
            ? sanitize_key($new_instance['iconset']) 
            : 'default';
        
        $instance['iconset_type'] = !empty($new_instance['iconset_type']) 
            ? sanitize_key($new_instance['iconset_type']) 
            : 'square';
        
        $instance['icons'] = !empty($new_instance['icons']) 
            ? $new_instance['icons'] 
            : [];

        // Sanitize icon selections
        if (is_array($instance['icons'])) {
            foreach ($instance['icons'] as $key => $value) {
                $instance['icons'][sanitize_key($key)] = (int) $value;
                if ($key !== sanitize_key($key)) {
                    unset($instance['icons'][$key]);
                }
            }
        }

        return $instance;
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance settings
     */
    public function form($instance)
    {
        // Get default options
        $defaults = $this->getDefaultOptions();
        
        if (empty($instance)) {
            $instance = $defaults;
        } else {
            $instance = array_merge($defaults, $instance);
        }

        $title = esc_attr($instance['title']);
        $iconset = esc_attr($instance['iconset']);
        $iconset_type = esc_attr($instance['iconset_type']);
        $icons = $instance['icons'];

        ?>
        <div class="wrap HSSWidget">
            <h3><?php _e('Widget Settings', 'html-social-share'); ?></h3>
            
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">
                    <?php _e('Title:', 'html-social-share'); ?>
                </label>
                <input 
                    class="widefat" 
                    id="<?php echo $this->get_field_id('title'); ?>" 
                    name="<?php echo $this->get_field_name('title'); ?>" 
                    type="text" 
                    value="<?php echo $title; ?>"
                />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('iconset'); ?>">
                    <?php _e('Button Style:', 'html-social-share'); ?>
                </label>
                <select 
                    class="widefat" 
                    id="<?php echo $this->get_field_id('iconset'); ?>" 
                    name="<?php echo $this->get_field_name('iconset'); ?>"
                >
                    <?php
                    $iconsets = $this->getAvailableIconsets();
                    foreach ($iconsets as $id => $name) {
                        echo sprintf(
                            '<option value="%s"%s>%s</option>',
                            esc_attr($id),
                            selected($iconset, $id, false),
                            esc_html($name)
                        );
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('iconset_type'); ?>">
                    <?php _e('Icon Type:', 'html-social-share'); ?>
                </label>
                <select 
                    class="widefat" 
                    id="<?php echo $this->get_field_id('iconset_type'); ?>" 
                    name="<?php echo $this->get_field_name('iconset_type'); ?>"
                >
                    <option value="square"<?php selected($iconset_type, 'square'); ?>>
                        <?php _e('Square', 'html-social-share'); ?>
                    </option>
                    <option value="round"<?php selected($iconset_type, 'round'); ?>>
                        <?php _e('Round', 'html-social-share'); ?>
                    </option>
                </select>
            </p>

            <p>
                <strong><?php _e('Select Networks:', 'html-social-share'); ?></strong>
            </p>

            <?php
            $networks = $this->getAvailableNetworks();
            foreach ($networks as $id => $label) {
                $checked = !empty($icons[$id]) ? 'checked' : '';
                ?>
                <p>
                    <input 
                        type="checkbox" 
                        id="<?php echo $this->get_field_id('icons') . '_' . $id; ?>" 
                        name="<?php echo $this->get_field_name('icons') . '[' . $id . ']'; ?>" 
                        value="1"
                        <?php echo $checked; ?>
                    />
                    <label for="<?php echo $this->get_field_id('icons') . '_' . $id; ?>">
                        <?php echo esc_html($label); ?>
                    </label>
                </p>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * Get default widget options
     *
     * @return array
     */
    private function getDefaultOptions(): array
    {
        return [
            'title' => __('Share this', 'html-social-share'),
            'iconset' => 'default',
            'iconset_type' => 'square',
            'icons' => [
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
                'googleplus' => 1,
                'bookmark' => 1,
                'pinterest' => 1,
                'mail' => 1,
            ]
        ];
    }

    /**
     * Get available iconsets
     *
     * @return array
     */
    private function getAvailableIconsets(): array
    {
        $iconsets = [];
        $iconsetDir = HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/iconset/';

        if (is_dir($iconsetDir)) {
            $dirs = @scandir($iconsetDir);
            if ($dirs) {
                foreach ($dirs as $dir) {
                    if ($dir !== '.' && $dir !== '..' && is_dir($iconsetDir . $dir)) {
                        $iconsets[$dir] = ucfirst(str_replace('_', ' ', $dir));
                    }
                }
            }
        }

        // Fallback if no iconsets found
        if (empty($iconsets)) {
            $iconsets['default'] = __('Default', 'html-social-share');
        }

        return $iconsets;
    }

    /**
     * Get available networks
     *
     * @return array
     */
    private function getAvailableNetworks(): array
    {
        return [
            'facebook' => __('Facebook', 'html-social-share'),
            'twitter' => __('Twitter / X', 'html-social-share'),
            'linkedin' => __('LinkedIn', 'html-social-share'),
            'googleplus' => __('Google+', 'html-social-share'),
            'pinterest' => __('Pinterest', 'html-social-share'),
            'whatsapp' => __('WhatsApp', 'html-social-share'),
            'telegram' => __('Telegram', 'html-social-share'),
            'reddit' => __('Reddit', 'html-social-share'),
            'tumblr' => __('Tumblr', 'html-social-share'),
            'mail' => __('Email', 'html-social-share'),
            'bookmark' => __('Bookmark', 'html-social-share'),
        ];
    }
}
