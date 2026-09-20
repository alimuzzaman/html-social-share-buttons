<?php
namespace Mediavine\Grow\Tools;

use Mediavine\Grow\Custom_Color;
use \Mediavine\Grow\Settings;

class Inline_Content extends Tool {
	use Renderable;

	/**
	 * Inline_Content constructor. Set metadata and slug
	 */
	public function init() {
		$this->slug          = 'content';
		$this->api_slug      = 'content';
		$this->name          = __( 'Inline Content', 'social-pug' );
		$this->type          = 'share_tool';
		$this->settings_slug = 'dpsp_location_content';
		$this->img           = 'assets/dist/tool-content.png?' . DPSP_VERSION;
		$this->admin_page    = 'admin.php?page=dpsp-content';

               $this->settings_sanitization = array(
			'display' => array(
				'screen_size'              => 'number',
				'minimum_individual_count' => 'number',
				'minimum_count'            => 'number',
				'custom_color'             => 'color',
				'custom_hover_color'       => 'color',
				'message'                  => 'text',
			),
		);
		add_filter( 'dpsp_output_inline_style', [ $this, 'inline_styles' ] );
	}

	/**
	 * Checks if the current filter allows an additional share button render.
	 *
	 * @return boolean
	 */
	public function is_duplicate_render_allowed() {
		// There are times when we legit may want to render the share buttons twice.
		// WooCommerce short descriptions are one of those times. Apply a filter for others.
		$allowed_filters = [ 'woocommerce_short_description' ];

		/**
		 * Filter the list of filters to allow duplicate rendering of the share buttons on the next filter call
		 * @param array $allowed_filters List of filters to allow multiple share button renders
		 */
		$allowed_filters = apply_filters( 'dpsp_allowed_filters_for_inline_content_render', $allowed_filters );

		foreach ( $allowed_filters as $allowed_filter ) {
			if ( doing_action( $allowed_filter ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * The rendering action of this tool.
	 *
	 * @return string HTML output of tool
	 */
	public function render() {
		// @TODO Migrate functionality from global function to this class
		if ( $this->is_duplicate_render_allowed() ) {
			return '';
		}

		$this->has_rendered = true;

		// Use lambda functions so we pass the correct current hooks to front-end data
		global $wp_current_filter;
		add_filter(
			'mv_grow_frontend_data', function( $data ) use ( $wp_current_filter ) {
			$data['inlineContentHook'] = $wp_current_filter;

			return $data;
			}
		);

		return '';
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
				$whatsapp_style = '@media screen and ( min-width : ' . $mobile_screen_width . 'px ) { .dpsp-content-wrapper .dpsp-network-list-item.dpsp-network-list-item-whatsapp { display: none } }';
			}
			$styles .= '
				@media screen and ( max-width : ' . $mobile_screen_width . 'px ) {
					.dpsp-content-wrapper.dpsp-hide-on-mobile,
					.dpsp-share-text.dpsp-hide-on-mobile {
						display: none;
					}
					.dpsp-has-spacing .dpsp-networks-btns-wrapper li {
						margin:0 2% 10px 0;
					}
					.dpsp-network-btn.dpsp-has-label:not(.dpsp-has-count) {
						max-height: 40px;
						padding: 0;
						justify-content: center;
					}
					.dpsp-content-wrapper.dpsp-size-small .dpsp-network-btn.dpsp-has-label:not(.dpsp-has-count){
						max-height: 32px;
					}
					.dpsp-content-wrapper.dpsp-size-large .dpsp-network-btn.dpsp-has-label:not(.dpsp-has-count){
						max-height: 46px;
					}
				}
			';
			$styles .= $whatsapp_style;
			$styles .= Custom_Color::get_style( $this->slug );
		}

		return $styles;
	}
}
