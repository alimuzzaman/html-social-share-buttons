<?php
namespace Mediavine\Grow;

/**
 * Represents a single network that can be shared to or followed
 */
class Network {
	/** @var string Simple representation of the network, used to identify it in most functions */
	protected $slug = '';

	/** @var string Network name for display that will be shown to the end user */
	protected $name = '';

	/** @var string Text for button label that will override the name */
	protected $label_override = '';

	/** @var string A string format for the follow link using with sprintf and passed data to turn it into a real link */
	protected $follow_format = '';

	/** @var string A string format for the share link using with sprintf and passed data to turn it into a real link */
	protected $share_format = '';

	/** @var bool Whether or not network has a share count capability */
	protected $has_share_count = false;

	/** @var Icon
	 *  Icon class instance for this network's Icon
	 */
	protected $icon;

	/** @var Icon  Icon class instance for this network's override Icon */
	protected $icon_override;

	/** @var string  Tooltip to be shown in networks selector */
	protected $tooltip = '';

	/**
	 * Load the data.
	 *
	 * @param array $args Network creation arguments
	 */
	public function __construct( $args ) {
		$this->slug            = $args['slug'];
		$this->name            = $args['name'];
		$this->share_format    = $args['share_format'];
		$this->follow_format   = $args['follow_format'];
		$this->has_share_count = $args['has_share_count'];
		$this->icon            = $args['icon'];
		$this->icon_override   = $args['icon_override'];
		$this->label_override  = $args['label_override'];
		$this->tooltip         = $args['tooltip'];
	}

	/**
	 * Get the follow format for this network.
	 *
	 * @return string
	 */
	public function get_follow_format() {
		return $this->follow_format;
	}

	/**
	 * Get the Share format for thsi network.
	 *
	 * @return string
	 */
	public function get_share_format() {
		return $this->share_format;
	}

	/**
	 * Return whether or not this network is enabled at a given location.
	 *
	 * @param string $location Location slug to check against
	 * @return bool
	 */
	public function active_for_location( $location ) {
		$location_settings = dpsp_get_location_settings( $location );
		if ( isset( $location_settings['networks'] ) ) {
			return array_key_exists( $this->slug, $location_settings['networks'] );
		}

		return false;
	}

	/**
	 * Get the slug of the network.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get the name of the network.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the label for the network.
	 *
	 * @return string
	 */
	public function get_label() {
		return ! empty( $this->label_override ) ? $this->label_override : $this->get_name();
	}

	/**
	 * Combine the passed handle with the follow format to get a real follow link for this network.
	 *
	 * @param string $handle Network username
	 * @return string
	 */
	public function get_follow_link( $handle = '' ) {
		if ( empty( $this->follow_format ) ) {
			error_log( 'MV Grow Error: Attempting to get follow link from non followable network ' . $this->name ); // @codingStandardsIgnoreLine

			return '';
		}

		// In some cases the follow link may need to be a differet format, allow users to put a full URL in just in case and return that if so.
		if ( strpos( $handle, 'http' ) !== false ) {
			return $handle;
		}
		$link = sprintf( $this->follow_format, $handle );

		return apply_filters( 'dpsp_get_network_follow_link', $link, $this->slug );
	}

	/**
	 * Combine the passed post data with the share format to get a real share link for this post and network.
	 *
	 * @param string $url URL of the post to be shared
	 * @param string $title (optional) Title of the post that will be shared
	 * @param string $image (optional) URL of an image to share along with the post
	 * @param string $description (optional) Description of the post to be shared
	 * @return string
	 */
	public function get_share_link( $url, $title = '', $image = '', $description = '' ) {
		if ( empty( $this->share_format ) ) {
			error_log( 'Hubbub Error: Attempting to get share link from non shareable network ' . $this->name ); // @codingStandardsIgnoreLine

			return '';
		}
		$link = sprintf( $this->share_format, $url, $title, $image, $description );

		// Add via param for twitter only if it should be set so
		if ( 'twitter' === $this->slug ) {
			$settings = Settings::get_setting( 'dpsp_settings', [] );
			$via      = ( ! empty( $settings['twitter_username'] ) && ! empty( $settings['tweets_have_username'] ) ) ? '&via=' . $settings['twitter_username'] : '';
			$link    .= $via;
		}

		// Create Empty link when Pinterest Grid should be loaded
		if ( 'pinterest' === $this->slug ) {
			$settings = Settings::get_setting( 'dpsp_pinterest_share_images_setting', [] );
			$link     = ! empty( $settings['share_image_pinterest_button_share_behavior'] ) && ( 'post_image' === $settings['share_image_pinterest_button_share_behavior'] ) ? $link : '#';
		}

		return apply_filters( 'dpsp_get_network_share_link', $link, $this->slug );
	}

	/**
	 * Get the Whether or not network supports share count
	 *
	 * @return bool
	 */
	public function has_count() {
		return $this->has_share_count;
	}

	/**
	 * Get the Icon class instance for this network.
	 *
	 * @return Icon
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Get the Icon class instance for this network's icon override.
	 *
	 * @return Icon
	 */
	public function get_icon_override() {
		return $this->icon_override;
	}

	/**
	 * Get the Tooltip for this network
	 *
	 * @return string
	 */
	public function get_tooltip() {
		return $this->tooltip;
	}
}
