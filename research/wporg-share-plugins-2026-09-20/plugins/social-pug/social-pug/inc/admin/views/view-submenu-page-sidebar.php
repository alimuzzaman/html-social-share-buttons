<form method="post" action="options.php">
	<div class="dpsp-page-wrapper dpsp-page-sidebar wrap">

		<?php
		$dpsp_location_sidebar = get_option( 'dpsp_location_sidebar', 'not_set' );
		settings_fields( 'dpsp_location_sidebar' );
		?>


		<!-- Page Title -->
		<h1 class="dpsp-page-title">
			<?php esc_html_e( 'Configure Sidebar Sharing Buttons', 'social-pug' ); ?>

			<input type="hidden" name="dpsp_buttons_location" value="dpsp_location_sidebar" />
			<input type="hidden" name="dpsp_location_sidebar[active]" value="<?php echo ( isset( $dpsp_location_sidebar['active'] ) ? 1 : '' ); ?>" <?php echo ( ! isset( $dpsp_location_sidebar['active'] ) ? 'disabled' : '' ); ?> />
		</h1>

		<!-- Networks Selectable and Sortable Panels -->
		<div id="dpsp-social-platforms-wrapper" class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Social Networks', 'social-pug' ); ?>
				<a id="dpsp-select-networks" class="dpsp-button-secondary" href="#"><?php esc_html_e( 'Select Networks', 'social-pug' ); ?></a>
			</div>

			<div id="dpsp-sortable-networks-empty" class="dpsp-card-inner <?php echo ( empty( $dpsp_location_sidebar['networks'] ) ? 'dpsp-active' : '' ); ?>">
				<p><?php esc_html_e( 'Select which social buttons to display', 'social-pug' ); ?></p>
			</div>

			<?php echo dpsp_output_sortable_networks( ( ! empty( $dpsp_location_sidebar['networks'] ) ? $dpsp_location_sidebar['networks'] : [] ), 'dpsp_location_sidebar' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>

			<?php
				$available_networks = dpsp_get_networks();
				echo dpsp_output_selectable_networks( $available_networks, ( ! empty( $dpsp_location_sidebar['networks'] ) ? $dpsp_location_sidebar['networks'] : [] ) );  // @codingStandardsIgnoreLine — escaping is done in the function
			?>

		</div>

		<!-- General Display Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Display Settings', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php
				dpsp_settings_field(
					'select', 'dpsp_location_sidebar[display][shape]', $dpsp_location_sidebar['display']['shape'], __( 'Button shape', 'social-pug' ), [
						'rectangular' => __( 'Rectangular', 'social-pug' ),
						'rounded'     => __( 'Rounded', 'social-pug' ),
						'circle'      => __( 'Circle', 'social-pug' ),
					]
				);
?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][icon_animation]', ( isset( $dpsp_location_sidebar['display']['icon_animation'] ) ? $dpsp_location_sidebar['display']['icon_animation'] : '' ), __( 'Show icon animation', 'social-pug' ), [ 'yes' ], __( 'Will animate the social media icon when the user hovers over the button.', 'social-pug' ) ); ?>

				<?php
				dpsp_settings_field(
					'select', 'dpsp_location_sidebar[display][position]', $dpsp_location_sidebar['display']['position'], __( 'Buttons position', 'social-pug' ), [
						'left'  => __( 'Left', 'social-pug' ),
						'right' => __( 'Right', 'social-pug' ),
					]
				);
?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_labels]', ( isset( $dpsp_location_sidebar['display']['show_labels'] ) ? $dpsp_location_sidebar['display']['show_labels'] : '' ), __( 'Show button labels', 'social-pug' ), [ 'yes' ] ); ?>
				
				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_labels_mobile]', ( isset( $dpsp_location_sidebar['display']['show_labels_mobile'] ) ? $dpsp_location_sidebar['display']['show_labels_mobile'] : '' ), __( 'Show labels on mobile', 'social-pug' ), [ 'yes' ], __( 'The Show button labels setting (above) must be enabled.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][spacing]', ( isset( $dpsp_location_sidebar['display']['spacing'] ) ? $dpsp_location_sidebar['display']['spacing'] : '' ), __( 'Button spacing', 'social-pug' ), [ 'yes' ], __( 'Adds bottom spacing for each button.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_mobile]', ( isset( $dpsp_location_sidebar['display']['show_mobile'] ) ? $dpsp_location_sidebar['display']['show_mobile'] : '' ), __( 'Show on mobile', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_sidebar[display][screen_size]', ( isset( $dpsp_location_sidebar['display']['screen_size'] ) ? $dpsp_location_sidebar['display']['screen_size'] : '' ), __( 'Mobile screen width (pixels)', 'social-pug' ), '', __( 'For screen widths smaller than this value ( in pixels ) the buttons will be displayed on screen if the show on mobile option is checked.', 'social-pug' ) ); ?>

			</div>

		</div>

		<!-- Share Counts -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Buttons Share Counts', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_count]', ( isset( $dpsp_location_sidebar['display']['show_count'] ) ? $dpsp_location_sidebar['display']['show_count'] : '' ), __( 'Show share count for each network', 'mediavine' ), [ 'yes' ], __( 'Display the share count for each social network.<br /><br />Please note: You may see a zero share count for Facebook if the share count is under 100 shares due to limitations with Facebook’s API.', 'mediavine' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_count_total]', ( isset( $dpsp_location_sidebar['display']['show_count_total'] ) ? $dpsp_location_sidebar['display']['show_count_total'] : '' ), __( 'Show total share count', 'social-pug' ), [ 'yes' ], __( 'Display the share count for all social networks.', 'social-pug' ) ); ?>
				<div class="dpsp-setting-field-wrapper dpsp-setting-field-text dpsp-has-field-label">
					<span class="dpsp-email-save-this-help-text">Share counts will only be displayed once they're greater than zero. See each setting's tooltip for additional information.</span>
				</div>

				<?php
				dpsp_settings_field(
					'select', 'dpsp_location_sidebar[display][total_count_position]', ( isset( $dpsp_location_sidebar['display']['total_count_position'] ) ? $dpsp_location_sidebar['display']['total_count_position'] : '' ), __( 'Total count position', 'social-pug' ), [
						'before' => __( 'Before Buttons', 'social-pug' ),
						'after'  => __( 'After Buttons', 'social-pug' ),
					]
				);
?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][count_round]', ( isset( $dpsp_location_sidebar['display']['count_round'] ) ? $dpsp_location_sidebar['display']['count_round'] : '' ), __( 'Share count round', 'social-pug' ), [ 'yes' ], __( 'If the share count for each network is bigger than 1000 it will be rounded to one decimal ( eg. 1267 will show as 1.2k ). Applies to Total Share Counts as well.', 'social-pug' ) ); ?>

			</div>

		</div>

		<!-- Post Type Display Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Post Type Display Settings', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'checkbox', 'dpsp_location_sidebar[post_type_display][]', ( isset( $dpsp_location_sidebar['post_type_display'] ) ? $dpsp_location_sidebar['post_type_display'] : [] ), '', dpsp_get_post_types() ); ?>

			</div>

		</div>

		<!-- Save Changes Button -->
		<input type="hidden" name="action" value="update" />
		<p class="submit"><input type="submit" class="dpsp-button-primary" value="<?php esc_html_e( 'Save Changes' ); ?>" /></p>

	</div>

</form>

<?php do_action( 'dpsp_submenu_page_bottom' ); ?>
