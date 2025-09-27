<?php
namespace HtmlSocialShare\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ShareButtonsWidget extends Widget_Base {
    public function get_name() {
        return 'html_social_share_buttons';
    }

    public function get_title() {
        return __( 'HTML Social Share Buttons', 'html-social-share' );
    }

    public function get_icon() {
        return 'eicon-share';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_networks',
            [
                'label' => __( 'Social Networks', 'html-social-share' ),
            ]
        );

        $this->add_control(
            'networks',
            [
                'label' => __( 'Networks', 'html-social-share' ),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    'facebook' => __( 'Facebook', 'html-social-share' ),
                    'twitter' => __( 'Twitter', 'html-social-share' ),
                    'linkedin' => __( 'LinkedIn', 'html-social-share' ),
                    'pinterest' => __( 'Pinterest', 'html-social-share' ),
                    'reddit' => __( 'Reddit', 'html-social-share' ),
                    'whatsapp' => __( 'WhatsApp', 'html-social-share' ),
                    'email' => __( 'Email', 'html-social-share' ),
                ],
                'default' => [ 'facebook', 'twitter', 'linkedin' ],
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // Style controls
        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Button Style', 'html-social-share' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_shape',
            [
                'label' => __( 'Shape', 'html-social-share' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'square' => __( 'Square', 'html-social-share' ),
                    'circle' => __( 'Circle', 'html-social-share' ),
                    'rounded' => __( 'Rounded', 'html-social-share' ),
                ],
                'default' => 'square',
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __( 'Button Color', 'html-social-share' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073e6',
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => __( 'Button Size', 'html-social-share' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [ 'min' => 24, 'max' => 96 ],
                ],
                'default' => [ 'size' => 40, 'unit' => 'px' ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Output rendering will be implemented in a later task
        echo '<div class="html-social-share-buttons">[Share buttons will render here]</div>';
    }
}
