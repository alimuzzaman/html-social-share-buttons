<?php

/**
 * Because certain widgets / plugins reset the global $post variable
 * we are going to cache it when WP has just loaded, so that we have the original post available at all times.
 */
function dpsp_cache_post_object() {
	global $dpsp_cache_wp_post;

	$dpsp_cache_wp_post = null;
	if ( is_singular() && ! is_front_page() && ! is_home() ) {
		global $post;
		$dpsp_cache_wp_post = $post;
	}
}

/**
 * Returns the current post object.
 *
 * @return mixed - WP_Post | null
 */
function dpsp_get_current_post() {
	global $dpsp_cache_wp_post;
	if ( ! is_null( $dpsp_cache_wp_post ) ) {
		return $dpsp_cache_wp_post;
	}

	global $post;
	if ( ! is_null( $post ) ) {
		return $post;
	}

	return null;
}

/**
 * Returns the post object for the given post id
 *
 * @param int $post_id
 * @param mixed - WP_Post | null
 * @return array|mixed|WP_Post|null
 */
function dpsp_get_post( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		return null;
	}

	$current_post = dpsp_get_current_post();
	if ( ! is_null( $current_post ) && $post_id === $current_post->ID ) {
		return $current_post;
	}

	return get_post( $post_id );
}

/**
 * Returns the url of the given post.
 *
 * @param int $post_id
 * @return string
 */
function dpsp_get_post_url( $post_id = 0 ) {
	$post_obj = dpsp_get_post( $post_id );
	if ( is_null( $post_obj ) ) {
		return '';
	}

	$post_url = get_permalink( $post_obj );

	/**
	 * Filter the post URL before returning.
	 * @param string $post_url
	 * @param int $post_id
	 */
	return apply_filters( 'dpsp_get_post_url', $post_url, $post_obj->ID );
}

/**
 * Returns the title of the given post.
 *
 * @param int $post_id
 * @return string
 */
function dpsp_get_post_title( $post_id = 0 ) {
	$post_obj = dpsp_get_post( $post_id );
	if ( is_null( $post_obj ) ) {
		return '';
	}

	$post_title = $post_obj->post_title;

	/**
	 * Filter the post title before returning.
	 * @param string $post_title
	 * @param int $post_id
	 */
	return apply_filters( 'dpsp_get_post_title', $post_title, $post_obj->ID );
}

/**
 * Returns the a description for the given post.
 *
 * @param int $post_id
 * @return string
 */
function dpsp_get_post_description( $post_id = 0 ) {
	$post_obj = dpsp_get_post( $post_id );
	if ( is_null( $post_obj ) ) {
		return '';
	}

	// Check to see if the post has an excerpt
	if ( ! empty( $post_obj->post_excerpt ) ) {
		$post_description = $post_obj->post_excerpt;
	} elseif ( ! empty( $post_obj->post_content ) ) {
	// If not, strip the content
		$post_description = strip_shortcodes( $post_obj->post_content );
		$post_description = wp_trim_words( $post_description, apply_filters( 'dpsp_post_description_length', 35 ), '' );
	} else {
		$post_description = '';
	}

	/**
	 * Filter the post description before returning.
	 * @param string $post_description
	 * @param int $post_id
	 */
	return apply_filters( 'dpsp_get_post_description', $post_description, $post_obj->ID );
}

/**
 * Returns the featured image data for the given post.
 *
 * @param int $post_id
 * @param string $size
 * @return mixed array | null
 */
function dpsp_get_post_image_data( $post_id = 0, $size = 'full' ) {
	$post_obj = dpsp_get_post( $post_id );
	if ( is_null( $post_obj ) ) {
		return null;
	}

	$post_thumbnail_id   = get_post_thumbnail_id( $post_obj->ID );
	$post_thumbnail_data = wp_get_attachment_image_src( $post_thumbnail_id, $size );

	if ( false === $post_thumbnail_data ) {
		$post_thumbnail_data = null;
	}

	/**
	 * Filter the post image data before returning.
	 * @param array $post_thumbnail_data
	 * @param int $post_id
	 * @param string $size
	 */
	return apply_filters( 'dpsp_get_post_image_data', $post_thumbnail_data, $post_obj->ID, $size );
}

/**
 * Returns the featured image URL for the given post.
 *
 * @param int $post_id
 * @param string $size
 * @return mixed string | false
 */
function dpsp_get_post_image_url( $post_id = 0, $size = 'full' ) {
	// Get post image data
	$image_data = dpsp_get_post_image_data( $post_id, $size );

	if ( ! is_array( $image_data ) ) {
		return false;
	}

	$post_thumbnail_url = $image_data[0];

	/**
	 * Filter the post image URL before returning.
	 * @param array $post_thumbnail_data
	 * @param int $post_id
	 * @param string $size
	 */
	return apply_filters( 'dpsp_get_post_image_url', $post_thumbnail_url, $post_id, $size );
}

/**
 * Returns the custom post title set in the Custom Social Options meta-box for a given post.
 *
 * @param int $post_id
 * @return string
 */
function dpsp_get_post_custom_title( $post_id = 0 ) {
	// Check to see if a custom title is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	// Set custom title
	$post_title = ( ! empty( $share_options['custom_title'] ) ? $share_options['custom_title'] : '' );

	return apply_filters( 'dpsp_get_post_custom_title', $post_title, $post_id );
}

/**
 * Returns the custom post description set in the Custom Social Options meta-box.
 *
 * @return string
 */
function dpsp_get_post_custom_description( $post_id = 0 ) {

	// Check to see if a custom description is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	// Set post description
	$post_description = ( ! empty( $share_options['custom_description'] ) ? $share_options['custom_description'] : '' );

	return apply_filters( 'dpsp_get_post_custom_description', $post_description, $post_id );

}

/**
 * Returns the custom post image data set in the Custom Social Options meta-box.
 *
 * @param int $post_id
 * @param string $size
 * @return mixed
 */
function dpsp_get_post_custom_image_data( $post_id = 0, $size = 'full' ) {
	// Check to see if a custom description is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	if ( empty( $share_options['custom_image']['id'] ) ) {
		return null;
	}

	$post_image_id   = (int) $share_options['custom_image']['id'];
	$post_image_data = wp_get_attachment_image_src( $post_image_id, $size );

	return apply_filters( 'dpsp_get_post_custom_image_data', $post_image_data, $post_id, $size );
}

/**
 * If the custom post title of the post is set in the Custom Social Options meta-box,
 * return it instead of the default post title.
 *
 * @param string
 * @return string
 */
function dpsp_add_custom_post_title( $post_title = '', $post_id = 0 ) {
	$custom_title = dpsp_get_post_custom_title( $post_id );
	$post_title   = ( ! empty( $custom_title ) ? $custom_title : $post_title );

	return $post_title;
}

/**
 * If the custom post description of the post is set in the Custom Social Options meta-box,
 * return it instead of the default post description.
 *
 * @param string
 * @return string
 */
function dpsp_add_custom_post_description( $post_description = '', $post_id = 0 ) {
	$custom_description = dpsp_get_post_custom_description( $post_id );
	$post_description   = ( ! empty( $custom_description ) ? $custom_description : $post_description );

	return $post_description;
}

/**
 * If the custom post image data of the post is set in the Custom Social Options meta-box,
 * return it instead of the default post image data.
 *
 * @param string
 * @return string
 */
function dpsp_add_custom_post_image_data( $post_image_data = [], $post_id = 0, $size = '' ) {
	$custom_image_data = dpsp_get_post_custom_image_data( $post_id, $size );
	$post_image_data   = ( ! is_null( $custom_image_data ) ? $custom_image_data : $post_image_data );

	return $post_image_data;
}

/**
 * Returns the custom post title for Pinterest set in the Custom Social Options meta-box.
 *
 * @return string
 */
function dpsp_get_post_pinterest_title( $post_id = 0 ) {
	// Check to see if a custom title is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	// Set post Pinterest title
	$pinterest_title = ( ! empty( $share_options['custom_title_pinterest'] ) ? $share_options['custom_title_pinterest'] : '' );

	return apply_filters( 'dpsp_get_post_pinterest_title', $pinterest_title, $post_id );
}

/**
 * Returns the custom post description for Pinterest set in the Custom Social Options meta-box.
 *
 * @return string
 */
function dpsp_get_post_pinterest_description( $post_id = 0 ) {
	// Check to see if a custom description is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	// Set post Pinterest description
	$pinterest_description = ( ! empty( $share_options['custom_description_pinterest'] ) ? $share_options['custom_description_pinterest'] : '' );

	return apply_filters( 'dpsp_get_post_pinterest_description', $pinterest_description, $post_id );
}

/**
 * Returns the ID of the Pinterest image set in the Custom Social Options meta-box.
 *
 * @param int $post_id
 * @return int
 */
function dpsp_get_post_pinterest_image_id( $post_id = 0 ) {
	// Check to see if a custom description is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	if ( empty( $share_options['custom_image_pinterest']['id'] ) ) {
		return 0;
	}

	return absint( $share_options['custom_image_pinterest']['id'] );
}

/**
 * Returns the custom post Pinterest image data set in the Custom Social Options meta-box.
 *
 * @param int $post_id
 * @param string $size
 * @return mixed array | null
 */
function dpsp_get_post_pinterest_image_data( $post_id = 0, $size = 'full' ) {
	// Check to see if a custom description is in place
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_share_options', true );

	if ( empty( $share_options['custom_image_pinterest']['id'] ) ) {
		return null;
	}

	$post_pinterest_image_id   = (int) $share_options['custom_image_pinterest']['id'];
	$post_pinterest_image_data = wp_get_attachment_image_src( $post_pinterest_image_id, $size );

	return apply_filters( 'dpsp_get_post_pinterest_image_data', $post_pinterest_image_data, $post_id, $size );
}

/**
 * Returns the post's permalink based on the given permalink structure.
 *
 * @param int|WP_Post $post
 * @param string $permalink - the permalink structure
 * @param bool $leavename
 * @return string|false
 */
function dpsp_get_post_permalink( $post = 0, $permalink = '', $leavename = false ) {
	if ( empty( $permalink ) ) {
		return false;
	}

	$rewritecode = [
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		$leavename ? '' : '%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		$leavename ? '' : '%pagename%',
	];

	if ( is_object( $post ) && isset( $post->filter ) && 'sample' === $post->filter ) {
		$sample = true;
	} else {
		$post   = get_post( $post );
		$sample = false;
	}

	if ( empty( $post->ID ) ) {
		return false;
	}

	/**
	 * Filters the permalink structure for a post before token replacement occurs.
	 * Only applies to posts with post_type of 'post'.
	 * @param string $permalink The site's permalink structure.
	 * @param WP_Post $post The post in question.
	 * @param bool $leavename Whether to keep the post name.
	 * @since 3.0.0
	 */
	$permalink = apply_filters( 'pre_post_link', $permalink, $post, $leavename );

	if ( 'plain' !== $permalink && ! in_array(
		$post->post_status,
		[
			'draft',
			'pending',
			'auto-draft',
			'future',
		],
		true
	) ) {
		$unixtime = strtotime( $post->post_date );

		$category = '';
		if ( strpos( $permalink, '%category%' ) !== false ) {
			$cats = get_the_category( $post->ID );
			if ( $cats ) {
				$cats = wp_list_sort(
					$cats,
					[
						'term_id' => 'ASC',
					]
				);

				/**
				 * Filters the category that gets used in the %category% permalink token.
				 * @param WP_Term $cat The category to use in the permalink.
				 * @param array $cats Array of all categories (WP_Term objects) associated with the post.
				 * @param WP_Post $post The post in question.
				 * @since 3.5.0
				 */
				$category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );

				$category_object = get_term( $category_object, 'category' );
				$category        = $category_object->slug;

				if ( ! empty( $category_object->parent ) ) {
					$parent   = $category_object->parent;
					$category = get_category_parents( $parent, false, '/', true ) . $category;
				}
			}
			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty( $category ) ) {
				$default_category = get_term( Mediavine\Grow\Settings::get_setting( 'default_category' ), 'category' );
				if ( $default_category && ! is_wp_error( $default_category ) ) {
					$category = $default_category->slug;
				}
			}
		}

		$author = '';
		if ( strpos( $permalink, '%author%' ) !== false ) {
			$authordata = get_userdata( $post->post_author );
			$author     = $authordata->user_nicename;
		}

		$date           = explode( ' ', date( 'Y m d H i s', $unixtime ) );
		$rewritereplace =
			[
				$date[0],
				$date[1],
				$date[2],
				$date[3],
				$date[4],
				$date[5],
				$post->post_name,
				$post->ID,
				$category,
				$author,
				$post->post_name,
			];
		$permalink      = home_url( str_replace( $rewritecode, $rewritereplace, $permalink ) );

	} else { // if they're not using the fancy permalink option
		$permalink = home_url( '?p=' . $post->ID );
	}

	/**
	* Filters the permalink for a post.
	* Only applies to posts with post_type of 'post'.
	* @param string $permalink The post's permalink.
	* @param WP_Post $post The post in question.
	* @param bool $leavename Whether to keep the post name.
	* @since 1.5.0
	*/
	return apply_filters( 'post_link', $permalink, $post, $leavename );
}

/**
 * Register hooks for functions-post.php.
 */
function dpsp_register_functions_post() {
	add_action( 'wp', 'dpsp_cache_post_object' );
	add_filter( 'dpsp_get_post_title', 'dpsp_add_custom_post_title', 10, 2 );
	add_filter( 'dpsp_get_post_description', 'dpsp_add_custom_post_description', 10, 2 );
	add_filter( 'dpsp_get_post_image_data', 'dpsp_add_custom_post_image_data', 10, 3 );
}
