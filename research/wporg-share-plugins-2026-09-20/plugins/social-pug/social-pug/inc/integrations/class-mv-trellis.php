<?php
namespace Mediavine\Grow\Integrations;

use Mediavine\Grow\Frontend_Content;
use Mediavine\Grow\View_Loader;

/**
 * Class MV_Trellis
 *
 * @package Mediavine\Grow\Integrations
 */
class MV_Trellis extends Integration {

	/** @var string[] */
	public $locations = [
		'inline_content_frontend',
		'output_pinterest_content_markup',
		'output_sticky_bar_content_markup',
	];

	/** @var null */
	private static $instance = null;

	/** @var string[] $inline_content Holds output of inline content positions */
	private $inline_content = [];

	/** @var array $inline_content_settings Holds settings for inline content location */
	private $inline_content_settings = [];

	/**
	 *
	 *
	 * @return Container|MV_Trellis|\Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function init() {
		add_filter( 'mv_trellis_css_allowlist', [ $this, 'css_bypass' ] );
		if ( dpsp_is_location_active( 'share_content' ) ) {
			$this->inline_content          = [
				'top'    => '',
				'bottom' => '',
			];
			$this->inline_content_settings = Frontend_Content::get_prepared_settings();
		}

	}

	/**
	 *
	 *
	 * @return bool|mixed
	 */
	public function should_run() {
		return class_exists( 'Mediavine\Trellis\Init' );
	}

	/**
	 * Add filters to trellis hooks, this function called automatically by the integration container.
	 *
	 * Trellis has a new hook to output content in its own aside container before the content,
	 * so we use that if it's available.
	 */
	public function inline_content_frontend() {
		if ( function_exists( 'mvt_aside_before_entry_content' ) ) {
			add_filter( 'tha_aside_before_entry_content', [ $this, 'inline_content_share_top' ] );
			add_filter( 'tha_aside_after_entry_content', [ $this, 'inline_content_share_bottom' ] );
		} else {
			add_filter( 'tha_entry_before', [ $this, 'inline_content_share_top' ] );
			add_filter( 'tha_entry_after', [ $this, 'inline_content_share_bottom' ] );
		}
	}

	/**
	 * Echo the top inline content position if it is enabled.
	 *
	 * @return boolean
	 */
	public function inline_content_share_top() {
		echo wp_kses( $this->get_inline_content_position( 'top' ), View_Loader::get_allowed_tags() );
		return true;
	}

	/**
	 *  Echo the bottom inline content position if it is enabled.
	 *
	 * @return boolean
	 */
	public function inline_content_share_bottom() {
		echo wp_kses( $this->get_inline_content_position( 'bottom' ), View_Loader::get_allowed_tags() );
		return true;
	}

	/**
	 * Get the HTML output for a specific inline content position.
	 *
	 * @param string $position Which position to get, 'top' or 'bottom' are the only valid values
	 * @return string
	 */
	private function get_inline_content_position( $position ) {
		// Make sure we are in a valid post and we should be outputting these buttons
		if ( $this->should_inline_content_output() ) {
			return '';
		}
		// If this position is not set to output return an empty string
		if ( $position !== $this->inline_content_settings['display']['position'] && 'both' !== $this->inline_content_settings['display']['position'] ) {
			return '';
		}

		// Check to see if we already might have the output before calculating it again
		if ( ! isset( $this->inline_content[ $position ] ) || empty( $this->inline_content[ $position ] ) ) {
			$this->inline_content[ $position ] = View_Loader::get_view(
				'/inc/tools/share-inline-content/views/frontend.php', [
					'settings'        => $this->inline_content_settings,
					'wrapper_classes' => Frontend_Content::get_wrapper_classes( $this->inline_content_settings ),
					'position'        => $position,
				]
			);
		}

		return $this->inline_content[ $position ];

	}

	/**
	 * Whether or not the inline content should be output.
	 *
	 * @return bool
	 */
	private static function should_inline_content_output() {
		if ( ! dpsp_is_tool_active( 'share_content' ) ) {
			return false;
		}

		if ( ! dpsp_is_location_displayable( 'content' ) ) {
			return false;
		}

		// Get the post object
		$post_obj = dpsp_get_current_post();

		if ( ! $post_obj ) {
			return false;
		}

		global $post;

		if ( $post_obj->ID !== $post->ID ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 *
	 * @param array $bypass Current array of bypass regexes
	 * @return array
	 */
	public static function css_bypass( $bypass = [] ) {
		$bypass[] = '((\.|#)?dpsp-(pin-it|pop-up|button-style|network|floating|animation|column|hide|has-button|position|grow-check-icon|grow-saved)[a-z-0-9]*)';

		return $bypass;
	}
}
