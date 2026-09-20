<form method="post" action="options.php">
	<div class="dpsp-page-wrapper dpsp-page-sidebar wrap">

		<?php
			$dpsp_location_sidebar = Mediavine\Grow\Settings::get_setting( 'dpsp_location_sidebar', 'not_set' );
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


		<!-- Button Style Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Button Style', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php $settings = dpsp_get_back_end_display_option( 'dpsp_location_sidebar' ); ?>

				<input type="radio" id="dpsp-settings-button-style-input-1" name="dpsp_location_sidebar[button_style]" value="1" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_sidebar['button_style'] ) && '1' === $dpsp_location_sidebar['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-1" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-1 dpsp-has-button-background dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'share', 'sidebar' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-5" name="dpsp_location_sidebar[button_style]" value="5" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_sidebar['button_style'] ) && '5' === $dpsp_location_sidebar['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-5" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-5 dpsp-column-1 dpsp-button-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'share', 'sidebar' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-8" name="dpsp_location_sidebar[button_style]" value="8" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_sidebar['button_style'] ) && '8' === $dpsp_location_sidebar['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-8" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-8 dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'share', 'sidebar' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

			</div>

		</div>


		<!-- General Display Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Display Settings', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php
					dpsp_settings_field(
						'select',
						'dpsp_location_sidebar[display][shape]',
						$dpsp_location_sidebar['display']['shape'],
						__( 'Button shape', 'social-pug' ),
						[
							'rectangular' => __( 'Rectangular', 'social-pug' ),
							'rounded'     => __( 'Rounded', 'social-pug' ),
							'circle'      => __(
								'Circle',
								'social-pug'
							),
						]
					);
				?>

				<?php
					dpsp_settings_field(
						'select',
						'dpsp_location_sidebar[display][size]',
						( isset( $dpsp_location_sidebar['display']['size'] ) ? $dpsp_location_sidebar['display']['size'] : '' ),
						__( 'Button size', 'social-pug' ),
						[
							'small'  => __( 'Small', 'social-pug' ),
							'medium' => __( 'Medium', 'social-pug' ),
							'large'  => __(
								'Large',
								'social-pug'
							),
						]
					);
				?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][icon_animation]', ( isset( $dpsp_location_sidebar['display']['icon_animation'] ) ? $dpsp_location_sidebar['display']['icon_animation'] : '' ), __( 'Show icon animation', 'social-pug' ), [ 'yes' ], __( 'Will animate the social media icon when the user hovers over the button.', 'social-pug' ) ); ?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_sidebar[display][position]',
					$dpsp_location_sidebar['display']['position'],
					__( 'Buttons position', 'social-pug' ),
					[
						'left'  => __( 'Left', 'social-pug' ),
						'right' => __(
							'Right',
							'social-pug'
						),
					]
				);
?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_labels]', ( isset( $dpsp_location_sidebar['display']['show_labels'] ) ? $dpsp_location_sidebar['display']['show_labels'] : '' ), __( 'Show button labels on desktop', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][spacing]', ( isset( $dpsp_location_sidebar['display']['spacing'] ) ? $dpsp_location_sidebar['display']['spacing'] : '' ), __( 'Button spacing', 'social-pug' ), [ 'yes' ], __( 'Adds bottom spacing for each button.', 'social-pug' ) ); ?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_sidebar[display][intro_animation]',
					( isset( $dpsp_location_sidebar['display']['intro_animation'] ) ? $dpsp_location_sidebar['display']['intro_animation'] : '' ),
					__( 'Intro Animation', 'social-pug' ),
					[
						'-1' => __( 'No Animation', 'social-pug' ),
						'1'  => __( 'Fade In', 'social-pug' ),
						'2'  => __(
							'Slide In',
							'social-pug'
						),
					]
				);
?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_after_scrolling]', ( isset( $dpsp_location_sidebar['display']['show_after_scrolling'] ) ? $dpsp_location_sidebar['display']['show_after_scrolling'] : '' ), __( 'Show after user scrolls', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_sidebar[display][scroll_distance]', ( isset( $dpsp_location_sidebar['display']['scroll_distance'] ) ? $dpsp_location_sidebar['display']['scroll_distance'] : '' ), __( 'Scroll distance (%)', 'social-pug' ), [ '30' ], __( 'The distance in percentage (%) of the total page height the user has to scroll before the buttons will appear.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][hide_at_stop_selector]', ( isset( $dpsp_location_sidebar['display']['hide_at_stop_selector'] ) ? $dpsp_location_sidebar['display']['hide_at_stop_selector'] : '' ), __( 'Hide after reaching Element', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_sidebar[display][stop_selector]', ( isset( $dpsp_location_sidebar['display']['stop_selector'] ) ? $dpsp_location_sidebar['display']['stop_selector'] : '' ), __( 'Hide Element Selector', 'social-pug' ), [ '30' ], __( 'Hides the floating sidebar when it reaches this element on the page. By default, an empty value here will make it so the floating sidebar will not hide when it reaches the footer area.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_mobile]', ( isset( $dpsp_location_sidebar['display']['show_mobile'] ) ? $dpsp_location_sidebar['display']['show_mobile'] : '' ), __( 'Show on mobile', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_sidebar[display][screen_size]', ( isset( $dpsp_location_sidebar['display']['screen_size'] ) ? $dpsp_location_sidebar['display']['screen_size'] : '' ), __( 'Mobile screen width (pixels)', 'social-pug' ), [], __( 'For screen widths smaller than this value ( in pixels ) the buttons will be displayed on screen if the show on mobile option is checked.', 'social-pug' ) ); ?>

			</div>

		</div>

		<!-- Share Counts -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Buttons Share Counts', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_count]', ( isset( $dpsp_location_sidebar['display']['show_count'] ) ? $dpsp_location_sidebar['display']['show_count'] : '' ), __( 'Show share count for each network', 'mediavine' ), [ 'yes' ], __( 'Display the share count for each network individually.<br><br>You may not see a count for Facebook if it is under 100 due to limitations with Facebook\'s API.', 'mediavine' ) ); ?>
				
				<?php dpsp_settings_field( 'text', 'dpsp_location_sidebar[display][minimum_individual_count]', ( isset( $dpsp_location_sidebar['display']['minimum_individual_count'] ) ? $dpsp_location_sidebar['display']['minimum_individual_count'] : '' ), __( 'Minimum individual share count', 'social-pug' ), [], __( 'Individual share counts will be displayed only if the number of shares for that network is higher than this number.<br><br>Zero counts will never be displayed. Leave blank for no minimum.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][show_count_total]', ( isset( $dpsp_location_sidebar['display']['show_count_total'] ) ? $dpsp_location_sidebar['display']['show_count_total'] : '' ), __( 'Show total share count', 'social-pug' ), [ 'yes' ], __( 'Display the total share count across all networks.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_sidebar[display][minimum_count]', ( isset( $dpsp_location_sidebar['display']['minimum_count'] ) ? $dpsp_location_sidebar['display']['minimum_count'] : '' ), __( 'Minimum total share count', 'social-pug' ), [], __( 'Total share counts will be displayed only if the total number of shares across all networks is higher than this number.<br><br>Zero counts will never be displayed. Leave blank for no minimum.', 'social-pug' ) ); ?>
				<div class="dpsp-setting-field-wrapper dpsp-setting-field-text dpsp-has-field-label dpsp-setting-field-text-total-share-count disabled">
					<span class="dpsp-email-save-this-help-text">Share counts will not be displayed at all (total or individuals) unless the total number of shares across all networks is higher than this number.</span>
				</div>
				
				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_sidebar[display][total_count_position]',
					( isset( $dpsp_location_sidebar['display']['total_count_position'] ) ? $dpsp_location_sidebar['display']['total_count_position'] : '' ),
					__( 'Total count position', 'social-pug' ),
					[
						'before' => __( 'Before Buttons', 'social-pug' ),
						'after'  => __(
							'After Buttons',
							'social-pug'
						),
					]
				); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_sidebar[display][count_round]', ( isset( $dpsp_location_sidebar['display']['count_round'] ) ? $dpsp_location_sidebar['display']['count_round'] : '' ), __( 'Share count round', 'social-pug' ), [ 'yes' ], __( 'Shortens large share counts into a human-readable format (for example, 1267 becomes 1.2k).', 'social-pug' ) ); ?>


			</div>

		</div>

		<!-- Custom Colors Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Buttons Custom Colors', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'color-picker', 'dpsp_location_sidebar[display][custom_color]', ( isset( $dpsp_location_sidebar['display']['custom_color'] ) ? $dpsp_location_sidebar['display']['custom_color'] : '' ), __( 'Buttons color', 'social-pug' ), [] ); ?>
				<?php dpsp_settings_field( 'color-picker', 'dpsp_location_sidebar[display][custom_hover_color]', ( isset( $dpsp_location_sidebar['display']['custom_hover_color'] ) ? $dpsp_location_sidebar['display']['custom_hover_color'] : '' ), __( 'Buttons hover color', 'social-pug' ), [] ); ?>

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
		<p class="submit"><input type="submit" class="dpsp-button-primary" value="<?php esc_attr_e( 'Save Changes', 'social-pug' ); ?>" /></p>
		<p><strong>Please note:</strong> To ensure that changes take effect, please clear all caches. (Need help? <a href="https://morehubbub.com/docs/cache-help/" title="Read our support doc on caches">See our support doc</a>.)</p>

		<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-made-with-love.php' ); ?>
		<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-unlock-features.php' ); ?>

		<?php do_action( 'dpsp_submenu_page_floating_sidebar_after_made_with_love' ); ?>
		
	</div>
</form>
<?php do_action( 'dpsp_submenu_page_bottom' ); ?>
