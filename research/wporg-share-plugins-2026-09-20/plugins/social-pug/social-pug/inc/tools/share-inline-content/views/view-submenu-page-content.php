<form method="post" action="options.php">

	<?php
		$dpsp_location_content = Mediavine\Grow\Settings::get_setting( 'dpsp_location_content', 'not_set' );
		settings_fields( 'dpsp_location_content' );
	?>

	<div class="dpsp-page-wrapper dpsp-page-content wrap">

		<!-- Page Title -->
		<h1 class="dpsp-page-title">
			<?php esc_html_e( 'Configure Content Sharing Buttons', 'social-pug' ); ?>

			<input type="hidden" name="dpsp_buttons_location" value="dpsp_location_content" />
			<input type="hidden" name="dpsp_location_content[active]" value="<?php echo ( isset( $dpsp_location_content['active'] ) ? 1 : '' ); ?>" <?php echo ( ! isset( $dpsp_location_content['active'] ) ? 'disabled' : '' ); ?> />
		</h1>


		<!-- Networks Selectable and Sortable Panels -->
		<div id="dpsp-social-platforms-wrapper" class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Social Networks', 'social-pug' ); ?>
				<a id="dpsp-select-networks" class="dpsp-button-secondary" href="#"><?php esc_html_e( 'Select Networks', 'social-pug' ); ?></a>
			</div>

			<div id="dpsp-sortable-networks-empty" class="dpsp-card-inner <?php echo ( empty( $dpsp_location_content['networks'] ) ? 'dpsp-active' : '' ); ?>">
				<p><?php esc_html_e( 'Select which social buttons to display', 'social-pug' ); ?></p>
			</div>

			<?php echo dpsp_output_sortable_networks( ( ! empty( $dpsp_location_content['networks'] ) ? $dpsp_location_content['networks'] : [] ), 'dpsp_location_content' ); // @codingStandardsIgnoreLine — escaping is already being done in the function ?>

			<?php
				$available_networks = dpsp_get_networks();
				echo dpsp_output_selectable_networks( $available_networks, ( ! empty( $dpsp_location_content['networks'] ) ? $dpsp_location_content['networks'] : [] ) );  // @codingStandardsIgnoreLine — escaping is already being done in the function
			?>

		</div>


		<!-- Button Style Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Button Style', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">
				<?php $settings = dpsp_get_back_end_display_option( 'dpsp_location_content' ); ?>

				<input type="radio" id="dpsp-settings-button-style-input-1" name="dpsp_location_content[button_style]" value="1" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '1' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-1" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-1 dpsp-has-icon-background dpsp-has-button-background dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-2" name="dpsp_location_content[button_style]" value="2" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '2' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-2" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-2 dpsp-has-icon-background dpsp-has-icon-dark dpsp-has-button-background dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-3" name="dpsp_location_content[button_style]" value="3" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '3' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-3" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-3 dpsp-column-1 dpsp-has-icon-background dpsp-button-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-4" name="dpsp_location_content[button_style]" value="4" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '4' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-4" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-4 dpsp-column-1 dpsp-has-button-background dpsp-icon-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-5" name="dpsp_location_content[button_style]" value="5" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '5' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-5" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-5 dpsp-column-1 dpsp-button-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-6" name="dpsp_location_content[button_style]" value="6" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '6' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-6" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-6 dpsp-column-1 dpsp-has-icon-background <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-7" name="dpsp_location_content[button_style]" value="7" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '7' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-7" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-7 dpsp-column-1 dpsp-icon-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-8" name="dpsp_location_content[button_style]" value="8" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_content['button_style'] ) && '8' === $dpsp_location_content['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-8" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-8 dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings );  // @codingStandardsIgnoreLine — escaping is already being done in the function ?>
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
					'dpsp_location_content[display][shape]',
					( isset( $dpsp_location_content['display']['shape'] ) ? $dpsp_location_content['display']['shape'] : '' ),
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
					'dpsp_location_content[display][size]',
					( isset( $dpsp_location_content['display']['size'] ) ? $dpsp_location_content['display']['size'] : '' ),
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

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][icon_animation]', ( isset( $dpsp_location_content['display']['icon_animation'] ) ? $dpsp_location_content['display']['icon_animation'] : '' ), __( 'Show icon animation', 'social-pug' ), [ 'yes' ], __( 'Will animate the social media icon when the user hovers over the button.', 'social-pug' ) ); ?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_content[display][position]',
					( isset( $dpsp_location_content['display']['position'] ) ? $dpsp_location_content['display']['position'] : '' ),
					__( 'Buttons Position', 'social-pug' ),
					[
						'top'    => __( 'Above Content', 'social-pug' ),
						'bottom' => __( 'Below Content', 'social-pug' ),
						'both'   => __(
							'Above and Below',
							'social-pug'
						),
					]
				);
?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_content[display][column_count]',
					( isset( $dpsp_location_content['display']['column_count'] ) ? $dpsp_location_content['display']['column_count'] : '' ),
					__( 'Number of columns', 'social-pug' ),
					[
						'auto' => __( 'Width Auto', 'social-pug' ),
						'1'    => __( '1 column', 'social-pug' ),
						'2'    => __( '2 columns', 'social-pug' ),
						'3'    => __( '3 columns', 'social-pug' ),
						'4'    => __( '4 columns', 'social-pug' ),
						'5'    => __( '5 columns', 'social-pug' ),
						'6'    => __(
							'6 columns',
							'social-pug'
						),
					]
				);
?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_content[display][message]', ( isset( $dpsp_location_content['display']['message'] ) ? $dpsp_location_content['display']['message'] : '' ), __( 'Share Text', 'social-pug' ), [] ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][spacing]', ( isset( $dpsp_location_content['display']['spacing'] ) ? $dpsp_location_content['display']['spacing'] : '' ), __( 'Button spacing', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][show_mobile]', ( isset( $dpsp_location_content['display']['show_mobile'] ) ? $dpsp_location_content['display']['show_mobile'] : '' ), __( 'Show buttons on mobile', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][show_labels_mobile]', ( isset( $dpsp_location_content['display']['show_labels_mobile'] ) ? $dpsp_location_content['display']['show_labels_mobile'] : '' ), __( 'Show button labels on mobile', 'social-pug' ), [ 'yes' ], __( 'The Show button labels setting (above) must be enabled.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][show_labels]', ( isset( $dpsp_location_content['display']['show_labels'] ) ? $dpsp_location_content['display']['show_labels'] : '' ), __( 'Show button labels on desktop', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_content[display][screen_size]', ( isset( $dpsp_location_content['display']['screen_size'] ) ? $dpsp_location_content['display']['screen_size'] : '' ), __( 'Mobile screen width (pixels)', 'social-pug' ), [], __( 'For screen widths smaller than this value ( in pixels ) the buttons will be displayed on screen if the show on mobile option is checked.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][double_inline_content_markup]', ( isset( $dpsp_location_content['display']['double_inline_content_markup'] ) ? $dpsp_location_content['display']['double_inline_content_markup'] : '' ), __( 'Enable second content render', 'social-pug' ), [ true ], __( 'By default, Hubbub displays the inline content buttons a single time. Some themes and plugins may cause Hubbub to display at an incorrect time and cause it not to appear. Enabling this setting can help with this issue.', 'social-pug' ) ); ?>

			</div>

		</div>

		<!-- Share Counts -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Buttons Share Counts', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][show_count]', ( isset( $dpsp_location_content['display']['show_count'] ) ? $dpsp_location_content['display']['show_count'] : '' ), __( 'Show share count for each network', 'mediavine' ), [ 'yes' ], __( 'Display the share count for each network individually.<br><br>You may not see a count for Facebook if it is under 100 due to limitations with Facebook\'s API.', 'mediavine' ) ); ?>
				
				<?php dpsp_settings_field( 'text', 'dpsp_location_content[display][minimum_individual_count]', ( isset( $dpsp_location_content['display']['minimum_individual_count'] ) ? $dpsp_location_content['display']['minimum_individual_count'] : '' ), __( 'Minimum individual share count', 'social-pug' ), [], __( 'Individual share counts will be displayed only if the number of shares for that network is higher than this number.<br><br>Zero counts will never be displayed. Leave blank for no minimum.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][show_count_total]', ( isset( $dpsp_location_content['display']['show_count_total'] ) ? $dpsp_location_content['display']['show_count_total'] : '' ), __( 'Show total share count', 'social-pug' ), [ 'yes' ], __( 'Display the total share count across all networks.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'text', 'dpsp_location_content[display][minimum_count]', ( isset( $dpsp_location_content['display']['minimum_count'] ) ? $dpsp_location_content['display']['minimum_count'] : '' ), __( 'Minimum total share count', 'social-pug' ), [], __( 'Total share counts will be displayed only if the total number of shares across all networks is higher than this number.<br><br>Zero counts will never be displayed. Leave blank for no minimum.', 'social-pug' ) ); ?>
				<div class="dpsp-setting-field-wrapper dpsp-setting-field-text dpsp-has-field-label dpsp-setting-field-text-total-share-count disabled">
					<span class="dpsp-email-save-this-help-text">Share counts will not be displayed at all (total or individuals) unless the total number of shares across all networks is higher than this number.</span>
				</div>
				
				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_content[display][total_count_position]',
					( isset( $dpsp_location_content['display']['total_count_position'] ) ? $dpsp_location_content['display']['total_count_position'] : '' ),
					__( 'Total count position', 'social-pug' ),
					[
						'before' => __( 'Before Buttons', 'social-pug' ),
						'after'  => __(
							'After Buttons',
							'social-pug'
						),
					]
				); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_content[display][count_round]', ( isset( $dpsp_location_content['display']['count_round'] ) ? $dpsp_location_content['display']['count_round'] : '' ), __( 'Share count round', 'social-pug' ), [ 'yes' ], __( 'Shortens large share counts into a human-readable format (for example, 1267 becomes 1.2k).', 'social-pug' ) ); ?>


			</div>

		</div>

		<!-- Custom Colors Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Buttons Custom Colors', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'color-picker', 'dpsp_location_content[display][custom_color]', ( isset( $dpsp_location_content['display']['custom_color'] ) ? $dpsp_location_content['display']['custom_color'] : '' ), __( 'Buttons color', 'social-pug' ), [] ); ?>
				<?php dpsp_settings_field( 'color-picker', 'dpsp_location_content[display][custom_hover_color]', ( isset( $dpsp_location_content['display']['custom_hover_color'] ) ? $dpsp_location_content['display']['custom_hover_color'] : '' ), __( 'Buttons hover color', 'social-pug' ), [] ); ?>

			</div>

		</div>


		<!-- Post Type Display Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Post Type Display Settings', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'checkbox', 'dpsp_location_content[post_type_display][]', ( isset( $dpsp_location_content['post_type_display'] ) ? $dpsp_location_content['post_type_display'] : [] ), '', dpsp_get_post_types() ); ?>

			</div>

		</div>


		<!-- Save Changes Button -->
		<input type="hidden" name="action" value="update" />
		<p class="submit"><input type="submit" class="dpsp-button-primary" value="<?php esc_html_e( 'Save Changes' ); ?>" /></p>
		<p><strong>Please note:</strong> To ensure that changes take effect, please clear all caches. (Need help? <a href="https://morehubbub.com/docs/cache-help/" title="Read our support doc on caches">See our support doc</a>.)</p>

		<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-made-with-love.php' ); ?>
		<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-unlock-features.php' ); ?>

		<?php do_action( 'dpsp_submenu_page_inline_content_after_made_with_love' ); ?>
	</div>

</form>
<?php do_action( 'dpsp_submenu_page_bottom' ); ?>
