<?php
/**
 * Meta-boxes file
 */

use Mediavine\Grow\Share_Count_Url_Counts;
use Mediavine\Grow\View_Loader;
use Mediavine\Grow\Share_Counts;
use Mediavine\Grow\Admin_Messages;
/**
 * Individual posts share statistics meta-box.
 */
function dpsp_meta_boxes() {

	$screens  = get_post_types( [ 'public' => true ] );
	$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

	if ( empty( $screens ) ) {
		return;
	}

	// Remove the attachment post type
	if ( ! empty( $screens['attachment'] ) ) {
		unset( $screens['attachment'] );
	}

	foreach ( $screens as $screen ) {
		// Share option meta-box (Pro only)
		if ( ! Social_Pug::is_free() ) {
			add_meta_box( 'dpsp_share_options', __( 'Hubbub: Share Options', 'social-pug' ), 'dpsp_share_options_output', $screen, 'normal', 'core' );
			// Share statistics meta-box
			add_meta_box( 'dpsp_share_statistics', __( 'Hubbub: Share Statistics', 'social-pug' ), 'dpsp_share_statistics_output', $screen, 'normal', 'core' );
		}
		// Add debugger metabox
		if ( ! empty( $settings['debugger_enabled'] ) ) {
			add_meta_box( 'dpsp_post_debugger', __( 'Hubbub: Debug Log', 'social-pug' ), 'dpsp_post_debugger_output', $screen, 'normal', 'core' );
		}
	}

}

/**
 * Callback for the Share Options meta box
 *
 * @param mixed $post The post the output share options for
 */
function dpsp_share_options_output( $post ) {

	// Get general settings
	$settings           = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
	$pinterest_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_pinterest_share_images_setting', [] );

	// Pull share options meta data
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post->ID, 'dpsp_share_options', true );

	if ( empty( $share_options ) || ! is_array( $share_options ) ) {
		$share_options = [];
	}

	// Nonce field
	wp_nonce_field( 'dpsp_meta_box', 'dpsptkn' );

	/**
	 * New version
	 *
	 */
	echo '<div id="dpsp_share_options_content">';

		// General social media content
		echo '<div class="dpsp-section">';
			// Social media image
			echo '<div class="dpsp-setting-field-wrapper dpsp-setting-field-image">';
				echo '<label for="dpsp_share_options[custom_image]"><span class="dpsp-admin-icon dpsp-admin-icon-share">' . wp_kses( dpsp_get_svg_icon_output( 'share' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Social Media Image', 'social-pug' );
				dpsp_output_backend_tooltip( __( 'Add an image that will populate the "og:image" Open Graph meta tag. For maximum exposure on social networks (excluding Pinterest) we recommend an image size of 1200px X 630px.', 'social-pug' ), false );
				echo '</label>';
				echo '<div>';

					$thumb_details = [];
					$image_details = [];

					if ( ! empty( $share_options['custom_image']['id'] ) ) {
						$thumb_details = wp_get_attachment_image_src( $share_options['custom_image']['id'], 'high' );
						$image_details = wp_get_attachment_image_src( $share_options['custom_image']['id'], 'full' );
					}

					if ( ! empty( $thumb_details[0] ) && ! empty( $image_details[0] ) ) {
						$thumb_src = $thumb_details[0];
						$image_src = $image_details[0];
					} else {
						$thumb_src = DPSP_PLUGIN_DIR_URL . 'assets/dist/custom-social-media-image.png?' . DPSP_VERSION;
						$image_src = '';
					}

					echo '<div>';
					echo '<img src="' . esc_url( $thumb_src ) . '" data-pin-nopin="true" />';
					echo '<span class="dpsp-field-image-placeholder" data-src="' . esc_url( DPSP_PLUGIN_DIR_URL . 'assets/dist/custom-social-media-image.png?' . DPSP_VERSION ) . '"></span>';
					echo '</div>';

					echo '<a class="dpsp-image-select dpsp-button-primary ' . ( ! empty( $share_options['custom_image']['id'] ) ? 'dpsp-hidden' : '' ) . '" href="#">' . esc_html__( 'Select Image', 'social-pug' ) . '</a>';
					echo '<a class="dpsp-image-remove dpsp-button-secondary ' . ( empty( $share_options['custom_image']['id'] ) ? 'dpsp-hidden' : '' ) . '" href="#">' . esc_html__( 'Remove Image', 'social-pug' ) . '</a>';

					echo '<input class="dpsp-image-id" type="hidden" name="dpsp_share_options[custom_image][id]" value="' . ( ! empty( $share_options['custom_image']['id'] ) ? esc_attr( $share_options['custom_image']['id'] ) : '' ) . '" />';
					echo '<input class="dpsp-image-src" type="hidden" name="dpsp_share_options[custom_image][src]" value="' . esc_attr( $image_src ) . '" />';

				echo '</div>';
			echo '</div>';

			// Social media title
			echo '<div class="dpsp-setting-field-wrapper">';
				$maximum_count   = 70;
				$current_count   = ( ! empty( $share_options['custom_title'] ) ? strlen( wp_kses_post( $share_options['custom_title'] ) ) : 0 );
				$remaining_count = $maximum_count - $current_count;

				echo '<label for="dpsp_share_options[custom_title]"><span class="dpsp-admin-icon dpsp-admin-icon-share">' . wp_kses( dpsp_get_svg_icon_output( 'share' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Social Media (Open Graph) Title', 'social-pug' ) . '<span class="dpsp-textarea-characters-remaining-wrapper" data-maximum-count="' . esc_attr( $maximum_count ) . '"><span class="dpsp-textarea-characters-remaining">' . esc_attr( $remaining_count ) . '</span> ' . esc_html__( 'Characters Remaining', 'social-pug' ) . '</span>';
					dpsp_output_backend_tooltip( __( 'Add a title that will populate the "og:title" Open Graph meta tag. This will be used when users share your content on social networks. The title of the post will be used if this field is empty.', 'social-pug' ), false );
				echo '</label>';
				echo '<textarea id="dpsp_share_options[custom_title]" name="dpsp_share_options[custom_title]" placeholder="' . esc_attr__( 'Write a social media title...', 'social-pug' ) . '">' . ( isset( $share_options['custom_title'] ) ? wp_kses_post( $share_options['custom_title'] ) : '' ) . '</textarea>';
			echo '</div>';

			// Social media description
			echo '<div class="dpsp-setting-field-wrapper">';

				$maximum_count   = 200;
				$current_count   = ( ! empty( $share_options['custom_description'] ) ? strlen( wp_kses_post( $share_options['custom_description'] ) ) : 0 );
				$remaining_count = $maximum_count - $current_count;

				echo '<label for="dpsp_share_options[custom_description]"><span class="dpsp-admin-icon dpsp-admin-icon-share">' . wp_kses( dpsp_get_svg_icon_output( 'share' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Social Media (Open Graph) Description', 'social-pug' ) . '<span class="dpsp-textarea-characters-remaining-wrapper" data-maximum-count="' . esc_attr( $maximum_count ) . '"><span class="dpsp-textarea-characters-remaining">' . esc_attr( $remaining_count ) . '</span> ' . esc_html__( 'Characters Remaining', 'social-pug' ) . '</span>';
					dpsp_output_backend_tooltip( __( 'Add a description that will populate the "og: description" Open Graph meta tag. This will be used when users share your content on social networks.', 'social-pug' ), false );
				echo '</label>';
				echo '<textarea id="dpsp_share_options[custom_description]" name="dpsp_share_options[custom_description]" placeholder="' . esc_html__( 'Write a social media description...', 'social-pug' ) . '">' . ( isset( $share_options['custom_description'] ) ? wp_kses_post( $share_options['custom_description'] ) : '' ) . '</textarea>';
			echo '</div>';

		echo '</div>';

		// Individual networks social media content
		echo '<div class="dpsp-section">';

			// Pinterest image
			echo '<div class="dpsp-setting-field-wrapper dpsp-setting-field-image">';
				echo '<label for="dpsp_share_options[custom_image_pinterest]"><span class="dpsp-admin-icon dpsp-admin-icon-pinterest">' . wp_kses( dpsp_get_svg_icon_output( 'pinterest' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Pinterest Image', 'social-pug' );
				dpsp_output_backend_tooltip( __( 'Add an image that will be used when this post is shared on Pinterest. For maximum exposure we recommend using an image that has a 2:3 aspect ratio, for example 800px X 1200px.', 'social-pug' ), false );
				echo '</label>';
				echo '<div>';

					$thumb_details = [];
					$image_details = [];

					if ( ! empty( $share_options['custom_image_pinterest']['id'] ) ) {
						$thumb_details = wp_get_attachment_image_src( $share_options['custom_image_pinterest']['id'], 'high' );
						$image_details = wp_get_attachment_image_src( $share_options['custom_image_pinterest']['id'], 'full' );
					}

					if ( ! empty( $thumb_details[0] ) && ! empty( $image_details[0] ) ) {
						$thumb_src = $thumb_details[0];
						$image_src = $image_details[0];
					} else {
						$thumb_src = DPSP_PLUGIN_DIR_URL . 'assets/dist/custom-social-media-image-pinterest.png?' . DPSP_VERSION;
						$image_src = '';
					}

					echo '<div>';
						echo '<img src="' . esc_attr( $thumb_src ) . '" data-pin-nopin="true" />';
						echo '<span class="dpsp-field-image-placeholder" data-src="' . esc_url( DPSP_PLUGIN_DIR_URL . 'assets/dist/custom-social-media-image-pinterest.png?' . DPSP_VERSION ) . '"></span>';
					echo '</div>';

					echo '<a class="dpsp-image-select dpsp-button-primary ' . ( ! empty( $share_options['custom_image_pinterest']['id'] ) ? 'dpsp-hidden' : '' ) . '" href="#">' . esc_html__( 'Select Image', 'social-pug' ) . '</a>';
					echo '<a class="dpsp-image-remove dpsp-button-secondary ' . ( empty( $share_options['custom_image_pinterest']['id'] ) ? 'dpsp-hidden' : '' ) . '" href="#">' . esc_html__( 'Remove Image', 'social-pug' ) . '</a>';

					echo '<input class="dpsp-image-id" type="hidden" name="dpsp_share_options[custom_image_pinterest][id]" value="' . ( ! empty( $share_options['custom_image_pinterest']['id'] ) ? esc_attr( $share_options['custom_image_pinterest']['id'] ) : '' ) . '" />';
					echo '<input class="dpsp-image-src" type="hidden" name="dpsp_share_options[custom_image_pinterest][src]" value="' . esc_attr( $image_src ) . '" />';

				echo '</div>';
			echo '</div>';

			// Pinterest title
			echo '<div class="dpsp-setting-field-wrapper">';
				
				// TODO: remove all code related to character counts for Pinterest
				/* 
				$maximum_count   = 70;
				$current_count   = ( ! empty( $share_options['custom_title_pinterest'] ) ? strlen( wp_kses_post( $share_options['custom_title_pinterest'] ) ) : 0 );
				$remaining_count = $maximum_count - $current_count;
				*/

			//echo '<label for="dpsp_share_options[custom_title_pinterest]"><span class="dpsp-admin-icon dpsp-admin-icon-pinterest">' . wp_kses( dpsp_get_svg_icon_output( 'pinterest' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Pinterest Title (deprecated)', 'social-pug' ) . '<span class="dpsp-textarea-characters-remaining-wrapper" data-maximum-count="' . esc_attr( $maximum_count ) . '"><span class="dpsp-textarea-characters-remaining">' . esc_attr( $remaining_count ) . '</span> ' . esc_html__( 'Characters Remaining', 'social-pug' ) . '</span></label>';
			echo '<label for="dpsp_share_options[custom_title_pinterest]"><span class="dpsp-admin-icon dpsp-admin-icon-deprecated dpsp-admin-icon-pinterest">' . wp_kses( dpsp_get_svg_icon_output( 'pinterest' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Pinterest Title (deprecated)', 'social-pug' ) . '</label>';
				echo '<p class="deprecated description">👉 Heads up! Pinterest no longer uses these custom title and description fields. Instead, they rely on the Social Media (Open Graph) Title and Description above. <a href="https://morehubbub.com/docs/pinterest-title-and-description-deprecated/" target="_blank">Please read our Support Doc to learn more</a>. (Pinterest Images are still working as expected.)</p>';
				echo '<textarea id="dpsp_share_options[custom_title_pinterest]" name="dpsp_share_options[custom_title_pinterest]" placeholder="">' . ( isset( $share_options['custom_title_pinterest'] ) ? wp_kses_post( $share_options['custom_title_pinterest'] ) : '' ) . '</textarea>';
			echo '</div>';

			// Pinterest description
			echo '<div class="dpsp-setting-field-wrapper">';

				/*
				$maximum_count   = 500;
				$current_count   = ( ! empty( $share_options['custom_description_pinterest'] ) ? strlen( wp_kses_post( $share_options['custom_description_pinterest'] ) ) : 0 );
				$remaining_count = $maximum_count - $current_count;
				*/

				//echo '<label for="dpsp_share_options[custom_description_pinterest]"><span class="dpsp-admin-icon dpsp-admin-icon-pinterest">' . wp_kses( dpsp_get_svg_icon_output( 'pinterest' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Pinterest Description (deprecated)', 'social-pug' ) . '<span class="dpsp-textarea-characters-remaining-wrapper" data-maximum-count="' . esc_attr( $maximum_count ) . '"><span class="dpsp-textarea-characters-remaining">' . esc_attr( $remaining_count ) . '</span> ' . esc_html__( 'Characters Remaining', 'social-pug' ) . '</span>';
				echo '<label for="dpsp_share_options[custom_description_pinterest]"><span class="dpsp-admin-icon dpsp-admin-icon-deprecated dpsp-admin-icon-pinterest">' . wp_kses( dpsp_get_svg_icon_output( 'pinterest' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Pinterest Description (deprecated)', 'social-pug' ) . '</label>';
				// reminder, if brought back, this needs to be in <label> dpsp_output_backend_tooltip( __( 'Add a customized message that will be used when this post is shared on Pinterest.', 'social-pug' ), false );
				echo '<textarea id="dpsp_share_options[custom_description_pinterest]" name="dpsp_share_options[custom_description_pinterest]" placeholder="">' . ( isset( $share_options['custom_description_pinterest'] ) ? wp_kses_post( $share_options['custom_description_pinterest'] ) : '' ) . '</textarea>';
			echo '</div>';

			// Twitter custom tweet
			echo '<div class="dpsp-setting-field-wrapper">';

				$has_via   = ! empty( $settings['twitter_username'] ) && ! empty( $settings['tweets_have_username'] );
				$tweet_via = $has_via ? ' via @' . $settings['twitter_username'] : '';

				$tweet_meta_content_length = 24 + strlen( $tweet_via ); /* 23 is the length of the URL as Twitter sees it + 1 for the empty space before it */

				$maximum_count   = apply_filters( 'dpsp_tweet_maximum_count', 280 ) - $tweet_meta_content_length;
				$current_count   = ( ! empty( $share_options['custom_tweet'] ) ? strlen( wp_kses_post( $share_options['custom_tweet'] ) ) : 0 );
				$remaining_count = $maximum_count - $current_count;

				echo '<label for="dpsp_share_options[custom_tweet]"><span class="dpsp-admin-icon dpsp-admin-icon-x">' . wp_kses( dpsp_get_svg_icon_output( 'twitter' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Custom Tweet', 'social-pug' ) . '<span class="dpsp-textarea-characters-remaining-wrapper" data-maximum-count="' . esc_attr( $maximum_count ) . '"><span class="dpsp-textarea-characters-remaining">' . esc_html( $remaining_count ) . '</span> ' . esc_html__( 'Characters Remaining', 'social-pug' ) . '</span>';
				dpsp_output_backend_tooltip( __( 'Add a customized tweet that will be used when this post is shared on Twitter.', 'social-pug' ), false );
				echo '</label>';
				echo '<textarea id="dpsp_share_options[custom_tweet]" name="dpsp_share_options[custom_tweet]" placeholder="' . esc_attr__( 'Write a custom tweet...', 'social-pug' ) . '">' . ( isset( $share_options['custom_tweet'] ) ? wp_kses_post( $share_options['custom_tweet'] ) : '' ) . '</textarea>';
				echo '<p class="description">' . esc_html__( 'Maximum characters is based off of the Twitter maximum, the post permalink, and whether your Twitter username is included in the tweet.', 'social-pug' ) . '</p>';
			echo '</div>';

		echo '</div>';

		// Multiple hidden Pinterest images section
		if ( ! empty( $pinterest_settings['share_image_post_multiple_hidden_pinterest_images'] ) ) {

			// Add nonce
			wp_nonce_field( 'dpsp_save_multiple_pinterest_images', 'dpsp_save_multiple_pinterest_images', false );

			echo '<div id="dpsp-meta-box-section-multiple-pinterest-images" class="dpsp-section">';

				echo '<div class="dpsp-setting-field-wrapper">';

					echo '<label><span class="dpsp-admin-icon dpsp-admin-icon-pinterest">' . wp_kses( dpsp_get_svg_icon_output( 'pinterest' ), View_Loader::get_allowed_tags() ) . '</span>' . esc_html__( 'Pinterest Hidden Images', 'social-pug' ) . '</label>';

					$hidden_images = dpsp_maybe_convert_post_meta_to_json( $post->ID, 'dpsp_pinterest_hidden_images', true );
					$hidden_images = ( ! empty( $hidden_images ) && is_array( $hidden_images ) ? $hidden_images : [] );

					// Add the image thumbnails
					foreach ( $hidden_images as $image_id ) {

						$image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );

						if ( empty( $image_src[0] ) ) {
							continue;
						}

						echo '<div class="dpsp-hidden-image-wrapper" data-image-id="' . absint( $image_id ) . '">';

							// Image thumbnail
							echo '<img src="' . esc_url( $image_src[0] ) . '" data-pin-nopin="true" />';

							// Remove image button
							echo '<a href="#" class="dpsp-button-secondary" title="' . esc_attr__( 'Remove image', 'social-pug' ) . '"><span class="dashicons dashicons-no-alt"></span></a>';

							// Add hidden field with the image_id
							echo '<input type="hidden" name="dpsp_pinterest_hidden_images[]" value="' . absint( $image_id ) . '" />';

						echo '</div>';

					}

					// Add the add new images button
					echo '<div class="dpsp-hidden-image-add-new dpsp-button-secondary">';
						echo '<span class="dashicons dashicons-plus"></span>';
						echo  '<div>' . esc_html__( 'Add images', 'social-pug' ) . '</div>';
					echo '</div>';

				echo '</div>';

			echo '</div>';

		}

	echo '</div>';

	// Overwrite options
	echo '<h4 class="dpsp-section-title">' . esc_html__( 'Display Options', 'social-pug' ) . '</h4>';
	echo '<div>';
		dpsp_settings_field( 'checkbox', 'dpsp_share_options[locations_overwrite][]', ( isset( $share_options['locations_overwrite'] ) ? $share_options['locations_overwrite'] : [] ), __( 'Hide checked tools', 'social-pug' ), dpsp_get_network_locations( 'all', false ) );
	echo '</div>';
	echo '<div>';
		dpsp_settings_field( 'checkbox', 'dpsp_share_options[locations_overwrite_show][]', ( isset( $share_options['locations_overwrite_show'] ) ? $share_options['locations_overwrite_show'] : [] ), __( 'Show checked tools', 'social-pug' ), dpsp_get_network_locations( 'all', false ) );
	echo '</div>';

}


/**
 * Callback for the share statistics meta-box.
 *
 * @param WP_Post $post WordPress post object instance.
 * @param array   $config Config options for the rendered output.
 */
function dpsp_share_statistics_output( $post, array $config = [] ) {

	$networks = dpsp_get_active_networks();

	if ( ! empty( $networks ) ) {

		$messages = ! empty( $config['messages'] ) ? $config['messages'] : new Admin_Messages();

		$networks_container = \Mediavine\Grow\Networks::get_instance();

		echo '<div class="dpsp-statistic-bars-wrapper">';

		// Get share counts
		$networks_shares = Share_Counts::post_share_counts( $post->ID );

		// Get total share counts
		$total_shares = Share_Counts::post_total_share_counts( $post->ID );

		// Shares header
		echo '<div class="dpsp-statistic-bar-wrapper dpsp-statistic-bar-header">';
			echo '<label>' . esc_html__( 'Network', 'social-pug' ) . '</label>';
			echo '<div class="dpsp-network-share-count"><span class="dpsp-count">' . esc_html__( 'Shares', 'social-pug' ) . '</span><span class="dpsp-divider">|</span><span class="dpsp-percentage">%</span></div>';
		echo '</div>';
		// Actual shares per network
		foreach ( $networks as $network_slug ) {
			$network = $networks_container->get( $network_slug );
			// Jump to the next one if the network by some chance does not support
			// share count
			if ( ! $network || ! $network->has_count() ) {
				continue;
			}

			// Get current network social share count
			$network_shares = ( isset( $networks_shares[ $network->get_slug() ] ) ? $networks_shares[ $network->get_slug() ] : 0 );

			// Get the percentage of the total shares for current network
			$share_percentage = ( 0 !== $total_shares ? (float) ( $network_shares / $total_shares * 100 ) : 0 );

			echo '<div class="dpsp-statistic-bar-wrapper dpsp-statistic-bar-wrapper-network">';
				echo '<label>' . esc_html( $network->get_name() ) . '</label>';

				echo '<div class="dpsp-statistic-bar dpsp-statistic-bar-' . esc_attr( $network->get_slug() ) . '">';
				echo '<div class="dpsp-statistic-bar-inner" style="width:' . esc_attr( round( $share_percentage, 1 ) ) . '%"></div>';
				echo '</div>';

			echo '<div class="dpsp-network-share-count"><span class="dpsp-count">' . esc_html( $network_shares ) . '</span><span class="dpsp-divider">|</span><span class="dpsp-percentage">' . esc_html( round( $share_percentage, 2 ) ) . '</span></div>';
			echo '</div>';

		}

		$has_refresh_timeout = Share_Counts::has_refresh_timeout( $post );

		if ( $has_refresh_timeout ) {
			$refresh_timeout   = (int) Share_Counts::get_refresh_timeout( $post );
			$minutes_remaining = ceil(abs( $refresh_timeout - time() ) / 60 );

			/* translators: %d: Minutes until the counts may be refreshed. */
			$refresh_shares_message = esc_html__(
				'This post may only be refreshed once per hour and the clock is ticking! Please wait %d minutes before refreshing again',
				'mediavine'
			);
			$rendered_message       = sprintf($refresh_shares_message, $minutes_remaining);
			$messages->add_message( $rendered_message, Admin_Messages::MESSAGE_TYPE_WARNING );
		}

		// Shares footer with total count
		echo '<div class="dpsp-statistic-bar-wrapper dpsp-statistic-bar-footer">';
			echo '<label>' . esc_html__( 'Total shares', 'social-pug' ) . '</label>';
			echo '<div class="dpsp-network-share-count"><span class="dpsp-count">' . esc_html( $total_shares ) . '</span></div>';
		echo '</div>';

		// phpcs:disable WordPress.Security.EscapeOutput
		echo $messages;
		// phpcs:enable

		// Refresh counts button
		echo '<div id="dpsp-refresh-share-counts-wrapper">';
			echo '<a id="dpsp-refresh-share-counts" class="dpsp-button-secondary ' . ( $has_refresh_timeout ? 'dpsp-refresh-shares-timeout disabled' : '' ) . '" href="#">' . esc_html__( 'Refresh shares', 'mediavine' ) . '</a>';
			echo '<span class="spinner"></span>';
			wp_nonce_field( 'dpsp_refresh_share_counts', 'dpsp_refresh_share_counts', false, true );
		echo '</div>';

		echo '</div>';

		/**
		 * Share recovery links
		 *
		 * Because the share statistics meta-box is rendered both on load and through
		 * AJAX when the Refresh Shares button is clicked, we need to only add it on pageload
		 *
		 */
		if ( ! Social_Pug::is_free() && ! wp_doing_ajax() ) {

			echo '<div id="dpsp-shares-recovery-post-previous-urls">';

				$urls = dpsp_maybe_convert_post_meta_to_json( $post->ID, 'dpsp_post_single_previous_urls', true );

				echo '<div class="dpsp-shares-recovery-post-previous-urls-header">';

				echo '<h4>' . esc_html__( 'Social Shares Recovery', 'social-pug' ) . '</h4>';

				echo wp_kses_post( dpsp_output_backend_tooltip( __( 'If you have modified the permalink for this particular post, add the previous URL variations for the post, so that Hubbub can recover the social shares for each individual URL.', 'social-pug' ), false ) );

				echo '</div>';

				// Add the empty placeholder with a message, when previous URLs don't exist
				echo '<div id="dpsp-shares-recovery-post-previous-urls-empty" ' . ( ! empty( $urls ) ? 'style="display: none;"' : '' ) . '>';
					echo '<p>' . esc_html__( 'If you have ever modified the permalink for this particular post and want to recover lost shares for any previous links this post had, add the old links by pressing the Add Link button.', 'social-pug' ) . '</p>';
				echo '</div>';

				// Add each previous URL for the post
				if ( ! empty( $urls ) ) {

					foreach ( $urls as $url ) {

						echo '<div class="dpsp-post-previous-url">';

							echo '<input type="text" name="dpsp_post_single_previous_urls[]" placeholder="eg. http://www.domain.com/sample-post/" value="' . esc_attr( $url ) . '" />';

							echo '<a href="#" class="dpsp-button-secondary">' . esc_html__( 'Remove', 'social-pug' ) . '</a>';

						echo '</div>';

					}
				}

				echo '<a href="#" id="dpsp-add-post-previous-url" class="dpsp-button-secondary">' . esc_html__( 'Add Link', 'social-pug' ) . '</a>';

			echo '</div>';

			// Hidden URL field used to add new fields through JS
			echo '<div class="dpsp-post-previous-url dpsp-hidden">';

				echo '<input type="text" name="dpsp_post_single_previous_urls[]" placeholder="eg. http://www.domain.com/sample-post/" value="" />';

				echo '<a href="#" class="dpsp-button-secondary">' . esc_html__( 'Remove', 'social-pug' ) . '</a>';

			echo '</div>';
		}
	}
}

/**
 * Callback for the debugger meta-box.
 */
function dpsp_post_debugger_output( $post ) {

	$post_meta = get_post_meta( $post->ID );

	echo '<textarea readonly style="width: 100%; min-height: 600px;">';

	// Add post data
	$output  = '----------------------------------------------------------------------------------' . PHP_EOL;
	$output .= 'post_id' . PHP_EOL;
	$output .= '----------------------------------------------------------------------------------' . PHP_EOL;
	$output .= $post->ID;
	$output .= PHP_EOL . PHP_EOL;
	$output .= '----------------------------------------------------------------------------------' . PHP_EOL;
	$output .= 'post_permalink' . PHP_EOL;
	$output .= '----------------------------------------------------------------------------------' . PHP_EOL;
	$output .= get_permalink( $post->ID );
	$output .= PHP_EOL . PHP_EOL;

	echo esc_textarea( $output );

	// Add Social Pug related meta-data
	$output = '';
	foreach ( $post_meta as $meta_key => $meta_value ) {

		if ( false === strpos( $meta_key, 'dpsp' ) ) {
			continue;
		}

		$output  = '----------------------------------------------------------------------------------' . PHP_EOL;
		$output .= $meta_key . PHP_EOL;
		$output .= '----------------------------------------------------------------------------------' . PHP_EOL;

		if ( is_serialized( $meta_value[0] ) ) {
			$output .= print_r( unserialize( $meta_value[0] ), true ); // @codingStandardsIgnoreLine
		} else {
			$output .= print_r( $meta_value[0] . PHP_EOL, true ); // @codingStandardsIgnoreLine
		}

		$output .= PHP_EOL;

		echo esc_textarea( $output );
	}

	echo '</textarea>';
}


/**
 * Ajax callback action that refreshes the social counts for the "Share Statistics"
 * meta-box from each single edit post admin screen.
 */
function dpsp_refresh_share_counts() {

	$messages = new Admin_Messages();

	if ( empty( $_POST['action'] ) || empty( $_POST['nonce'] ) || empty( $_POST['post_id'] ) ) {
		wp_die( 'Invalid Request, missing parameters', 'Missing Parameters', [ 'response' => 400 ] );
	}

	$action = filter_input( INPUT_POST, 'action' );
	if ( 'dpsp_refresh_share_counts' !== $action ) {
		wp_die('Incorrect Action, Share counts not retrieved.', 'Incorrect Action', [ 'response' => 400 ] );
	}


	$nonce = filter_input( INPUT_POST, 'nonce' );
	if ( ! wp_verify_nonce( $nonce, 'dpsp_refresh_share_counts' ) ) {
		wp_die('Nonce not verified, please try again.', 'Invalid Nonce', [ 'response' => 403 ] );
	}

	$post_id = (int) $_POST['post_id'];
	$post    = get_post( $post_id );

	$valid_post_status = ! in_array( $post->post_status, [ 'future', 'draft', 'pending', 'trash', 'auto-draft' ], true );

	$is_json_param    = filter_input( INPUT_POST, 'json', FILTER_VALIDATE_BOOLEAN );
	$is_json_response = $is_json_param ? $is_json_param : false;

	if ( $valid_post_status ) {

		$share_count_instance = Share_Counts::get_instance();
		// Get social shares from the networks
		try {
			$share_counts = $share_count_instance->refresh_post_share_counts( $post );
		} catch ( Exception $e ) {
			$share_counts = null;
			$messages->add_message( $e->getMessage(), Admin_Messages::MESSAGE_TYPE_ERROR );
		}

		// Flush existing shares before pulling a new set
		if ( $share_counts instanceof Share_Count_Url_Counts ) {
			update_post_meta( $post_id, 'dpsp_networks_shares', '' );
		}

		// Update share counts in the db
		$shares_updated = $share_counts && $share_count_instance->update_post_share_counts( $post, $share_counts );

		if ( $shares_updated ) {
			$messages->add_message('Share Counts refreshed successfully', Admin_Messages::MESSAGE_TYPE_SUCCESS);
		}
	} else {
		$messages->add_message('Only Published Posts can refresh share counts.', Admin_Messages::MESSAGE_TYPE_ERROR );
	}

	if ( $is_json_response ) {
		$last_updated = get_post_meta( $post_id, Share_Counts::LAST_UPDATED_META_KEY, true );
		$result       = [
			'errors'   => [],
			'messages' => [],
			'shares'   => [
				'counts'       => Share_Counts::post_share_counts( $post_id ),
				'last_updated' => $last_updated ? $last_updated : 0,
				'total'        => Share_Counts::post_total_share_counts( $post_id ),
			],
		];
		foreach ( $messages->get_messages() as $current_message_type => $current_messages ) {
			foreach ( $current_messages as $current_message ) {
				switch ( $current_message_type ) {
					case Admin_Messages::MESSAGE_TYPE_ERROR:
						$result['errors'][] = $current_message;
						break;
					default:
						$result['messages'][] = $current_message;
				}
			}
		}

		echo wp_json_encode( $result );
	} else {
		// Echos the share statistics
		dpsp_share_statistics_output( $post, [ 'messages' => $messages ] );
	}
	wp_die();
}

/**
 * Save meta data for Social Pug meta boxes.
 */
function dpsp_save_post_meta( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['dpsptkn'] ) ) {
		return;
	}

	$nonce = filter_input( INPUT_POST, 'dpsptkn' );
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'dpsp_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	$post_type = filter_input( INPUT_POST, 'post_type' );
	if ( 'page' === $post_type ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Clear cached shortened links
	delete_post_meta( $post_id, 'dpsp_short_link_bitly' );

	// Save information for the Share Options meta-box
	$dpsp_share_options = isset( $_POST['dpsp_share_options'] ) ? wp_unslash( $_POST['dpsp_share_options'] ) : false; // @codingStandardsIgnoreLine
	if ( ! empty( $dpsp_share_options ) ) {
		$share_options = $dpsp_share_options;
	} else {
		$share_options = '';
	}

	update_post_meta( $post_id, 'dpsp_share_options_json', wp_slash( json_encode( $share_options, JSON_UNESCAPED_UNICODE ) ) );
	update_post_meta( $post_id, 'dpsp_share_options', $share_options ); // Maintaining backwards compatibitly, to be deprecated TODO: Remove when safe to do so

	// Save information for the Pinterest hidden images
	$save_multiple_pinterest_images_nonce = filter_input( INPUT_POST, 'dpsp_save_multiple_pinterest_images' );
	if ( ! empty( $save_multiple_pinterest_images_nonce ) && wp_verify_nonce( $save_multiple_pinterest_images_nonce, 'dpsp_save_multiple_pinterest_images' ) ) {

		$dpsp_pinterest_hidden_images = filter_input( INPUT_POST, 'dpsp_pinterest_hidden_images', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		// Remove the images if none are present
		if ( ! empty( $dpsp_pinterest_hidden_images ) ) {

			// Sanitize the values
			$hidden_images = array_map( 'absint', $dpsp_pinterest_hidden_images );
			$hidden_images = array_filter( $hidden_images );

		} else {
			$hidden_images = '';
		}

		// Update hidden images value
		update_post_meta( $post_id, 'dpsp_pinterest_hidden_images_json', json_encode( $hidden_images, JSON_UNESCAPED_UNICODE ) );
		update_post_meta( $post_id, 'dpsp_pinterest_hidden_images', $hidden_images ); // Maintaining backwards compatibitly, to be deprecated TODO: Remove when safe to do so
	}

	// Save information for the Share Statistics meta-box
	$dpsp_post_single_previous_urls = filter_input( INPUT_POST, 'dpsp_post_single_previous_urls', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	if ( ! empty( $dpsp_post_single_previous_urls ) ) {

		$previous_urls = ( is_array( $dpsp_post_single_previous_urls ) ? $dpsp_post_single_previous_urls : [] );

		foreach ( $previous_urls as $key => $previous_url ) {
			// Sanitize the URL
			$previous_urls[ $key ] = wp_http_validate_url( sanitize_text_field( $previous_url ) );
		}

		// Exclude invalid and empty values
		$previous_urls = array_filter( $previous_urls );

		// Make sure there are no duplicates
		$previous_urls = array_unique( $previous_urls );

	} else {
		$previous_urls = '';
	}

	// Update previous URL's
	update_post_meta( $post_id, 'dpsp_post_single_previous_urls_json', json_encode( $previous_urls, JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $post_id, 'dpsp_post_single_previous_urls', $previous_urls ); // Maintaining backwards compatibitly, to be deprecated TODO: Remove when safe to do so
}

/**
 *
 */
function dpsp_refresh_all_share_counts_ajax() {
	if ( empty( $_POST['action'] ) || empty( $_POST['nonce'] ) ) {
		return;
	}

	if ( 'dpsp_refresh_all_share_counts' !== filter_input( INPUT_POST, 'action' ) ) {
		return;
	}

	if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'dpsp_refresh_all_share_counts' ) ) {
		return;
	}

	Share_Counts::invalidate_all();
	wp_die();
}

/**
 * Register hooks for admin-metaboxes.php
 */
function dpsp_register_admin_metaboxes() {
	add_action( 'add_meta_boxes', 'dpsp_meta_boxes' );
	add_action( 'wp_ajax_dpsp_refresh_share_counts', 'dpsp_refresh_share_counts' );
	add_action( 'save_post', 'dpsp_save_post_meta' );
	add_action( 'wp_ajax_dpsp_refresh_all_share_counts', 'dpsp_refresh_all_share_counts_ajax' );
}
