<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Helper
 */
class Kiwi_Social_Share_Helper {

	/**
	 * @return array
	 */
	public static function get_custom_post_types() {
		$args = array(
			'public' => true,
		);

		$post_types	 = get_post_types( $args, 'objects' );
		$return		 = array();

		foreach ( $post_types as $post_type ) {
			$return[ $post_type->name ] = $post_type->label;
		}

		return $return;
	}

	/**
	 * @param string $network
	 * @param string $prop
	 *
	 * @return array
	 */
	public static function get_network_colors( $network = '',
											$prop = '' ) {
		$defaults = array(
			'monochrome'	 => array(
				'background'		 => '#666666',
				'text'				 => '#ffffff',
				'hover_background'	 => '#222222',
				'hover_text'		 => '#ffffff',
			),
			'facebook'		 => array(
				'background'		 => '#3b5998',
				'text'				 => '#ffffff',
				'hover_background'	 => '#1b4199',
				'hover_text'		 => '#ffffff',
			),
			'twitter'		 => array(
				'background'		 => '#4eaef8',
				'text'				 => '#ffffff',
				'hover_background'	 => '#1193f7',
				'hover_text'		 => '#ffffff',
			),
			'pinterest'		 => array(
				'background'		 => '#bd081c',
				'text'				 => '#ffffff',
				'hover_background'	 => '#770107',
				'hover_text'		 => '#ffffff',
			),
			'fintel'		 => array(
				'background'		 => '#087515',
				'text'				 => '#ffffff',
				'hover_background'	 => '#087515',
				'hover_text'		 => '#ffffff',
			),
			'linkedin'		 => array(
				'background'		 => '#1a85bc',
				'text'				 => '#ffffff',
				'hover_background'	 => '#006aa8',
				'hover_text'		 => '#ffffff',
			),
			'reddit'		 => array(
				'background'		 => '#ff4500',
				'text'				 => '#ffffff',
				'hover_background'	 => '#e22500',
				'hover_text'		 => '#ffffff',
			),
			'email'			 => array(
				'background'		 => '#4d9159',
				'text'				 => '#ffffff',
				'hover_background'	 => '#0e9126',
				'hover_text'		 => '#ffffff',
			),
			'telegram'		 => array(
				'background'		 => '#179cde',
				'text'				 => '#ffffff',
				'hover_background'	 => '#008cea',
				'hover_text'		 => '#ffffff',
			),
			'whatsapp'		 => array(
				'background'		 => '#0dc143',
				'text'				 => '#ffffff',
				'hover_background'	 => '#499b06',
				'hover_text'		 => '#ffffff',
			),
			'skype'			 => array(
				'background'		 => '#009ee5',
				'text'				 => '#ffffff',
				'hover_background'	 => '#008ae0',
				'hover_text'		 => '#ffffff',
			),
			'mix'			 => array(
				'background'		 => '#009ee5',
				'text'				 => '#ffffff',
				'hover_background'	 => '#008ae0',
				'hover_text'		 => '#ffffff',
			),
		);

		if ( !empty( $network ) && !empty( $prop ) ) {
			return $defaults[ $network ][ $prop ];
		}

		$saved_order = self::get_setting_value( 'networks_ordering' );

		if ( !empty( $saved_order ) ) {
			$array_keys	 = explode( ',', $saved_order );
			$defaults	 = array_replace( array_flip( array_filter( $array_keys ) ), $defaults );
		}

		$saved_options = get_option( 'kiwi_network_colors', false );
		if ( !$saved_options ) {
			return $defaults;
		}

		return array_merge( $defaults, $saved_options );
	}

	/**
	 * @return array
	 */
	public static function get_social_network_identities() {
		return array(
			'facebook'		 => array(
				'label'	 => esc_html__( 'Facebook', 'kiwi-social-share' ),
				'id'	 => 'facebook',
				'icon'	 => 'facebook',
			),
			'twitter'		 => array(
				'label'	 => esc_html__( 'Twitter', 'kiwi-social-share' ),
				'id'	 => 'twitter',
				'icon'	 => 'twitter',
			),
			'pinterest'		 => array(
				'label'	 => esc_html__( 'Pinterest', 'kiwi-social-share' ),
				'id'	 => 'pinterest',
				'icon'	 => 'pinterest',
			),
			'fintel'		 => array(
				'label'	 => esc_html__( 'Fintel', 'kiwi-social-share' ),
				'id'	 => 'fintel',
				'icon'	 => 'fintel',
			),
			'linkedin'		 => array(
				'label'	 => esc_html__( 'LinkedIn', 'kiwi-social-share' ),
				'id'	 => 'linkedin',
				'icon'	 => 'linkedin',
			),
			'reddit'		 => array(
				'label'	 => esc_html__( 'Reddit', 'kiwi-social-share' ),
				'id'	 => 'reddit',
				'icon'	 => 'reddit',
			),
			'email'			 => array(
				'label'	 => esc_html__( 'Email', 'kiwi-social-share' ),
				'id'	 => 'email',
				'icon'	 => 'envelope',
			),
			'telegram'		 => array(
				'label'	 => esc_html__( 'Telegram', 'kiwi-social-share' ),
				'id'	 => 'telegram',
				'icon'	 => 'telegram',
			),
			'whatsapp'		 => array(
				'label'	 => esc_html__( 'WhatsApp', 'kiwi-social-share' ),
				'id'	 => 'whatsapp',
				'icon'	 => 'whatsapp',
			),
			'skype'			 => array(
				'label'	 => esc_html__( 'Skype', 'kiwi-social-share' ),
				'id'	 => 'skype',
				'icon'	 => 'skype',
			),
			'mix'			 => array(
				'label'	 => esc_html__( 'Mix', 'kiwi-social-share' ),
				'id'	 => 'mix',
				'icon'	 => 'mix',
			),
		);
	}

	/**
	 * @return array
	 */
	public static function get_checked_networks() {
		$number		 = number_format( rand( 1, 9999 ) );
		$defaults	 = array(
			'facebook'		 => array(
				'name'		 => 'facebook',
				'count'		 => $number,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'twitter'		 => array(
				'name'		 => 'twitter',
				'count'		 => $number,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'pinterest'		 => array(
				'name'		 => 'pinterest',
				'count'		 => $number,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'fintel'		 => array(
				'name'		 => 'fintel',
				'count'		 => $number,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'linkedin'		 => array(
				'name'		 => 'linkedin',
				'count'		 => $number,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'mix'		 => array(
				'name'		 => 'mix',
				'count'		 => 0,
				'checked'	 => array(),
				'locked'	 => false
			),
			/* start-pro-version */
			'reddit'	 => array(
				'name'		 => 'reddit',
				'count'		 => $number,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'email'		 => array(
				'name'		 => 'email',
				'count'		 => 0,
				'checked'	 => array(),
				'locked'	 => false,
			),
			'telegram'	 => array(
				'name'		 => 'telegram',
				'count'		 => 0,
				'checked'	 => array(),
				'locked'	 => false
			),
			'whatsapp'	 => array(
				'name'		 => 'whatsapp',
				'count'		 => 0,
				'checked'	 => array(),
				'locked'	 => false
			),
			'skype'		 => array(
				'name'		 => 'skype',
				'count'		 => 0,
				'checked'	 => array(),
				'locked'	 => false
			),			
		/* end-pro-version */
		);

		$saved_order = self::get_setting_value( 'networks_ordering' );

		if ( !empty( $saved_order ) ) {
			$array_keys	 = explode( ',', $saved_order );
			$defaults	 = array_replace( array_flip( array_filter( $array_keys ) ), $defaults );
		}

		$saved_article_bar_option	 = self::get_setting_value( 'networks_article_bar', array() );
		$saved_floating_bar_option	 = self::get_setting_value( 'networks_floating_bar', array() );

		if ( !empty( $saved_article_bar_option ) ) {
			foreach ( $saved_article_bar_option as $network ) {

				if ( isset( $defaults[ $network ]['checked'] ) ) {
					$defaults[ $network ][ 'checked' ][] = 'article-bar';
				}
				
			}
		}

		if ( !empty( $saved_floating_bar_option ) ) {
			foreach ( $saved_floating_bar_option as $network ) {
				if ( isset( $defaults[ $network ]['checked'] ) ) {
					$defaults[ $network ][ 'checked' ][] = 'floating-bar';
				}
			}
		}

		return $defaults;
	}

	/**
	 * @param string $option  Option to get value
	 * @param string $default Default
	 * @param string $group   The options' group
	 *
	 * @return mixed|string
	 */
	public static function get_setting_value( $option = '', $default = '', $group = '' ) {
		if ( empty( $group ) ) {
			$group = 'kiwi_general_settings';
		}

		$options = get_option( $group, array() );

		if ( empty( $option ) ) {
			return $options;
		}

		if ( empty( $options ) || empty( $options[ $option ] ) ) {
			return $default;
		}

		return $options[ $option ];
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public static function get_excerpt_by_id( $id ) {

		$id = absint( $id );

		$the_post = get_post( $id );

		if ( NULL == $the_post ) {
			return '';
		}

		if ( has_excerpt() ) {
			$the_excerpt = $the_post->post_excerpt;
		} else {
			$the_excerpt = $the_post->post_content;
		}


		$the_excerpt	 = strip_tags( strip_shortcodes( $the_excerpt ) );
		$the_excerpt	 = str_replace( ']]>', ']]&gt;', $the_excerpt );
		$excerpt_length	 = apply_filters( 'excerpt_length', 100 );
		$excerpt_more	 = apply_filters( 'excerpt_more', ' ' . '[...]' );

		$words = preg_split( "/[\n\r\t ]+/", $the_excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
		if ( count( $words ) > $excerpt_length ) {
			array_pop( $words );
			$the_excerpt = implode( ' ', $words );
		}
		$the_excerpt = preg_replace( "/\r|\n/", "", $the_excerpt );

		return $the_excerpt;
	}

	/**
	 *
	 * We'll need to for the title of the posts
	 * Converts smart quotes
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public static function convert_smart_quotes( $content ) {
		$content = str_replace( '"', '\'', $content );
		$content = str_replace( '&#8220;', '\'', $content );
		$content = str_replace( '&#8221;', '\'', $content );
		$content = str_replace( '&#8216;', '\'', $content );
		$content = str_replace( '&#8217;', '\'', $content );

		return $content;
	}

}