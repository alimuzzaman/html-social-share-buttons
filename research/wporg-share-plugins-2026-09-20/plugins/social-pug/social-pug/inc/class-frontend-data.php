<?php
namespace Mediavine\Grow;

use Mediavine\Grow\Tools\Toolkit;
use Mediavine\Grow\Settings;
use Social_Pug;
use Mediavine\Grow\Share_Counts;
use WP_Post;
use WP_Term;

/**
 * Management for data exposed through the frontend page markup.
 */
class Frontend_Data extends Asset_Loader {

	/** @var Frontend_Data|null $instance */
	private static $instance = null;

	/**  @var array|null $data Data to be output to frontend scripts  */
	private $data = null;

	/**  @var array|null $data Data for admin screens to be output to frontend scripts  */
	private $admin_data = null;

	/**
	 *
	 *
	 * @return Asset_Loader|Frontend_Data|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 *  Set up data and add hook for output.
	 */
	public function init() {
		$this->data       = [];
		$this->admin_data = [];
		add_action( 'wp_footer', [ $this, 'output_data' ] );
		add_action( 'admin_footer', [ $this, 'output_admin_data' ] );
		add_filter( 'mv_grow_frontend_data', [ $this, 'general_data' ] );
		add_filter( 'mv_grow_frontend_data', [ $this, 'get_counts' ] );
		add_filter( 'mv_grow_frontend_data', [ $this, 'should_run' ] );
		add_filter( 'mv_grow_frontend_admin_data', [ $this, 'get_admin_svg' ] );
		add_filter( 'mv_grow_frontend_admin_data', [ $this, 'get_admin_is_free' ] );
		add_filter( 'mv_grow_frontend_admin_data', [ $this, 'get_admin_api_data' ] );
		add_filter( 'mv_grow_frontend_admin_data', [ $this, 'get_twitter_4ever' ] );
		add_filter( 'mv_grow_frontend_admin_data', [ $this, 'get_admin_is_development' ] );
	}

	/**
	 * Run hook to collect all data.
	 *
	 * @return array
	 */
	public function get_data() {
		$this->data = apply_filters( 'mv_grow_frontend_data', $this->data );
		return $this->data;
	}

	/**
	 * Run hook to collect all data for admin.
	 *
	 * @return array
	 */
	public function get_admin_data() {
		$this->admin_data = apply_filters( 'mv_grow_frontend_admin_data', $this->admin_data );
		return $this->admin_data;
	}

	/**
	 * Get share counts for post if they exist.
	 *
	 * @param $data array Data that will be output
	 * @return array Data to be output
	 */
	public function get_counts( $data = [] ) {
		$post = dpsp_get_current_post();
		if ( $post ) {
			$data['shareCounts'] = Share_Counts::post_share_counts( $post->ID );

			foreach ( $data['shareCounts'] as $social => $value ) {
				$data['shareCounts'][ $social ] = (int) $value;
			}
		}

		return $data;
	}

	/**
	 * Output data as data attribute on div.
	 */
	public function output_data() {
		if ( ( is_home() && is_front_page() ) || is_archive() || is_admin() ) {
			// If this is an archive or a non static front page, don't output data
			return;
		}

		$data = htmlspecialchars( json_encode( $this->get_data() ), ENT_QUOTES, 'UTF-8' );
		echo wp_kses( '<div id="mv-grow-data" data-settings=\'' . $data . '\'></div>', View_Loader::get_allowed_tags() );
	}

	/**
	 * Output data as data attribute on div for admin screens.
	 */
	public function output_admin_data() {
		$admin_data = htmlspecialchars( json_encode( $this->get_admin_data() ), ENT_QUOTES, 'UTF-8' );
		echo wp_kses( '<div id="mv-grow-admin-data" data-settings=\'' . $admin_data . '\'></div>', View_Loader::get_allowed_tags() );
	}

	/**
	 * Add some general data to the output.
	 *
	 * @param array $data Current frontend page data.
	 * @return array
	 */
	public function general_data( $data = [] ) {
		$trellis_integration = \Mediavine\Grow\Integrations\MV_Trellis::get_instance();
		$general             = [
			'contentSelector' => apply_filters( 'mv_grow_content_wrapper_selector', false ),
			'show_count'      => [],
			'isTrellis'       => $trellis_integration->should_run(),
		];
		$tools_with_counts   = [ 'content', 'pop_up', 'sidebar', 'sticky_bar' ];
		foreach ( Toolkit::get_instance()->get_all() as $tool_slug => $tool ) {
			if ( ! in_array( $tool_slug, $tools_with_counts, true ) ) {
				continue;
			}
			$tool_settings                       = $tool->get_settings();
			$show_count                          = $tool_settings['display']['show_count'] ?? null;
			$general['show_count'][ $tool_slug ] = filter_var( $show_count, FILTER_VALIDATE_BOOLEAN );
		}

		$general['license_last4'] = substr( get_option( 'mv_grow_license'), -4 );

		$data['general'] = $general;

		$data = $this->with_current_post( $data );

		return $data;
	}

	/**
	 * Determine if Grow should do anything based on if the page is singular or not.
	 *
	 * @param $data array Existing data that will be output to frontend
	 * @return array
	 */
	public function should_run( $data = [] ) {
		$data['shouldRun'] = is_singular();

		return $data;
	}

	/**
	 * Output Icon Data for all networks.
	 *
	 * @param $admin_data array Existing data that will be output to frontend
	 * @return array
	 */
	public function get_admin_svg( $admin_data = [] ) {
		$networks               = Networks::get_instance();
		$admin_data['iconData'] = dpsp_get_svg_data_for_networks( $networks->get_all() );
		return $admin_data;
	}

	/**
	 * Output Twitter 4ever preference (whether or not they'd prefer the Twitter branding over X brand)
	 *
	 * @param $admin_data array Existing data that will be output to frontend
	 * @return array
	 */
	public function get_twitter_4ever( $admin_data = [] ) {
		$settings = get_option('dpsp_settings');
		if ( ! empty( $settings['twitter_4ever'] ) ) :
			$admin_data['twitter_4ever'] = $settings['twitter_4ever'];
		endif;
		return $admin_data;
	}

	/**
	 * Output Whether or not the free version of the plugin is running
	 *
	 * @param $admin_data array Existing data that will be output to frontend
	 * @return array
	 */
	public function get_admin_is_free( $admin_data = [] ) {
		$admin_data['isFree'] = Social_Pug::is_free();
		return $admin_data;
	}

	/**
	 * Output Whether or not the plugin is running in development mode
	 *
	 * @param $admin_data array Existing data that will be output to frontend
	 * @return array
	 */
	public function get_admin_is_development( $admin_data = [] ) {
		$admin_data['isDevelopment'] = apply_filters( 'mv_grow_dev_mode', false );
		return $admin_data;
	}

	/**
	 * Output API Data for Admin
	 *
	 * @param $admin_data array Existing data that will be output to frontend
	 * @return array
	 */
	public function get_admin_api_data( $admin_data = [] ) {
		$admin_data['apiData'] = [
			'root'  => get_rest_url(),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		];
		return $admin_data;
	}

	/**
	 * If viewing a single post, add it to the data collection.
	 *
	 * @param array $data Current Grow data.
	 * @return array
	 */
	private function with_current_post( array $data ) : array {
		if ( 'post' !== get_post_type() || false === is_single() ) {
			$data['post'] = null;
			return $data;
		}

		$post = get_post();
		// Unless something truly heinous has occurred, this should always be a WP_Post.
		if ( ! ( $post instanceof WP_Post ) ) {
			// @codeCoverageIgnoreStart
			$data['post'] = null;
			return $data;
			// @codeCoverageIgnoreEnd
		}

		$data['post'] = [ 'ID' => $post->ID ];

		$categories = get_the_category();
		if ( ! is_array( $categories ) ) {
			$data['post']['categories'] = [];
			return $data;
		}

		$post_categories = [];
		foreach ( $categories as $category ) {
			if ( ! ( $category instanceof WP_Term ) ) {
				continue;
			}

			$post_categories[] = [ 'ID' => $category->term_id ];
		}
		$data['post']['categories'] = $post_categories;

		return $data;
	}
}
