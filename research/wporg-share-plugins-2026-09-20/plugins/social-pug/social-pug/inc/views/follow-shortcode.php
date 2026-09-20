<div <?php echo ! empty( $args['args']['id'] ) ? 'id="' . esc_attr( $args['args']['id'] ) . '"' : ''; ?>
		class="<?php echo esc_attr( $args['wrapper_classes'] ); ?>">
	<?php echo dpsp_get_output_network_buttons( $args['settings'], 'follow', 'follow_widget' ); ?>
</div>
