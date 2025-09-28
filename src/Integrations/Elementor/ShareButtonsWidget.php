<?php
namespace HtmlSocialShare\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use HtmlSocialShare\ShareRendererInterface;
use HtmlSocialShare\Networks;

/**
 * Elementor HTML Social Share Buttons Widget
 * 
 * @since 3.0.0
 */
class ShareButtonsWidget extends Widget_Base
{
    /**
     * Share renderer instance
     * 
     * @var ShareRendererInterface
     */
    private $shareRenderer;

    /**
     * Constructor
     * 
     * @param array $data Widget data
     * @param array $args Widget arguments
     * @param ShareRendererInterface $shareRenderer Share renderer instance
     */
    public function __construct($data = [], $args = null, ShareRendererInterface $shareRenderer = null)
    {
        $this->shareRenderer = $shareRenderer;
        parent::__construct($data, $args);
    }

    /**
     * Get widget name
     * 
     * @return string
     */
    public function get_name(): string
    {
        return 'html-social-share-buttons';
    }

    /**
     * Get widget title
     * 
     * @return string
     */
    public function get_title(): string
    {
        return esc_html__('HTML Social Share Buttons', 'html-social-share');
    }

    /**
     * Get widget icon
     * 
     * @return string
     */
    public function get_icon(): string
    {
        return 'eicon-share';
    }

    /**
     * Get widget categories
     * 
     * @return array
     */
    public function get_categories(): array
    {
        return ['social'];
    }

    /**
     * Get widget keywords
     * 
     * @return array
     */
    public function get_keywords(): array
    {
        return ['social', 'share', 'buttons', 'facebook', 'twitter', 'linkedin'];
    }

    /**
     * Register widget controls
     * 
     * @return void
     */
    protected function register_controls(): void
    {
        $this->registerContentControls();
        $this->registerStyleControls();
    }

    /**
     * Register content controls
     * 
     * @return void
     */
    private function registerContentControls(): void
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'html-social-share'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Title control
        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'html-social-share'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Share this with your friends', 'html-social-share'),
                'placeholder' => esc_html__('Enter title...', 'html-social-share'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // Show title toggle
        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'html-social-share'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'html-social-share'),
                'label_off' => esc_html__('Hide', 'html-social-share'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // Network selection
        $availableNetworks = Networks::getAvailableNetworks();
        $networkOptions = [];
        foreach ($availableNetworks as $key => $network) {
            $networkOptions[$key] = $network['label'];
        }

        $this->add_control(
            'networks',
            [
                'label' => esc_html__('Social Networks', 'html-social-share'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $networkOptions,
                'default' => ['facebook', 'twitter', 'linkedin'],
                'separator' => 'before',
            ]
        );

        // Iconset selection
        $this->add_control(
            'iconset',
            [
                'label' => esc_html__('Icon Style', 'html-social-share'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => esc_html__('Default', 'html-social-share'),
                    'square' => esc_html__('Square', 'html-social-share'),
                    'circle' => esc_html__('Circle', 'html-social-share'),
                    'minimal' => esc_html__('Minimal', 'html-social-share'),
                ],
                'default' => 'default',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register style controls
     * 
     * @return void
     */
    private function registerStyleControls(): void
    {
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'html-social-share'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Alignment
        $this->add_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'html-social-share'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'html-social-share'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'html-social-share'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'html-social-share'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .html-social-share-buttons' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Button size
        $this->add_control(
            'button_size',
            [
                'label' => esc_html__('Button Size', 'html-social-share'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 16,
                        'max' => 64,
                        'step' => 2,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 32,
                ],
                'selectors' => [
                    '{{WRAPPER}} .html-social-share-buttons a' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Button spacing
        $this->add_control(
            'button_spacing',
            [
                'label' => esc_html__('Button Spacing', 'html-social-share'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .html-social-share-buttons a' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render the widget output
     * 
     * @return void
     */
    protected function render(): void
    {
        if (!$this->shareRenderer) {
            echo '<div class="elementor-alert elementor-alert-warning">' . 
                 esc_html__('Share renderer not available.', 'html-social-share') . 
                 '</div>';
            return;
        }

        $settings = $this->get_settings_for_display();
        
        $networks = $settings['networks'] ?? [];
        $iconset = $settings['iconset'] ?? 'default';
        $title = $settings['title'] ?? '';
        $showTitle = $settings['show_title'] === 'yes';
        $alignment = $settings['alignment'] ?? 'left';

        if (empty($networks)) {
            echo '<div class="elementor-alert elementor-alert-info">' . 
                 esc_html__('Please select at least one social network.', 'html-social-share') . 
                 '</div>';
            return;
        }

        // Set iconset on renderer
        if (method_exists($this->shareRenderer, 'setIconset')) {
            $iconsetMappings = [
                'default' => 'default_square',
                'square' => 'flat_square', 
                'circle' => 'flat_circle',
                'minimal' => 'prajin_square'
            ];
            $mappedIconset = $iconsetMappings[$iconset] ?? 'default_square';
            $this->shareRenderer->setIconset($mappedIconset);
        }

        // Generate output
        $this->renderShareButtons($networks, $title, $showTitle, $alignment);
    }

    /**
     * Render share buttons HTML
     * 
     * @param array $networks Selected networks
     * @param string $title Widget title
     * @param bool $showTitle Whether to show title
     * @param string $alignment Button alignment
     * @return void
     */
    private function renderShareButtons(array $networks, string $title, bool $showTitle, string $alignment): void
    {
        echo '<div class="html-social-share-buttons" style="text-align: ' . esc_attr($alignment) . ';" role="group" aria-label="' . esc_attr__('Social sharing buttons', 'html-social-share') . '">';

        if ($showTitle && !empty($title)) {
            echo '<div class="share-title">' . esc_html($title) . '</div>';
        }

        echo '<div class="share-buttons" role="group" aria-label="' . esc_attr__('Share buttons', 'html-social-share') . '">';

        foreach ($networks as $network) {
            $profile = [
                'handle' => '@example', 
                'network' => $network,
                'type' => 'share',
                'visible' => true
            ];
            
            $buttonHtml = $this->shareRenderer->render($network, $profile);
            echo $buttonHtml . ' ';
        }

        echo '</div></div>';
    }

    /**
     * Render the widget output in the editor
     * 
     * @return void
     */
    protected function content_template(): void
    {
        ?>
        <#
        if (settings.networks && settings.networks.length > 0) {
            var alignment = settings.alignment || 'left';
            var showTitle = settings.show_title === 'yes';
            var title = settings.title || '';
        #>
        <div class="html-social-share-buttons" style="text-align: {{{ alignment }}};" role="group" aria-label="<?php echo esc_attr__('Social sharing buttons', 'html-social-share'); ?>">
            <# if (showTitle && title) { #>
            <div class="share-title">{{{ title }}}</div>
            <# } #>
            <div class="share-buttons" role="group" aria-label="<?php echo esc_attr__('Share buttons', 'html-social-share'); ?>">
                <# _.each(settings.networks, function(network) { #>
                <a href="#" class="share-button share-{{{ network }}}" aria-label="<?php echo esc_attr__('Share on', 'html-social-share'); ?> {{{ network }}}">
                    <span class="dashicons dashicons-share"></span>
                </a>
                <# }); #>
            </div>
        </div>
        <# } else { #>
        <div class="elementor-alert elementor-alert-info">
            <?php echo esc_html__('Please select at least one social network.', 'html-social-share'); ?>
        </div>
        <# } #>
        <?php
    }
}