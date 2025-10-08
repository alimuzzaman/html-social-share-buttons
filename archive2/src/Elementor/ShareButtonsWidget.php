<?php
namespace HtmlSocialShare\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\ArrayUtils;
use HtmlSocialShare\Renderers\RenderUtils;
use HtmlSocialShare\Renderers\ShareUrlBuilder;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Elementor Share Buttons Widget with enhanced security
 * 
 * Provides social sharing functionality within Elementor with proper input
 * validation, output escaping, and pure function separation.
 * 
 * @since 3.0.0
 */
class ShareButtonsWidget extends Widget_Base
{
    /**
     * ShareUrlBuilder instance
     * 
     * @var ShareUrlBuilder
     */
    private $urlBuilder;

    /**
     * Widget constructor with dependency injection
     */
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $this->urlBuilder = ShareUrlBuilder::createWithWordPressIntegration();
    }

    /**
     * Get widget name
     * 
     * @return string Widget name
     */
    public function get_name()
    {
        return 'html_social_share_buttons';
    }

    /**
     * Get widget title
     * 
     * @return string Widget title
     */
    public function get_title()
    {
        return __('HTML Social Share Buttons', 'html-social-share');
    }

    /**
     * Get widget icon
     * 
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'eicon-share';
    }

    /**
     * Get widget categories
     * 
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Get widget keywords for search
     * 
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['social', 'share', 'buttons', 'facebook', 'twitter', 'linkedin'];
    }

    /**
     * Register widget controls with validation
     * 
     * @return void
     */
    protected function _register_controls()
    {
        $this->registerNetworkControls();
        $this->registerStyleControls();
        $this->registerAdvancedControls();
    }

    /**
     * Register network selection controls
     * 
     * @return void
     */
    private function registerNetworkControls()
    {
        $this->start_controls_section(
            'section_networks',
            [
                'label' => __('Social Networks', 'html-social-share'),
            ]
        );

        $this->add_control(
            'networks',
            [
                'label' => __('Networks', 'html-social-share'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => self::getAvailableNetworks(),
                'default' => ['facebook', 'twitter', 'linkedin'],
                'label_block' => true,
                'description' => __('Select which social networks to display', 'html-social-share'),
            ]
        );

        $this->add_control(
            'custom_url',
            [
                'label' => __('Custom URL', 'html-social-share'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('Leave empty to use current page URL', 'html-social-share'),
                'description' => __('Override the URL being shared', 'html-social-share'),
            ]
        );

        $this->add_control(
            'custom_title',
            [
                'label' => __('Custom Title', 'html-social-share'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Leave empty to use page title', 'html-social-share'),
                'description' => __('Override the title being shared', 'html-social-share'),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register style controls
     * 
     * @return void
     */
    private function registerStyleControls()
    {
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Button Style', 'html-social-share'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_shape',
            [
                'label' => __('Shape', 'html-social-share'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'square' => __('Square', 'html-social-share'),
                    'circle' => __('Circle', 'html-social-share'),
                    'rounded' => __('Rounded', 'html-social-share'),
                ],
                'default' => 'square',
            ]
        );

        $this->add_control(
            'button_style',
            [
                'label' => __('Button Style', 'html-social-share'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'filled' => __('Filled', 'html-social-share'),
                    'outline' => __('Outline', 'html-social-share'),
                    'minimal' => __('Minimal', 'html-social-share'),
                ],
                'default' => 'filled',
            ]
        );

        $this->add_responsive_control(
            'button_size',
            [
                'label' => __('Button Size', 'html-social-share'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 24, 'max' => 96],
                ],
                'default' => ['size' => 40, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .hssb-share' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_spacing',
            [
                'label' => __('Button Spacing', 'html-social-share'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 20],
                ],
                'default' => ['size' => 4, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .hssb-share' => 'margin: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register advanced controls
     * 
     * @return void
     */
    private function registerAdvancedControls()
    {
        $this->start_controls_section(
            'section_advanced',
            [
                'label' => __('Advanced', 'html-social-share'),
            ]
        );

        $this->add_control(
            'show_labels',
            [
                'label' => __('Show Labels', 'html-social-share'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'html-social-share'),
                'label_off' => __('No', 'html-social-share'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'target_blank',
            [
                'label' => __('Open in New Window', 'html-social-share'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'html-social-share'),
                'label_off' => __('No', 'html-social-share'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'nofollow',
            [
                'label' => __('Add nofollow', 'html-social-share'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'html-social-share'),
                'label_off' => __('No', 'html-social-share'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output with security
     * 
     * @return void
     */
    protected function render()
    {
        try {
            $settings = $this->get_settings_for_display();
            $config = $this->buildButtonConfiguration($settings);
            
            if (empty($config['networks'])) {
                $this->renderEmptyState();
                return;
            }

            $this->renderShareButtons($config);
        } catch (\Throwable $e) {
            $this->renderErrorState($e);
        }
    }

    /**
     * Build button configuration from settings with validation
     * 
     * @param array $settings Widget settings
     * @return array Sanitized configuration
     */
    private function buildButtonConfiguration(array $settings): array
    {
        $config = [];

        // Sanitize networks
        $networks = $settings['networks'] ?? [];
        if (is_array($networks)) {
            $config['networks'] = array_filter(
                array_map([SecurityUtils::class, 'sanitizeKey'], $networks),
                [self::class, 'isValidNetwork']
            );
        } else {
            $config['networks'] = [];
        }

        // Sanitize URL
        $customUrl = $settings['custom_url']['url'] ?? '';
        $config['url'] = SecurityUtils::sanitizeUrl($customUrl) ?: get_permalink();

        // Sanitize title
        $config['title'] = SecurityUtils::sanitizeTextField($settings['custom_title'] ?? '') ?: get_the_title();

        // Sanitize style options
        $config['shape'] = SecurityUtils::sanitizeKey($settings['button_shape'] ?? 'square');
        $config['style'] = SecurityUtils::sanitizeKey($settings['button_style'] ?? 'filled');
        $config['size'] = max(24, min(96, (int) ($settings['button_size']['size'] ?? 40)));

        // Boolean options
        $config['show_labels'] = ($settings['show_labels'] ?? 'no') === 'yes';
        $config['target_blank'] = ($settings['target_blank'] ?? 'yes') === 'yes';
        $config['nofollow'] = ($settings['nofollow'] ?? 'yes') === 'yes';

        return $config;
    }

    /**
     * Render share buttons with proper escaping
     * 
     * @param array $config Button configuration
     * @return void
     */
    private function renderShareButtons(array $config)
    {
        $containerId = RenderUtils::generateUniqueId('elementor-share');
        $containerClass = RenderUtils::generateButtonClasses('container', ['elementor', $config['style']]);

        echo '<div id="' . SecurityUtils::escapeAttribute($containerId) . '" class="' . SecurityUtils::escapeAttribute($containerClass) . '">';

        foreach ($config['networks'] as $network) {
            $this->renderShareButton($network, $config);
        }

        echo '</div>';

        // Add inline CSS for styling
        $this->addInlineStyles($config);
    }

    /**
     * Render individual share button
     * 
     * @param string $network Network identifier
     * @param array $config Button configuration
     * @return void
     */
    private function renderShareButton(string $network, array $config)
    {
        $shareUrl = $this->urlBuilder->buildUrl($network, [], $config['url'], $config['title']);
        $buttonClass = RenderUtils::generateButtonClasses($network, [$config['shape'], $config['style']]);
        $attributes = RenderUtils::generateA11yAttributes($network);

        // Add target and rel attributes
        if ($config['target_blank']) {
            $attributes['target'] = '_blank';
        }
        if ($config['nofollow']) {
            $attributes['rel'] = 'nofollow noopener';
        }

        $attributesString = RenderUtils::buildAttributes(array_merge($attributes, [
            'href' => $shareUrl,
            'class' => $buttonClass,
            'data-network' => $network
        ]));

        echo '<a ' . $attributesString . '>';
        
        // Render icon (simplified for Elementor)
        echo '<span class="hssb-icon" aria-hidden="true">' . SecurityUtils::escapeHtml(ucfirst($network)) . '</span>';
        
        if ($config['show_labels']) {
            echo '<span class="hssb-label">' . SecurityUtils::escapeHtml(ucfirst($network)) . '</span>';
        }
        
        echo '</a>';
    }

    /**
     * Add inline styles for buttons
     * 
     * @param array $config Button configuration
     * @return void
     */
    private function addInlineStyles(array $config)
    {
        $styles = [
            'width' => $config['size'] . 'px',
            'height' => $config['size'] . 'px',
            'line-height' => $config['size'] . 'px',
            'text-align' => 'center',
            'display' => 'inline-block',
            'margin' => '4px',
        ];

        // Add shape-specific styles
        if ($config['shape'] === 'circle') {
            $styles['border-radius'] = '50%';
        } elseif ($config['shape'] === 'rounded') {
            $styles['border-radius'] = '8px';
        }

        $cssRules = RenderUtils::generateInlineStyles($styles);
        if ($cssRules) {
            echo '<style>.hssb-share { ' . SecurityUtils::escapeAttribute($cssRules) . ' }</style>';
        }
    }

    /**
     * Render empty state
     * 
     * @return void
     */
    private function renderEmptyState()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<div class="elementor-alert elementor-alert-warning">';
            echo SecurityUtils::escapeHtml(__('Please select at least one social network to display.', 'html-social-share'));
            echo '</div>';
        }
    }

    /**
     * Render error state
     * 
     * @param \Throwable $error Error that occurred
     * @return void
     */
    private function renderErrorState(\Throwable $error)
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<div class="elementor-alert elementor-alert-danger">';
            echo SecurityUtils::escapeHtml(__('Error rendering share buttons: ', 'html-social-share'));
            echo SecurityUtils::escapeHtml($error->getMessage());
            echo '</div>';
        }

        // Log error for debugging
        error_log('Elementor ShareButtonsWidget error: ' . $error->getMessage());
    }

    /**
     * Check if Elementor Pro is available
     * 
     * @return bool True if Elementor Pro is available
     */
    public function is_pro_available()
    {
        return defined('ELEMENTOR_PRO_VERSION');
    }

    // ===== PURE FUNCTIONS (NO SIDE EFFECTS) =====

    /**
     * Pure function: Get available social networks
     * 
     * @return array Network options for Elementor control
     */
    public static function getAvailableNetworks(): array
    {
        return [
            'facebook' => __('Facebook', 'html-social-share'),
            'twitter' => __('X (Twitter)', 'html-social-share'),
            'linkedin' => __('LinkedIn', 'html-social-share'),
            'pinterest' => __('Pinterest', 'html-social-share'),
            'reddit' => __('Reddit', 'html-social-share'),
            'whatsapp' => __('WhatsApp', 'html-social-share'),
            'telegram' => __('Telegram', 'html-social-share'),
            'email' => __('Email', 'html-social-share'),
            'tumblr' => __('Tumblr', 'html-social-share'),
            'vk' => __('VKontakte', 'html-social-share'),
        ];
    }

    /**
     * Pure function: Validate network identifier
     * 
     * @param string $network Network identifier
     * @return bool True if valid network
     */
    public static function isValidNetwork(string $network): bool
    {
        $availableNetworks = array_keys(self::getAvailableNetworks());
        return in_array($network, $availableNetworks, true);
    }

    /**
     * Pure function: Get default widget configuration
     * 
     * @return array Default configuration
     */
    public static function getDefaultConfig(): array
    {
        return [
            'networks' => ['facebook', 'twitter', 'linkedin'],
            'shape' => 'square',
            'style' => 'filled',
            'size' => 40,
            'show_labels' => false,
            'target_blank' => true,
            'nofollow' => true,
        ];
    }

    /**
     * Pure function: Validate widget configuration
     * 
     * @param array $config Configuration to validate
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateConfig(array $config): array
    {
        $errors = [];

        if (empty($config['networks']) || !is_array($config['networks'])) {
            $errors[] = 'At least one network must be selected';
        } else {
            foreach ($config['networks'] as $network) {
                if (!self::isValidNetwork($network)) {
                    $errors[] = "Invalid network: {$network}";
                }
            }
        }

        if (!empty($config['url']) && !SecurityUtils::sanitizeUrl($config['url'])) {
            $errors[] = 'Invalid URL provided';
        }

        $validShapes = ['square', 'circle', 'rounded'];
        if (!empty($config['shape']) && !in_array($config['shape'], $validShapes, true)) {
            $errors[] = 'Invalid button shape';
        }

        if (!empty($config['size']) && (!is_numeric($config['size']) || $config['size'] < 24 || $config['size'] > 96)) {
            $errors[] = 'Button size must be between 24 and 96 pixels';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
