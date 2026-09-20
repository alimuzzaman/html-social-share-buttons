<div id="dpsp-sticky-bar-wrapper" class="
	<?php echo( ! empty( $args['settings']['display']['intro_animation'] ) && '-1' !== $args['settings']['display']['intro_animation'] ? 'dpsp-animation-' . esc_attr( $args['settings']['display']['intro_animation'] ) : 'dpsp-no-animation' ); ?>
	<?php echo( ! empty( $args['settings']['display']['show_on_device'] ) ? 'dpsp-device-' . esc_attr( $args['settings']['display']['show_on_device'] ) : 'dpsp-device-mobile' ); ?>
	<?php echo( ! empty( $args['settings']['display']['position_desktop'] ) ? 'dpsp-position-desktop-' . esc_attr( $args['settings']['display']['position_desktop'] ) : 'dpsp-position-desktop-bottom' ); ?>
	<?php echo( ! empty( $args['settings']['display']['position_mobile'] ) ? 'dpsp-position-mobile-' . esc_attr( $args['settings']['display']['position_mobile'] ) : 'dpsp-position-mobile-bottom' ); ?>" data-trigger-scroll="<?php echo esc_attr( $args['trigger_data'] ); ?>">

	<div id="dpsp-sticky-bar" class="<?php echo esc_attr( $args['wrapper_classes'] ); ?>">

		<?php
		if ( $args['show_total_count'] ) {
			echo dpsp_get_output_total_share_count( 'sticky_bar' );
		}

		if ( isset( $args['settings']['networks'] ) ) {
			echo dpsp_get_output_network_buttons( $args['settings'], 'share', 'sticky_bar' );
		}
		?>

	</div>

</div>
