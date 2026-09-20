<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Click_To_Tweet
 */
class Kiwi_Social_Share_Click_To_Tweet {
	/**
	 * @var array
	 */
	public $post_types = array();

	/**
	 * Kiwi_Social_Share_Click_To_Tweet constructor.
	 */
	public function __construct() {
		$this->set_options();

		if ( is_admin() ) {
			add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
			add_action( 'init', array( $this, 'tinymce_button' ) );
		}
		add_shortcode( 'KiwiClickToTweet', array( $this, 'KiwiClickToTweetShortcode' ) );
	}

	/**
	 * Set Kiwi Backend Options
	 */
	public function set_options() {
		if ( Kiwi_Social_Share_Helper::get_setting_value( 'click_to_tweet_posttypes', 'all' ) === 'all' ) {
			$this->post_types = 'all';
		} else {
			$post_types       = Kiwi_Social_Share_Helper::get_setting_value( 'click_to_tweet_posttypes_list', array() );
			$this->post_types = array_merge( $this->post_types, $post_types );
		};
	}

	/**
	 *
	 */
	public function tinymce_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) == 'true' ) {

			/* start-pro-version */
			global $typenow;

			if ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
				$post    = get_post( absint( $_GET['post'] ) );
				if ( empty( $post->post_type ) ) {
					$typenow = 'post';
				} else {
					$typenow = $post->post_type;
				}
			}

			if ( is_array( $this->post_types ) && ! in_array( $typenow, $this->post_types ) ) {
				return;
			}
			/* end-pro-version */

			$kiwi = Kiwi_Social_Share::instance();
			wp_enqueue_style( $kiwi->_token . '-frontend' );
			wp_enqueue_script( $kiwi->_token . '-frontend' );
			wp_enqueue_style( 'icomoon', esc_url( $kiwi->assets_url ) . 'vendors/icomoon/style.css', array(), $kiwi->_version );

			add_filter( 'mce_external_plugins', array( $this, 'tinymce_register_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'tinymce_register_button' ) );
		}
	}

	/**
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function tinymce_register_button( $buttons ) {
		array_push( $buttons, '|', 'KiwiClickToTweet' );

		return $buttons;
	}

	/**
	 * @param $plugin_array
	 *
	 * @return mixed
	 */
	public function tinymce_register_plugin( $plugin_array ) {
		$kiwi = Kiwi_Social_Share::instance();

		$plugin_array['KiwiClickToTweet'] = esc_url( $kiwi->assets_url ) . 'js/KiwiClickToTweet' . $kiwi->script_suffix . '.js';

		return $plugin_array;
	}

	/**
	 * @param $ver
	 *
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;

		return $ver;
	}

	/**
	 * The function to build the click to tweets
	 *
	 * @param  array $atts An array of attributes
	 *
	 * @return string The html of a click to tweet
	 */
	public function KiwiClickToTweetShortcode( $atts ) {

		$twitter_button = new Kiwi_Social_Share_Social_Button_Twitter();
		$atts['tweet']  = rtrim( $atts['tweet'] );
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

		$url = $twitter_button->get_current_page_url( get_the_ID() );

		/* start-pro-version */
		if ( $twitter_button->short_url !== NULL ) {
			$url = $twitter_button->short_url;
		};

		/* end-pro-version */

		$content = '<blockquote class="kiwi-click-to-tweet-content-area"><a data-class="popup" data-network="twitter" class="kiwi-click-to-tweet-url" href="//twitter.com/intent/tweet?url=' . esc_url( $url ) . '&text=' . urlencode( html_entity_decode( $atts['tweet'], ENT_COMPAT, 'UTF-8' ) ) . $additional . '" rel="nofollow" target="_blank" ' . $tracking_html . '>';
		$content .= '<span class="kiwi-click-to-tweet"><span class="kiwi-click-to-tweet-text">' . $atts['quote'] . '</span><span class="kiwi-click-to-tweet-button">' . __( 'Click To Tweet', 'kiwi-social-share' ) . '<i class="kicon-twitter"></i></span></span>';
		$content .= '</a>';
		$content .= '</blockquote>';

		return $content;
	}
}