<?php
namespace Mediavine\Grow\Tools;

use Mediavine\Grow\Custom_Color;

class Follow_Widget extends Tool {
	use Renderable;

	/**
	 * Follow Widget constructor. Set metadata and slug
	 */
	public function init() {
		$this->slug          = 'follow_widget';
		$this->api_slug      = 'follow';
		$this->name          = __( 'Follow Widget', 'social-pug' );
		$this->type          = 'follow_tool';
		$this->settings_slug = 'dpsp_location_follow_widget';
		$this->img           = 'assets/dist/tool-follow-widget.png?' . DPSP_VERSION ;
		$this->admin_page    = 'admin.php?page=dpsp-follow-widget';

               $this->settings_sanitization = array(
			'display' => array(
				'custom_color'             => 'color',
				'custom_hover_color'       => 'color',
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
			$styles .= Custom_Color::get_style( 'follow_widget' );
		}

		return $styles;
	}
}
