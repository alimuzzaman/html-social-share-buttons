<?php
/**
 * WordPress Widget
 *
 * @package HtmlSocialShare
 */

namespace HtmlSocialShare\Admin;

use HtmlSocialShare\Renderers\ButtonRenderer;
use HtmlSocialShare\IconSystem\IconRegistry;

/**
 * HTML Social Share Buttons Widget
 */
class Widget extends \WP_Widget {
    /**
     * @var ButtonRenderer
     */
    private ButtonRenderer $buttonRenderer;
    
    /**
     * @var IconRegistry
     */
    private IconRegistry $iconRegistry;
    
    /**
     * Constructor
     *
     * @param ButtonRenderer $buttonRenderer
     * @param IconRegistry $iconRegistry
     */
    public function __construct(
        ButtonRenderer $buttonRenderer,
        IconRegistry $iconRegistry
    ) {
        $this->buttonRenderer = $buttonRenderer;
        $this->iconRegistry = $iconRegistry;
        
        $widget_ops = [
            'classname' => 'html_share_button_widget',
            'description' => __('Display social share buttons in widget areas', 'zm-sh'),
        ];
        
        parent::__construct(
            'html_share_button_widget',
            __('HTML Social Share Buttons', 'zm-sh'),
            $widget_ops
        );
    }
    
    /**
     * Output the widget content
     *
     * @param array $args Display arguments
     * @param array $instance Widget instance settings
     */
    public function widget($args, $instance): void {
        echo $args['before_widget'];
        
        // Output title if set
        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }
        
        // Prepare options for rendering
        $options = [
            'class' => 'in_widget',
            'iconset' => $instance['iconset'] ?? 'default',
            'iconset_type' => $instance['iconset_type'] ?? 'square',
            'icons' => $instance['icons'] ?? [],
        ];
        
        // Render buttons
        echo $this->buttonRenderer->render($options);
        
        echo $args['after_widget'];
    }
    
    /**
     * Output the widget settings form
     *
     * @param array $instance Current widget instance settings
     * @return string|void
     */
    public function form($instance): void {
        // Default values
        $title = isset($instance['title']) ? $instance['title'] : '';
        $iconset = isset($instance['iconset']) ? $instance['iconset'] : 'default';
        $iconset_type = isset($instance['iconset_type']) ? $instance['iconset_type'] : 'square';
        $icons = isset($instance['icons']) ? $instance['icons'] : [];
        
        ?>
        <div class="html-social-share-widget-form">
            <!-- Title -->
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                    <?php esc_html_e('Title:', 'zm-sh'); ?>
                </label>
                <input 
                    class="widefat" 
                    id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                    type="text" 
                    value="<?php echo esc_attr($title); ?>"
                />
            </p>
            
            <!-- Iconset -->
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('iconset')); ?>">
                    <?php esc_html_e('Button Style:', 'zm-sh'); ?>
                </label>
                <select 
                    class="widefat" 
                    id="<?php echo esc_attr($this->get_field_id('iconset')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('iconset')); ?>"
                >
                    <?php
                    $iconsets = $this->iconRegistry->getAvailableIconsets();
                    foreach ($iconsets as $id) {
                        $selected = ($iconset === $id) ? 'selected="selected"' : '';
                        echo '<option value="' . esc_attr($id) . '" ' . $selected . '>' . esc_html(ucfirst($id)) . '</option>';
                    }
                    ?>
                </select>
            </p>
            
            <!-- Type -->
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('iconset_type')); ?>">
                    <?php esc_html_e('Type:', 'zm-sh'); ?>
                </label>
                <select 
                    class="widefat" 
                    id="<?php echo esc_attr($this->get_field_id('iconset_type')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('iconset_type')); ?>"
                >
                    <option value="square" <?php selected($iconset_type, 'square'); ?>><?php esc_html_e('Square', 'zm-sh'); ?></option>
                    <option value="circle" <?php selected($iconset_type, 'circle'); ?>><?php esc_html_e('Circle', 'zm-sh'); ?></option>
                </select>
            </p>
            
            <!-- Networks -->
            <p>
                <label><?php esc_html_e('Select Networks:', 'zm-sh'); ?></label>
                <?php
                $defaultIcons = $this->iconRegistry->getDefaultIcons();
                foreach ($defaultIcons as $iconId => $iconData) {
                    $checked = isset($icons[$iconId]) && !empty($icons[$iconId]) ? 'checked="checked"' : '';
                    ?>
                    <label style="display: block;">
                        <input 
                            type="checkbox" 
                            name="<?php echo esc_attr($this->get_field_name('icons')); ?>[<?php echo esc_attr($iconId); ?>]" 
                            value="1"
                            <?php echo $checked; ?>
                        />
                        <?php echo esc_html($iconData['name']); ?>
                    </label>
                    <?php
                }
                ?>
            </p>
        </div>
        <?php
    }
    
    /**
     * Update widget settings
     *
     * @param array $new_instance New widget instance settings
     * @param array $old_instance Old widget instance settings
     * @return array Updated settings
     */
    public function update($new_instance, $old_instance): array {
        $instance = [];
        
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        $instance['iconset'] = sanitize_key($new_instance['iconset'] ?? 'default');
        $instance['iconset_type'] = sanitize_key($new_instance['iconset_type'] ?? 'square');
        
        // Handle icons array
        $instance['icons'] = [];
        if (isset($new_instance['icons']) && is_array($new_instance['icons'])) {
            foreach ($new_instance['icons'] as $icon => $enabled) {
                if (!empty($enabled)) {
                    $instance['icons'][sanitize_key($icon)] = '1';
                }
            }
        }
        
        return $instance;
    }
    
    /**
     * Register the widget
     */
    public static function registerWidget(): void {
        register_widget(__CLASS__);
    }
}
