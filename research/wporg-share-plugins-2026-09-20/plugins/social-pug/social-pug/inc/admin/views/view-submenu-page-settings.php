<div class="dpsp-page-wrapper dpsp-page-settings wrap">

	<h1 class="dpsp-page-title"><?php esc_html_e( 'Settings', 'social-pug' ); ?></h1>

	<form method="post" action="options.php">
		<?php
		$dpsp_settings = get_option( 'dpsp_settings', 'not_set' );
		$mv_grow_license = Mediavine\Grow\Settings::get_setting( 'mv_grow_license', null );

		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;

		if ( empty( $mv_grow_license ) && isset( $dpsp_settings['mv_grow_license'] ) && ! empty( $dpsp_settings['mv_grow_license'] ) ) {
			update_option( 'mv_grow_license', $dpsp_settings['mv_grow_license'] ); // Just for Lite?
		}
		if ( empty( $dpsp_settings['mv_grow_license'] ) && ! empty( $mv_grow_license ) ) {
			delete_option( 'mv_grow_license' );
		}
		settings_fields( 'dpsp_settings' );
		?>

		<!-- General Settings Tab Content -->
		<div id="dpsp-tab-general-settings">

			<?php 
			if ( $hubbub_activation_lite->is_lite_registered() ) {
				include( \Mediavine\Grow\View_Loader::$plugin_path . '/inc/admin/views/view-tab-settings-social-identity.php' );
			}  else { ?>

			<div class="dpsp-card">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'Social Identity', 'social-pug' ); ?>
				</div>

				<div class="dpsp-card-inner">

					<?php dpsp_settings_field( 'text', 'dpsp_settings[twitter_username]', ( isset( $dpsp_settings['twitter_username'] ) ? $dpsp_settings['twitter_username'] : '' ), __( 'X Username', 'social-pug' ), [ '' ] ); ?>
					<?php dpsp_settings_field( 'switch', 'dpsp_settings[tweets_have_username]', ( isset( $dpsp_settings['tweets_have_username'] ) ? $dpsp_settings['tweets_have_username'] : '' ), __( 'Add X Username to all tweets', 'social-pug' ), [ 'yes' ] ); ?>

				</div>

			</div>

			<?php } ?>

			<!-- Misc -->
			<div id="dpsp-card-misc" class="dpsp-card">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'Misc', 'social-pug' ); ?>
				</div>

				<div class="dpsp-card-inner">

					<?php
					dpsp_settings_field(
						'select', 'dpsp_settings[facebook_share_counts_provider]', ( isset( $dpsp_settings['facebook_share_counts_provider'] ) ? $dpsp_settings['facebook_share_counts_provider'] : '' ), __( 'Facebook Share Counts Provider', 'social-pug' ), [
							'authorized_app' => __( 'Hubbub Lite App', 'social-pug' ),
							'own_app'        => __( 'Facebook Graph API', 'social-pug' ),
						]
					);
?>

					<div class="dpsp-setting-field-wrapper dpsp-setting-field-text dpsp-has-field-label dpsp-setting-field-facebook-authorize-app">

						<?php $facebook_access_token = Mediavine\Grow\Settings::get_setting( 'dpsp_facebook_access_token' ); ?>

						<?php if ( ! empty( $facebook_access_token['access_token'] ) && ! empty( $facebook_access_token['expires_in'] ) ) : ?>

							<?php if ( time() < $facebook_access_token['expires_in'] ) : ?>

								<div class="dpsp-setting-field-facebook-app-authorized">
									<span class="dashicons dashicons-yes"></span>
									<strong><?php esc_html_e( 'Authorized', 'social-pug' ); ?></strong>
									<?php
									// translators: %s
									echo wp_kses_post( '- ' . sprintf( __( 'Expires on %s', 'social-pug' ), date( 'F d, Y', absint( $facebook_access_token['expires_in'] ) ) ) );
									?>
								</div>

							<?php else : ?>

								<div class="dpsp-setting-field-facebook-app-authorized-expired">
									<span class="dashicons dashicons-warning"></span>
									<strong><?php esc_html_e( 'Authorization Expired', 'social-pug' ); ?></strong>
									<?php esc_html_e( '- Please reauthorize.', 'social-pug' ); ?>
								</div>
								<?php
								$api_url = add_query_arg(
									[
										'action'     => 'authorize_facebook_app_free',
										'referer'    => home_url(),
										'tkn'        => wp_create_nonce( 'dpsp_authorize_facebook_app' ),
										'client_url' => urlencode( add_query_arg( [ 'page' => 'dpsp-settings' ], admin_url( 'admin.php' ) ) ), // @codingStandardsIgnoreLine
									], 'https://api.morehubbub.com/1.0/'
								);
								?>
								<a class="dpsp-button-primary" href="<?php echo esc_url( $api_url ); ?>"><?php esc_html_e( 'Reauthorize App', 'social-pug' ); ?></a>

							<?php endif; ?>

						<?php else : ?>
							<?php
							$api_url =
									add_query_arg(
										[
											'action'     => 'authorize_facebook_app_free',
											'referer'    => home_url(),
											'tkn'        => wp_create_nonce( 'dpsp_authorize_facebook_app' ),
											'client_url' => urlencode( add_query_arg( [ 'page' => 'dpsp-settings' ], admin_url( 'admin.php' ) ) ), // @codingStandardsIgnoreLine
										], 'https://api.morehubbub.com/1.0/'
									);
							?>
							<a class="dpsp-button-primary" href="<?php echo esc_url( $api_url ); ?>"><?php esc_html_e( 'Authorize App', 'social-pug' ); ?></a>

						<?php endif; ?>

					</div>

					<?php dpsp_settings_field( 'text', 'dpsp_settings[facebook_app_id]', ( isset( $dpsp_settings['facebook_app_id'] ) ? $dpsp_settings['facebook_app_id'] : '' ), __( 'Facebook App ID', 'social-pug' ), [ '' ] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[facebook_app_secret]', ( isset( $dpsp_settings['facebook_app_secret'] ) ? $dpsp_settings['facebook_app_secret'] : '' ), __( 'Facebook App Secret', 'social-pug' ), [ '' ] ); ?>
					<?php dpsp_settings_field( 'switch', 'dpsp_settings[disable_meta_tags]', ( isset( $dpsp_settings['disable_meta_tags'] ) ? $dpsp_settings['disable_meta_tags'] : '' ), __( 'Disable Open Graph Meta Tags', 'social-pug' ), [ 'yes' ] ); ?>

				</div>

			</div>

			<div class="dpsp-card">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'License Key', 'social-pug' ); ?>
				</div>

				<div class="dpsp-card-inner">

					<?php
					
					
						// translators: %s Plugin Name
						dpsp_settings_field( 'text', 'dpsp_settings[mv_grow_license]', ( isset( $dpsp_settings['mv_grow_license'] ) ? $dpsp_settings['mv_grow_license'] : '' ), 'Lite License key' );

						$license_tier = $hubbub_activation_lite->get_license_tier();
						?>
						<div class="dpsp-setting-field-wrapper dpsp-setting-field-text dpsp-has-field-label">
							<span class="dpsp-email-save-this-help-text">
								<?php
								if ( isset( $dpsp_settings['mv_grow_license'] ) && $license_tier == 'lite' ) {
									$license_text = 'This is a Hubbub Lite license key.';
								} else {
									$license_text = '<a href="#" class="dpsp-button-secondary dpsp-button-unlock-hubbub-lite" id="dpsp-button-unlock-features-license-key">' . esc_html__( '🔓 Register For Free', 'social-pug' ) . '</a> ';
									$license_text .= '<a href="https://morehubbub.com/" class="dpsp-button-secondary" target="_blank">' . esc_html__( '🔓 Get Pro Features', 'social-pug' ) . '</a>';
									$license_text .= '<br/>';
									
								}
								echo $license_text;
								?>
							</span>
				</div>

			</div>

		</div><!-- End of General Settings Tab Content -->

		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="dpsp_settings[always_update]" value="<?php echo ( isset( $dpsp_settings['always_update'] ) && 1 === $dpsp_settings['always_update'] ? 0 : 1 ); ?>" />
		<p class="submit"><input type="submit" class="dpsp-button-primary" value="<?php esc_html_e( 'Save Changes' ); ?>" /></p>
		<p><strong>Please note:</strong> To ensure that changes take effect, please clear all caches. (Need help? <a href="https://morehubbub.com/docs/cache-help/" title="Read our support doc on caches">See our support doc</a>.)</p>
	</form>

	<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-made-with-love.php' ); ?>
	<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-unlock-features.php' ); ?>

	<?php do_action( 'dpsp_submenu_page_settings_lite_after_made_with_love' ); ?>
	
</div>
</div>

<?php do_action( 'dpsp_submenu_page_bottom' ); ?>
