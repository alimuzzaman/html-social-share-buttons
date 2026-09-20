<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kiwi_Social_Share_Highlight_Share {
	/**
	 * The single instance of Kiwi_Social_Share_Highlight_Share
	 *
	 * @var    object
	 * @access   private
	 * @since    1.0.0
	 */
	private static $_instance = NULL;

	/**
	 * @var
	 */
	public $post_types = array();

	/**
	 * Kiwi_Social_Share_Highlight_Share constructor.
	 */
	public function __construct() {
		$this->set_options();
		add_action( 'wp', array( $this, 'wp_loaded' ), 10 );
	}

	/**
	 * Set Kiwi Backend Options
	 */
	public function set_options() {
		$highlight_to_tweet = Kiwi_Social_Share_Helper::get_setting_value( 'highlight_to_tweet_posttypes', 'all' );
		if ( $highlight_to_tweet === 'all' ) {
			$this->post_types = 'all';
		} else {
			$post_types       = Kiwi_Social_Share_Helper::get_setting_value( 'highlight_to_tweet_posttypes_list', array() );
			$this->post_types = array_merge( $this->post_types, $post_types );
		};
	}

	/**
	 *  Start plugin action here
	 */
	public function wp_loaded() {

		/* start-pro-version */
		if ( is_array( $this->post_types ) && ! in_array( get_post_type(), $this->post_types ) ) {
			return;
		}
		/* end-pro-version */

		if ( is_feed() ) {
			return;
		}

		$kiwi = Kiwi_Social_Share::instance();
		wp_enqueue_style( $kiwi->_token . '-frontend' );
		wp_enqueue_script( $kiwi->_token . '-frontend' );

		//Load content area
		add_filter( 'the_content', array( $this, 'wrap_content_area' ) );
		add_filter( 'the_excerpt', array( $this, 'wrap_excerpt_area' ) );
		add_action( 'wp_footer', array( $this, 'highlighter_section' ) );
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function wrap_content_area( $content ) {
		if ( ! in_the_loop() ) {
			return $content;
		}

		$twitter_button = new Kiwi_Social_Share_Social_Button_Twitter();
		$title          = get_the_title( get_the_ID() );
		$url            = $twitter_button->get_current_page_url( get_the_ID() );

		/* start-pro-version */
		if ( $twitter_button->short_url !== NULL ) {
			$url = $twitter_button->short_url;
		};

		/* end-pro-version */

		$content = sprintf( '<div class="kiwi-highlighter-content-area" data-url="%s" data-title="%s">%s</div>', esc_url( $url ), esc_attr( $title ), $content );

		return $content;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function wrap_excerpt_area( $content ) {
		if ( ! in_the_loop() ) {
			return $content;
		}

		$twitter_button = new Kiwi_Social_Share_Social_Button_Twitter();
		$title          = get_the_title( get_the_ID() );
		$url            = $twitter_button->get_current_page_url( get_the_ID() );

		/* start-pro-version */
		if ( $twitter_button->short_url !== NULL ) {
			$url = $twitter_button->short_url;
		};

		/* end-pro-version */

		$content = sprintf( '<div class="kiwi-highlighter-excerpt-area" data-url="%s" data-title="%s">%s</div>', esc_url( $url ), esc_attr( $title ), $content );

		return $content;
	}

	/**
	 *
	 */
	public function highlighter_section() {
		
		$kiwi = Kiwi_Social_Share::instance();
		wp_enqueue_style( $kiwi->_token . '-frontend' );
		wp_enqueue_script( $kiwi->_token . '-frontend' );

		$additional     = '';
		$tracking_html  = '';

		$twitter_handle = Kiwi_Social_Share_Helper::get_setting_value( 'twitter_username', '', 'kiwi_social_identities' );
		$tracking       = Kiwi_Social_Share_Helper::get_setting_value( 'ga_tracking', '', 'kiwi_social_identities' );

		if ( ! empty( $twitter_handle ) ) {
			$additional = '&via=' . str_replace( '@', '', $twitter_handle );
		}
		if ( ! empty( $tracking ) ) {
			$tracking_html = ' data-tracking="true" ';
		}

		$html = '';
		$html .= '<div class="kiwi-highlight-sharer">';
		$html .= '<a href="//twitter.com/intent/tweet?url=%url%&text=%text%' . $additional . '" target="_blank" class="kiwi-nw-twitter" ' . $tracking_html . '><span class="kicon-twitter"></span></a>';
		$html .= '</div>';

		echo wp_kses_post( $html );
	}
}