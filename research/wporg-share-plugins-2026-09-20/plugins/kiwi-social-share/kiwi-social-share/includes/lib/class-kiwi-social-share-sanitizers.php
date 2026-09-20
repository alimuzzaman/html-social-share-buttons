<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Sanitizers
 */
class Kiwi_Social_Share_Sanitizers {

	/**
	 * @param $input
	 *
	 * @return array
	 */
	public static function automatic( $input ) {
		$sanitized = array();
		$token     = Kiwi_Social_Share::instance();
		$token     = $token->_token;
		/**
		 * In some cases, it's too early to check this array (it's not yet formed)
		 */
		if ( ! is_array( $input ) ) {
			return $input;
		}

		foreach ( $input as $key => $val ) {
			switch ( $key ) {
				case 'networks_ordering':
					$sanitized[ $key ] = sanitize_text_field( $val );
					break;
				case 'license_key':
					$sanitized[ $key ] = trim( sanitize_text_field( $val ) );
					delete_transient( $token . '_license_message' );
					break;
				case 'networks_article_bar':
				case 'networks_floating_bar':
					$sanitized[ $key ] = self::checkbox_multiple_fields_social( $val );
					break;
				case 'floating_bar_color':
					$sanitized[ $key ] = sanitize_hex_color( $val );
					break;
				case 'share_buttons':
				case 'share_buttons_posttypes':
				case 'floating_panel_posttypes':
				case 'floating_panel':
				case 'share_counts':
				case 'click_to_tweet':
				case 'share_buttons_location':
				case 'floating_panel_location':
				case 'custom_meta_boxes_posttypes':
				case 'click_to_tweet_posttypes':
				case 'highlight_to_tweet_posttypes':
				case 'styles_colors':
				case 'button_shape':
				case 'button_shape_floating':
				case 'ga_tracking':
				case 'advanced_shortcode_manager':
				case 'mobile_only_sharing':
					$sanitized[ $key ] = self::radio_fields( $val );
					break;
				case 'floating_panel_posttypes_list':
				case 'share_buttons_posttypes_list':
				case 'click_to_tweet_posttypes_list':
				case 'highlight_to_tweet_posttypes_list':
				case 'custom_meta_boxes_posttypes_list':
					$sanitized[ $key ] = self::checkbox_multiple_fields_post_types( $val );
					break;
				default :
					$sanitized[ $key ] = sanitize_text_field( $val );
					break;
			}
		}

		return $sanitized;
	}


	/**
	 * Sanitize Radio fields
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public static function radio_fields( $input ) {
		if ( ! empty( $input ) ) {
			return sanitize_text_field( $input );
		}

		return $input;
	}

	/**
	 * Sanitize Text fields
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public static function text_fields( $input ) {
		if ( ! empty( $input ) ) {
			return sanitize_text_field( $input );
		}

		return $input;
	}

	/**
	 * Sanitize multiple values to be saved in the database
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function checkbox_multiple_fields( $input ) {
		if ( ! empty( $input ) ) {
			$sanitized = array();
			foreach ( $input as $val ) {
				$sanitized[] = sanitize_text_field( $val );
			}

			return $sanitized;
		}

		return $input;
	}

	/**
	 * We don`t allow saving other values in the database except these
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function checkbox_multiple_fields_social( $input ) {
		$allowed = array(
			'facebook',
			'twitter',
			'linkedin',
			'pinterest',
			'fintel',
			'mix',
			'reddit',
			'email',
			'whatsapp',
			'telegram',
			'skype'
		);

		if ( ! empty( $input ) ) {
			foreach ( $input as $index => $social ) {
				if ( ! in_array( $social, $allowed ) ) {
					unset( $input[ $index ] );
				}
			}
		}

		return self::checkbox_multiple_fields( $input );
	}

	/**
	 * Don't allow undefined custom post types injection
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function checkbox_multiple_fields_post_types( $input ) {
		$allowed = Kiwi_Social_Share_Helper::get_custom_post_types();

		if ( ! empty( $input ) ) {
			foreach ( $input as $index => $type ) {
				if ( ! array_key_exists( $type, $allowed ) ) {
					unset( $input[ $index ] );
				}
			}
		}

		return self::checkbox_multiple_fields( $input );
	}

	/**
	 * Sanitize hex fields
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function multiple_hex_fields( $input ) {
		if ( ! empty( $input ) ) {
			$sanitized = array();
			foreach ( $input as $array => $properties ) {
				foreach ( $properties as $property => $val ) {
					$sanitized[ $array ][ $property ] = sanitize_hex_color( $val );
				}
			}

			$instance = Kiwi_Social_Share::instance();
			/**
			 * Delete transient when we update the options
			 * CHECK ->> includes/class-kiwi-social-share-customization.php:163
			 */
			delete_transient( $instance->_token . '_css_transient' );

			return $sanitized;
		}


		return $input;
	}
}