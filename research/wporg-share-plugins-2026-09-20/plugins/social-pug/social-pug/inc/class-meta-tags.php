<?php
namespace Mediavine\Grow;

use Social_Pug;

/**
 * Manage all meta tag building & output.
 */
class Meta_Tags {
	/**
	 * All-in-one main process called via WordPress hook.
	 */
	public static function build_and_output() {
		// Require single-post context.
		if ( ! is_singular() ) {
			return;
		}

		// Require meta tags to be enabled.
		$settings = Settings::get_setting( 'dpsp_settings', [] );
		if ( ! empty( $settings['disable_meta_tags'] ) ) {
			return;
		}

		// Require post data.
		$post = dpsp_get_current_post();
		if ( is_null( $post ) ) {
			return;
		}

		// If a password is required, or, the user hasn't supplied the correct password
		// https://github.com/nerdpressteam/grow-social/issues/1694
		if ( post_password_required( $post ) ) {
			return;
		}

		// Get all the tag data.
		$tags = self::get_data( $post, $settings );

		// Allow plugins to add other tags.
		$tags = apply_filters( 'mv_grow_build_tags', $tags, $post );

		self::render( $tags );
	}

	/**
	 * Output meta tags HTML.
	 *
	 * @param array $tags
	 */
	public static function render( array $tags ) {
		$attribution = 'Hubbub v.' . esc_attr( DPSP_VERSION ) . ' https://morehubbub.com/';

		echo '<!-- ' . esc_attr( $attribution ) . ' -->';

		echo self::build_html( $tags ); // @codingStandardsIgnoreLine
		do_action( 'dpsp_output_meta_tags' );

		echo esc_attr( PHP_EOL ) . '<!-- ' . esc_attr( $attribution ) . ' -->' . esc_attr( PHP_EOL );
	}

	/**
	 * Fetches data for Open Graph, Twitter, and Facebook meta tags.
	 *
	 * The indices used in the array map to their final attribute names.
	 *
	 * @param \WP_Post $post
	 * @param array $settings WordPress settings for this plugin.
	 * @return array Meta tag data.
	 */
	public static function get_data( \WP_Post $post, array $settings ) {
		// Get default title, description, and image.
		$title      = dpsp_get_post_title( $post->ID );
		$desc       = dpsp_get_post_description( $post->ID );
		$image_data = dpsp_get_post_image_data( $post->ID );

		// Only pull custom share data if Pro.
		if ( ! Social_Pug::is_free() ) {
			$title      = dpsp_get_post_custom_title( $post->ID ) ?: $title;
			$desc       = dpsp_get_post_custom_description( $post->ID ) ?: $desc;
			$image_data = dpsp_get_post_custom_image_data( $post->ID ) ?: $image_data;
		}

		// Set basic meta tag data.
		$tag_data = [
			'og:locale'              => get_locale(),
			'og:type'                => 'article',
			'og:title'               => $title,
			'og:description'         => $desc,
			'og:url'                 => dpsp_get_post_url( $post->ID ),
			'og:site_name'           => get_bloginfo( 'name' ),
			'og:updated_time'        => date( 'c', strtotime( $post->post_modified ) ),
			'article:published_time' => date( 'c', strtotime( $post->post_date ) ),
			'article:modified_time'  => date( 'c', strtotime( $post->post_modified ) ),
			'twitter:card'           => 'summary_large_image',
			'twitter:title'          => $title,
			'twitter:description'    => $desc,
			'flipboard-article'		 => $desc,
		];

		// Set Facebook App ID.
		if ( ! empty( $settings['facebook_app_id'] ) ) {
			$tag_data['fb:app_id'] = $settings['facebook_app_id'];
		}

		// Set image data.
		if ( is_array( $image_data ) ) {
			$tag_data['og:image']      = $image_data[0];
			$tag_data['twitter:image'] = $image_data[0];

			if ( ! empty( $image_data[1] ) ) {
				$tag_data['og:image:width'] = $image_data[1];
			}
			if ( ! empty( $image_data[2] ) ) {
				$tag_data['og:image:height'] = $image_data[2];
			}
		}

		return $tag_data;
	}

	/**
	 * Builds HTML for meta tags with the given attributes.
	 *
	 * @param array $tags Unindexed array of indexed arrays containing data for each `meta` tag.
	 * @return string HTML.
	 */
	public static function build_html( array $tags ) {
		$output = '';
		foreach ( $tags as $property => $value ) {
			
			// Meta tag attributes by network:
			// Twitter: name
			// Flipboard: class
			// All others: property
			$attr_name = ( 0 === strpos( $property, 'twitter:' ) ) ? 'name' : 'property';
			
			// Only Flipboard requires a CLASS! on the meta-tag. See issue: 1719
			if ( strpos( $property, 'flipboard' ) !== false ) {
				$attr_name = 'class';
			}

			$output   .= esc_attr( PHP_EOL ) . '<meta ' . $attr_name . '="' . esc_attr( $property ) . '" content="' . esc_attr( $value ) . '" />';
		}

		return $output;
	}
}
