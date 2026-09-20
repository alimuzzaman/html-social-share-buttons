<?php
namespace Mediavine\Grow\Tools;

use Mediavine\Grow\Settings;
use Mediavine\Grow\Custom_Color;
use Mediavine\Grow\Share_Counts;

class Floating_Sidebar extends Tool {
	use Renderable;

	/**
	 * Floating Sidebar constructor. Set metadata and slug
	 */
	public function init() {
		$this->slug          = 'sidebar';
		$this->api_slug      = 'sidebar';
		$this->name          = __( 'Floating Sidebar', 'social-pug' );
		$this->type          = 'share_tool';
		$this->settings_slug = 'dpsp_location_sidebar';
		$this->img           = 'assets/dist/tool-sidebar.png?' . DPSP_VERSION;
		$this->admin_page    = 'admin.php?page=dpsp-sidebar';

               $this->settings_sanitization = array(
			'display' => array(
				'scroll_distance'          => 'number',
				'stop_selector'            => 'string',
				'minimum_individual_count' => 'number',
				'minimum_count'            => 'number',
				'custom_color'             => 'color',
				'custom_hover_color'       => 'color',
				'screen_size'              => 'number',
			),
		);

		add_filter( 'dpsp_output_inline_style', [ $this, 'inline_styles' ] );
		add_filter( 'mv_grow_frontend_data', [ $this, 'frontend_data' ] );
	}

	/**
	 * The rendering action of this tool.
	 *
	 * @return string HTML output of tool
	 */
	public function render() {
		// @TODO Migrate functionality from global function to this class
		$this->has_rendered = true;
		return '';
	}

	/**
	 * Combine total count and network button output to form the buttons html output.
	 *
	 * @param array $settings settings for this location
	 * @return string HTML output for the buttons and social count
	 */
	public static function compose_buttons( $settings ) {
		$total_shares = ( $settings['show_total_count'] && isset( $settings['display']['show_count_total'] ) ? dpsp_get_output_total_share_count( 'sidebar' ) : '' );
		$buttons      = ( isset( $settings['networks'] ) ? dpsp_get_output_network_buttons( $settings, 'share', 'sidebar' ) : '' );
		return ( ! empty( $settings['display']['total_count_position'] ) && 'after' === $settings['display']['total_count_position'] ? $buttons . $total_shares : $total_shares . $buttons );
	}

	/**
	 * Get the settings with the minimum count and show total count already calculated.
	 *
	 * @return array
	 */
	public static function get_prepared_settings() {
		$settings                     = Settings::get_setting( 'dpsp_location_sidebar', [] );
		$settings['show_total_count'] = ! isset( $settings['display']['minimum_count'] ) || empty( $settings['display']['minimum_count'] ) || ( ! empty( $settings['display']['minimum_count'] ) && (int) $settings['display']['minimum_count'] < Share_Counts::post_total_share_counts() );
		return $settings;
	}

	/**
	 * Build a CSS class string based on settings.
	 *
	 * @param array $settings Tool settings
	 * @return string CSS Classes separated by space
	 */
	public static function get_wrapper_classes( $settings ) {
		// Classes for the wrapper
		$wrapper_classes   = [];
		$wrapper_classes[] = ( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . $settings['display']['shape'] : '' );
		$wrapper_classes[] = ( isset( $settings['display']['size'] ) ? 'dpsp-size-' . $settings['display']['size'] : 'dpsp-size-small' );
		$wrapper_classes[] = ( isset( $settings['display']['spacing'] ) ? 'dpsp-bottom-spacing' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_count'] ) ? 'dpsp-has-buttons-count' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_mobile'] ) ? 'dpsp-show-on-mobile' : 'dpsp-hide-on-mobile' );

		$wrapper_classes[] = ( isset( $settings['display']['position'] ) ? 'dpsp-position-' . $settings['display']['position'] : '' );
		// Button styles
		$wrapper_classes[] = ( isset( $settings['button_style'] ) ? 'dpsp-button-style-' . $settings['button_style'] : '' );

		// Set intro animation
		$wrapper_classes[] = ( ! empty( $settings['display']['intro_animation'] ) && '-1' !== $settings['display']['intro_animation'] ? 'dpsp-animation-' . esc_attr( $settings['display']['intro_animation'] ) : 'dpsp-no-animation' );

		return implode( ' ', $wrapper_classes );
	}

	/**
	 * Filter styles for inline style output, used for mobile breakpoints and custom color.
	 *
	 * @param string $styles Styles come in
	 * @return string Styles go out
	 */
	public function inline_styles( $styles ) {
		if ( $this->active ) {
			$mobile_screen_width = ( ! empty( $this->settings['display']['screen_size'] ) ? (int) $this->settings['display']['screen_size'] : $this->default_mobile_breakpoint );
			$plugin_settings     = Settings::get_setting( 'dpsp_settings', [] );
			$whatsapp_style      = '';
			if ( ! empty( $plugin_settings['whatsapp_display_only_mobile'] ) ) {
				$whatsapp_style = '@media screen and ( min-width : ' . $mobile_screen_width . 'px ) { #dpsp-floating-sidebar .dpsp-network-list-item.dpsp-network-list-item-whatsapp { display: none } }';
			}
			$styles .= '
			@media screen and ( max-width : ' . $mobile_screen_width . 'px ) {
				aside#dpsp-floating-sidebar.dpsp-hide-on-mobile.opened {
					display: none;
				}
			}
			';
			$styles .= $whatsapp_style;
			$styles .= Custom_Color::get_style( 'sidebar' );
		}

		return $styles;
	}

	/**
	 * Get the Stop Selector from the Floating Sidebar Settings to pass to front end data
	 * @param array $settings Settings Array for the Floating Sidebar
	 * @return bool|string
	 */
	public function get_stop_selector( $settings = [] ) {
		if ( isset( $settings['display'] ) && isset( $settings['display']['hide_at_stop_selector'] ) && 'yes' === $settings['display']['hide_at_stop_selector'] && ! empty( $settings['display']['stop_selector'] ) ) {
			return sanitize_text_field( ( $settings['display']['stop_selector'] ) );
		}
		return false;
	}

	/**
	 * Add Data specific to the Floating Sidebar to the front end output
	 *
	 * @param array $data
	 * @return array
	 */
	public function frontend_data( $data = [] ) {
		if ( $this->active ) {
			$floating_sidebar        = [
				'stopSelector' => apply_filters( 'mv_grow_floating_sidebar_stop_selector', $this->get_stop_selector( $this->settings ) ),
			];
			$data['floatingSidebar'] = $floating_sidebar;
		}
		return $data;
	}
}
