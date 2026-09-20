<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kiwi_Social_Share_Frontend {
	/**
	 * The single instance of Kiwi_Social_Share.
	 *
	 * @var    object
	 * @access   private
	 * @since    1.0.0
	 */
	private static $_instance = NULL;

	/**
	 * Kiwi_Social_Share_Frontend constructor.
	 *
	 * @param string $file
	 * @param string $version
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		// Add the custom css styles
		/* start-pro-version */
		new Kiwi_Social_Share_Customization();
		/* end-pro-version */
		// Load the article bar
		new Kiwi_Social_Share_View_Article_Bar();
		// Load the floating bar
		new Kiwi_Social_Share_View_Floating_Bar();
		// Load Highlight Share
		if ( Kiwi_Social_Share_Helper::get_setting_value( 'highlight_to_tweet', '' ) === 'on' ) {
			new Kiwi_Social_Share_Highlight_Share();
		}
		// Add the Open Graph Meta
		add_action( 'wp_head', array( $this, 'add_open_graph_meta' ) );
	}

	/**
	 * Adds the open graph meta
	 */
	public function add_open_graph_meta() {
		// Create/check default values
		$info = array(
			'postID'              => absint( get_the_ID() ),
			'title'               => esc_html( get_the_title() ),
			'imageURL'            => get_post_thumbnail_id( absint( get_the_ID() ) ),
			'description'         => esc_html( Kiwi_Social_Share_Helper::get_excerpt_by_id( absint( get_the_ID() ) ) ),
			'fb_app_id'           => esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_page_id', '', 'kiwi_social_identities' ) ),
			'fp_url'              => esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_page_url', '', 'kiwi_social_identities' ) ),
			'user_twitter_handle' => esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'twitter_username', '', 'kiwi_social_identities' ) )
		);

		if ( ! empty( $info['user_twitter_handle'] ) ) {
			$info['user_twitter_handle'] = str_replace( '@', '', $info['user_twitter_handle'] );
		}

		$metabox = array(
			'title'               => get_post_meta( get_the_ID(), 'kiwi_social-media-title', true ),
			'description'         => get_post_meta( get_the_ID(), 'kiwi_social-media-description', true ),
			'imageURL'            => get_post_meta( get_the_ID(), 'kiwi_social-media-image', true ),
			'twitter_description' => get_post_meta( get_the_ID(), 'kiwi_social-media-custom-tweet', true ),
		);

		$info = wp_parse_args( $metabox, $info );

		$twitter_button = new Kiwi_Social_Share_Social_Button_Twitter();
		$url            = $twitter_button->get_current_page_url( get_the_ID() );

		/* start-pro-version */
		if ( $twitter_button->short_url !== NULL ) {
			$url = $twitter_button->short_url;
		};
		/* end-pro-version */
		$info['header_output'] = '';
		// We only modify the Open Graph tags on single blog post pages
		if ( is_singular() ) {
			if ( ( isset( $info['title'] ) && $info['title'] ) || ( isset( $info['description'] ) && $info['description'] ) || ( isset( $info['imageURL'] ) && $info['imageURL'] ) ) {

				// Check if Yoast Exists so we can coordinate output with their plugin accordingly
				if ( ! defined( 'WPSEO_VERSION' ) ) {
					// Add twitter stuff
					$info['header_output'] .= PHP_EOL . '<!-- Twitter OG tags by Kiwi Social Sharing Plugin -->';

					$info['header_output'] .= PHP_EOL . '<meta name="twitter:card" content="summary" />';
					$info['header_output'] .= PHP_EOL . '<meta name="twitter:title" content="' . esc_attr( trim( $info['title'] ) ) . '" />';
					$info['header_output'] .= PHP_EOL . '<meta name="twitter:url" content="' . esc_url( $url ) . '" />';

					if ( ! empty( $info['user_twitter_handle'] ) ) {
						$info['header_output'] .= PHP_EOL . '<meta name="twitter:site" content="' . esc_attr( trim( $info['user_twitter_handle'] ) ) . '" />';
						$info['header_output'] .= PHP_EOL . '<meta name="twitter:creator" content="' . esc_attr( trim( $info['user_twitter_handle'] ) ) . '" />';
					}

					if ( ! empty( $info['twitter_description'] ) ) {
						$info['header_output'] .= PHP_EOL . '<meta name="twitter:description" content="' . esc_attr( $info['twitter_description'] ) . '" />';
					}

					if ( ! empty( $info['imageURL'] ) ) {
						$info['header_output'] .= PHP_EOL . '<meta name="twitter:image" content="' . esc_attr( $info['imageURL'] ) . '" />';
					}

					$info['header_output'] .= PHP_EOL . '<!--  / Twitter OG tags by Kiwi Social Sharing Plugin -->';

					// Add all our Open Graph Tags to the Return Header Output
					$info['header_output'] .= PHP_EOL . '<!-- Meta OG tags by Kiwi Social Sharing Plugin -->';
					$info['header_output'] .= PHP_EOL . '<meta property="og:type" content="article" /> ';

					// Open Graph Title: Create an open graph title meta tag
					if ( $info['title'] ) {
						// If nothing else is defined, let's use the post title
						$info['header_output'] .= PHP_EOL . '<meta property="og:title" content="' . esc_attr( Kiwi_Social_Share_Helper::convert_smart_quotes( htmlspecialchars_decode( get_the_title() ) ) ) . '" />';
					}

					if ( $info['description'] ) {
						// If nothing else is defined, let's use the post excerpt
						$info['header_output'] .= PHP_EOL . '<meta property="og:description" content="' . esc_attr( Kiwi_Social_Share_Helper::convert_smart_quotes( $info['description'] ) ) . '" />';
					}

					if ( has_post_thumbnail( $info['postID'] ) ) {
						// If nothing else is defined, let's use the post Thumbnail as long as we have the URL cached
						$og_image = wp_get_attachment_image_src( get_post_thumbnail_id( $info['postID'] ), 'full' );
						if ( $og_image ) {
							$info['header_output'] .= PHP_EOL . '<meta property="og:image" content="' . esc_url( $og_image[0] ) . '" />';
						}
					}

					$info['header_output'] .= PHP_EOL . '<meta property="og:url" content="' . esc_url( $url ) . '" />';
					$info['header_output'] .= PHP_EOL . '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';
					$info['header_output'] .= PHP_EOL . '<meta property="article:published_time" content="' . esc_attr( get_post_time( 'c' ) ) . '" />';
					$info['header_output'] .= PHP_EOL . '<meta property="article:modified_time" content="' . esc_attr( get_post_modified_time( 'c' ) ) . '" />';
					$info['header_output'] .= PHP_EOL . '<meta property="og:updated_time" content="' . esc_attr( get_post_modified_time( 'c' ) ) . '" />';

					// add facebook app id
					if ( ! empty( $info['fb_app_id'] ) ) {
						$info['header_output'] .= PHP_EOL . '<meta property = "fb:app_id" content="' . esc_attr( trim( $info['fb_app_id'] ) ) . '" />';
					}

					// add facebook url
					if ( ! empty( $info['fp_url'] ) ) {
						$info['header_output'] .= PHP_EOL . '<meta property="article:publisher" content="' . esc_attr( trim( $info['fp_url'] ) ) . '" />';
					}

					// append the closing comment :)
					$info['header_output'] .= PHP_EOL . '<!--/end meta tags by Kiwi Social Sharing Plugin -->';
					// Return the variable containing our information for the meta tags
					
					echo wp_kses( $info['header_output'] . PHP_EOL, array( 
						'meta'  => array(
							'property' => array(),
							'content' => array(),
							'name' => array(),
						)
					) );

				}
			}
		}
	}

	/**
	 * Main Kiwi_Social_Share_Frontend Instance
	 *
	 * Ensures only one instance of Kiwi_Social_Share_Frontend is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see   Kiwi_Social_Share()
	 * @return Main Kiwi_Social_Share_Frontend instance
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()


}