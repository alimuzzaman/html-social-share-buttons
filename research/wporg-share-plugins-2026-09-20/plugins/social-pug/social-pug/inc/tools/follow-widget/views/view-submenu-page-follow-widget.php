<form method="post" action="options.php">

	<?php
		$dpsp_location_follow_widget = Mediavine\Grow\Settings::get_setting( 'dpsp_location_follow_widget', 'not_set' );
		settings_fields( 'dpsp_location_follow_widget' );
	?>

	<div class="dpsp-page-wrapper dpsp-page-content wrap">

		<!-- Page Title -->
		<h1 class="dpsp-page-title">
			<?php esc_html_e( 'Configure Follow Widget Buttons', 'social-pug' ); ?>

			<input type="hidden" name="dpsp_buttons_location" value="dpsp_location_follow_widget" />
			<input type="hidden" name="dpsp_location_follow_widget[active]" value="<?php echo ( isset( $dpsp_location_follow_widget['active'] ) ? 1 : '' ); ?>" <?php echo ( ! isset( $dpsp_location_follow_widget['active'] ) ? 'disabled' : '' ); ?> />
		</h1>


		<!-- Networks Selectable and Sortable Panels -->
		<div id="dpsp-social-platforms-wrapper" class="dpsp-card follow-widget-settings">

			<?php
			$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
			$count_networks_missing = 0;
			$missing_accounts = [];
			foreach ( $dpsp_location_follow_widget['networks'] as $key=>$label ) {
				if ( ( isset( $settings["{$key}_username"]) && empty( $settings["{$key}_username"] ) ) ||  ! isset( $settings["{$key}_username"])) {
					$missing_accounts[] = $key;
					$count_networks_missing++;
				}
			}
			?>

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Social Networks', 'social-pug' ); ?>
				<?php if ( $count_networks_missing > 0 ) {
					$string_usernames = ( $count_networks_missing > 1 ) ? 'usernames' : 'username';
					?>
					<div class="dpsp-setting-field-tooltip-wrapper ">
						<span class="dpsp-setting-field-tooltip-icon dpsp-follow-widget-not-set-icon"></span>
						<div class="dpsp-setting-field-tooltip dpsp-transition" style="opacity: 0; visibility: hidden;">You have <?=$count_networks_missing;?> <?=$string_usernames;?> missing account information. Click "Edit Usernames" button.</div>
					</div>
				<?php } ?>
				<a id="dpsp-edit-network-info" class="dpsp-button-secondary" href="<?=admin_url( 'admin.php?page=dpsp-settings&dpsp-tab=social-identity' )?>"><?php echo esc_html__( 'Edit Usernames', 'social-pug' ); ?></a>
				<a id="dpsp-select-networks" class="dpsp-button-secondary" href="#"><?php echo esc_html__( 'Select Networks', 'social-pug' ); ?></a>
			</div>

			<div id="dpsp-sortable-networks-empty" class="dpsp-card-inner <?php echo ( empty( $dpsp_location_follow_widget['networks'] ) ? 'dpsp-active' : '' ); ?>">
				<p><?php esc_html_e( 'Select which social buttons to display', 'social-pug' ); ?></p>
			</div>

			<?php echo dpsp_output_sortable_networks( ( ! empty( $dpsp_location_follow_widget['networks'] ) ? $dpsp_location_follow_widget['networks'] : [] ), 'dpsp_location_follow_widget', $missing_accounts );  // @codingStandardsIgnoreLine — escaping is done in the function ?>

			<?php
				$available_networks = dpsp_get_networks( 'follow' );
				echo dpsp_output_selectable_networks( $available_networks, ( ! empty( $dpsp_location_follow_widget['networks'] ) ? $dpsp_location_follow_widget['networks'] : [] ) );  // @codingStandardsIgnoreLine — escaping is done in the function
			?>

		</div>


		<!-- Button Style Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Button Style', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php $settings = dpsp_get_back_end_display_option( 'dpsp_location_follow_widget' ); ?>

				<input type="radio" id="dpsp-settings-button-style-input-1" name="dpsp_location_follow_widget[button_style]" value="1" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '1' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-1" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-1 dpsp-has-icon-background dpsp-has-button-background dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' ); // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-2" name="dpsp_location_follow_widget[button_style]" value="2" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '2' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-2" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-2 dpsp-has-icon-background dpsp-has-icon-dark dpsp-has-button-background dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-3" name="dpsp_location_follow_widget[button_style]" value="3" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '3' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-3" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-3 dpsp-column-1 dpsp-has-icon-background dpsp-button-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-4" name="dpsp_location_follow_widget[button_style]" value="4" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '4' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-4" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-4 dpsp-column-1 dpsp-has-button-background dpsp-icon-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-5" name="dpsp_location_follow_widget[button_style]" value="5" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '5' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-5" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-5 dpsp-column-1 dpsp-button-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-6" name="dpsp_location_follow_widget[button_style]" value="6" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '6' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-6" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-6 dpsp-column-1 dpsp-has-icon-background <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-7" name="dpsp_location_follow_widget[button_style]" value="7" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '7' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-7" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-7 dpsp-column-1 dpsp-icon-hover <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
					</div>
				</label>

				<input type="radio" id="dpsp-settings-button-style-input-8" name="dpsp_location_follow_widget[button_style]" value="8" class="dpsp-settings-button-style-input" <?php echo isset( $dpsp_location_follow_widget['button_style'] ) && '8' === $dpsp_location_follow_widget['button_style'] ? 'checked="checked"' : ''; ?> />
				<label for="dpsp-settings-button-style-input-8" class="dpsp-settings-button-style dpsp-transition">
					<div class="dpsp-button-style-8 dpsp-column-1 <?php echo( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . esc_attr( $settings['display']['shape'] ) : '' ); ?>">
						<?php echo dpsp_get_output_network_buttons( $settings, 'follow',  'follow_widget' );  // @codingStandardsIgnoreLine — escaping is done in the function ?>
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
					'dpsp_location_follow_widget[display][shape]',
					( isset( $dpsp_location_follow_widget['display']['shape'] ) ? $dpsp_location_follow_widget['display']['shape'] : '' ),
					esc_html__( 'Button shape', 'social-pug' ),
					[
						'rectangular' => esc_html__( 'Rectangular', 'social-pug' ),
						'rounded'     => esc_html__( 'Rounded', 'social-pug' ),
						'circle'      => esc_html__(
							'Circle',
							'social-pug'
						),
					]
				);
				?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_follow_widget[display][size]',
					( isset( $dpsp_location_follow_widget['display']['size'] ) ? $dpsp_location_follow_widget['display']['size'] : '' ),
					esc_html__( 'Button size', 'social-pug' ),
					[
						'small'  => esc_html__( 'Small', 'social-pug' ),
						'medium' => esc_html__( 'Medium', 'social-pug' ),
						'large'  => esc_html__(
							'Large',
							'social-pug'
						),
					]
				);
	?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_follow_widget[display][icon_animation]', ( isset( $dpsp_location_follow_widget['display']['icon_animation'] ) ? $dpsp_location_follow_widget['display']['icon_animation'] : '' ), esc_html__( 'Show icon animation', 'social-pug' ), [ 'yes' ], esc_html__( 'Will animate the social media icon when the user hovers over the button.', 'social-pug' ) ); ?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_follow_widget[display][column_count]',
					( isset( $dpsp_location_follow_widget['display']['column_count'] ) ? $dpsp_location_follow_widget['display']['column_count'] : '' ),
					esc_html__( 'Number of columns', 'social-pug' ),
					[
						'auto' => esc_html__( 'Width Auto', 'social-pug' ),
						'1'    => esc_html__( '1 column', 'social-pug' ),
						'2'    => esc_html__( '2 columns', 'social-pug' ),
						'3'    => esc_html__( '3 columns', 'social-pug' ),
						'4'    => esc_html__( '4 columns', 'social-pug' ),
						'5'    => esc_html__( '5 columns', 'social-pug' ),
						'6'    => esc_html__(
							'6 columns',
							'social-pug'
						),
					]
				);
?>

				<?php
				dpsp_settings_field(
					'select',
					'dpsp_location_follow_widget[display][alignment]',
					( isset( $dpsp_location_follow_widget['display']['alignment'] ) ? $dpsp_location_follow_widget['display']['alignment'] : '' ),
					esc_html__( 'Button alignment', 'social-pug' ),
					[
						'left'   => esc_html__( 'Left', 'social-pug' ),
						'center' => esc_html__( 'Center', 'social-pug' ),
						'right'  => esc_html__(
							'Right',
							'social-pug'
						),
					]
				);
?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_follow_widget[display][show_labels]', ( isset( $dpsp_location_follow_widget['display']['show_labels'] ) ? $dpsp_location_follow_widget['display']['show_labels'] : '' ), esc_html__( 'Show button labels', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_follow_widget[display][show_labels_mobile]', ( isset( $dpsp_location_follow_widget['display']['show_labels_mobile'] ) ? $dpsp_location_follow_widget['display']['show_labels_mobile'] : '' ), esc_html__( 'Show labels on mobile', 'social-pug' ), [ 'yes' ], __( 'The Show button labels setting (above) must be enabled.', 'social-pug' ) ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_follow_widget[display][spacing]', ( isset( $dpsp_location_follow_widget['display']['spacing'] ) ? $dpsp_location_follow_widget['display']['spacing'] : '' ), esc_html__( 'Button spacing', 'social-pug' ), [ 'yes' ] ); ?>

				<?php dpsp_settings_field( 'switch', 'dpsp_location_follow_widget[display][show_mobile]', ( isset( $dpsp_location_follow_widget['display']['show_mobile'] ) ? $dpsp_location_follow_widget['display']['show_mobile'] : '' ), esc_html__( 'Show on mobile', 'social-pug' ), [ 'yes' ] ); ?>

			</div>

		</div>


		<!-- Custom Colors Settings -->
		<div class="dpsp-card">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Buttons Custom Colors', 'social-pug' ); ?>
			</div>

			<div class="dpsp-card-inner">

				<?php dpsp_settings_field( 'color-picker', 'dpsp_location_follow_widget[display][custom_color]', ( isset( $dpsp_location_follow_widget['display']['custom_color'] ) ? $dpsp_location_follow_widget['display']['custom_color'] : '' ), esc_html__( 'Buttons color', 'social-pug' ), [] ); ?>
				<?php dpsp_settings_field( 'color-picker', 'dpsp_location_follow_widget[display][custom_hover_color]', ( isset( $dpsp_location_follow_widget['display']['custom_hover_color'] ) ? $dpsp_location_follow_widget['display']['custom_hover_color'] : '' ), esc_html__( 'Buttons hover color', 'social-pug' ), [] ); ?>

			</div>

		</div>


		<!-- Save Changes Button -->
		<input type="hidden" name="action" value="update" />
		<p class="submit"><input type="submit" class="dpsp-button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" /></p>
		<p><strong>Please note:</strong> To ensure that changes take effect, please clear all caches. (Need help? <a href="https://morehubbub.com/docs/cache-help/" title="Read our support doc on caches">See our support doc</a>.)</p>

		<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-made-with-love.php' ); ?>

		<?php do_action( 'dpsp_submenu_page_follow_widget_after_made_with_love' ); ?>
	</div>

</form>
