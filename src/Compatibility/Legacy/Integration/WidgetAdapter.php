<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration;

class WidgetAdapter extends \WP_Widget {
	public static function registerWidget() {
		global $zm_sh;

		if ( ! is_object( $zm_sh ) ) {
			return;
		}
		if ( isset( $zm_sh->excluded ) && true === $zm_sh->excluded ) {
			return;
		}

		register_widget( 'zm_html_share_widget' );
	}

	public function __construct() {
		parent::__construct(
			'html_share_button_widget',
			'Html share button widget',
			array(
				'description' => __(
					"Html share button. It show lite share button only with html. It's not using any javascript whats anothers do.",
					'html-social-share-buttons'
				),
			)
		);
	}

	public function widget( $arguments, $instance ) {
		global $zm_sh;

		if ( ! is_object( $zm_sh ) || ! method_exists( $zm_sh, 'zm_sh_btn' ) ) {
			return;
		}

		$instance['icons'] = $this->normalizeIconSelection(
			isset( $instance['icons'] ) ? $instance['icons'] : array()
		);
		$beforeWidget = isset( $arguments['before_widget'] ) ? $arguments['before_widget'] : '';
		$afterWidget = isset( $arguments['after_widget'] ) ? $arguments['after_widget'] : '';
		$beforeTitle = isset( $arguments['before_title'] ) ? $arguments['before_title'] : '';
		$afterTitle = isset( $arguments['after_title'] ) ? $arguments['after_title'] : '';
		$title = apply_filters(
			'widget_title',
			isset( $instance['title'] ) ? $instance['title'] : ''
		);

		echo wp_kses_post( $beforeWidget );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $beforeTitle ) . esc_html( $title ) . wp_kses_post( $afterTitle );
		}
		$instance['class'] = 'in_widget';
		echo wp_kses_post( $zm_sh->zm_sh_btn( $instance ) );
		echo wp_kses_post( $afterWidget );
	}

	public function update( $newInstance, $oldInstance ) {
		$instance = array();
		$instance['title'] = isset( $newInstance['title'] )
			? sanitize_text_field( $newInstance['title'] )
			: '';
		$instance['icons'] = $this->normalizeIconSelection(
			isset( $newInstance['icons'] ) ? $newInstance['icons'] : array()
		);
		$instance['iconset_type'] = isset( $newInstance['iconset_type'] )
			? sanitize_key( $newInstance['iconset_type'] )
			: 'square';
		$instance['iconset'] = isset( $newInstance['iconset'] )
			? sanitize_key( $newInstance['iconset'] )
			: 'default';

		return $instance;
	}

	private function normalizeIconSelection( $icons ) {
		if ( ! is_array( $icons ) || empty( $icons ) ) {
			return array();
		}

		$keys = array_keys( $icons );
		$networkIds = $keys === range( 0, count( $keys ) - 1 ) ? $icons : $keys;
		$normalized = array();
		foreach ( $networkIds as $networkId ) {
			if ( ! is_scalar( $networkId ) ) {
				continue;
			}

			$networkId = sanitize_key( (string) $networkId );
			if ( '' !== $networkId ) {
				$normalized[ $networkId ] = '1';
			}
		}

		return $normalized;
	}

	public function form( $instance ) {
		global $zm_sh_default_options;

		if ( empty( $instance ) ) {
			$instance = $zm_sh_default_options;
		}
		$form = new \zm_form();
		?>
		<div class="wrap HSSWidget">
			<?php settings_fields( 'zm_shbt_opt' ); ?>
			<h3>Select theme and Icon Style</h3>
			<?php $form->text( $this->get_field_id( 'title' ), 'Enter a Title', $this->get_field_name( 'title' ), $instance['title'] ); ?>
			<?php $form->select_iconset( $this->get_field_id( 'iconset' ), 'Select Button Style', $this->get_field_name( 'iconset' ), $instance['iconset'] ); ?>
			<?php $form->dropdown( $this->get_field_id( 'iconset_type' ), 'Select Type', $form->iconsets->get_iconset( $instance['iconset'] )->types, $this->get_field_name( 'iconset_type' ), $instance['iconset_type'] ); ?>
			<?php $form->icon_fields_widget( $this->get_field_id( 'icons' ), $this->get_field_name( 'icons' ), $instance['icons'], 'Select Buttons', 'Enable', $instance['iconset'] ); ?>
		</div>
		<?php
	}
}
