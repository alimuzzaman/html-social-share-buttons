<?php
namespace Mediavine\Grow\Tools;

use Mediavine\Grow\Custom_Color;
use Mediavine\Grow\Settings;

class Sticky_Bar extends Tool {
	use Renderable;

	/**
	 * Sticky_Bar constructor. Set metadata and slug.
	 */
	public function init() {
		$this->slug          = 'sticky_bar';
		$this->api_slug      = 'sticky-bar';
		$this->name          = __( 'Sticky Bar', 'social-pug' );
		$this->type          = 'share_tool';
		$this->settings_slug = 'dpsp_location_sticky_bar';
		$this->img           = 'assets/dist/tool-mobile.png?' . DPSP_VERSION;
		$this->admin_page    = 'admin.php?page=dpsp-sticky-bar';

               $this->settings_sanitization = array(
			'display' => array(
				'scroll_distance'          => 'number',
				'minimum_individual_count' => 'number',
				'minimum_count'            => 'number',
				'custom_color'             => 'color',
				'custom_hover_color'       => 'color',
				'custom_background_color'  => 'color',
				'screen_size'              => 'number',
			),
		);

		add_filter( 'dpsp_output_inline_style', [ $this, 'inline_styles' ] );
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
	 * Filter styles for inline style output, used for custom color.
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
				$whatsapp_style = '@media screen and ( min-width : ' . $mobile_screen_width . 'px ) { #dpsp-sticky-bar .dpsp-network-list-item.dpsp-network-list-item-whatsapp { display: none } }';
			}
			$styles .= $whatsapp_style;
			$styles .= Custom_Color::get_style( 'sticky_bar' );
		}

		return $styles;
	}
}
