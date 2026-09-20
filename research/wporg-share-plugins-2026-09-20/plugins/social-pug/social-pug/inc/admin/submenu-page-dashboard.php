<?php
/**
 * Functions related to the Dashboard Admin Page
 */

function dpsp_register_dashboard_subpage() {
	add_submenu_page( 'dpsp-social-pug', __( 'Dashboard', 'social-pug' ), __( 'Dashboard', 'social-pug' ), 'manage_options', 'dpsp-dashboard', 'dpsp_dashboard_subpage' );
}

function dpsp_dashboard_subpage() {
	$tabs = [
		'dashboard' => __( 'Dashboard', 'social-pug' )
	];

	if ( ! \Social_Pug::is_free() ) {
		$hubbub_activation 	= new \Mediavine\Grow\Activation;
		$license_tier 		= $hubbub_activation->get_license_tier();

		if ( ! empty( $license_tier ) && $license_tier != 'pro' ) {
			$tabs['find-fix'] = __( 'Find & Fix', 'social-pug' );
		}
	}

	$tabs = apply_filters( 'dpsp_submenu_page_dashboard_tabs', $tabs );

	//$pro = ( \Social_Pug::is_free() ) ? '' : '-pro';
	//include DPSP_PLUGIN_DIR . '/inc/admin/views/view-submenu-page-dashboard' . $pro . '.php';
	include DPSP_PLUGIN_DIR . '/inc/admin/views/view-submenu-page-dashboard.php';
}

function dpsp_register_admin_dashboard() {
	add_action( 'admin_menu', 'dpsp_register_dashboard_subpage', 10 );

	add_action( 'wp_ajax_dpsp_ajax_get_hubbub_metaboxes', 'dpsp_ajax_get_hubbub_metaboxes' );
	add_action( 'wp_ajax_dpsp_ajax_dashboard_save_post_meta', 'dpsp_ajax_dashboard_save_post_meta' );
}

/**
 * Creates a link for Find & Fix feature on the Dashboard
 * to append the display all posts parameter while retaining
 * all other parameters.
 */
function dpsp_dashboard_output_show_all_posts_link() {
	
	// Get the current URL path and query
	$url = $_SERVER['REQUEST_URI'];
	
	// Parse the URL to get components
	$parsed_url = parse_url($url);

	// Parse existing query parameters
	$query_params = [];
	if (isset($parsed_url['query'])) {
		parse_str($parsed_url['query'], $query_params);
	}

	// Add or update the parameter
	$query_params['dpsp_display_all_posts'] = 'yes';

	// Rebuild the query string
	$new_query = http_build_query($query_params);

	// Rebuild the full URL
	$new_url = $parsed_url['path'] . '?' . $new_query;

	return $new_url;
}

function dpsp_dashboard_get_post_list( $list = 'most_shares' ) {
	global $wpdb;
	
	$is_requires_attention_list = in_array($list, [
		'social_information',
		'custom_title',
		'custom_description',
		'custom_image',
		'custom_image_pinterest',
	], true);
	$transient_key 					= ( $is_requires_attention_list ) ? 'dpsp_dashboard_posts_requires_attention' : 'dpsp_dashboard_posts_' . $list;
	$cached_posts 					= get_transient( $transient_key );

	if ( ! $cached_posts ) {

		$cached_posts = array();
		
		$query = new WP_Query( 
			array(
				'post_type'			=> apply_filters( 'dpsp_filter_dashboard_post_types', array_keys( dpsp_get_post_types() ), $is_requires_attention_list ),
				'posts_per_page' 	=> -1,
				'orderby' 			=> 'ID',
				'order' 			=> 'DESC',
				'post_status' 		=> 'publish',
			)
		);
		
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				
				$post_share_counts  = dpsp_maybe_convert_post_meta_to_json( get_the_id(), 'dpsp_networks_shares', true );

				$total_shares_count		= 0;
				if ( is_array( $post_share_counts ) ) :
					foreach( $post_share_counts as $network => $shares ) {
						$total_shares_count = $total_shares_count + $shares;
					}
				endif;

				$post_saves_count		= get_post_meta( get_the_ID(), 'dpsp_save_this_count', true );

				if ( !$post_saves_count ) $post_saves_count = 0;

				if ( $is_requires_attention_list ) :

					$missing_data 			= [];
					$post_share_options  	= dpsp_maybe_convert_post_meta_to_json( get_the_id(), 'dpsp_share_options', true );

					if ( is_array( $post_share_options ) && count( $post_share_options ) > 0 ) {

						if ( ! isset( $post_share_options['custom_title'] ) ) {
							$missing_data[] = 'custom_title';
						}
						if ( isset( $post_share_options['custom_title'] ) && $post_share_options['custom_title'] == '' ) {
							$missing_data[] = 'custom_title';
						}

						if ( ! isset( $post_share_options['custom_description'] ) ) {
							$missing_data[] = 'custom_description';
						}
						if ( isset( $post_share_options['custom_description'] ) && $post_share_options['custom_description'] == '' ) {
							$missing_data[] = 'custom_description';
						}

						if ( ! isset( $post_share_options['custom_image'] ) ) {
							$missing_data[] = 'custom_image';
						}

						if ( isset( $post_share_options['custom_image'] ) && $post_share_options['custom_image']['src'] == '' ) {
							$missing_data[] = 'custom_image';
						}

						// DEPRECATED if ( ! isset( $post_share_options['custom_title_pinterest'] ) ) {
						// 	$missing_data[] = 'custom_title_pinterest';
						// }
						// if ( isset( $post_share_options['custom_title_pinterest'] ) && $post_share_options['custom_title_pinterest'] == '' ) {
						// 	$missing_data[] = 'custom_title_pinterest';
						// }

						// if ( ! isset( $post_share_options['custom_description_pinterest'] ) ) {
						// 	$missing_data[] = 'custom_description_pinterest';
						// }
						// if ( isset( $post_share_options['custom_description_pinterest'] ) && $post_share_options['custom_description_pinterest'] == '' ) {
						// 	$missing_data[] = 'custom_description_pinterest';
						// }

						if ( ! isset( $post_share_options['custom_image_pinterest'] ) ) {
							$missing_data[] = 'custom_image_pinterest';
						}

						if ( isset( $post_share_options['custom_image_pinterest'] ) && $post_share_options['custom_image_pinterest']['src'] == '' ) {
							$missing_data[] = 'custom_image_pinterest';
						}

					} else {
						$missing_data 		= array( 'custom_title', 'custom_description', 'custom_image', 'custom_title_pinterest', 'custom_description_pinterest', 'custom_image_pinterest' );
					}

					// TODO: Bring back for Featured Image
					//  if ( ! get_the_post_thumbnail( get_the_ID() ) ) {
					// 	$missing_data[] = 'featured_image';
					// }

					if ( count( $missing_data ) < 1 ) :
						continue; // Do not add to the list, because this post has all of its information
					endif;

					$cached_posts[] = [ 
						'id' 			=> get_the_ID(),
						'permalink' 	=> get_the_permalink(),
						'title' 		=> ( ! empty( get_the_title() ) ) ? get_the_title() : 'Untitled',
						'post_date'		=> get_the_date(),
						'shares_count' 	=> $total_shares_count,
						'saves_count' 	=> $post_saves_count,
						'missing_data' 	=> $missing_data,
					];
				else :
					$cached_posts[] = [ 
						'id' 			=> get_the_ID(),
						'permalink' 	=> get_the_permalink(),
						'title' 		=> ( ! empty( get_the_title() ) ) ? get_the_title() : 'Untitled',
						'post_date'		=> get_the_date(),
						'shares_count' 	=> $total_shares_count,
						'saves_count' 	=> $post_saves_count
					];
				endif;
			endwhile;
		endif;

		switch ( $list ) { // In order they appear in the dropdown UI

			case 'most_shares':
				usort($cached_posts, function ($a, $b) {
					return $b['shares_count'] <=> $a['shares_count'];
				});
				break;
			case 'most_saves':
				usort($cached_posts, function ($a, $b) {
					return $b['saves_count'] <=> $a['saves_count'];
				});
				break;
			case 'recently_published': // No sorting needed, due to WP_Query
				break;
			case 'fewest_shares':
				usort($cached_posts, function ($a, $b) {
					return $a['shares_count'] <=> $b['shares_count'];
				});
				break;
			case 'fewest_saves':
				usort($cached_posts, function ($a, $b) {
					return $a['saves_count'] <=> $b['saves_count'];
				});
				break;
			case 'social_information':
				set_transient( 'dpsp_dashboard_count_requires_attention', count( $cached_posts ), 6 * HOUR_IN_SECONDS );
				break;
			default:
			break;
		}

		wp_reset_postdata();

		set_transient( $transient_key, json_encode( $cached_posts ), 60 );
	} else {
		$cached_posts = json_decode( $cached_posts, true, 5 );
	}

	if ( $is_requires_attention_list && isset( $_GET['dpsp_list_attention_search'] ) ) {
		$filtered_cached_posts = array_filter( $cached_posts, function( $post ) {	
			return stripos($post['title'], $_GET['dpsp_list_attention_search'] ) !== false;
		});
		$cached_posts = array_values($filtered_cached_posts);
	}

	if ( $is_requires_attention_list && $list != 'social_information' ) : // Filter the list to only include the selected choice
		$filtered_cached_posts = array_filter( $cached_posts, function( $post ) use ( $list ) {	
			return isset( $post['missing_data'] ) && in_array( $list, $post['missing_data'] );
		});
		
		$cached_posts = array_values($filtered_cached_posts);
	endif;

	if ( ! isset( $_GET['dpsp_display_all_posts'] ) ) {
		$cached_posts = array_slice( $cached_posts, 0, 50 ); // Limit all lists to 50 posts
	}

	if ( \Social_Pug::is_free() ) {
		$cached_posts = array_slice( $cached_posts, 0, 10 ); // Limit all lists to 10 posts
	}

return $cached_posts;
}

/**
 * Calculates, stores or retrieves the total number from the post meta key (used on the Dashboard)
 * 
 * @param string The post meta key to search for. E.g. dpsp_save_this_count
 * @return int Zero or the total calculated amount formatted with thousands.
 */
function dpsp_show_total_count( $key ) {
	if ( empty( $key ) ) return 0;

	$total = get_transient( 'dpsp_dashboard_total_' . $key );
	
	if ( ! $total ) {
		global $wpdb;

		$total = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT SUM(meta_value + 0) FROM {$wpdb->postmeta} WHERE meta_key = %s", 
				$key
			) 
		);

		set_transient( 'dpsp_dashboard_total_' . $key, $total, 60 );
	}

	return (int)$total;
}

function dpsp_dashboard_display_news( $number_of_entries = 6 ) {
    
	$rss_url 		= 'https://morehubbub.com/feed/dashboard_news/category/all/';

	if ( \Social_Pug::is_free() ) {
		$tier = 'lite';
	} else {
		$hubbub_activation 	= new \Mediavine\Grow\Activation;
		$tier 		= $hubbub_activation->get_license_tier();
	}

	switch ($tier) {
		case 'lite':
			$second_rss_url = 'https://morehubbub.com/feed/dashboard_news/category/lite-only/';
			break;
		case 'pro':
			$second_rss_url = 'https://morehubbub.com/feed/dashboard_news/category/pro-only/';
			break;
		case 'pro+':
			$second_rss_url = 'https://morehubbub.com/feed/dashboard_news/category/pro-plus-only/';
			break;
		case 'priority':
			$second_rss_url = 'https://morehubbub.com/feed/dashboard_news/category/priority-only/';
			break;
		default:
			$second_rss_url = 'https://morehubbub.com/feed/dashboard_news/category/pro-only/';
			break;
	}

	$feeds = array( $rss_url, $second_rss_url );

	$return_html	= '';
        
	if ( ! function_exists( 'fetch_feed' ) ) {
		require_once ABSPATH . WPINC . '/feed.php';
	}

	$rss = fetch_feed( $feeds );

	if ( is_wp_error( $rss ) ) {
		error_log( 'Warning: WordPress fetch_feed() function is not working. Reported by Hubbub.' ); // @codingStandardsIgnoreLine
		$return_html .= '<p>There was an issue loading the news feed. Please let your webhost know to check your logs.</p><p>Until that is fixed, you can <a href="https://morehubbub.com/changelog/" target="_blank">read the Hubbub changelog</a> for the latest news.</p>';
		return $return_html;
	}
	
	$rss_feed = $rss->get_items( 0, $number_of_entries );

    if ( ! empty( $rss_feed ) ) {
        
        foreach ( $rss_feed as $item ) {

			/**
			 * Adds target="_blank" to all links in the RSS item
			 */
			$desc = $item->get_description();
			$doc = new DOMDocument();
			libxml_use_internal_errors(true);
			$doc->loadHTML('<?xml encoding="utf-8" ?>' . $desc);
			libxml_clear_errors();

			foreach ($doc->getElementsByTagName('a') as $a) {
				$a->setAttribute('target', '_blank');
			}

			$body = $doc->getElementsByTagName('body')->item(0);
			$desc_with_target = '';
			foreach ($body->childNodes as $child) {
				$desc_with_target .= $doc->saveHTML($child);
			}
			// End adds target

			$return_html .= '<div class="dpsp-news-item">';
			$return_html .= '<h3>' . esc_html( $item->get_title() ) . '</h3>' ."\n";
			$return_html .= $desc_with_target;
			$return_html .= '</div>' . "\n\n";
	
		}	
        
    } else {
        $return_html .= '<p>No news to display.</p>';
    }

	unset($rss);

return $return_html;
}

function dpsp_ajax_get_hubbub_metaboxes() {

	if ( !is_user_logged_in() ) {
		echo 0;
		wp_die();
	}

	if ( \Social_Pug::is_free() ) {
		echo 0;
		wp_die();
	}

	$arguments = stripslashes_deep( $_POST );

	if ( ! current_user_can( 'edit_post', $arguments['post_id'] ) ) {
		echo 0;
		wp_die();
	}

	$dpsp_token = filter_input( INPUT_POST, '_ajax_nonce' );
	if ( empty( $dpsp_token ) || ! wp_verify_nonce( $dpsp_token, 'hubbub_dashboard_quick_edit' ) ) {
		echo'failed';		echo 0;
		wp_die();
	}

	ob_start();
	echo '<h3>Quick Edit: <a title="View this post" href="' . get_the_permalink( $arguments['post_id'] ) . '" target="_blank">' . get_the_title( $arguments['post_id'] ) . '</a> <a title="Edit this post" href="' . admin_url( 'post.php?post=' . $arguments['post_id'] . '&action=edit' ) . '" target="_blank"><span class="dashicons dashicons-edit"></span></a></h3>';
	echo '<div class="buttons">
		<a class="cancel" href="#">Cancel</a>
		<button type="button" id="dpsp-dashboard-edit-post-save" aria-disabled="false" class="dpsp-dashboard-save-button components-button editor-post-publish-button editor-post-publish-button__button is-primary is-compact">Save</button><br><small>Note: Saving does not update Modified Date.</small>
	</div>'."\n";
	dpsp_share_options_output( get_post( $arguments['post_id'] ) );
	echo '<input type="hidden" id="dpsp-dashboard-edit-post-id" name="dpsp-dashboard-edit-post-id" value="' . $arguments['post_id'] . '" />'."\n";
	//echo '<button type="button" id="dpsp-dashboard-edit-post-save" aria-disabled="false" class="dpsp-dashboard-save-button components-button editor-post-publish-button editor-post-publish-button__button is-primary is-compact">Save</button>';
	//echo '<p style="text-align:right;"><small>Note: Saving does not update Modified Date.</small></p>';
	$html = ob_get_clean();

	echo $html;
	wp_die();
}

/**
 * Save meta data for Social Media information
 * A copy of dpsp_save_post_meta
 */
function dpsp_ajax_dashboard_save_post_meta() {

	if ( !is_user_logged_in() ) {
		echo 0;
		wp_die();
	}

	if ( \Social_Pug::is_free() ) {
		echo 0;
		wp_die();
	}

	$arguments = stripslashes_deep( $_POST );

	if ( ! current_user_can( 'edit_post', $arguments['post_id'] ) ) {
		echo 0;
		wp_die();
	}

	$dpsp_token = filter_input( INPUT_POST, '_ajax_nonce' );
	if ( empty( $dpsp_token ) || ! wp_verify_nonce( $dpsp_token, 'hubbub_dashboard_quick_edit_save' ) ) {
		echo 0;
		wp_die();
	}

	$post_id 			= $arguments['post_id'];
	$dpsp_share_options = $arguments['dpsp_share_options'];

	// Save information for the Share Options meta-box
	$dpsp_share_options = isset( $_POST['dpsp_share_options'] ) ? wp_unslash( $_POST['dpsp_share_options'] ) : false; // @codingStandardsIgnoreLine
	if ( ! empty( $dpsp_share_options ) ) {
		$share_options = $dpsp_share_options;
	} else {
		$share_options = '';
	}

	update_post_meta( $post_id, 'dpsp_share_options_json', wp_slash( json_encode( $share_options, JSON_UNESCAPED_UNICODE ) ) );

	// Save information for the Pinterest hidden images
	// $save_multiple_pinterest_images_nonce = filter_input( INPUT_POST, 'dpsp_save_multiple_pinterest_images' );
	// if ( ! empty( $save_multiple_pinterest_images_nonce ) && wp_verify_nonce( $save_multiple_pinterest_images_nonce, 'dpsp_save_multiple_pinterest_images' ) ) {

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
	//}

	delete_transient( 'dpsp_dashboard_count_requires_attention' );
	delete_transient( 'dpsp_dashboard_posts_social_information' );
	delete_transient( 'dpsp_dashboard_posts_requires_attention' );
	$reload_list = dpsp_dashboard_get_post_list( 'social_information' ); // Reloads the list to create the count
}