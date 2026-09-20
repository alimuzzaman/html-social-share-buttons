<?php

// For Code Sniffer.
if ( ! class_exists( 'WP_Widget' ) ) {
	return;
}

/**
 * Social media follow buttons
 */
class DPSP_Social_Media_Follow_Buttons extends WP_Widget {

	/**
	 * DPSP_Social_Media_Follow_Buttons constructor.
	 */
	public function __construct() {
		parent::__construct(
			'dpsp_social_media_follow',
			__( 'Hubbub: Social Media Follow Buttons', 'social-pug' ),
			[ 'description' => __( 'Display social media follow buttons.', 'social-pug' ) ]
		);
	}

	/**
	 * Outputs the content of the widget in the front-end.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$settings = dpsp_get_location_settings( 'follow_widget' );

		if ( ! empty( $settings['active'] ) && ! empty( $settings['networks'] ) ) {

			add_filter( 'mv_grow_scripts_should_enqueue', '__return_true' );

			echo ( isset( $args['before_widget'] ) ? wp_kses_post( $args['before_widget'] ) : '' );

			echo ( isset( $args['before_title'] ) ? wp_kses_post( $args['before_title'] ) : '' );
			echo ( isset( $instance['title'] ) ? wp_kses_post( $instance['title'] ) : '' );
			echo ( isset( $args['after_title'] ) ? wp_kses_post( $args['after_title'] ) : '' );

			echo ( ! empty( $instance['description'] ) ? wp_kses_post( $instance['description'] ) : '' );

			echo do_shortcode( '[socialpug_follow]' );

			echo ( isset( $args['after_widget'] ) ? wp_kses_post( $args['after_widget'] ) : '' );
		}
	}

	/**
	 * Outputs the options form in the back-end.
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// Set saved values
		$title       = ( ! empty( $instance['title'] ) ? esc_html( $instance['title'] ) : '' );
		$description = ( ! empty( $instance['description'] ) ? esc_html( $instance['description'] ) : '' );

		// Widget title
		echo '<p>';
		echo '<label class="dpsp-widget-section-title" for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . esc_html__( 'Title:', 'social-pug' ) . '</label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( $title ) . '" />';
		echo '</p>';

		// Widget description
		echo '<p>';
		echo '<label class="dpsp-widget-section-title" for="' . esc_attr( $this->get_field_id( 'description' ) ) . '">' . esc_html__( 'Description:', 'social-pug' ) . '</label>';
		echo '<textarea class="widefat" id="' . esc_attr( $this->get_field_id( 'description' ) ) . '" name="' . esc_attr( $this->get_field_name( 'description' ) ) . '">' . esc_html( $description ) . '</textarea>';
		echo '</p>';

		// Widget buttons settings
		echo '<p>';
		echo '<label class="dpsp-widget-section-title">' . esc_html__( 'Buttons Settings:', 'social-pug' ) . '</label>';
		echo '<span>' . esc_html__( 'You can edit the look and feel of the buttons by pressing the button below:', 'social-pug' ) . '</span><br /><br />';
		echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=dpsp-follow-widget' ) ) . '">' . esc_html__( 'Buttons Settings', 'social-pug' ) . '</a>';
		echo '</p>';

		// Need to match the signature of the parent method. Returning noform will render no save button. Otherwise, save is rendered.
		return '';
	}

	/**
	 * Processing widget options on save.
	 *
	 * @param array $new_instance - The new options
	 * @param array $old_instance - The previous options
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = [];
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['description'] = ( ! empty( $new_instance['description'] ) ) ? esc_html( $new_instance['description'] ) : '';

		return $instance;
	}
}

if ( ! function_exists( 'dpsp_social_media_follow_buttons' ) ) {
	/**
	 * Safely load widget.
	 */
	function dpsp_social_media_follow_buttons() {
		// Only run if follow widget is active
		if ( ! dpsp_is_tool_active( 'follow_widget' ) ) {
			return;
		}
		register_widget( 'DPSP_Social_Media_Follow_Buttons' );
	}
}
