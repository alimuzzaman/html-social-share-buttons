<?php
namespace Mediavine\Grow;

class Compatibility {

	/** @var string[] Yoast classes to have output blocked. */
	const MV_YOAST_PRESENTER_DENYLIST = [
		'Open_Graph\Locale_Presenter',
		'Open_Graph\Type_Presenter',
		'Open_Graph\Title_Presenter',
		'Open_Graph\Description_Presenter',
		'Open_Graph\Url_Presenter',
		'Open_Graph\Site_Name_Presenter',
		'Open_Graph\Article_Publisher_Presenter',
		'Open_Graph\Article_Author_Presenter',
		'Open_Graph\Article_Published_Time_Presenter',
		'Open_Graph\Article_Modified_Time_Presenter',
		'Open_Graph\Image_Presenter',
		'Twitter\Card_Presenter',
		'Twitter\Title_Presenter',
		'Twitter\Description_Presenter',
		'Twitter\Image_Presenter',
		'Twitter\Creator_Presenter',
		'Twitter\Site_Presenter',
	];

	/**
	 * Determine whether Yoast is available & enabled.
	 *
	 * @return bool
	 */
	public static function is_yoast_enabled() {
		return apply_filters( 'mv_grow_dev_yoast_enabled', defined( 'WPSEO_VERSION' ) );
	}

	/**
	 * Provide compatibility with Yoast when rendering meta tags.
	 *
	 * Set Yoast data as fallbacks and add extra Yoast tags to our output.
	 */
	public static function set_yoast_meta_data() {
		if ( self::is_yoast_enabled() ) {
			add_filter( 'dpsp_get_post_title', [ 'Mediavine\Grow\Compatibility', 'set_yoast_fallback_title' ], 10, 2 );
			add_filter( 'dpsp_get_post_description', [ 'Mediavine\Grow\Compatibility', 'set_yoast_fallback_description' ], 10, 2 );
			add_filter( 'dpsp_get_post_image_data', [ 'Mediavine\Grow\Compatibility', 'set_yoast_fallback_image_data' ], 10, 2 );
			add_filter( 'mv_grow_build_tags', [ 'Mediavine\Grow\Compatibility', 'get_yoast_tags' ], 10, 2 );
		}
	}

	/**
	 * Render our meta tags in Yoast's location.
	 *
	 * @param $hook_name
	 * @return string
	 */
	public static function set_yoast_meta_tag_hook( $hook_name ) {
		if ( self::is_yoast_enabled() ) {
			$hook_name = 'wpseo_head';
		}
		return $hook_name;
	}

	/**
	 * Get & parse a Yoast meta value from the database.
	 *
	 * @param int $post_id
	 * @param string $attribute_slug
	 * @param string $fallback
	 * @return string Yoast meta value or fallback if none is found.
	 */
	public static function get_yoast_value( $post_id, $attribute_slug, $fallback = '' ) {
		// Require a Yoast dependency.
		if ( ! function_exists( 'wpseo_replace_vars' ) ) {
			return $fallback;
		}

		$pattern = get_post_meta( $post_id, '_yoast_wpseo_' . $attribute_slug, true );
		if ( ! $pattern ) {
			return $fallback;
		}

		return wpseo_replace_vars( $pattern, get_post( $post_id ) );
	}

	/**
	 * Get OG tags that are not handled by Grow but may be set by Yoast.
	 *
	 * @param array $tags
	 * @param \WP_Post $post
	 * @return array
	 */
	public static function get_yoast_tags( array $tags, \WP_Post $post ) {
		// Yoast extra Open Graph tag.
		$facebook = apply_filters( 'wpseo_opengraph_author_facebook', get_the_author_meta( 'facebook', $post->post_author ) );
		if ( ! empty( $facebook ) && is_string( $facebook ) ) {
			$tags['article:author'] = $facebook;
		}

		// Yoast extra Twitter tag.
		$twitter = apply_filters( 'wpseo_twitter_creator_account', ltrim( trim( get_the_author_meta( 'twitter', $post->post_author ) ), '@' ) );
		if ( ! empty( $twitter ) && is_string( $twitter ) ) {
			$tags['twitter:creator'] = '@' . $twitter;
		}

		return $tags;
	}

	/**
	 * Build a Yoast image's data array.
	 *
	 * @param int $post_id
	 * @param string $attribute_slug
	 * @return array Image data.
	 */
	public static function get_yoast_image_data( $post_id, $attribute_slug ) {
		// Require a Yoast dependency.
		if ( ! class_exists( '\WPSEO_Image_Utils' ) ) {
			return [];
		}

		$image_url = get_post_meta( $post_id, '_yoast_wpseo_' . $attribute_slug, true );
		if ( empty( $image_url ) ) {
			return [];
		}

		$image_id = \WPSEO_Image_Utils::get_attachment_by_url( $image_url );
		if ( empty( $image_id ) ) {
			return [];
		}

		$data = wp_get_attachment_image_src( $image_id, 'full' );
		if ( ! is_array( $data ) ) {
			return [];
		}

		return $data;
	}

	/**
	 * Allow use of Yoast's title if none was found for Grow Social.
	 *
	 * @param $post_title
	 * @param $post_id
	 * @return string
	 */
	public static function set_yoast_fallback_title( $post_title, $post_id ) {
		$yoast_title = self::get_yoast_value( $post_id, 'title' );
		$yoast_title = self::get_yoast_value( $post_id, 'opengraph-title', $yoast_title );
		if ( ! empty( $yoast_title ) ) {
			return $yoast_title;
		}

		return $post_title;
	}

	/**
	 * Allow use of Yoast's description if none was found for Grow Social.
	 *
	 * @param $post_desc
	 * @param $post_id
	 * @return string
	 */
	public static function set_yoast_fallback_description( $post_desc, $post_id ) {
		$yoast_desc = self::get_yoast_value( $post_id, 'metadesc' );
		$yoast_desc = self::get_yoast_value( $post_id, 'opengraph-description', $yoast_desc );
		if ( ! empty( $yoast_desc ) ) {
			return $yoast_desc;
		}

		return $post_desc;
	}

	/**
	 * Allow use of Yoast's image data if none was found for Grow Social.
	 *
	 * @param $post_image_data
	 * @param $post_id
	 * @return array
	 */
	public static function set_yoast_fallback_image_data( $post_image_data, $post_id ) {
		$yoast_image_data = self::get_yoast_image_data( $post_id, 'twitter-image' );

		if ( empty( $yoast_image_data ) ) {
			// Fallback to Open Graph if no Twitter image found.
			$yoast_image_data = self::get_yoast_image_data( $post_id, 'opengraph-image' );
		}

		if ( ! empty( $yoast_image_data ) ) {
			return $yoast_image_data;
		}

		return $post_image_data;
	}

	/**
	 * Determine if a given Yoast Presenter class instance should be blocked from output.
	 *
	 * @param object $presenter Yoast presenter class instance
	 * @return bool
	 */
	public static function is_yoast_presenter_on_denylist( $presenter ) {
		$yoast_namespace = 'Yoast\WP\SEO\Presenters\\';
		foreach ( self::MV_YOAST_PRESENTER_DENYLIST as $class ) {
			if ( class_exists( $yoast_namespace . $class ) && is_a( $presenter, $yoast_namespace . $class ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter the presenters list from Yoast to remove the denylist.
	 *
	 * @param array $presenters
	 * @return array Presenters not on the denylist.
	 */
	public static function filter_yoast_presenters( $presenters ) {
		$pass = [];
		foreach ( $presenters as $presenter ) {
			if ( ! self::is_yoast_presenter_on_denylist( $presenter ) ) {
				$pass[] = $presenter;
			}
		}

		return $pass;
	}

	/**
	 * Disable known Open Graph & Twitter meta tags generated by other plugins.
	 *
	 * Plugins covered: Jetpack, Yoast SEO.
	 */
	public static function disable_known_meta_tags() {
		// Do nothing on singular pages.
		if ( ! is_singular() ) {
			return;
		}

		// Require meta-tags option enabled.
		$settings = Settings::get_setting( 'dpsp_settings', [] );
		if ( ! empty( $settings['disable_meta_tags'] ) ) {
			return;
		}

		// Require current post.
		if ( is_null( dpsp_get_current_post() ) ) {
			return;
		}

		// Disable Jackpack Open Graph tags.
		add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );
		add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );

		// Remove the Open Graph and Twitter tags added by Yoast.
		add_filter( 'wpseo_frontend_presenters', [ 'Mediavine\Grow\Compatibility', 'filter_yoast_presenters' ] );
		if ( self::is_yoast_enabled() ) {
			global $wpseo_og;
			remove_action( 'wpseo_head', [ $wpseo_og, 'opengraph' ], 30 );
			remove_action( 'wpseo_head', [ 'WPSEO_Twitter', 'get_instance' ], 40 );
		}
	}
}
