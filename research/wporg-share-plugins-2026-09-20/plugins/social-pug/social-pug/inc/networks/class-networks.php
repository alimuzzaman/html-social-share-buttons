<?php
namespace Mediavine\Grow;

use Mediavine\Grow\Network;
use Social_Pug;

class Networks {
	/** @var Networks */
	private static $instance = null;

	/** @var Network[] Array of network instances */
	private $networks;

	/**
	 * Get instance of Class.
	 *
	 * @return Networks
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Register all networks at initialization.
	 */
	public function init() {
		$this->register_networks();
	}

	/**
	 * Turn the raw network array into an array of Network Class Instances.
	 *
	 * @return Network[]
	 */
	public function register_networks() {
		array_map( [ $this, 'register_network' ], $this->get_raw() );

		return $this->networks;
	}

	/**
	 * Get raw Network data for all networks, including pro networks if available.
	 *
	 * @return array[]
	 */
	private function get_raw() {
		// @TODO: Replace this raw array implementation with something a little more elegant
		$raw_networks = [
			[
				'slug'            => 'facebook',
				'name'            => 'Facebook',
				'share_format'    => 'https://www.facebook.com/sharer/sharer.php?u=%1$s&t=%2$s',
				'follow_format'   => 'https://www.facebook.com/%1$s',
				'has_share_count' => true,
			],
			[
				'slug'            => 'twitter',
				'name'            => 'Twitter',
				'share_format'    => 'https://twitter.com/intent/tweet?text=%2$s&url=%1$s',
				'follow_format'   => 'https://twitter.com/%1$s',
				'has_share_count' => Social_Pug::get_instance()->has_license,
			],
			[
				'slug'            => 'twitter',
				'name'            => 'X',
				'share_format'    => 'https://x.com/intent/tweet?text=%2$s&url=%1$s',
				'follow_format'   => 'https://x.com/%1$s',
				'has_share_count' => Social_Pug::get_instance()->has_license,
			],
			[
				'slug'            => 'pinterest',
				'name'            => 'Pinterest',
				'share_format'    => 'https://pinterest.com/pin/create/button/?url=%1$s&media=%3$s&description=%2$s',
				'follow_format'   => 'https://pinterest.com/%1$s',
				'has_share_count' => true,
			],
			[
				'slug'          => 'linkedin',
				'name'          => 'LinkedIn',
				'share_format'  => 'https://www.linkedin.com/shareArticle?url=%1$s&title=%2$s&summary=%4$s&mini=true',
				'follow_format' => 'https://www.linkedin.com/in/%1$s',
			],
			[
				'name'           => 'Grow',
				'slug'           => 'grow',
				'label_override' => 'Save',
				'icon_override'  => 'grow-heart',
				'share_format'   => '#',
				'tooltip'        => 'The Grow button will only show as an option if the Grow widget is also on the post. <a rel="noopener" href="https://help.grow.me/hc/en-us/articles/4416469985819-What-is-Grow">Learn more about Grow</a>',
			],
			[
				'slug'          => 'email',
				'name'          => 'Email',
				'share_format'  => 'mailto:?subject=%2$s&amp;body=%1$s', // 2 is page title, 1 is URL
				'follow_format' => 'mailto:%1$s'
			],
			[
				'slug'         => 'print',
				'name'         => 'Print',
				'share_format' => '#',
			],
		];
		if ( class_exists( '\Mediavine\Grow\Pro_Networks' ) ) {
			$raw_networks = array_merge( $raw_networks, Pro_Networks::get_raw() );
		}
		
		$settings = \Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
		if ( empty($settings['twitter_4ever']) ) :
			unset($raw_networks[1]); // Remove Twitter
		else :
			unset($raw_networks[2]); // Remove X
		endif;


		return $raw_networks;
	}

	/**
	 * Get a Network class instance by slug.
	 *
	 * @param string $slug Network Slug
	 * @return Network
	 */
	public function get( $slug ) {
		if ( isset( $this->networks[ $slug ] ) ) {
			return $this->networks[ $slug ];
		}
		return null;
	}

	/**
	 * Return all currently registered networks.
	 *
	 * @return Network[]
	 */
	public function get_all() {
		return $this->networks;
	}

	/**
	 * Get all shareable networks.
	 *
	 * @return Network[]
	 */
	public function get_shareable() {
		return $this->get_networks_with_format( 'share' );
	}

	/**
	 * Get all networks that have a specific url format available.
	 *
	 * @param string $format
	 * @return Network[]
	 */
	private function get_networks_with_format( $format ) {
		$method = 'get_' . $format . '_format';

		return array_filter(
			$this->networks, function ( Network $network ) use ( $method ) {
			if ( method_exists( $network, $method ) ) {
				return ! empty( $network->$method() );
			}

			return false;
			}
		);
	}



	/**
	 * Get all followable networks.
	 *
	 * @return Network[]
	 */
	public function get_followable() {
		return $this->get_networks_with_format( 'follow' );
	}

	/**
	 * Get all countable networks.
	 *
	 * @return Network[]
	 */
	public function get_countable() {
		return array_filter(
			$this->networks, function ( Network $network ) {
				return $network->has_count();
			}
		);
	}

	/**
	 * Turn an array of Network classes into an associative array where the key is the slug and the value is the name.
	 *
	 * @param Network[] $networks
	 * @return array
	 */
	public function make_simple_array( $networks ) {
		$output = [];
		foreach ( $networks as $slug => $network ) {
			$output[ $slug ] = $network->get_label();
		}

		return $output;
	}

	/**
	 * Turn an array of Network classes into an numerical indexed array where the value is the slug
	 *
	 * @param Network[] $networks
	 * @return string[] Array of slugs
	 */
	public function make_slug_array( $networks ) {
		return array_map(
			function ( $network ) {
			return $network->get_slug();
			}, $networks
		);
	}

	/**
	 * Turn an array of network data in to a Network class instance.
	 *
	 * @param array $network
	 */
	public function register_network( $network ) {
		$icon_data = Network_Icons::get_icon( $network['slug'] );

		$icon          = new Icon( $network['slug'], $icon_data );
		$icon_override = null;

		$icons = Icons::get_instance();
		$icons->register_icon( $icon );

		if ( ! empty( $network['icon_override'] ) ) {
			$icon_override_data = Network_Icons::get_icon( $network['icon_override'] );
			$icon_override      = new Icon( $network['slug'] . '_override', $icon_override_data );
			$icons->register_icon( $icon_override );
		}

		$share_format    = isset( $network['share_format'] ) ? $network['share_format'] : '';
		$follow_format   = isset( $network['follow_format'] ) ? $network['follow_format'] : '';
		$label_override  = isset( $network['label_override'] ) ? $network['label_override'] : '';
		$has_share_count = isset( $network['has_share_count'] ) ? $network['has_share_count'] : false;
		$tooltip         = isset( $network['tooltip'] ) ? $network['tooltip'] : '';
		$args            = [
			'slug'            => $network['slug'],
			'name'            => $network['name'],
			'share_format'    => $share_format,
			'follow_format'   => $follow_format,
			'icon'            => $icon,
			'icon_override'   => $icon_override,
			'label_override'  => $label_override,
			'has_share_count' => $has_share_count,
			'tooltip'         => $tooltip,
		];

		$this->networks[ $network['slug'] ] = new Network( $args );
	}
}
