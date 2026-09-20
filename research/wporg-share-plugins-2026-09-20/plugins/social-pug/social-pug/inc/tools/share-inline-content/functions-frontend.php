<?php
namespace Mediavine\Grow;

use Mediavine\Grow\Tools\Renderable;
use Mediavine\Grow\Tools\Toolkit;
use Mediavine\Grow\Share_Counts;

/**
 * Handles the front end of the Inline Content tool
 *
 * To be merged with the main inline conten class
 */
class Frontend_Content {

	/** @var Frontend_Content|null $instance */
	private static $instance = null;

	/** @var array $markup  */
	private $markup = [];

	/** @var int  */
	private $markup_count_limit = 1;

	/** @var array  */
	private $hooks = [];

	private const OUTPUT_HOOK_PRIORITY = 30;

	/**
	 * Makes sure class is only instantiated once and runs init.
	 *
	 * @return Frontend_Content Instantiated class
	 */
	public static function get_instance() : Frontend_Content {
		if ( ! self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Hooks to be run on class instantiation.
	 *
	 * @return void
	 */
	public function init() {
		$toolkit        = Toolkit::get_instance();
		$inline_content = $toolkit->get( 'content' );
		// Only run if share content is active
		if ( ! ( $inline_content && $inline_content->is_active() ) ) {
			return;
		}

		if ( Integrations\Container::has_location( 'inline_content_frontend' ) ) {
			Integrations\Container::do_location( 'inline_content_frontend' );
		} else {
			add_action( 'loop_start', [ $this, 'pre_output_frontend_content' ] );
		}

		$settings = $inline_content->get_settings();

		if ( ! empty( $settings['post_type_display'] ) && in_array( 'product', $settings['post_type_display'] ) ) {
			add_filter( 'woocommerce_short_description', [ $this, 'generate_content_markup' ], 20 );
		}
		add_filter( 'mv_grow_frontend_data', [ $this, 'localize_icon_svg_data' ] );
		self::critical_inline_css();
	}

	/**
	 * Perform checks to determine if it's safe to hook to the_content.
	 * @return bool Whether or not the output has been added to the proper hooks.
	 */
	public function pre_output_frontend_content() : bool {
		if ( ! self::is_valid_location() ) {
			return false;
		}

		// Determine if the amount of inline content renders is 1 or 2
		$settings           = self::get_prepared_settings();
		$markup_count_limit = ! empty( $settings['display']['double_inline_content_markup'] ) ? 2 : 1;

		/**
		 * Filters the limit for the number of times Grow markup can be output on `the_content`
		 * @param array $markup_count_limit
		 */
		$this->markup_count_limit = apply_filters( 'mv_grow_markup_count_limit', $markup_count_limit );

		$this->add_frontend_content();
		return true;
	}

	/**
	 * Add frontend content by hooking to the appropriate filters.
	 */
	public function add_frontend_content() {
		$markup = $this->dpsp_prepare_front_end_content();
		if ( empty( $markup ) ) {
			return;
		}

		$this->markup = $markup;

		$this->hooks = [
			'tha_entry_content_before',
			'tha_entry_content_after',
			'genesis_before_entry_content',
			'genesis_after_entry_content',
		];

		/**
		 * Filters order of hooks to output inline content widget on singular posts.
		 *
		 * Developers hooking to this filter need to add both 'before' and 'after' versions
		 * of their hooks for full compatibility with Grow
		 *
		 * @param array $hooks Order of hooks to output inline content widget
		 */
		$this->hooks = apply_filters( 'mv_grow_inline_content_order_singular', $this->hooks );

		// treating this similar to a template hierarchy because we're checking for whether or not the other filters
		// have fired or are firing. We want the_content to be the absolute last in this hierarchy
		$this->hooks[] = 'the_content';

		// Loop through hooks
		foreach ( $this->hooks as $hook ) {
			add_filter( $hook, [ $this, 'output_frontend_content' ], self::OUTPUT_HOOK_PRIORITY );
		}
	}

	/**
	 * Output content.
	 *
	 * @param string $content Content passed through filter
	 * @return string|null
	 */
	public function output_frontend_content( string $content = '' ) : ?string {
		if ( empty( $this->markup ) ) {
			return $content;
		}

		if ( doing_action( 'before_feast_layout' ) || doing_action( 'genesis_header' ) || doing_action( 'genesis_after_header' ) || doing_action( 'genesis_footer' ) || doing_action( 'genesis_sidebar' ) || doing_action( 'kadence_before_sidebar' ) || doing_action( 'kadence_before_footer' ) || doing_action('kadence_dynamic_sidebar_content') || doing_action('kadence_after_header') ) {
			return $content;
		}

		$this->check_content_filters();

		if ( doing_filter( 'the_content' ) ) {
			return $this->generate_content_markup( $content );
		}

		if ( false !== strpos( current_filter(), 'before' ) && ! empty( $this->markup['top'] ) ) {
			echo wp_kses_post( $this->markup['top'] );
			return null;
		}

		if ( false !== strpos( current_filter(), 'after' ) && ! empty( $this->markup['bottom'] ) ) {
			echo wp_kses_post( $this->markup['bottom'] );
			return null;
		}

		return null;
	}

	/**
	 * Loop through hooks property and check if the hooks are or have been active, and if so, remove the_content filter.
	 */
	public function check_content_filters() {
		foreach ( $this->hooks as $hook ) {
			if ( ( 'the_content' !== $hook ) && ( doing_action( $hook ) || did_action( $hook ) ) ) {
				if ( has_filter( 'the_content', [ $this, 'output_frontend_content' ] ) ) {
					remove_filter( 'the_content', [ $this, 'output_frontend_content' ], self::OUTPUT_HOOK_PRIORITY );
				}
			}
		}
	}

	/**
	 * Checks if the current filter is blocked from displaying inline content share buttons.
	 *
	 * @return boolean
	 */
	public static function is_blocked_filter() : bool {
		global $wp_current_filter;
		// We need to filter out all instances where this callback functions is applied
		// due to the_content filter being used by other plugins
		if ( ! empty( $wp_current_filter ) && is_array( $wp_current_filter ) ) {

			$blocked_filters = [
				'wp_head',
				'p3_content_end',
			];

			/**
			 * Filter the list of filters to skip rendering inline share buttons to move to the next run filter
			 * @param array $blocked_filters List of filters to stop forward progression of share button render
			 */
			$blocked_filters = apply_filters( 'dpsp_output_the_content_callback', $blocked_filters );

			if ( is_array( $blocked_filters ) ) {
				foreach ( $wp_current_filter as $filter ) {
					if ( in_array( $filter, $blocked_filters, true ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Check that we are in a good location, in a valid post, and all data can be successfully retrieved.
	 *
	 * @return bool
	 */
	public static function is_valid_location() : bool {
		if ( self::is_blocked_filter() ) {
			return false;
		}

		if ( ! is_main_query() ) {
			return false;
		}

		if ( is_embed() ) {
			return false;
		}

		if ( ! dpsp_is_tool_active( 'share_content' ) ) {
			return false;
		}

		if ( ! dpsp_is_location_displayable( 'content' ) ) {
			return false;
		}

		if ( self::has_pagebuilder() ) {
			return false;
		}

		$tool_container = \Mediavine\Grow\Tools\Toolkit::get_instance();

		// Get the post object
		$post_obj = dpsp_get_current_post();

		if ( ! $post_obj ) {
			return false;
		}

		global $post;

		if ( $post_obj->ID !== $post->ID ) {
			return false;
		}

		/**
		 * Return the content if the output for this callback isn't permitted by filters.
		 * This filter has been added for edge cases.
		 * @param bool $should_return_content
		 */
		if ( false === apply_filters( 'dpsp_output_the_content_callback', true ) ) {
			return false;
		}

		/**
		 * @var Renderable $tool_instance
		 */
		$tool_instance = $tool_container->get( 'content' );
		if ( $tool_instance->has_rendered() ) {
			return false;
		}
		$tool_instance->render();

		return true;
	}

	/**
	 * Get the settings with the minimum count and show total count already calculated.
	 *
	 * @return array
	 */
	public static function get_prepared_settings() : array {
		$settings = dpsp_get_location_settings( 'content' );

		// Get minimum count or default to 0
		$settings['minimum_count'] = ( ! empty( $settings['display']['minimum_count'] ) ? (int) $settings['display']['minimum_count'] : 0 );

		// Check show total count setting and also check count against the minimum to determine if the total will actually be shown for this post.
		$settings['show_total_count'] = $settings['minimum_count'] <= (int) Share_Counts::post_total_share_counts() && ! empty( $settings['display']['show_count_total'] );

		return $settings;
	}

	/**
	 * Output the inline content buttons on the page.
	 *
	 * @return array Content with buttons added
	 */
	public function dpsp_prepare_front_end_content() : array {
		// Get saved settings
		$settings = self::get_prepared_settings();
		$output   = [];

		if ( 'top' === $settings['display']['position'] || 'both' === $settings['display']['position'] ) {
			$output['top'] = \Mediavine\Grow\View_Loader::get_view(
				'/inc/tools/share-inline-content/views/frontend.php', [
					'settings'        => $settings,
					'wrapper_classes' => self::get_wrapper_classes( $settings ),
					'position'        => 'top',
				]
			);
		}

		if ( 'bottom' === $settings['display']['position'] || 'both' === $settings['display']['position'] ) {
			$output['bottom'] = \Mediavine\Grow\View_Loader::get_view(
				'/inc/tools/share-inline-content/views/frontend.php', [
					'settings'        => $settings,
					'wrapper_classes' => self::get_wrapper_classes( $settings ),
					'position'        => 'bottom',
				]
			);
		}

		return $output;
	}

	/**
	 * Build a CSS class string based on settings.
	 *
	 * @param array $settings Tool settings
	 * @return string CSS Classes separated by space
	 */
	public static function get_wrapper_classes( array $settings = [] ) : string {
		$wrapper_classes   = [ 'dpsp-content-wrapper' ];
		$wrapper_classes[] = ( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . $settings['display']['shape'] : '' );
		$wrapper_classes[] = ( isset( $settings['display']['size'] ) ? 'dpsp-size-' . $settings['display']['size'] : 'dpsp-size-medium' );
		$wrapper_classes[] = ( isset( $settings['display']['spacing'] ) ? 'dpsp-has-spacing' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_labels'] ) || isset( $settings['display']['show_count'] ) ? '' : 'dpsp-no-labels' );
		$wrapper_classes[] = ( isset( $settings['display']['show_labels_mobile'] ) || isset( $settings['display']['show_count'] ) ? '' : 'dpsp-no-labels-mobile' );
		$wrapper_classes[] = ( isset( $settings['display']['show_count'] ) ? 'dpsp-has-buttons-count' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_mobile'] ) ? 'dpsp-show-on-mobile' : 'dpsp-hide-on-mobile' );

		$total_share_count = dpsp_get_output_total_share_count( 'content' );

		if ( ! empty( $total_share_count ) ) {
			$wrapper_classes[] = ( isset( $settings['show_total_count'] ) && $settings['show_total_count'] ? 'dpsp-show-total-share-count' : '' );
			$wrapper_classes[] = ( isset( $settings['show_total_count'] ) && $settings['show_total_count'] ? ( ! empty( $settings['display']['total_count_position'] ) ? 'dpsp-show-total-share-count-' . $settings['display']['total_count_position'] : 'dpsp-show-total-share-count-before' ) : '' );
		}

		// Button styles
		$wrapper_classes[] = ( isset( $settings['button_style'] ) ? 'dpsp-button-style-' . $settings['button_style'] : '' );

		return implode( ' ', array_filter( $wrapper_classes ) );
	}

	/**
	 * Combine the buttons and total share count to get the final buttons string.
	 *
	 * @param array $settings Tool settings
	 * @return string
	 */
	public static function compose_buttons( array $settings ) : string {
		$button_output = '';

		// Output total share counts
		if ( $settings['show_total_count'] ) {
			$button_output .= dpsp_get_output_total_share_count( 'content' );
		}

		// Gets the social network buttons
		if ( isset( $settings['networks'] ) ) {
			$button_output .= dpsp_get_output_network_buttons( $settings, 'share', 'content' );
		}

		return $button_output;
	}

	/**
	 * Add SVG Icon Path Data to the front end data object hook to make sure that icons can be recreated on the front end if SVG output is stripped.
	 *
	 * @param array $data Data coming in from the filter
	 * @return array Data to be output
	 */
	public function localize_icon_svg_data( array $data = [] ): array {
		$settings = dpsp_get_location_settings( 'content' );
		if ( ! isset( $settings['networks'] ) || empty( $settings['networks'] ) ) {
			return $data;
		}
		$svg_arr = $data['buttonSVG'] ?? [];
		if ( isset( $settings['networks']['grow'] ) ) {
			// TODO: Clean up grow.me icon variation better
			$settings['networks']['grow_override'] = [ 'label' => 'Save' ];
		}
		$data['buttonSVG'] = array_merge( $svg_arr, dpsp_get_svg_data_for_networks( $settings['networks'] ) );
		return $data;
	}

	/**
	 * Check if a page builder is active and on the page.
	 *
	 * @return bool
	 */
	public static function has_pagebuilder() : bool {
		if ( class_exists( 'ET_GB_Block_Layout' ) ) {
			return ( \ET_GB_Block_Layout::is_layout_block_preview() && is_main_query() );
		}

		// add means for other plugins to extend
		return apply_filters( 'mv_grow_has_pagebuilder_admin', false );
	}

	/**
	 * Generate the content markup that contains the Grow sharing buttons.
	 *
	 * Uses the markup count limit to determine if we are allowed to output again.
	 *
	 * @param string $content Post content string. Required.
	 * @return string
	 */
	public function generate_content_markup( string $content ) : string {
		if ( ! dpsp_is_location_displayable( 'content' ) ) {
			return $content;
		}
		// if $this->markup is empty, then this method is being called directly
		// or hooked to a 3rd party content filter, for example woocommerce_short_description
		if ( empty( $this->markup ) ) {
			$this->markup = $this->dpsp_prepare_front_end_content();
		}

		$top_content    = ! empty( $this->markup['top'] ) ? $this->markup['top'] : '';
		$bottom_content = ! empty( $this->markup['bottom'] ) ? $this->markup['bottom'] : '';
		$content        = $top_content . $content . $bottom_content;

		--$this->markup_count_limit;
		if ( 1 > $this->markup_count_limit ) {
			$this->markup = [];
		}

		return $content;
	}

	/**
	 * Generate critical inline css for the buttons to reduce layout shift when external css may be deferred
	 *
	 * @since 2.16.0
	 *
	 * @return bool
	 */
	public static function critical_inline_css() : bool {
		$settings             = self::get_prepared_settings();
		$size                 = empty( $settings ) || empty( $settings['display'] ) || empty( $settings['display']['size'] ) ? 'medium' : $settings['display']['size'];
		$height_map           = [
			'large'  => '46px',
			'medium' => '40px',
			'small'  => '32px',
		];
		$total_share_position = ( empty( $settings ) || empty( $settings['display'] ) || empty( $settings['display']['total_count_position'] ) || 'after' === $settings['display']['total_count_position'] ) ? 'right' : 'left';
		$critical_map         = [
			'single-button-link'      => [ 'font-size:14px', 'padding:0rem', 'max-height:' . $height_map[ $size ] ],
			'single-button-list-item' => [ 'float:left' ],
			'button-list-wrapper'     => [ 'padding:0', 'margin:0', 'list-style-type:none' ],
			'content-wrapper'         => [ 'min-height:' . $height_map[ $size ], 'position:relative' ],
			'content-share-text'      => [ 'margin-bottom:10px' ],
			'total-share-wrapper'     => [ 'position:absolute', $total_share_position . ':0' ],
		];

		add_filter(
			'mv_grow_critical_styles_content', function( $styles, $slug ) use ( $critical_map ) {
				if ( empty( $critical_map[ $slug ] ) ) {
					return $styles;
				}
				return array_merge( $styles, $critical_map[ $slug ] );
			}, 100, 2
		);

		return true;
	}
}
