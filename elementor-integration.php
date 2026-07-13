<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register an Elementor widget that delegates rendering to the existing shortcode callback.
 * The class is declared inside the hook because Elementor is optional.
 */
add_action( 'elementor/widgets/register', 'zm_sh_register_elementor_widget' );

function zm_sh_register_elementor_widget( $widgets_manager ) {
	if ( ! class_exists( '\Elementor\Widget_Base' ) || ! function_exists( 'zm_sh_shortcode_cb' ) ) {
		return;
	}

	if ( ! class_exists( 'ZM_SH_Elementor_Share_Widget' ) ) {
		class ZM_SH_Elementor_Share_Widget extends \Elementor\Widget_Base {
			public function get_name() {
				return 'zm_social_share';
			}

			public function get_title() {
				return esc_html__( 'Html Social Share', 'html-social-share-buttons' );
			}

			public function get_icon() {
				return 'eicon-share';
			}

			public function get_categories() {
				return array( 'general' );
			}

			protected function register_controls() {
				$this->start_controls_section(
					'content_section',
					array(
						'label' => esc_html__( 'Share buttons', 'html-social-share-buttons' ),
						'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
					)
				);

				$this->add_control(
					'title',
					array(
						'label'       => esc_html__( 'Title', 'html-social-share-buttons' ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => esc_html__( 'Share this page', 'html-social-share-buttons' ),
						'label_block' => true,
					)
				);

					$this->add_control(
						'iconset',
						array(
							'label'   => esc_html__( 'Icon set', 'html-social-share-buttons' ),
							'type'    => \Elementor\Controls_Manager::SELECT,
							'options' => zm_sh_get_builder_iconset_options(),
							'default' => 'inherit',
						)
					);

					$this->add_control(
						'iconset_type',
					array(
						'label'   => esc_html__( 'Button shape', 'html-social-share-buttons' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							'square' => esc_html__( 'Square', 'html-social-share-buttons' ),
							'circle' => esc_html__( 'Circle', 'html-social-share-buttons' ),
						),
						'default' => 'square',
					)
				);

				$this->add_control(
					'icons',
					array(
							'label'       => esc_html__( 'Networks', 'html-social-share-buttons' ),
							'type'        => \Elementor\Controls_Manager::SELECT2,
							'multiple'    => true,
							'description' => esc_html__( 'Keep at least one network selected. Clearing all networks hides this widget on the frontend.', 'html-social-share-buttons' ),
						'options'     => array(
							'facebook'  => 'Facebook',
							'x'         => 'X',
							'linkedin'  => 'LinkedIn',
							'pinterest' => 'Pinterest',
							'telegram'  => 'Telegram',
							'bluesky'   => 'Bluesky',
							'mail'      => 'Email',
						),
						'default'     => array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' ),
						'label_block' => true,
					)
				);

				$this->end_controls_section();
			}

			protected function render() {
				$settings = $this->get_settings_for_display();
				$icons    = isset( $settings['icons'] ) && is_array( $settings['icons'] ) ? $settings['icons'] : array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' );
				if ( empty( $icons ) ) {
					return;
				}
				$output = zm_sh_shortcode_cb(
						array(
							'title'        => isset( $settings['title'] ) ? $settings['title'] : '',
							'iconset'      => zm_sh_get_builder_iconset( isset( $settings['iconset'] ) ? $settings['iconset'] : 'inherit' ),
						'iconset_type' => isset( $settings['iconset_type'] ) ? $settings['iconset_type'] : 'square',
						'icons'        => implode( ',', array_map( 'sanitize_key', $icons ) ),
						'class'        => 'in_elementor',
					)
				);

				echo wp_kses_post( $output );
			}
		}
	}

	$widgets_manager->register( new ZM_SH_Elementor_Share_Widget() );
}
