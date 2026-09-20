<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: Simple Social Buttons
 * Plugin URI: https://simplesocialbuttons.com/?utm_source=simple-social-buttons-lite&utm_medium=plugin-url-link
 * Description: Simple Social Buttons adds an advanced set of social media sharing buttons to your WordPress sites,
 * such as: Facebook, Twitter, WhatsApp, Viber, Reddit, LinkedIn and Pinterest.
 * This makes it the most <code>Flexible Social Sharing Plugin ever for Everyone.</code>
 * Version: 7.1.0
 * Author: WPBrigade
 * Author URI: https://www.WPBrigade.com/?utm_source=simple-social-buttons-lite&utm_medium=author-url-link
 * Text Domain: simple-social-buttons
 * Domain Path: /lang
 * GitHub Plugin URI: https://github.com/WPBrigade/simple-social-buttons
 *
 * @package SimpleSocialButtons
 *
 * phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed
 */

/*
	Copyright 2011 - 2025, Muhammad Adnan (WPBrigade)  (email : support@wpbrigade.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ssb_wpb68931334' ) ) {
	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return mixed
	 */
	function ssb_wpb68931334() { // phpcs:ignore Squiz.Commenting.FunctionComment.WrongStyle
		global $ssb_wpb68931334;

		if ( ! isset( $ssb_wpb68931334 ) || ! is_array( $ssb_wpb68931334 ) ) {
			require_once __DIR__ . '/lib/wpb-sdk/start.php';

			/**
			 * Initialize WPB SDK.
			 *
			 * @phpstan-ignore-next-line
			 */
			$ssb_wpb68931334 = wpb_sdk_dynamic_init(
				array(
					'id'              => '1',
					'slug'            => 'simple-social-buttons',
					'type'            => 'plugin',
					'plugin_file'     => __FILE__,
					'sdk_views_dir'   => __DIR__ . '/lib/wpb-sdk/views',
					'public_key'      => '9|r3YzQhJPlhJS3qfobd92Sogkc6i6OdH13H8opb0Rd93c3f5a',
					'secret_key'      => 'sk_b36c525848fee035',
					'is_premium'      => false,
					'has_addons'      => false,
					'has_paid_plans'  => false,
					'optin_user_meta' => array(
						'token'          => '_ssb_verification_token',
						'email_verified' => '_ssb_email_verified',
					),
					'optin'           => array(
						'option_name'      => '_ssb_optin',
						'settings_page'    => 'simple-social-buttons',
						'optin_page'       => 'ssb-optin',
						'verify_query_arg' => 'simple-social-buttons_optin_verify',
						'ajax_prefix'      => 'ssb',
						'product_name'     => 'Simple Social Buttons',
						'logo_path'        => 'assets/images/ssb-logo.svg',
					),
					'telemetry'       => array(
						'optout_submit_key' => 'ssb-submit-optout',
					),
					'menu'            => array(
						'slug'    => 'simple-social-buttons',
						'account' => false,
						'support' => false,
					),
					'settings'        => array(
						'ssb_networks'                  => '{\"icon_selection\":\"fbshare,twitter,linkedin,fblike\"}',
						'ssb_themes'                    => '{\"icon_style\":\"simple-icons\"}',
						'ssb_positions'                 => '{\"position\":{\"inline\":\"inline\"}}',
						'ssb_inline'                    => '{\"location\":\"below\",\"posts\":{\"post\":\"post\"}}',
						'ssb_advanced'                  => '{\"ssb_og_tags\":\"1\"}',
						'ssb_pr_version'                => '5.2.0',
						'widget_ssb_widget'             => '{\"_multiwidget\":1}',
						'ssb_sidebar'                   => '',
						'ssb_media'                     => '',
						'ssb_popup'                     => '',
						'ssb_flyin'                     => '',
						'ssb_active_time'               => '1722672369',
						'_ssb_optin'                    => '',
						'wpb_sdk_simple-social-buttons' => '',
						'wpb_sdk_simple-social-buttons_fallback_verify_token' => '',
						'wpb_sdk_simple-social-buttons_initial_log_sent' => '',
					),
				)
			);
		}

		return $ssb_wpb68931334;
	}

	ssb_wpb68931334();
	do_action( 'ssb_wpb68931334_loaded' );
}

/**
 * Main plugin class.
 *
 * @package SimpleSocialButtons
 */
class SimpleSocialButtonsPR { // phpcs:ignore

	/**
	 * Plugin name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $plugin_name = 'Simple Social Buttons';

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $plugin_version = '7.1.0';

	/**
	 * Plugin Prefix
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $plugin_prefix = 'ssb_pr_';

	/**
	 * SSB hide on single plage setting key.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $hide_custom_meta_key = '_ssb_hide';

	/**
	 * Backward compatibility for old property name.
	 *
	 * @deprecated 7.0.0 Use $hide_custom_meta_key instead.
	 * @param string $name Property name being accessed.
	 * @return mixed Property value or null if not found.
	 */
	public function __get( $name ) {
		if ( 'hideCustomMetaKey' === $name ) {
			return $this->hide_custom_meta_key;
		}
		return null;
	}

	/**
	 * Get and normalize the ssb_networks option.
	 *
	 * Ensures the value is an array with at least icon_selection and custom_buttons,
	 * so callers can rely on structure. Future changes (e.g. migration, caching) can live here.
	 *
	 * @return array{icon_selection: string, custom_buttons: array}
	 * @since 7.0.0
	 */
	public static function ssb_get_networks_option() {
		$raw = get_option( 'ssb_networks', array() );
		if ( ! is_array( $raw ) ) {
			$raw = array();
		}
		if ( ! isset( $raw['custom_buttons'] ) || ! is_array( $raw['custom_buttons'] ) ) {
			$raw['custom_buttons'] = array();
		}
		if ( ! array_key_exists( 'icon_selection', $raw ) || ! is_string( $raw['icon_selection'] ) ) {
			$raw['icon_selection'] = isset( $raw['icon_selection'] ) ? (string) $raw['icon_selection'] : '';
		}
		return $raw;
	}

	/**
	 * Allocate the next custom button ID and advance the counter in $networks.
	 *
	 * Uses next_custom_id in ssb_networks for O(1) allocation; falls back to scanning
	 * custom_buttons keys only when the counter is missing (e.g. upgraded installs).
	 *
	 * @param array<string, mixed> $networks Current ssb_networks array (passed by reference; next_custom_id is updated).
	 * @return string The new ID (e.g. 'custom_1', 'custom_2').
	 * @since 7.0.0
	 */
	public static function ssb_next_custom_button_id( array &$networks ) {
		$custom = isset( $networks['custom_buttons'] ) && is_array( $networks['custom_buttons'] ) ? $networks['custom_buttons'] : array();
		$next   = isset( $networks['next_custom_id'] ) && is_numeric( $networks['next_custom_id'] ) ? (int) $networks['next_custom_id'] : null;
		if ( null === $next || $next < 1 ) {
			$max = 0;
			foreach ( array_keys( $custom ) as $cid ) {
				if ( preg_match( '/^custom_(\d+)$/', $cid, $m ) ) {
					$max = max( $max, (int) $m[1] );
				}
			}
			$next = $max + 1;
		}
		$networks['next_custom_id'] = $next + 1;
		return 'custom_' . (string) $next;
	}

	/**
	 * Update the ssb_networks option with normalized structure.
	 *
	 * Ensures custom_buttons is an array before saving. Future changes (e.g. migration, caching) can live here.
	 *
	 * @param array $networks Networks data (icon_selection, custom_buttons, etc.).
	 * @return bool True if the option was updated, false otherwise.
	 * @since 7.0.0
	 */
	public static function ssb_update_networks_option( $networks ) {
		if ( ! is_array( $networks ) ) {
			return false;
		}
		if ( ! isset( $networks['custom_buttons'] ) || ! is_array( $networks['custom_buttons'] ) ) {
			$networks['custom_buttons'] = array();
		}
		return update_option( 'ssb_networks', $networks );
	}

	/**
	 * Facebook api key for graph api.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $fb_app_id = '891268654262273';

	/**
	 * Plugin default setting.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $plugin_default_settings = array(
		'twitter'       => '3',
		'pinterest'     => '0',
		'beforepost'    => '1',
		'afterpost'     => '0',
		'beforepage'    => '1',
		'afterpage'     => '0',
		'beforearchive' => '0',
		'afterarchive'  => '0',
		'fbshare'       => '0',
		'linkedin'      => '0',
		'cache'         => 'on',
	);

	/**
	 * SSB all networks. Populated from ssb_get_known_buttons() in the constructor.
	 *
	 * @since 1.0.0
	 * @version 7.0.0
	 * @var array
	 */
	public $arr_known_buttons = array();

	/**
	 * Array to store current settings, to avoid passing them between functions.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $settings = array();

	/**
	 * User selected networks.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $selected_networks = array();

	/**
	 * User selected themes.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $selected_theme = '';

	/**
	 * User selected position to show icons.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $selected_position = '';

	/**
	 * Inline position user settings.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $inline_option = '';

	/**
	 * Sidebar position setting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $sidebar_option = '';

	/**
	 * Sidebar position setting.
	 *
	 * @since 6.1.0
	 *
	 * @var string
	 */
	public $media_option = '';

	/**
	 * Sidebar position setting.
	 *
	 * @since 6.1.0
	 *
	 * @var string
	 */
	public $popup_option = '';

	/**
	 * Sidebar position setting.
	 *
	 * @since 6.1.0
	 *
	 * @var string
	 */
	public $flyin_option = '';

	/**
	 * Advance settings.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $extra_option = '';

	/**
	 * Snapchat / Creative Kit options.
	 *
	 * @since 7.0.0
	 * @var array|string
	 */
	public $snapchat_option = '';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @version 7.0.0
	 */
	public function __construct() {

		$this->constants();
		include_once SSB_PLUGIN_DIR . '/inc/ssb-upgrade-routine.php';

		register_activation_hook( __FILE__, array( $this, 'ssb_plugin_install' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'ssb_plugin_deactivate' ) );

		$this->includes();
		$this->arr_known_buttons = ssb_get_known_buttons();
		$this->ssb_set_selected_networks();
		$this->ssb_set_selected_theme();
		$this->ssb_set_selected_position();
		$this->ssb_set_inline_option();
		$this->ssb_set_sidebar_option();
		$this->ssb_set_extra_option();
		$this->ssb_set_snapchat_option();
		$this->media_option = get_option( 'ssb_media' );
		$this->popup_option = get_option( 'ssb_popup' );
		$this->flyin_option = get_option( 'ssb_flyin' );

		add_action( 'admin_init', array( $this, 'ssb_factory_reset_settings_on_update' ) );

		$content_filter_priority = apply_filters( 'ssb_the_content_priority', 12 );
		$excerpt_filter_priority = apply_filters( 'ssb_the_excerpt_priority', 12 );
		/**
		 * Filter hooks.
		 */
		add_filter( 'the_content', array( $this, 'ssb_insert_buttons' ), $content_filter_priority );

		add_filter( 'the_excerpt', array( $this, 'ssb_insert_excerpt_buttons' ), $excerpt_filter_priority );

		add_filter( 'wp_trim_words', array( $this, 'ssb_on_excerpt_content' ), 11, 4 );

		add_action( 'wp_enqueue_scripts', array( $this, 'ssb_front_enqueue_scripts' ) );
		add_action( 'enqueue_block_assets', array( $this, 'ssb_front_block_scripts' ) );

		// Queue up our hook function.
		add_action( 'wp_footer', array( $this, 'ssb_footer_functions' ), 99 );

		add_filter( 'ssb_footer_scripts', array( $this, 'ssb_output_cache_trigger' ) );

		add_action( 'wp_ajax_ssb_fetch_data', array( $this, 'ssb_ajax_fetch_fresh_data' ) );
		add_action( 'wp_ajax_nopriv_ssb_fetch_data', array( $this, 'ssb_ajax_fetch_fresh_data' ) );
		add_action( 'wp_ajax_ssb_track_share_click', array( $this, 'ssb_ajax_track_share_click' ) );
		add_action( 'wp_ajax_nopriv_ssb_track_share_click', array( $this, 'ssb_ajax_track_share_click' ) );

		add_action( 'wp_footer', array( $this, 'ssb_include_sidebar' ) );
		add_action( 'wp_head', array( $this, 'css_file' ) );

		add_action( 'admin_init', array( $this, 'ssb_review_update_notice' ) );
		foreach ( $this->ssb_get_shortcode_tags() as $shortcode_tag ) {
			add_shortcode( $shortcode_tag, array( $this, 'ssb_short_code_content' ) );
		}
		add_action( 'wp_head', array( $this, 'ssb_add_meta_tags' ) );

		add_filter( 'cron_schedules', array( $this, 'ssb_register_internal_share_cron_schedule' ) ); // phpcs:ignore WordPress.WP.CronInterval
		add_action( 'init', array( $this, 'ssb_schedule_internal_share_queue_flush' ) );
		add_action( 'ssb_flush_internal_share_queue', array( $this, 'ssb_flush_internal_share_queue' ) );
		add_action( 'ssb_purge_all_api_batch', array( $this, 'ssb_purge_all_api_batch_event' ) );

		add_action( 'init', array( $this, 'ssb_register_block' ) );

		add_action( 'wp_wpb_sdk_after_uninstall', array( $this, 'ssb_plugin_uninstall' ) );

		add_action( 'admin_menu', array( $this, 'ssb_register_options_page' ) );
		add_action( 'admin_init', array( $this, 'ssb_redirect_optin' ) );
		add_filter( 'wpb_sdk_plugin_action_links', array( $this, 'ssb_filter_plugin_action_links' ), 10, 3 );
	}

	/**
	 * Add new page in Appearance to customize Opt In Page.
	 */
	public function ssb_register_options_page() {

		add_submenu_page(
			'SSB',
			__( 'Activate', 'simple-social-buttons' ),
			__( 'Activate', 'simple-social-buttons' ),
			'manage_options',
			'ssb-optin',
			array( $this, 'ssb_render_optin' )
		);
	}

	/**
	 * Show Opt-in Page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ssb_render_optin() {
		if ( function_exists( 'wpb_sdk_render_optin_form' ) ) {
			wpb_sdk_render_optin_form( 'simple-social-buttons' );
		}
	}


	/**
	 * Redirect to opt-in page if needed.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ssb_redirect_optin() {

		/**
		 * Fix the Broken Access Control (BAC) security fix.
		 *
		 * @since 1.6.3
		 */
		if ( current_user_can( 'manage_options' ) ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! get_option( '_ssb_optin' ) && isset( $_GET['page'] ) ) {
				$page = sanitize_text_field( wp_unslash( (string) $_GET['page'] ) );
				if ( 'simple-social-buttons' === $page || 'ssb' === $page || 'abw' === $page ) {
					/**
					 * XSS Attack vector found and fixed.
					 *
					 * @since 1.5.11
					 */
					$page_redirect = 'ssb' === $page ? 'ssb' : 'simple-social-buttons';
					wp_safe_redirect( admin_url( 'admin.php?page=ssb-optin&redirect-page=' . rawurlencode( $page_redirect ) ) );
					exit;
				}
			} elseif ( get_option( '_ssb_optin' ) && ( 'yes' === get_option( '_ssb_optin' ) ) && isset( $_GET['page'] ) ) {
				// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only `page` admin query arg for redirect routing.
				$page = sanitize_text_field( wp_unslash( (string) $_GET['page'] ) );
				// phpcs:enable WordPress.Security.NonceVerification.Recommended
				if ( 'ssb-optin' === $page ) {
					wp_safe_redirect( admin_url( 'admin.php?page=simple-social-buttons' ) );
					exit;
				}
			}
		}
	}

	/**
	 * Product-specific plugin row links (Settings, Upgrade Pro). Opt In/Out stays in the SDK.
	 *
	 * @since 2.1.4
	 *
	 * @param array<int, string> $links Plugin row action links.
	 * @param string             $slug  Product slug.
	 * @return array<int, string>
	 */
	public function ssb_filter_plugin_action_links( $links, $slug = '' ) {
		if ( '' !== $slug && 'simple-social-buttons' !== $slug ) {
			return $links;
		}

		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=simple-social-buttons' ) ),
			esc_html__( 'Settings', 'simple-social-buttons' )
		);
		array_unshift( $links, $settings_link );

		if ( ! class_exists( 'Simple_Social_Buttons_Pro' ) ) {
			$pro_url   = 'https://wpbrigade.com/wordpress/plugins/simple-social-buttons/';
			$pro_open  = '<a href="' . esc_url( $pro_url ) . '" target="_blank" rel="noopener noreferrer"';
			$pro_open .= ' style="color:#3db634;font-weight:600;">';
			$pro_link  = sprintf(
				/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag, %3$s: opening span tag, %4$s: closing span tag. */
				esc_html__( '%1$s %3$s Upgrade Pro %4$s %2$s', 'simple-social-buttons' ),
				$pro_open,
				'</a>',
				'<span class="simple-social-buttons-pro-link">',
				'</span>'
			);
			$links[] = $pro_link;
		}

		return $links;
	}

	/**
	 * Setter function to set user selected networks.
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 7.0.0
	 * @return void
	 */
	public function ssb_set_selected_networks() {
		$networks = self::ssb_get_networks_option();
		$icons    = array();
		if ( ! empty( $networks['icon_selection'] ) && is_string( $networks['icon_selection'] ) ) {
			$icons = array_map( 'trim', explode( ',', $networks['icon_selection'] ) );
			$icons = array_values( array_filter( $icons ) );
			$icons = array_values(
				array_filter(
					$icons,
					function ( $id ) {
						return 'fblike' !== $id;
					}
				)
			);
			$icons = ssb_filter_custom_button_ids_from_order( $icons );
		}
		$this->selected_networks = ! empty( $icons ) ? array_flip( array_merge( array( 0 ), $icons ) ) : array();
	}

	/**
	 * Set user selected theme.
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 4.0.4
	 * @return void
	 */
	public function ssb_set_selected_theme() {
		$theme                = get_option( 'ssb_themes' );
		$this->selected_theme = is_array( $theme ) && ! empty( $theme ) ? $theme['icon_style'] : '';
	}

	/**
	 * Set network position to show
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 4.0.4
	 * @return void
	 */
	public function ssb_set_selected_position() {
		$theme                   = get_option( 'ssb_positions' );
		$this->selected_position = is_array( $theme ) && ! empty( $theme ) ? $theme['position'] : '';
	}

	/**
	 * Set inline position setting.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function ssb_set_inline_option() {
		$this->inline_option = get_option( 'ssb_inline' );
	}

	/**
	 * Set sidebar position settings.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function ssb_set_sidebar_option() {
		$this->sidebar_option = get_option( 'ssb_sidebar' );
	}


	/**
	 * Set  advance settings.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function ssb_set_extra_option() {
		$this->extra_option = get_option( 'ssb_advanced' );
	}

	/**
	 * Set Snapchat / Creative Kit settings.
	 *
	 * @access public
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_set_snapchat_option() {
		$option                = get_option( 'ssb_snapchat', array() );
		$this->snapchat_option = is_array( $option ) ? $option : array();
	}

	/**
	 * Whether Snapchat is enabled in the active network list.
	 *
	 * @return bool
	 * @since 7.0.0
	 */
	private function ssb_has_snapchat_network() {
		return isset( $this->selected_networks['snapchat'] ) && $this->selected_networks['snapchat'] > 0;
	}

	/**
	 * Enqueue Snap Creative Kit Web SDK for in-app sharing on mobile.
	 *
	 * @return void
	 * @since 7.0.0
	 * @version 7.0.1
	 */
	private function ssb_enqueue_snapchat_creative_kit_sdk() {
		if ( ! $this->ssb_has_snapchat_network() ) {
			return;
		}

		if ( ssb_is_amp_request() ) {
			return;
		}

		wp_register_script(
			'snapkit-creative-kit-sdk',
			'https://sdk.snapkit.com/js/v1/create.js',
			array(),
			SSB_VERSION,
			true
		);
		wp_enqueue_script( 'snapkit-creative-kit-sdk' );

		$init = 'window.snapKitInit=function(){'
			. 'if(window.snap&&window.snap.creativekit&&window.snap.creativekit.initalizeShareButtons){'
			. 'window.snap.creativekit.initalizeShareButtons('
			. 'document.getElementsByClassName("snapchat-share-button"));}};';
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		wp_add_inline_script( 'snapkit-creative-kit-sdk', $init, 'before' );
	}

	/**
	 * Ajax callback function get fresh count.
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 7.0.1
	 * @return void
	 */
	public function ssb_ajax_fetch_fresh_data() {

		if ( ! check_ajax_referer( 'ssb_security', 'security', false ) ) {
			wp_send_json_error( 'Invalid security token.' );
			wp_die();
		}

		$post_id = isset( $_POST['postID'] ) ? (int) $_POST['postID'] : 0;

		// Throttle abusive/anonymous polling; fall back to cached counts instead of a live fetch.
		if ( ssb_is_rate_limited( 'ssb_fetch_limit', apply_filters( 'ssb_fetch_fresh_rate_limit_window', 30 ), (string) $post_id ) ) {
			if ( $post_id > 0 ) {
				echo wp_json_encode( $this->ssb_format_share_counts_for_json_response( ssb_fetch_cached_counts( $this->arr_known_buttons, $post_id ) ) );
			} else {
				wp_send_json_error( 'Rate limited. Please try again shortly.' );
			}
			wp_die();
		}

		// ssb_is_cache_fresh() itself allows a rebuild when this exact nonce-verified request asks for it.
		if ( $post_id > 0 && ssb_is_cache_fresh( $post_id ) ) {
			echo wp_json_encode( $this->ssb_format_share_counts_for_json_response( ssb_fetch_cached_counts( $this->arr_known_buttons, $post_id ) ) );
			wp_die();
		}

		$order = array();
		foreach ( $this->arr_known_buttons as $button_name ) {

			if ( isset( $this->selected_networks[ $button_name ] ) && $this->selected_networks[ $button_name ] > 0 ) {
				$order[ $button_name ] = $this->selected_networks[ $button_name ];
			}
		}

		$share_counts = $this->ssb_get_fresh_share_counts_for_networks( $post_id, get_permalink( $post_id ), $order );

		if ( $post_id > 0 && isset( $_POST['ssb_cache'] ) && 'rebuild' === sanitize_text_field( wp_unslash( $_POST['ssb_cache'] ) ) ) { // phpcs:ignore
			ssb_flush_internal_share_queue_for_post( $post_id );
		}

		$share_counts = ssb_merge_api_and_internal_share_counts( $share_counts, $post_id, $order );

		update_post_meta( $post_id, 'ssb_cache_timestamp', floor( ( ( gmdate( 'U' ) / 60 ) / 60 ) ) );

		echo wp_json_encode( $this->ssb_format_share_counts_for_json_response( $share_counts ) );
		wp_die();
	}

	/**
	 * Format share counts for the fetch-fresh-data JSON response (pretty-printed + raw values).
	 *
	 * @param array $share_counts Network => count map.
	 * @return array
	 * @since 7.0.1
	 */
	private function ssb_format_share_counts_for_json_response( $share_counts ) {
		$raw_share_counts = $share_counts;
		foreach ( $share_counts as $key => $value ) {
			$share_counts[ $key ] = ssb_count_format( $value );
		}
		$share_counts['raw'] = $raw_share_counts;
		return $share_counts;
	}

	/**
	 * Build fresh share counts for selected networks and URL.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $permalink Permalink URL.
	 * @param array  $networks Ordered networks.
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_fresh_share_counts_for_networks( $post_id, $permalink, $networks ) {
		$share_links = array();
		foreach ( $networks as $social_name => $priority ) {
			if ( preg_match( '/^custom_\d+$/', $social_name ) ) {
				continue;
			}
			if ( ! ssb_is_network_has_counts( $social_name ) ) {
				continue;
			}
			$callback = 'ssb_' . $social_name . '_generate_link';
			if ( ! is_callable( $callback ) ) {
				continue;
			}
			$share_links[ $social_name ] = call_user_func( $callback, $permalink );
		}

		$alt_share_links = $this->http_or_https_link_generate( $permalink );
		$result          = ssb_fetch_shares_via_http_api( array_filter( $share_links ) );

		return ssb_fetch_fresh_counts( $result, $post_id, $alt_share_links );
	}

	/**
	 * Refetch API share counts for a post (admin purge / cron).
	 *
	 * @param int        $post_id  Post ID.
	 * @param array|null $networks Optional network order map; defaults to selected networks.
	 * @return array|false Share counts array or false when permalink missing.
	 * @since 7.0.0
	 */
	public function ssb_refetch_api_share_counts_for_post( $post_id, $networks = null ) {
		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return false;
		}

		$permalink = get_permalink( $post_id );
		if ( ! $permalink ) {
			return false;
		}

		if ( ! is_array( $networks ) || empty( $networks ) ) {
			$networks = array();
			foreach ( $this->arr_known_buttons as $button_name ) {
				if ( isset( $this->selected_networks[ $button_name ] ) && $this->selected_networks[ $button_name ] > 0 ) {
					$networks[ $button_name ] = $this->selected_networks[ $button_name ];
				}
			}
		}

		return $this->ssb_get_fresh_share_counts_for_networks( $post_id, $permalink, $networks );
	}

	/**
	 * Front end count refresh ajax callback.
	 *
	 * @access public
	 * @param array $info information about post/page.
	 * @since 6.2.0
	 * @version 7.0.1
	 * @return array
	 */
	public function ssb_output_cache_trigger( $info ) {
		if ( ssb_is_amp_request() ) {
			return $info;
		}

		// Only an admin may force a rebuild via the URL; anonymous visitors always get the cached copy.
		$ssb_cache_rebuild = ! empty( $_GET['ssb_cache'] ) && current_user_can( 'manage_options' ); // phpcs:ignore

		// Return early if we're not on a single page or we have fresh cache.
		if ( ssb_is_cache_fresh( $info['postID'] ) && ! $ssb_cache_rebuild ) {
			return $info;
		}

		// Return if we're on a WooCommerce account page.
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return $info;
		}
		ob_start();

		?>

		document.addEventListener("DOMContentLoaded", function() {
			var if_ssb_exist = document.getElementsByClassName( "simplesocialbuttons" ).length > 0;
			if (if_ssb_exist) {	
				var ssb_admin_ajax = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
				var ssb_post_id = <?php echo esc_js( $info['postID'] ); ?> ;
				jQuery( document ).ready(function(){
				var is_ssb_used = jQuery('.simplesocialbuttons');
				if( is_ssb_used ) {

					var data = {
					'action'    : 'ssb_fetch_data',
					'postID'    :  ssb_post_id,
					'security'  : '<?php echo esc_js( wp_create_nonce( 'ssb_security' ) ); ?>',
					'ssb_cache' : 'rebuild'
					};
					jQuery.post(ssb_admin_ajax, data, function(data, textStatus, xhr) {
						var array = JSON.parse(data);

						jQuery.each( array, function( index, value ){

							if( index == 'total' ){
								jQuery('.ssb_'+ index +'_counter').html(value + '<span>Shares</span>');
							}else{
								jQuery('.ssb_'+ index +'_counter').html(value);
							}
						});

					});
				}
				})
			}
		});

		<?php
		$info['footer_output'] .= ob_get_clean();

		return $info;
	}

	/**
	 * AJAX endpoint to track internal share clicks.
	 *
	 * @since 7.0.0
	 * @version 7.0.1
	 * @return void
	 */
	public function ssb_ajax_track_share_click() {
		if ( ! check_ajax_referer( 'ssb_track_share_click', 'security', false ) ) {
			wp_send_json_error( array( 'message' => 'Invalid security token.' ) );
			wp_die();
		}

		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$network = isset( $_POST['network'] ) ? sanitize_key( wp_unslash( $_POST['network'] ) ) : '';

		if ( $post_id <= 0 || empty( $network ) ) {
			wp_send_json_error( array( 'message' => 'Invalid payload.' ) );
			wp_die();
		}

		$trackable = ssb_get_internal_share_trackable_networks();
		if ( ! in_array( $network, $trackable, true ) ) {
			wp_send_json_error( array( 'message' => 'Network not trackable.' ) );
			wp_die();
		}

		$window_seconds = (int) apply_filters( 'ssb_internal_share_rate_limit_window', 60, $post_id, $network );

		if ( ssb_is_rate_limited( 'ssb_click_limit', $window_seconds, $post_id . '|' . $network ) ) {
			wp_send_json_success(
				array(
					'queued'       => false,
					'rate_limited' => true,
				)
			);
			wp_die();
		}

		ssb_increment_internal_share_queue( $post_id, $network, 1 );

		wp_send_json_success( array( 'queued' => true ) );
		wp_die();
	}

	/**
	 * Register internal share queue cron schedule.
	 *
	 * @param array $schedules Existing schedules.
	 * @return array
	 * @since 7.0.0
	 */
	public function ssb_register_internal_share_cron_schedule( $schedules ) {
		$interval_hours                        = $this->ssb_get_internal_flush_interval_hours();
		$schedules['ssb_internal_share_flush'] = array(
			'interval' => $interval_hours * HOUR_IN_SECONDS,
			'display'  => sprintf(
				/* translators: %d: interval in hours. */
				__( 'SSB Internal Share Flush (every %d hours)', 'simple-social-buttons' ),
				$interval_hours
			),
		);

		return $schedules;
	}

	/**
	 * Ensure cron event is scheduled.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_schedule_internal_share_queue_flush() {
		if ( ! wp_next_scheduled( 'ssb_flush_internal_share_queue' ) ) {
			wp_schedule_event( time() + 5 * MINUTE_IN_SECONDS, 'ssb_internal_share_flush', 'ssb_flush_internal_share_queue' );
		}
	}

	/**
	 * Flush queued internal share counts into post meta history.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_flush_internal_share_queue() {
		$queue = ssb_pop_internal_share_queue();
		if ( empty( $queue ) || ! is_array( $queue ) ) {
			return;
		}

		foreach ( $queue as $post_id => $network_counts ) {
			$post_id = (int) $post_id;
			if ( $post_id <= 0 || ! is_array( $network_counts ) || empty( $network_counts ) ) {
				continue;
			}
			ssb_merge_internal_share_history( $post_id, $network_counts );
		}
	}

	/**
	 * Cron callback for site-wide API share count purge batches.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_purge_all_api_batch_event() {
		if ( ! class_exists( 'Ssb_Admin_Bar_Purge' ) ) {
			include_once SSB_PLUGIN_DIR . '/classes/class-ssb-admin-bar-purge.php';
		}
		Ssb_Admin_Bar_Purge::process_api_batch( $this );
	}

	/**
	 * Return configured internal flush interval in hours.
	 *
	 * @since 7.0.0
	 * @return int
	 */
	private function ssb_get_internal_flush_interval_hours() {
		$advanced       = get_option( 'ssb_advanced', array() );
		$interval_hours = isset( $advanced['ssb_internal_flush_interval'] ) ? (int) $advanced['ssb_internal_flush_interval'] : 2;
		$interval_hours = max( 1, min( 24, $interval_hours ) );
		return (int) apply_filters( 'ssb_internal_flush_interval_hours', $interval_hours );
	}

	/**
	 * SSB footer injection script.
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 7.0.1
	 * @return void
	 */
	public function ssb_footer_functions() {
		// Check it is 404 page or not single (page/post).
		if ( is_404() || ! is_singular() ) {
			return;
		}

		$ssb_cache_rebuild = ! empty( $_GET['ssb_cache'] ) && current_user_can( 'manage_options' ); // phpcs:ignore

		if ( ( $this->is_ssb_on( 'sidebar' ) || $this->is_ssb_on( 'inline' ) ) || $ssb_cache_rebuild ) {

			// Fetch a few variables.
			$info['postID']        = (int) get_the_ID();
			$info['footer_output'] = '';

			// Pass the array through our custom filters.
			$info = apply_filters( 'ssb_footer_scripts', $info );

			// If we have output, output it.
			if ( $info['footer_output'] ) {
				echo '<script type="text/javascript">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $info['footer_output'];
				echo '</script>';
			}
		}
	}

	/**
	 * Shortcode tags NextGEN registers (used only if Imagely API is unavailable).
	 *
	 * @since 7.0.0
	 * @var string[]
	 */
	private static $ssb_nextgen_shortcode_fallback_tags = array(
		'ngg',
		'ngg_images',
		'imagely',
		'nggallery',
		'nggslideshow',
		'singlepic',
		'slideshow',
		'album',
		'nggalbum',
		'thumb',
		'nggthumb',
		'random',
		'nggrandom',
		'recent',
		'nggrecent',
		'imagebrowser',
		'nggimagebrowser',
		'tagcloud',
		'nggtagcloud',
		'nggtags',
	);

	/**
	 * NextGEN-registered shortcode tags (dynamic when Imagely\NGG\Display\Shortcodes is loaded).
	 *
	 * @since 7.0.0
	 * @return string[]
	 */
	private function ssb_get_nextgen_shortcode_tags() {
		static $cached = null;

		if ( null !== $cached ) {
			return $cached;
		}

		$cached = array();

		if ( class_exists( '\Imagely\NGG\Display\Shortcodes' ) ) {
			$registered = \Imagely\NGG\Display\Shortcodes::get_instance()->get_shortcodes();
			if ( is_array( $registered ) ) {
				$cached = array_keys( $registered );
			}
		}

		if ( empty( $cached ) ) {
			$cached = self::$ssb_nextgen_shortcode_fallback_tags;
		}

		return $cached;
	}

	/**
	 * Detect NextGEN output in post content (shortcodes, blocks).
	 *
	 * Uses NextGEN's Shortcodes registry when available (DRY); falls back to a static tag list
	 * only if that API is not loaded yet.
	 *
	 * @since 7.0.0
	 * @param WP_Post $post Post object.
	 * @return bool
	 */
	private function ssb_post_has_nextgen_gallery_output( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$content = $post->post_content;

		if ( function_exists( 'has_block' ) ) {
			if ( has_block( 'imagely/main-block', $post )
				|| has_block( 'imagely/nextgen-gallery', $post ) ) {
				return true;
			}
		}

		// Serialized block markup edge cases (matches NextGEN BlockManager registrations).
		if ( false !== stripos( $content, 'imagely/main-block' )
			|| false !== stripos( $content, 'imagely/nextgen-gallery' ) ) {
			return true;
		}

		foreach ( $this->ssb_get_nextgen_shortcode_tags() as $tag ) {
			if ( '' !== $tag && has_shortcode( $content, $tag ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * NextGEN Gallery integration (Pro) injects buttons via Pro scripts but relies on Lite
	 * front.css / front.js (base markup styles and SSB localization).
	 *
	 * Only enqueues when the main query includes content that actually renders NextGEN.
	 *
	 * @since 7.0.0
	 * @return bool
	 */
	private function ssb_needs_front_assets_for_nextgen_gallery() {
		if ( ! class_exists( 'C_Photocrati_Installer' ) ) {
			return false;
		}

		$opts = get_option( 'ssb_ngg_gallery', array() );
		if ( ! is_array( $opts ) ) {
			return false;
		}

		// Match truthiness used in Simple_Social_Buttons_Pro::ssb_ngg_scripts().
		$setting_on = ! empty( $opts['ssb_enable_ngg_setting'] );
		$list_on    = ! empty( $opts['ssb_enable_ngg_on_list'] );

		if ( ! $setting_on && ! $list_on ) {
			return false;
		}

		global $wp_query;

		if ( empty( $wp_query ) || empty( $wp_query->posts ) || ! is_array( $wp_query->posts ) ) {
			return false;
		}

		foreach ( $wp_query->posts as $post_obj ) {
			if ( $this->ssb_post_has_nextgen_gallery_output( $post_obj ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether to load front CSS/JS on this request (share UI may appear).
	 *
	 * Early detection for `wp_enqueue_scripts`. `[SSB]` also calls
	 * `ssb_enqueue_front_assets()` when it renders (e.g. sidebar widgets).
	 *
	 * Widget-only or late-rendered shortcodes can use filter `ssb_enqueue_front_assets`.
	 *
	 * @since 7.0.0
	 * @return bool
	 */
	private function ssb_needs_front_assets() {
		if ( is_admin() ) {
			return false;
		}

		if ( is_feed() || is_embed() || wp_is_json_request() ) {
			return false;
		}

		// Matches footer logic that may emit ssb_fetch_data tooling without placements enabled.
		if ( ! empty( $_GET['ssb_cache'] ) && current_user_can( 'manage_options' ) && is_singular() ) { // phpcs:ignore
			return true;
		}

		/**
		 * Force loading Simple Social Buttons front assets when automatic detection misses output
		 * (e.g. shortcode inside a Text widget).
		 *
		 * @since 7.0.0
		 * @param bool $enqueue Whether to enqueue front assets.
		 */
		if ( apply_filters( 'ssb_enqueue_front_assets', false ) ) {
			return true;
		}

		$positions     = array( 'inline', 'sidebar', 'media', 'popup', 'flyin' );
		$positions_cfg = is_array( $this->selected_position ) ? $this->selected_position : array();
		foreach ( $positions as $position ) {
			if ( isset( $positions_cfg[ $position ] ) && $this->is_ssb_on( $position ) ) {
				return true;
			}
		}

		if ( $this->ssb_needs_front_assets_for_nextgen_gallery() ) {
			return true;
		}

		global $wp_query;
		if ( empty( $wp_query ) || empty( $wp_query->posts ) || ! is_array( $wp_query->posts ) ) {
			return false;
		}

		foreach ( $wp_query->posts as $post_obj ) {
			if ( ! $post_obj instanceof WP_Post ) {
				continue;
			}

			foreach ( $this->ssb_get_shortcode_tags() as $shortcode_tag ) {
				if ( has_shortcode( $post_obj->post_content, $shortcode_tag ) ) {
					return true;
				}
			}

			if ( function_exists( 'has_block' )
				&& ( has_block( 'ssb/shortcode', $post_obj ) || has_block( 'ssb/click-to-tweet', $post_obj ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return supported share-button shortcode tags.
	 *
	 * WordPress shortcode tags are case-sensitive, so retain the legacy
	 * uppercase tag and provide a lowercase alias.
	 *
	 * @since 7.1.0
	 * @return string[]
	 */
	private function ssb_get_shortcode_tags() {
		return array( 'SSB', 'ssb' );
	}

	/**
	 * Front enqueue script.
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 7.1.0
	 * @return void
	 */
	public function ssb_front_enqueue_scripts() {
		if ( ssb_is_amp_request() ) {
			?>
			<style amp-custom>
				<?php
				include_once plugin_dir_path( __FILE__ ) . 'assets/css/front.php';
				?>
			</style>
				<?php

				// Avoid adding any non-AMP compliant scripts.
				// Consider using amp-bind, amp-analytics, etc., if needed.
		} else {
			$this->ssb_register_front_assets();

			if ( ! $this->ssb_needs_front_assets() ) {
				return;
			}

			$this->ssb_enqueue_front_assets();
		}
	}

	/**
	 * Register front CSS/JS handles (idempotent).
	 *
	 * @since 7.1.0
	 * @return void
	 */
	private function ssb_register_front_assets() {
		if ( ! wp_script_is( 'ssb-front-js', 'registered' ) ) {
			wp_register_script(
				'ssb-front-js',
				plugins_url( 'assets/js/front.min.js', __FILE__ ),
				array( 'jquery' ),
				SSB_VERSION,
				true
			);
		}

		if ( ! wp_style_is( 'ssb-front-css', 'registered' ) ) {
			wp_register_style(
				'ssb-front-css',
				plugins_url( 'assets/css/front.min.css', __FILE__ ),
				array(),
				SSB_VERSION
			);
		}
	}

	/**
	 * Enqueue registered front assets (idempotent).
	 *
	 * Safe to call from shortcode render when positions are disabled and
	 * `[SSB]` only appears in a widget / late template.
	 *
	 * @since 7.1.0
	 * @return void
	 */
	private function ssb_enqueue_front_assets() {
		if ( is_admin() ) {
			return;
		}

		if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
			return;
		}

		static $enqueued = false;
		if ( $enqueued ) {
			return;
		}

		$this->ssb_register_front_assets();

		wp_enqueue_script( 'ssb-front-js' );
		wp_enqueue_style( 'ssb-front-css' );
		$this->ssb_enqueue_snapchat_creative_kit_sdk();

		if ( ! empty( $this->extra_option['ssb_css'] ) ) {
			wp_add_inline_style(
				'ssb-front-css',
				ssb_sanitize_custom_css( $this->extra_option['ssb_css'] )
			);
		}

		if ( ! empty( $this->extra_option['ssb_js'] ) ) {
			$custom_js = ssb_get_custom_js_for_output( $this->extra_option['ssb_js'] );

			if ( '' !== $custom_js ) {
				wp_add_inline_script( 'ssb-front-js', $custom_js );
			}
		}

		wp_localize_script(
			'ssb-front-js',
			'SSB',
			array(
				'ajax_url'                 => admin_url( 'admin-ajax.php' ),
				'share_track_nonce'        => wp_create_nonce( 'ssb_track_share_click' ),
				'share_trackable_networks' => ssb_get_internal_share_trackable_networks(),
				'popup_features'           => apply_filters(
					'ssb_share_popup_window_features',
					'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'
				),
				'i18n'                     => array(
					'hide_bar' => __( 'Hide social sharing bar', 'simple-social-buttons' ),
					'show_bar' => __( 'Show social sharing bar', 'simple-social-buttons' ),
				),
			)
		);

		$enqueued = true;
	}



	/**
	 * Block front scripts.
	 *
	 * @since 3.0.0
	 * @version 7.0.1
	 * @return void
	 */
	public function ssb_front_block_scripts() {
		// Block editor (admin) always needs these scripts; front end only when SSB output is expected.
		if ( ! is_admin() && ! $this->ssb_needs_front_assets() ) {
			return;
		}

		if ( ! ssb_is_amp_request() ) {
			// This code will run on non-AMP pages.
			wp_enqueue_script( 'ssb-blocks-front-js', plugins_url( 'assets/js/frontend-blocks.js', __FILE__ ), array(), SSB_VERSION, true );

		}

		// Custom JS is attached to ssb-front-js in ssb_front_enqueue_scripts() to avoid
		// double execution and nested <script> breakage via wp_add_inline_script().
	}

	/**
	 * All third party api/ helper functions.
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 7.0.0
	 * @return void
	 */
	public function includes() {

		include_once SSB_PLUGIN_DIR . '/inc/ssb-utils.php';
		include_once SSB_PLUGIN_DIR . '/inc/ssb-compatibility.php';
		include_once SSB_PLUGIN_DIR . '/ssb-social-counts/ssb-facebook.php';
		include_once SSB_PLUGIN_DIR . '/ssb-social-counts/ssb-twitter.php';
		include_once SSB_PLUGIN_DIR . '/ssb-social-counts/ssb-tumblr.php';
		include_once SSB_PLUGIN_DIR . '/ssb-social-counts/ssb-line.php';
	}

	/**
	 * Resets all settings to default on plugin update.
	 *
	 * This function is used to reset all settings to default on plugin update.
	 * It will only work if the user has checked the checkbox for resetting
	 * settings on the plugin's settings page.
	 *
	 * @since 5.3.3
	 * @access public
	 * @return void
	 */
	public function ssb_factory_reset_settings_on_update() {

		if ( isset( $this->extra_option['ssb_factory_reset'] ) && '1' === $this->extra_option['ssb_factory_reset'] ) {
			$this->ssb_plugin_install( true );
			update_option(
				'ssb_snapchat',
				array(
					'snapchat_client_id' => '',
				)
			);
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'simple-social-buttons-pro/simple-social-buttons-pro.php' )
			|| is_plugin_active_for_network( 'simple-social-buttons-pro/simple-social-buttons-pro.php' ) ) {
				require_once SSB_PRO_PLUGIN_DIR . '/simple-social-buttons-pro.php';
				ssb_pro_plugin_install( true );
			}
		}
	}

	/**
	 * Plugin constant.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function constants() {
		if ( ! defined( 'SSB_FEEDBACK_SERVER' ) ) {

			define( 'SSB_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
		}
		if ( ! defined( 'SSB_VERSION' ) ) {

			define( 'SSB_VERSION', $this->plugin_version );
		}
		if ( ! defined( 'SSB_PLUGIN_DIR' ) ) {
			define( 'SSB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'SSB_PLUGIN_URL' ) ) {
			define( 'SSB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
	}


	/**
	 * Set default settings.
	 *
	 * @param mixed $default_value Default value.
	 * @access public
	 * @since 1.0.0
	 * @version 5.3.3
	 * @return void
	 */
	public function ssb_plugin_install( $default_value = false ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		if ( ! is_multisite() ) {

			$this->default_settings( $default_value );

		} else {

			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$ssb_blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $ssb_blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->default_settings( $default_value );
				restore_current_blog();
			}
		}
		update_option( $this->plugin_prefix . 'version', $this->plugin_version );
	}
	/**
	 * Plugin default settings.
	 *
	 * @version 7.0.1
	 * @param mixed $default_value Default value.
	 * @return void
	 */
	public function default_settings( $default_value ) {
		if ( get_option( 'ssb_networks' ) === $default_value ) {
			$_default = array(
				'icon_selection' => 'fbshare,twitter,linkedin',
				'custom_buttons' => array(),
			);
			self::ssb_update_networks_option( $_default );
		}

		if ( get_option( 'ssb_themes' ) === $default_value ) {
			$_default = array(
				'icon_style' => 'simple-icons',
			);
			update_option( 'ssb_themes', $_default );
		}

		if ( get_option( 'ssb_positions' ) === $default_value ) {
			$_default = array(
				'position' => array(
					'inline' => 'inline',
				),
			);
			update_option( 'ssb_positions', $_default );
		}

		if ( get_option( 'ssb_inline' ) === $default_value ) {
			$_default = array(
				'location' => 'below',
				'posts'    => array(
					'post' => 'post',
				),
			);
			update_option( 'ssb_inline', $_default );
		}

		if ( get_option( 'ssb_advanced' ) === $default_value ) {
			$_default = array(
				'ssb_og_tags'                 => '1',
				'ssb_internal_flush_interval' => '2',
			);
			update_option( 'ssb_advanced', $_default );
		}

		if ( get_option( 'ssb_advanced' ) === $default_value ) {
			$_default = array(
				'ssb_factory_reset' => '0',
			);
			update_option( 'ssb_advanced', $_default );
		}

		if ( get_option( 'ssb_snapchat' ) === $default_value ) {
			$_default = array(
				'snapchat_client_id' => '',
			);
			update_option( 'ssb_snapchat', $_default );
		}
	}

	/**
	 * Get settings value.
	 *
	 * @param string $section Section name.
	 * @param string $value Value key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public function get_settings( $section, $value, $default = false ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound
		$section = $section . '_option';
		$_arr    = $this->$section;
		return isset( $_arr[ $value ] ) && ! empty( $_arr[ $value ] ) ? $_arr[ $value ] : $default;
	}

	/**
	 * Whether a section setting is enabled (supports bool, 1/0, and legacy strings).
	 *
	 * @param string $section Section slug (inline, sidebar, etc.).
	 * @param string $key     Setting key.
	 * @return bool
	 * @since 7.0.0
	 */
	public function ssb_is_setting_enabled( $section, $key ) {
		$section_key = $section . '_option';
		$options     = isset( $this->$section_key ) ? $this->$section_key : array();

		if ( ! is_array( $options ) || ! array_key_exists( $key, $options ) ) {
			return false;
		}

		$value = $options[ $key ];

		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			return (int) $value > 0;
		}

		$value = strtolower( trim( (string) $value ) );

		return in_array( $value, array( '1', 'true', 'yes', 'on' ), true );
	}

	/**
	 * Resolved icon limit for a section (0 when disabled or invalid).
	 *
	 * @param string $section Section slug (inline, sidebar, etc.).
	 * @return int
	 * @since 7.0.0
	 */
	public function ssb_get_icon_limit_for_section( $section ) {
		if ( ! $this->ssb_is_setting_enabled( $section, 'icon_limit' ) ) {
			return 0;
		}

		$section_key = $section . '_option';
		$options     = isset( $this->$section_key ) ? $this->$section_key : array();
		$raw         = isset( $options['icon_limit_value'] ) ? $options['icon_limit_value'] : 0;
		$limit       = absint( $raw );

		return max( 3, $limit );
	}

	/**
	 *  Where to insert
	 *
	 * @access public
	 * @since 1.0.0
	 * @return mixed
	 */
	public function where_to_insert() {

		$return = false;

		// Single Page/Post.
		if ( isset( $this->selected_position['inline'] ) && 'false' === get_post_meta( get_the_ID(), $this->hide_custom_meta_key, true ) ) {
			$return = true;
		}
		return $return;
	}


	/**
	 * Add inline for the excerpt.
	 *
	 * @param string $content Post content.
	 * @access public
	 * @since 2.0
	 * @return string
	 */
	public function ssb_insert_excerpt_buttons( $content ) {

		if ( is_single() ) {
			return $content;
		}

		return $this->ssb_insert_buttons( $content );
	}

	/**
	 * Return class
	 *
	 * @param int|null $post_id Post ID.
	 * @access public
	 * @since 2.0.4
	 * @return string
	 */
	public function add_post_class( $post_id = null ) {
		$post = get_post( $post_id );

		$classes = '';

		if ( ! $post ) {
			return $classes;
		}

		$classes .= 'post-' . $post->ID . ' ';
		$classes .= $post->post_type . ' ';

		return $classes;
	}


	/**
	 * Add Inline Buttons.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $content Post content.
	 * @return string
	 */
	public function ssb_insert_buttons( $content ) {

		// Return the content if we are not in loop.
		if ( ! is_main_query() || ! in_the_loop() ) {
			return $content;
		}

		// Return Content if hide ssb.
		if ( 'true' === get_post_meta( get_the_ID(), $this->hide_custom_meta_key, true ) ) {
			return $content;
		}

		if ( is_archive() && '0' === $this->get_settings( 'inline', 'show_on_archive', '0' ) && ! is_tag() && ! is_category() ) {
			return $content; }
		if ( is_category() && '0' === $this->get_settings( 'inline', 'show_on_category', '0' ) ) {
			return $content; }
		if ( is_tag() && '0' === $this->get_settings( 'inline', 'show_on_tag', '0' ) ) {
			return $content; }
		if ( is_search() && '0' === $this->get_settings( 'inline', 'show_on_search', '0' ) ) {
			return $content; }

		if ( isset( $this->selected_position['inline'] ) ) {
			// Show Total at the end.
			if ( $this->get_settings( 'inline', 'total_share' ) ) {
				$show_total = true;
			} else {
				$show_total = false;
			}

			$extra_class = 'simplesocialbuttons_inline simplesocialbuttons-align-' . $this->get_settings( 'inline', 'icon_alignment', 'left' ) . ' ' . $this->add_post_class();

			if ( $this->get_settings( 'inline', 'share_counts' ) ) {
				$show_count   = true;
				$extra_class .= ' ssb_counter-activate';
			} else {
				$show_count = false;
			}

			if ( $this->get_settings( 'inline', 'hide_mobile' ) ) {
				$extra_class .= ' simplesocialbuttons-mobile-hidden'; }
				$extra_class .= ' simplesocialbuttons-inline-' . $this->get_settings( 'inline', 'animation', 'no-animation' );

			$_selected_network = apply_filters( 'ssb_inline_social_networks', $this->selected_networks );

			$extra_data = array(
				'class'    => $extra_class,
				'position' => 'inline',
			);
			$icon_limit = $this->ssb_get_icon_limit_for_section( 'inline' );
			if ( $icon_limit > 0 ) {
				$extra_data['icon_limit'] = $icon_limit;
			}
			$ssb_buttonscode = $this->ssb_generate_buttons_code( $_selected_network, $show_count, $show_total, $extra_data );

			$sharing_text = '';

			if ( isset( $this->inline_option['share_title'] ) && '' !== trim( $this->inline_option['share_title'] ) ) {
				if ( $this->get_settings( 'inline', 'hide_mobile' ) ) {
					$sharing_text = '<span class=" simplesocialbuttons-mobile-hidden ssb_inline-share_heading ' . $this->get_settings( 'inline', 'icon_alignment', 'left' ) . '">'
					. esc_html( $this->inline_option['share_title'] ) . '</span>';
				} else {
					$sharing_text = '<span class=" ssb_inline-share_heading ' . $this->get_settings( 'inline', 'icon_alignment', 'left' ) . '">'
					. esc_html( $this->inline_option['share_title'] ) . '</span>';
				}
			}
			if ( in_array( $this->get_post_type(), $this->get_settings( 'inline', 'posts', array() ), true ) ) {
				if ( 'above' === $this->inline_option['location'] || 'above_below' === $this->inline_option['location'] ) {
					$content = $sharing_text . $ssb_buttonscode . $content;
				}
				if ( 'below' === $this->inline_option['location'] || 'above_below' === $this->inline_option['location'] ) {
					$content = $content . $sharing_text . $ssb_buttonscode;
				}
			}
		}

		return $content;
	}

	/**
	 * Generate HTML code for specific  order.
	 *
	 * @param array   $order The order array of active social icons.
	 * @param boolean $show_count Show number of counts if true.
	 * @param boolean $show_total Show total if true.
	 * @param array   $extra_data The array of extra data.
	 * @param string  $image The image src.
	 *
	 * @access public
	 * @since 1.0.0
	 * @version 7.0.1
	 * @return string
	 */
	public function ssb_generate_buttons_code( $order = null, $show_count = false, $show_total = false, $extra_data = array(), $image = false ) {

		/**
		 * Check if buttons are for an image instead of a post.
		 *
		 * @since 5.0.0
		 */
		if ( $image ) {
			preg_match( '/src="([^"]+)"/', $image, $image_src );

			if ( count( $image_src ) === 2 ) {
				$image = $image_src[1];
				$alt   = '';
			}
		}

		// Define empty buttons code to use.
		$ssb_buttonscode = '';
		$post_id         = get_the_ID() ?: get_queried_object_id(); // phpcs:ignore Universal.Operators.DisallowShortTernary.Found
		$theme           = isset( $extra_data['theme'] ) ? $extra_data['theme'] : $this->selected_theme;

		/**
		* 'ssb_get_share_post' can be used to change the post that is shared.
		 *
		* @param int $post_id is the post ID
		* @param $extra_data
		*
		* @since 3.2.4
		* @version 7.0.0
		*/
		$post_id = apply_filters( 'ssb_get_share_post', $post_id, $extra_data );

		if ( ! is_array( $order ) ) {
			$order = array();
		}

		// Get post permalink and title.
		$permalink = get_permalink( $post_id );
		$title     = rawurlencode( html_entity_decode( get_the_title( $post_id ), ENT_COMPAT, 'UTF-8' ) );
		// Keep the page URL for networks (e.g. Pinterest) that need page + media separately.
		$ssb_share_page_url = $permalink;
		$ssb_share_post_id  = (int) $post_id;
		$ssb_share_media    = is_string( $image ) ? $image : '';

		// Sorting the buttons.
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$arr_buttons = array();
		foreach ( $this->arr_known_buttons as $button_name ) {
			if ( ! empty( $order[ $button_name ] ) && 0 !== (int) $order[ $button_name ] ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				$arr_buttons[ $button_name ] = $order[ $button_name ];
			}
		}
		// Add custom buttons (custom_1, custom_2, ...) from order (Pro only).
		if ( ssb_is_pro_active() ) {
			foreach ( $order as $btn_id => $sort_val ) {
				if ( ! in_array( $btn_id, $this->arr_known_buttons, true ) && ssb_is_custom_button_id( $btn_id ) && 0 !== (int) $sort_val ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons[ $btn_id ] = $sort_val;
				}
			}
		}
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		asort( $arr_buttons );

		// Add total share index in array.
		if ( $show_total ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$arr_buttons['totalshare'] = '100';
		}

		// Special case if post id not exist for example short code run on widget out side the loop in archive page and old counts not exist.
		if ( false === $permalink && ! $image ) {
			$permalink = get_site_url();
			$title     = get_bloginfo( 'name' );
			$post_id   = 0;
		}

		// If an image is found set the permalink to image's.
		if ( $image ) {
			$ssb_share_media = $image;
			$permalink       = $image ? $image : $permalink;
			$title           = isset( $alt ) && $alt ? $alt : get_bloginfo( 'name' );
			$post_id         = 0;
		}

		// Get the value for http or https solve options.
		$http_solve = false;
		if ( isset( $this->extra_option['http_https_resolve'] ) ) {
			if ( false === get_post_meta( $post_id, 'ssb_old_counts', true ) ) {
				$http_solve = true;
			}
		}

		$non_exist_post_record = false;
		// Special case if post id not exist for example short code run on widget out side the loop in archive page and old counts not exist.
		if ( 0 === $post_id ) {
			$non_exist_post_record = get_option( 'ssb_not_exist_post_old_counts' );
			$non_exist_post_record = true;
		}

		// Use the post_url with parameter if set, otherwise use the permalink.
		$permalink = isset( $extra_data['post_url'] ) ? esc_url( $permalink . $extra_data['post_url'] ) : esc_url( $permalink );

		// Reset the cache timestamp if needed.
		// If false fetch the new share counts.
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase,WordPress.PHP.YodaConditions.NotYoda
		if ( ( isset( $this->settings['cache'] ) && 'off' === $this->settings['cache'] ) || ( true === $http_solve ) || ( $non_exist_post_record ) ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$share_counts = $this->ssb_get_fresh_share_counts_for_networks( $post_id, $permalink, $arr_buttons );
		} else {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$share_counts = ssb_fetch_cached_counts( array_flip( $arr_buttons ), $post_id );
		}
		$share_counts = ssb_merge_api_and_internal_share_counts( $share_counts, $post_id, $arr_buttons );

		/**
		 * 'ssb_network_counts' is the filter for applying check is either show network counts after some amount of  share.
		 *
		 * @since 3.0.2
		 *
		 * @param boolean $show_count is show count enable.
		 * @param array $show_count all network share counts.
		 */
		$show_count = apply_filters( 'ssb_network_counts', $show_count, $share_counts, $extra_data );

		/**
		 * 'ssb_active_network' is the filter activated share network.
		 *
		 * @since 3.0.2
		 *
		 * @param array $arr_buttons selected networks.
		 * @param array $share_counts all network share counts.
		 * @param array $extra_data extra data.
		 */
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$arr_buttons = apply_filters( 'ssb_active_network', $arr_buttons, $share_counts, $extra_data );

		$arr_hidden_buttons              = array();
		$defer_totalshare_after_overflow = false;
		$deferred_totalshare_html        = '';
		if ( empty( $extra_data['buttons_only'] ) && ! empty( $extra_data['icon_limit'] ) && (int) $extra_data['icon_limit'] > 0 ) {
			$icon_limit       = (int) $extra_data['icon_limit'];
			$totalshare_entry = null;
			if ( isset( $arr_buttons['totalshare'] ) ) {
				$totalshare_entry = $arr_buttons['totalshare'];
				unset( $arr_buttons['totalshare'] );
			}
			if ( count( $arr_buttons ) > $icon_limit ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				$arr_hidden_buttons = array_slice( $arr_buttons, $icon_limit, null, true );
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				$arr_buttons = array_slice( $arr_buttons, 0, $icon_limit, true );
			}
			if ( null !== $totalshare_entry ) {
				if ( ! empty( $arr_hidden_buttons ) ) {
					$defer_totalshare_after_overflow = true;
				}
				$arr_buttons['totalshare'] = $totalshare_entry;
			}
		}

		$_html = '';

		// Add The heading text.
		if ( 'sidebar' === $extra_data['position'] && ! empty( $extra_data['before_text'] ) && empty( $extra_data['buttons_only'] ) ) {
			$_html .= '<h4 class="ssb_sidebar_heading_text">' . $extra_data['before_text'] . '</h4>';
		}

		$arr_buttons_code = array();
		$ssb_btn_attrs    = array(
			'rel'    => 'nofollow',
			'target' => '_blank',
		);

		/**
		 * 'ssb_button_attrs' filter can be used to change the button attributes.
		 * Only rel and target attributes are allowed to change for now.
		 *
		 * @param array $ssb_btn_attrs The array of attributes.
		 *
		 * @since 4.1.0
		 */
		$ssb_btn_attr_filter  = (array) apply_filters( 'ssb_button_attrs', $ssb_btn_attrs );
		$allowed_button_attrs = array( 'rel', 'target' );
		$ssb_attr_html        = '';
		$ssb_is_amp           = ssb_is_amp_request();
		$ssb_trigger_attr     = 'data-href';
		$ssb_click_attr       = 'onClick';
		if ( $ssb_is_amp ) {
			$ssb_trigger_attr = 'href';
			$ssb_click_attr   = 'data-click';
		}

		foreach ( $ssb_btn_attr_filter as $ssb_btn_attr_index => $value ) {

			$value              = esc_attr( wp_unslash( $value ) );
			$ssb_btn_attr_index = esc_attr( wp_unslash( $ssb_btn_attr_index ) );
			if ( in_array( $ssb_btn_attr_index, $allowed_button_attrs, true ) ) {
				$ssb_attr_html .= ' ' . $ssb_btn_attr_index . '="' . $value . '" ';
			}
		}
		$ssb_base_attr_html = $ssb_attr_html;
		$ssb_element_tag    = 'button';
		if ( $ssb_is_amp ) {
			$ssb_element_tag = 'a';
		}
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		foreach ( $arr_buttons as $button_name => $button_sort ) {
			$ssb_attr_html = $ssb_base_attr_html
				. ' data-ssb-network="' . esc_attr( $button_name ) . '" '
				. ' data-ssb-post-id="' . (int) $post_id . '" ';

			// phpcs:disable Generic.Files.LineLength.MaxExceeded
			// Non-AMP: delegated share behavior in assets/js/front.js. AMP keeps legacy onclick/data-click.
			$ssb_ck_popup        = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="javascript:window.open(this.dataset.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"'
				: ' data-ssb-share-action="popup"';
			$ssb_ck_blank        = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="javascript:window.open(this.dataset.href, \'_blank\' );return false;"'
				: ' data-ssb-share-action="blank"';
			$ssb_ck_self         = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="javascript:window.open(this.dataset.href, \'_self\' );return false;"'
				: ' data-ssb-share-action="self"';
			$messenger_amp_js    = 'javascript:window.open(this.dataset.href, \'_blank\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;';
			$ssb_ck_messenger    = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="' . $messenger_amp_js . '"'
				: ' data-ssb-share-action="messenger"';
			$ssb_ck_mailto       = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="javascript:window.location.href = this.dataset.href;return false;"'
				: ' data-ssb-share-action="mailto"';
			$ssb_ck_print        = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="javascript:window.print();return false;"'
				: ' data-ssb-share-action="print"';
			$pinterest_amp_js    = 'var e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'https://assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e);return false;';
			$ssb_ck_pinterest    = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="' . $pinterest_amp_js . '"'
				: ' data-ssb-share-action="pinterest-pin"';
			$ssb_ck_custom_blank = $ssb_is_amp
				? ' ' . $ssb_click_attr . '="window.open(this.getAttribute(\'data-href\')||this.dataset.href,\'_blank\');return false;"'
				: ' data-ssb-share-action="blank"';

			/**
			 * Fix the duplication of buttons by resetting the variable.
			 *
			 * @since 5.3.2
			 */
			$_html = '';

			switch ( $button_name ) {

				case 'fbshare':
					$fbshare_share = ( isset( $share_counts['fbshare'] ) && $share_counts['fbshare'] > 0 ) ? $share_counts['fbshare'] : 0;

					if ( 'simple-icons' === $theme ) {
						// phpcs:disable Generic.Files.LineLength.MaxExceeded
						$_html .= '		<' . $ssb_element_tag . ' class="ssb_fbshare-icon" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Facebook Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr
						. '="' . esc_url( ssb_build_share_url( 'fbshare', $permalink ) ) . '"' . $ssb_ck_popup . '>
						<span class="icon"><svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="_1pbq" color="#ffffff"><path fill="#ffffff" fill-rule="evenodd"
						class="icon" d="M8 14H3.667C2.733 13.9 2 13.167 2 12.233V3.667A1.65 1.65 0 0 1 3.667 2h8.666A1.65 1.65 0 0 1 14 3.667v8.566c0 .934-.733 1.667-1.667
						1.767H10v-3.967h1.3l.7-2.066h-2V6.933c0-.466.167-.9.867-.9H12v-1.8c.033 0-.933-.266-1.533-.266-1.267 0-2.434.7-2.467 2.133v1.867H6v2.066h2V14z">
						</path></svg></span>
						<span class="simplesocialtxt">' . esc_html__( 'Share', 'simple-social-buttons' ) . ' </span>';

						if ( $show_count ) {
							$_html .= ' <span class="ssb_counter">' . ssb_count_format( $fbshare_share ) . '</span>';
						}

						$_html .= ' </' . $ssb_element_tag . '>';
					} else {

						$_html = '<' . $ssb_element_tag . ' class="simplesocial-fb-share" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Facebook Share', 'simple-social-buttons' ) . '" '
						. $ssb_trigger_attr . '="' . esc_url( ssb_build_share_url( 'fbshare', $permalink ) ) . '"' . $ssb_ck_popup . '>
						<span class="simplesocialtxt">' . esc_html__( 'Facebook', 'simple-social-buttons' ) . ' </span> ';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_fbshare_counter">' . ssb_count_format( $fbshare_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

							// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
							$arr_buttons_code[] = $_html;

					break;
				case 'bluesky':
					$bluesky_share = ( isset( $share_counts['bluesky'] ) && $share_counts['bluesky'] > 0 ) ? $share_counts['bluesky'] : 0;
					if ( 'simple-icons' === $theme ) {
						$_html .= '		<' . $ssb_element_tag . '  class="ssb_bluesky-icon" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Bluesky Share', 'simple-social-buttons' ) . '" '
						. $ssb_trigger_attr . '="https://bsky.app/intent/compose?text=' . $permalink . '"' . $ssb_ck_popup . '>
						<span class="icon"><svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com
						License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
						<path d="M111.8 62.2C170.2 105.9 233 194.7 256 242.4c23-47.6 85.8-136.4 144.2-180.2c42.1-31.6 110.3-56 110.3 21.8c0 15.5-8.9 130.5-14.1 149.2C478.2 298
						412 314.6 353.1 304.5c102.9 17.5 129.1 75.5 72.5 133.5c-107.4 110.2-154.3-27.6-166.3-62.9l0 0c-1.7-4.9-2.6-7.8-3.3-7.8s-1.6 3-3.3 7.8l0 0c-12 35.3-59
						173.1-166.3 62.9c-56.5-58-30.4-116 72.5-133.5C100 314.6 33.8 298 15.7 233.1C10.4 214.4 1.5 99.4 1.5 83.9c0-77.8 68.2-53.4 110.3-21.8z"/></svg></span>
						<span class="simplesocialtxt">' . esc_html__( 'Bluesky', 'simple-social-buttons' ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $bluesky_share ) . '</span>';
						}
						$_html .= ' </' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . '  class="simplesocial-bluesky-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Bluesky Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="https://bsky.app/intent/compose?text=' . $permalink . '"' . $ssb_ck_popup . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Bluesky', 'simple-social-buttons' ) . ' </span> ';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_bluesky_counter">' . ssb_count_format( $bluesky_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

						$arr_buttons_code[] = $_html;

					break;
				case 'telegram':
					$telegram_share = ( isset( $share_counts['telegram'] ) && $share_counts['telegram'] > 0 ) ? $share_counts['telegram'] : 0;
					if ( 'simple-icons' === $theme ) {
						$_html .= '		<' . $ssb_element_tag . '  class="ssb_telegram-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Telegram Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="https://t.me/share/url?url=' . $permalink . '"' . $ssb_ck_popup . '>'
							. '<span class="icon"><svg aria-hidden="true" focusable="false" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">'
							. '<path d="M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8'
							. 'c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1'
							. 'L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z"/>'
							. '</svg></span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Telegram', 'simple-social-buttons' ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $telegram_share ) . '</span>';
						}
						$_html .= ' </' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . '  class="simplesocial-telegram-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Telegram Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="https://t.me/share/url?url=' . $permalink . '"' . $ssb_ck_popup . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Telegram', 'simple-social-buttons' ) . ' </span> ';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_telegram_counter">' . ssb_count_format( $telegram_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

						$arr_buttons_code[] = $_html;

					break;
				case 'threads':
					$threads_share = ( isset( $share_counts['threads'] ) && $share_counts['threads'] > 0 ) ? $share_counts['threads'] : 0;
					if ( 'simple-icons' === $theme ) {
						$threads_svg_path = 'M93.405,59.512c-0.529-0.254-1.067-0.498-1.612-0.732c-0.948-17.477-10.498-27.482-26.533-27.585'
							. 'c-0.073,0-0.145,0-0.218,0  c-9.591,0-17.568,4.094-22.477,11.543l8.819,6.049c3.668-5.565,9.424-6.751,13.663-6.751'
							. 'c0.049,0,0.098,0,0.147,0  c5.28,0.034,9.264,1.569,11.842,4.562c1.877,2.179,3.132,5.191,3.753,8.992'
							. 'c-4.681-0.796-9.744-1.04-15.155-0.73  c-15.245,0.878-25.046,9.77-24.388,22.124c0.334,6.267,3.456,11.658,8.791,15.18'
							. 'c4.51,2.977,10.32,4.433,16.357,4.104  c7.973-0.437,14.228-3.479,18.591-9.041c3.314-4.224,5.41-9.698,6.335-16.595'
							. 'c3.8,2.293,6.616,5.311,8.171,8.938  c2.645,6.166,2.799,16.3-5.47,24.561c-7.244,7.237-15.952,10.368-29.112,10.465'
							. 'c-14.598-0.108-25.639-4.79-32.817-13.915  C25.371,92.139,21.897,79.797,21.768,64c0.13-15.797,3.603-28.139,10.325-36.684'
							. 'c7.178-9.125,18.218-13.807,32.817-13.915  c14.704,0.109,25.937,4.813,33.39,13.983c3.654,4.496,6.41,10.151,8.226,16.744'
							. 'l10.334-2.757  c-2.202-8.115-5.666-15.108-10.38-20.908C96.925,8.707,82.951,2.684,64.946,2.559h-0.072'
							. 'C46.905,2.683,33.088,8.73,23.805,20.53  c-8.26,10.501-12.521,25.112-12.664,43.427l0,0.043l0,0.043'
							. 'c0.143,18.315,4.404,32.926,12.664,43.427  c9.283,11.8,23.1,17.847,41.069,17.971h0.072c15.975-0.111,27.235-4.293,36.512-13.561'
							. 'c12.137-12.125,11.771-27.323,7.771-36.653  C106.358,68.536,100.887,63.102,93.405,59.512z M65.823,85.445c-6.682,0.376-13.623-2.623-13.966-9.047'
							. '  c-0.254-4.763,3.39-10.078,14.376-10.711c1.258-0.073,2.493-0.108,3.706-0.108c3.99,0,7.724,0.388,11.118,1.13'
							. '  C79.79,82.519,72.365,85.086,65.823,85.445z';
						$_html           .= '		<' . $ssb_element_tag . '  class="ssb_threads-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'threads Share', 'simple-social-buttons' ) . '"  '
							. $ssb_trigger_attr . '="https://www.threads.net/intent/post?text=' . $permalink . '"' . $ssb_ck_popup . '>'
							. '<span class="icon"><svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1"'
							. ' width="128px" height="128px" viewBox="0 0 128 128"'
							. ' enable-background="new 0 0 128 128" xml:space="preserve">'
							. '<path d="' . $threads_svg_path . '"/>'
							. '<g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg></span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Threads', 'simple-social-buttons' ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $threads_share ) . '</span>';
						}
						$_html .= ' </' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . ' class="simplesocial-threads-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Threads Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="https://www.threads.net/intent/post?text=' . $permalink . '"' . $ssb_ck_popup . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Threads', 'simple-social-buttons' ) . ' </span> ';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_threads_counter">' . ssb_count_format( $threads_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

						$arr_buttons_code[] = $_html;

					break;
				case 'twitter':
					$twitter_share  = ( isset( $share_counts['twitter'] ) && $share_counts['twitter'] > 0 ) ? $share_counts['twitter'] : 0;
					$twitter_handle = isset( $this->extra_option['twitter_handle'] ) ? $this->extra_option['twitter_handle'] : '';

					if ( 'simple-icons' === $theme ) {
						$twitter_url      = ssb_twitter_share_link( $permalink, rawurldecode( $title ), $twitter_handle );
						$twitter_svg_path = 'M4.9 0H0L5.782 7.7098L0.315 14H2.17L6.6416 8.8557L10.5 14H15.4L9.3744 5.9654L14.56
						0H12.705L8.5148 4.8202L4.9 0ZM11.2 12.6L2.8 1.4H4.2L12.6 12.6H11.2Z';
						$_html            = '<' . $ssb_element_tag . ' class="ssb_tweet-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Twitter/X Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . esc_url( $twitter_url ) . '"' . $ssb_ck_popup . '>'
							. '<span class="icon"><svg aria-hidden="true" focusable="false" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">'
							. '<path d="' . $twitter_svg_path . '" fill="#fff"/></svg></span>';

						if ( $show_count ) {
							$_html .= '<i class="simplesocialtxt">' . esc_html__( 'Post', 'simple-social-buttons' ) . ' ' . ssb_count_format( $twitter_share ) . '</i>';
						} else {
							$_html .= '<i class="simplesocialtxt">' . esc_html__( 'Post', 'simple-social-buttons' ) . ' </i>';

						}

						$_html .= '</' . $ssb_element_tag . '>';

					} else {
						$twitter_url = ssb_twitter_share_link( $permalink, rawurldecode( $title ), $twitter_handle );
						$_html       = '<' . $ssb_element_tag . ' class="simplesocial-twt-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Twitter/X Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . esc_url( $twitter_url ) . '"' . $ssb_ck_popup . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Twitter/X', 'simple-social-buttons' ) . '</span> ';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_twitter_counter">' . ssb_count_format( $twitter_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons_code[] = $_html;

					break;
				case 'copylink':
					$copylink_share = ( isset( $share_counts['copylink'] ) && $share_counts['copylink'] > 0 ) ? $share_counts['copylink'] : 0;
					$copylink_text  = apply_filters( 'ssb_copylink_text', __( 'Copy', 'simple-social-buttons' ) );
					if ( 'simple-icons' === $theme ) {
						$copylink_js       = 'ssb_copy_share_link(this); return false;';
						$copylink_svg_path = 'M14,5.55A.8.8,0,0,0,14,5.34V5.26A.74.74,0,0,0,13.81,5L9.14.24A1.07,1.07,0,0,0,8.92.09H8.85A.62.62,0,0,0,8.59,0H5.44A2.32,2.32,0,0,0,
						3.79.7a2.47,2.47,0,0,0-.68,1.7v.8H2.33a2.3,2.3,0,0,0-1.65.7A2.47,2.47,0,0,0,0,5.6v8a2.47,2.47,0,0,0,.68,1.7,2.3,2.3,0,0,0,1.65.7H8.56a2.32,2.32,0,0,0,
						1.65-.7,2.47,2.47,0,0,0,.68-1.7v-.8h.78a2.3,2.3,0,0,0,1.65-.7A2.47,2.47,0,0,0,14,10.4V5.55ZM9.33,2.73l2,2.07H10.11a.76.76,0,0,1-.55-.23A.86.86,0,0,1,9.33,
						4Zm0,10.87a.85.85,0,0,1-.22.57.78.78,0,0,1-.55.23H2.33a.78.78,0,0,1-.55-.23.85.85,0,0,1-.22-.57v-8A.85.85,0,0,1,1.78,5a.78.78,0,0,1,.55-.23h.78v5.6a2.47,
						2.47,0,0,0,.68,1.7,2.32,2.32,0,0,0,1.65.7H9.33Zm3.11-3.2a.85.85,0,0,1-.22.57.78.78,0,0,1-.55.23H5.44A.78.78,0,0,1,4.89,11a.85.85,0,0,1-.22-.57v-8a.85.85,
						0,0,1,.22-.57.78.78,0,0,1,.55-.23H7.78V4a2.42,2.42,0,0,0,.68,1.7,2.3,2.3,0,0,0,1.65.7h2.33Z';
						$_html             = '<' . $ssb_element_tag . ' class="ssb_copylink-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Copy Link', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '=" ' . $permalink . '"  '
							. $ssb_click_attr . '="' . $copylink_js . '">'
							. '<span class="icon"><svg aria-hidden="true" focusable="false" id="Layer_1" data-name="Layer 1"'
							. ' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 16">'
							. '<path d="' . $copylink_svg_path . '" fill="#fff"/></svg></span>'
							. '<span class="simplesocialtxt">' . esc_html( $copylink_text ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $copylink_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';

					} else {
						$copylink_js = 'ssb_copy_share_link(this); return false;';
						$_html       = '<' . $ssb_element_tag . ' class="simplesocial-copy-link copy-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Copy Link', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '=" ' . $permalink . '"  '
							. $ssb_click_attr . '="' . $copylink_js . '">'
							. '<span class="simplesocialtxt">' . esc_html( $copylink_text ) . '</span>'
							. '<span class="ssb_tooltip">' . esc_html__( 'Copied', 'simple-social-buttons' ) . '</span> ';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_copylink_counter">' . ssb_count_format( $copylink_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons_code[] = $_html;

					break;
				case 'linkedin':
					$linkedin_share = ( isset( $share_counts['linkedin'] ) && $share_counts['linkedin'] > 0 ) ? $share_counts['linkedin'] : 0;
					if ( 'simple-icons' === $theme ) {
						$linkedin_url    = ssb_linkdin_share_link( $permalink );
						$linkedin_path_1 = 'M-296.2,401.6c0-3.2,0-6.3,0-9.5h0.1c1,0,2,0,2.9,0c0.1,0,0.1,0,0.1,0.1c0,0.4,0,0.8,0,1.2 c0.1-0.1,0.2-0.3,0.3-0.4c0.5-0.7,
						1.2-1,2.1-1.1c0.8-0.1,1.5,0,2.2,0.3c0.7,0.4,1.2,0.8,1.5,1.4c0.4,0.8,0.6,1.7,0.6,2.5 c0,1.8,0,3.6,0,5.4v0.1c-1.1,0-2.1,0-3.2,0c0-0.1,
						0-0.1,0-0.2c0-1.6,0-3.2,0-4.8c0-0.4,0-0.8-0.2-1.2c-0.2-0.7-0.8-1-1.6-1 c-0.8,0.1-1.3,0.5-1.6,1.2c-0.1,0.2-0.1,0.5-0.1,0.8c0,1.7,0,3.4,
						0,5.1c0,0.2,0,0.2-0.2,0.2c-1,0-1.9,0-2.9,0 C-296.1,401.6-296.2,401.6-296.2,401.6z';
						$linkedin_path_2 = 'M-298,401.6L-298,401.6c-1.1,0-2.1,0-3,0c-0.1,0-0.1,0-0.1-0.1c0-3.1,0-6.1,0-9.2 c0-0.1,0-0.1,0.1-0.1c1,0,2,0,2.9,
						0h0.1C-298,395.3-298,398.5-298,401.6z';
						$linkedin_path_3 = 'M-299.6,390.9c-0.7-0.1-1.2-0.3-1.6-0.8c-0.5-0.8-0.2-2.1,1-2.4c0.6-0.2,1.2-0.1,1.8,0.2 c0.5,0.4,0.7,0.9,0.6,1.5c-0.1,0.7-0.5,1.1-1.1,
						1.3C-299.1,390.8-299.4,390.8-299.6,390.9L-299.6,390.9z';
						$_html           = '<' . $ssb_element_tag . ' class="ssb_linkedin-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'LinkedIn Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $linkedin_url . '"' . $ssb_ck_popup . ' >'
							. '<span class="icon"><svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"'
							. ' width="15" height="14.1" viewBox="-301.4 387.5 15 14.1"'
							. ' xml:space="preserve">'
							. '<g fill="#FFFFFF">'
							. '<path d="' . $linkedin_path_1 . '"/>'
							. '<path d="' . $linkedin_path_2 . '"/>'
							. '<path d="' . $linkedin_path_3 . '"/>'
							. '</g></svg></span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Share', 'simple-social-buttons' ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $linkedin_share ) . '</span>';
						}
						$_html .= ' </' . $ssb_element_tag . '>';
					} else {
						$linkedin_url = ssb_linkdin_share_link( $permalink );
						$_html        = '<' . $ssb_element_tag . ' ' . $ssb_attr_html
							. ' class="simplesocial-linkedin-share" aria-label="' . esc_attr__( 'LinkedIn Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $linkedin_url . '"' . $ssb_ck_popup . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'LinkedIn', 'simple-social-buttons' ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_linkedin_counter">' . ssb_count_format( $linkedin_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons_code[] = $_html;

					break;
				case 'pinterest':
					$pinterest_share = ( isset( $share_counts['pinterest'] ) && $share_counts['pinterest'] > 0 ) ? $share_counts['pinterest'] : 0;
					$pin_page_url    = $ssb_share_page_url ? $ssb_share_page_url : $permalink;
					$pin_media       = ssb_get_pinterest_media_url( $ssb_share_post_id, $ssb_share_media );
					// $title is rawurlencoded for normal posts; decode for the helper.
					$pin_description = rawurldecode( (string) $title );
					$pinterest_url   = ssb_pinterest_share_link( $pin_page_url, $pin_media, $pin_description );
					// With media, open the create/button URL directly; otherwise use pinmarklet picker.
					$pinterest_action = ( '' !== $pin_media ) ? $ssb_ck_popup : $ssb_ck_pinterest;

					if ( 'simple-icons' === $theme ) {
						$pinterest_svg_path_1 = 'M29.449,14.662 C29.449,22.722 22.868,29.256 14.75,29.256 C6.632,29.256 0.051,22.722 0.051,14.662 C0.051,6.601 6.632,0.067 14.75,
						0.067 C22.868,0.067 29.449,6.601 29.449,14.662';
						$pinterest_svg_path_2 = 'M14.733,1.686 C7.516,1.686 1.665,7.495 1.665,14.662 C1.665,20.159 5.109,24.854 9.97,26.744 C9.856,25.718 9.753,24.143 10.016,
						23.022 C10.253,22.01 11.548,16.572 11.548,16.572 C11.548,16.572 11.157,15.795 11.157,14.646 C11.157,12.842 12.211,11.495 13.522,11.495 C14.637,
						11.495 15.175,12.326 15.175,13.323 C15.175,14.436 14.462,16.1 14.093,17.643 C13.785,18.935 14.745,19.988 16.028,19.988 C18.351,19.988 20.136,
						17.556 20.136,14.046 C20.136,10.939 17.888,8.767 14.678,8.767 C10.959,8.767 8.777,11.536 8.777,14.398 C8.777,15.513 9.21,16.709 9.749,17.359 C9.856,
						17.488 9.872,17.6 9.84,17.731 C9.741,18.141 9.52,19.023 9.477,19.203 C9.42,19.44 9.288,19.491 9.04,19.376 C7.408,18.622 6.387,16.252 6.387,14.349 C6.387,
						10.256 9.383,6.497 15.022,6.497 C19.555,6.497 23.078,9.705 23.078,13.991 C23.078,18.463 20.239,22.062 16.297,22.062 C14.973,22.062 13.728,21.379 13.302,
						20.572 C13.302,20.572 12.647,23.05 12.488,23.657 C12.193,24.784 11.396,26.196 10.863,27.058 C12.086,27.434 13.386,27.637 14.733,27.637 C21.95,27.637 27.801,
						21.828 27.801,14.662 C27.801,7.495 21.95,1.686 14.733,1.686';
						$_html                = ' <' . $ssb_element_tag . ' class="ssb_pinterest-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Pinterest Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . esc_url( $pinterest_url ) . '"' . $pinterest_action . '>'
							. '<span class="icon"> <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"'
							. ' height="30px" width="30px" viewBox="-1 -1 31 31"><g>'
							. '<path d="' . $pinterest_svg_path_1 . '" fill="#fff" stroke="#fff" stroke-width="1"></path>'
							. '<path d="' . $pinterest_svg_path_2 . '" fill="#bd081c"></path>'
							. '</g></svg> </span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Pinterest', 'simple-social-buttons' ) . '</span>';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $pinterest_share ) . '</span>';
						}
						$_html .= ' </' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . ' class="simplesocial-pinterest-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Pinterest Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . esc_url( $pinterest_url ) . '"'
							. $pinterest_action . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Pinterest', 'simple-social-buttons' ) . '</span>';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_pinterest_counter">' . ssb_count_format( $pinterest_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons_code[] = $_html;

					break;
				case 'totalshare':
					$total_share = ( isset( $share_counts['total'] ) && $share_counts['total'] > 0 ) ? $share_counts['total'] : 0;
					$total_html  = "<span class='ssb_total_counter'>" . ssb_count_format( $total_share ) . '<span>' . esc_html__( 'Shares', 'simple-social-buttons' ) . '</span></span>';
					if ( $defer_totalshare_after_overflow ) {
						$deferred_totalshare_html = $total_html;
						break;
					}
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons_code[] = $total_html;
					break;
				case 'reddit':
					$reddit_score = ( isset( $share_counts['reddit'] ) && $share_counts['reddit'] > 0 ) ? $share_counts['reddit'] : 0;

					if ( 'simple-icons' === $theme ) {
						$reddit_url      = 'https://reddit.com/submit?url=' . $permalink . '&title=' . $title;
						$reddit_svg_path = 'M307.523,231.062c1.11,2.838,1.614,5.769,1.614,8.681c0,5.862-2.025,11.556-5.423,16.204 c-3.36,4.593-8.121,8.158-13.722,
						9.727h0.01c-0.047,0.019-0.094,0.019-0.117,0.037c-0.023,0-0.061,0.019-0.079,0.019 c-2.623,0.896-5.312,1.316-7.98,1.316c-6.254,
						0-12.396-2.254-17.306-6.096c-4.872-3.826-8.56-9.324-9.717-15.845h-0.01 c0-0.019,0-0.042-0.009-0.069c0-0.019,
						0-0.038-0.019-0.065h0.019c-0.364-1.681-0.551-3.36-0.551-5.021 c0-5.647,1.923-11.07,5.097-15.551c3.164-4.453,7.626-7.99,12.848-9.811c0.019,0,0.038-0.01,
						0.038-0.01 c0.027,0,0.027-0.027,0.051-0.027c2.954-1.092,6.072-1.639,9.157-1.639c5.619,0,11.154,1.704,15.821,4.821 c4.611,3.066,8.354,7.561,10.23,
						13.143c0.019,0.037,0.019,0.07,0.037,0.103c0,0.037,0.019,0.057,0.037,0.084H307.523z M290.329,300.349c-2.202-1.428-4.751-2.291-7.448-2.291c-2.175,0-4.434,
						0.621-6.445,1.955l0,0 c-19.004,11.342-41.355,17.558-63.547,17.558c-16.65,
						0-33.199-3.514-48.192-10.879l-0.077-0.037l-0.075-0.028 
						c-2.261-0.924-4.837-2.889-7.647-4.76c-1.428-0.925-2.919-1.844-4.574-2.521c-1.633-0.695-3.447-1.181-5.386-1.181 c-1.605,0-3.292,0.359-4.957,1.115c-0.086,
						0.033-0.168,0.065-0.252,0.098h0.009c-2.616,0.999-4.66,2.829-5.974,4.994 c-1.372,2.23-2.046,4.826-2.046,7.411c0,2.334,0.551,4.667,1.691,6.786c1.085,2.007,
						2.754,3.762,4.938,4.938 c21.429,14.454,46.662,21.002,71.992,20.979c22.838,0,45.814-5.287,66.27-14.911l0.107-0.065l0.103-0.056 c2.697-1.597,6.282-3.029,
						9.661-5.115c1.671-1.064,3.304-2.296,4.704-3.897c1.4-1.591,2.525-3.551,3.16-5.875v-0.01 c0.266-1.026,0.392-2.025,
						0.392-3.024c0-1.899-0.467-3.701-1.241-5.32C294.361,303.775,292.504,301.778,290.329,300.349z M139.875,265.589c0.037,0,0.086,0.014,0.128,0.037c2.735,0.999,
						5.554,1.493,8.345,1.493c6.963,0,13.73-2.852,18.853-7.5 c5.115-4.662,8.618-11.257,8.618-18.775c0-0.196,0-0.392-0.009-0.625c0.019-0.336,0.028-0.705,
						0.028-1.083 c0-7.458-3.456-14.08-8.522-18.762c-5.085-4.686-11.836-7.551-18.825-7.551c-1.867,0-3.769,0.219-5.628,0.653 c-0.028,0-0.049,0.009-0.077,0.009c0,
						0-0.019,0-0.028,0c-9.252,1.937-17.373,8.803-20.37,18.248l0,0v0.01 c0,0.019-0.009,0.037-0.009,0.037c-0.861,2.586-1.262,5.255-1.262,7.896c0,5.787,1.913,
						11.426,5.211,16.064 c3.269,4.56,7.894,8.145,13.448,9.819C139.816,265.561,139.835,265.571,139.875,265.589z M430.033,198.094v0.038 c0.066,0.94,0.084,1.878,
						0.084,2.81c0,10.447-3.351,20.493-8.941,29.016c-5.218,7.976-12.414,14.649-20.703,19.177 c0.532,4.158,0.84,8.349,0.84,12.526c-0.01,22.495-7.766,44.607-21.272,
						62.329v0.009h-0.028 c-24.969,33.216-63.313,52.804-102.031,62.684h-0.01l-0.027,0.023c-20.647,5.013-41.938,7.574-63.223,7.574 c-31.729,
						0-63.433-5.722-93.018-17.585l-0.009-0.028h-0.028c-30.672-12.643-59.897-32.739-77.819-62.184 c-9.642-15.71-14.935-34.141-14.935-52.659c0-4.19,0.283-8.387,
						0.843-12.536c-8.072-4.545-15.063-10.99-20.255-18.687 c-5.542-8.266-9.056-17.95-9.5-28.187v-0.04v-0.037v-0.082c0.009-14.337,6.237-27.918,
						15.915-37.932 c9.677-10.011,22.896-16.554,37.075-16.554c0.196,0,0.392,0,0.588,0c1.487-0.101,2.987-0.159,4.488-0.159 c7.122,0,14.26,1.153,21.039,3.752l0.037,
						0.028l0.038,0.012c5.787,2.437,11.537,5.377,16.662,9.449 c1.661-0.871,3.472-1.851,5.504-2.625c31.064-18.395,67.171-25.491,102.358-27.538c0.306-17.431,
						2.448-35.68,10.949-51.65 c7.08-13.269,19.369-23.599,34-27.179l0.061-0.03l0.079-0.009c5.573-1.078,11.192-1.575,16.774-1.575 c14.869,0,29.561,3.521,43.31,
						9.017c6.086-9.185,14.776-16.354,24.97-20.375l0.098-0.056l0.098-0.037 c5.983-1.864,12.303-2.954,18.646-2.954c6.692,0,13.437,1.223,19.756,4.046v-0.023c0.009,
						0.023,0.019,0.023,0.019,0.023 c0.047,0.016,0.084,0.044,0.116,0.044c9.059,3.489,16.727,9.937,22.164,17.95c5.442,8.048,8.644,
						17.688,8.644,27.599 c0,1.827-0.103,
						3.657-0.317,5.489l-0.019,0.037c0,0.028,0,0.068-0.01,0.096c-1.063,12.809-7.551,24.047-16.736,32.063 c-9.24,8.048-21.207,12.909-33.49,12.909c-1.97,
						0-3.958-0.11-5.937-0.374c-12.182-0.931-23.541-6.826-31.886-15.595 c-8.373-8.755-13.768-20.453-13.768-33.08c0-0.611,0.056-1.237,
						0.074-1.843c-11.435-5.092-23.578-9.316-35.646-9.306 c-1.746,0-3.491,0.096-5.237,0.273h-0.019c-9.035,0.871-17.436,6.566-21.506,14.757v0.009v0.028 c-6.179,
						12.034-7.411,26.101-7.598,40.064c34.639,2.259,69.483,10.571,100.043,28.138h0.047l0.438,0.259 c0.579,0.343,1.652,0.931,2.623,1.449c2.101-1.704,4.322-3.456,
						6.856-4.966c9.264-6.17,20.241-9.238,31.223-9.238 c4.872,0,9.749,0.621,14.481,1.834h0.019l0.196,0.058c0.07,0.01,0.121,0.033,0.178,0.033v0.009 c11.183,2.845,
						21.3,9.267,28.917,17.927c7.612,8.674,12.731,19.648,13.73,31.561v0.025H430.033z M328.002,84.733 c0,0.469,0.01,0.95,0.057,1.44v0.028v0.056c0.224,6.018,3.065,
						11.619,7.383,15.756c4.34,4.14,10.1,6.702,15.942,6.725h0.08h0.079 c0.42,0.033,0.85,0.033,1.26,0.033c5.899,0.009,11.752-2.532,16.148-6.655c4.405-4.144,
						7.309-9.78,7.542-15.849l0.009-0.028v-0.037 c0.038-0.464,0.057-0.903,
						0.057-1.377c0-6.247-2.922-12.202-7.496-16.612c-4.555-4.406-10.688-7.136-16.735-7.12 c-1.951,
						0-3.884,0.266-5.778,0.854l-0.065,0.005l-0.056,0.023c-4.984,1.295-9.656,4.368-13.012,8.449 C330.046,74.486,328.002,79.508,328.002,84.733z M72.312,
						177.578c-4.63-2.156-9.418-3.696-14.15-3.676 c-0.794,0-1.597,0.047-2.39,0.133h-0.11l-0.11,0.014c-6.795,0.187-13.653,3.15-18.801,7.899 c-5.152,4.732-8.559,
						11.122-8.821,18.167v0.065l-0.012,0.058c-0.046,0.57-0.065,1.137-0.065,1.683 c0,4.345,1.333,8.545,3.593,12.368c1.673,2.847,3.867,5.441,6.348,7.701C45.735,
						204.602,58.142,189.845,72.312,177.578z M374.066,262.635c0-15.5-5.592-31.069-14.646-43.604c-18.053-25.119-46.055-41.502-75.187-50.636l-0.205-0.072 
						c-5.592-1.715-11.238-3.234-16.933-4.534c-17.025-3.876-34.48-5.806-51.917-5.806c-23.414,0-46.827,3.465-69.245,10.379 c-29.125,9.243-57.221,25.51-75.233,
						50.71v0.019c-9.129,12.587-14.475,28.208-14.475,43.763c0,5.727,0.716,11.453,2.23,17.025 l0.019,0.01c3.278,12.508,9.689,23.671,17.989,33.393c8.295,9.745,
						18.472,18.058,29.176,24.839c2.371,1.47,4.751,2.87,7.187,4.237 c31.094,17.356,66.898,24.964,102.445,24.964c6.012,0,12.06-0.214,18.033-0.644c35.797-2.959,
						71.742-13.525,100.8-35.115 l0.01-0.023c9.25-6.837,17.818-15.112,24.595-24.525c6.805-9.418,11.789-19.947,14.002-31.382V275.6l0.009-0.01 C373.627,271.32,
						374.066,266.985,374.066,262.635z M402.32,200.95c-0.009-3.762-0.868-7.507-2.753-11l-0.047-0.044l-0.019-0.056 
						c-2.521-5.19-6.479-9.11-11.248-11.782c-4.77-2.69-10.352-4.056-15.952-4.056c-5.063,0-10.1,1.132-14.57,3.379 c14.216,12.344,26.687,27.179,34.746,
						44.636c2.595-2.259,4.808-5.018,6.464-8.084C401.098,209.92,402.32,205.405,402.32,200.95z';
						$_html           = ' <' . $ssb_element_tag . ' class="ssb_reddit-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Reddit Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $reddit_url . '"' . $ssb_ck_popup . '>'
							. '<span class="icon"> <svg aria-hidden="true" focusable="false" version="1.1" id="Capa_1"'
							. ' xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"'
							. ' width="430.117px" height="430.117px" viewBox="0 0 430.117 430.117"'
							. ' style="enable-background:new 0 0 430.117 430.117;" xml:space="preserve">'
							. ' <g> <path id="reddit" d="' . $reddit_svg_path . '"/></svg></span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Reddit', 'simple-social-buttons' ) . ' </span>';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $reddit_score ) . '</span>';
						}

						$_html .= '</' . $ssb_element_tag . '>';
					} else {
						$reddit_url = 'https://reddit.com/submit?url=' . $permalink . '&title=' . $title;
						$_html      = '<' . $ssb_element_tag . ' class="simplesocial-reddit-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Reddit Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $reddit_url . '"' . $ssb_ck_popup . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Reddit', 'simple-social-buttons' ) . '</span> ';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_reddit_counter">' . ssb_count_format( $reddit_score ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';

					}

					$arr_buttons_code[] = $_html;
					break;
				case 'whatsapp':
					$whatsapp_share = ( isset( $share_counts['whatsapp'] ) && $share_counts['whatsapp'] > 0 ) ? $share_counts['whatsapp'] : 0;
					if ( 'simple-icons' === $theme ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$whatsapp_url       = ssb_whats_app_share_link( $permalink );
						$whatsapp_svg_path  = 'M90,43.841c0,24.213-19.779,43.841-44.182,43.841c-7.747,0-15.025-1.98-21.357-5.455L0,90l7.975-23.522
						 c-4.023-6.606-6.34-14.354-6.34-22.637C1.635,19.628,21.416,0,45.818,0C70.223,0,90,19.628,90,43.841z M45.818,6.982   c-20.484,0-37.146,
						 16.535-37.146,36.859c0,8.065,2.629,15.534,7.076,21.61L11.107,79.14l14.275-4.537   c5.865,3.851,12.891,6.097,20.437,6.097c20.481,0,
						 37.146-16.533,37.146-36.857S66.301,6.982,45.818,6.982z M68.129,53.938 
						 c-0.273-0.447-0.994-0.717-2.076-1.254c-1.084-0.537-6.41-3.138-7.4-3.495c-0.993-0.358-1.717-0.538-2.438,0.537 
						 c-0.721,1.076-2.797,3.495-3.43,4.212c-0.632,0.719-1.263,0.809-2.347,0.271c-1.082-0.537-4.571-1.673-8.708-5.333  
						c-3.219-2.848-5.393-6.364-6.025-7.441c-0.631-1.075-0.066-1.656,0.475-2.191c0.488-0.482,1.084-1.255,1.625-1.882  
						c0.543-0.628,0.723-1.075,1.082-1.793c0.363-0.717,0.182-1.344-0.09-1.883c-0.27-0.537-2.438-5.825-3.34-7.977 
						c-0.902-2.15-1.803-1.792-2.436-1.792c-0.631,0-1.354-0.09-2.076-0.09c-0.722,0-1.896,0.269-2.889,1.344 
						c-0.992,1.076-3.789,3.676-3.789,8.963c0,5.288,3.879,10.397,4.422,11.113c0.541,0.716,7.49,11.92,18.5,16.223 
						C58.2,65.771,58.2,64.336,60.186,64.156c1.984-0.179,6.406-2.599,7.312-5.107C68.398,56.537,68.398,54.386,68.129,53.938z';
						$arr_buttons_code[] = ' <' . $ssb_element_tag . ' class="ssb_whatsapp-icon simplesocial-whatsapp-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'WhatsApp Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $whatsapp_url . '"' . $ssb_ck_blank . '>'
							. '<span class="icon"> <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1"'
							. ' x="0px" y="0px" width="512px" height="512px" viewBox="0 0 90 90"'
							. ' style="enable-background:new 0 0 90 90;" xml:space="preserve" class="">'
							. '<g><g> <path id="WhatsApp" d="' . $whatsapp_svg_path . '"/> </g></g> </svg> </span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'WhatsApp', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $whatsapp_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					} else {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$whatsapp_url       = ssb_whats_app_share_link( $permalink );
						$arr_buttons_code[] = '<' . $ssb_element_tag . ' class="simplesocial-whatsapp-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'WhatsApp Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $whatsapp_url . '"' . $ssb_ck_blank . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'WhatsApp', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter ssb_whatsapp_counter">' . ssb_count_format( $whatsapp_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					}
					break;
				case 'viber':
					$viber_share = ( isset( $share_counts['viber'] ) && $share_counts['viber'] > 0 ) ? $share_counts['viber'] : 0;
					if ( 'simple-icons' === $theme ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$viber_url          = ssb_viber_share_link( $permalink );
						$viber_svg_path     = 'M20.812 2.343c-.596-.549-3.006-2.3-8.376-2.325 0 0-6.331-.38-9.415 2.451C1.302 4.189.698 6.698.634 9.82.569 12.934.487 18.774 6.12
						 20.36h.005l-.005 2.416s-.034.979.609 1.178c.779.24 1.236-.504 1.98-1.303.409-.439.972-1.088 1.397-1.582 3.851.322 6.813-.416 7.149-.525.777-.254
						  5.176-.816 5.893-6.658.738-6.021-.357-9.83-2.338-11.547v.004zm.652 11.112c-.615 4.876-4.184 5.187-4.83 5.396-.285.092-2.895.738-6.164.525 0 0-2.445
						   2.941-3.195 3.705-.121.121-.271.166-.361.145-.135-.029-.164-.18-.164-.404l.015-4.006c-.015 0 0 0 0
						    0-4.771-1.336-4.485-6.301-4.425-8.91.044-2.596.538-4.726 1.994-6.167 2.611-2.371 7.997-2.012 7.997-2.012 4.543.016 6.721 1.385 7.223 1.846 1.674 1.432
							 2.529 4.865 1.904 9.893l.006-.011zM7.741 4.983c.242 0 .459.109.629.311.004.002.58.695.83 1.034.235.32.551.83.711 1.115.285.51.104 1.032-.172
							  1.248l-.566.45c-.285.229-.25.653-.25.653s.84 3.157 3.959 3.953c0 0 .426.039.654-.246l.451-.569c.213-.285.734-.465 1.244-.181.285.15.795.466
							   1.116.704.339.24 1.032.826 1.036.826.33.271.404.689.18 1.109v.016c-.23.405-.541.78-.934 1.141h-.008c-.314.27-.629.42-.944.449-.03 0-.075.016-.136
							    0-.135 0-.27-.029-.404-.061v-.014c-.48-.135-1.275-.48-2.596-1.216-.855-.479-1.574-.96-2.189-1.455-.315-.255-.645-.54-.976-.87l-.076-.028-.03-.03
								-.029-.029c-.331-.33-.615-.66-.871-.98-.48-.609-.96-1.327-1.439-2.189-.735-1.32-1.08-2.115-1.215-2.596H5.7c-.045-.134-.075-.269-.06-.404-.015-.061
								 0-.105 0-.141.03-.299.189-.614.458-.944h.005c.355-.39.738-.704 1.146-.933.164-.091.329-.135.479-.135h.016l-.003.012zm4.095-.683h.116l.076.002h.02l
								 .089.005h.511l.135.015h.074l.15.016h.03l.104.015h.016l.074.015c.046 0 .076.016.105.016h.091l.075.029.06.016.06.015.03.015h.045l.046.016h.029l.074.
								 016.045.014.046.016.06.016.03.014c.03 0 .06.016.091.016l.044.015.046.016.119.044.061.031.135.06.045.015.045.016.09.045.061.015.029.015.076.031.029
								 .014.061.031.045.014.045.03.059.03.046.029.03.016.061.03.044.03.075.045.045.016.074.044.016.015.045.031.09.074.046.03.044.03.031.014.045.031.074.
								 074.061.045.045.03.016.015.029.016.074.061.046.044.03.03.045.029.045.031.029.015.12.12.06.061.135.135.031.029c.016.016.045.045.061.075l.029.03.166
								 .194.045.06c.014.016.014.031.029.031l.09.135.045.045.09.12.076.12.045.09.059.105.045.09.016.029.029.061.076.15.074.149.031.075c.059.135.104.27.164
								 .42.074.195.135.404.18.63.045.165.076.315.105.48l.029.27.045.3c.016.121.031.256.031.375.014.121.014.24.014.359v.256c0 .016-.006.029-.014.045-.016.
								 03-.031.045-.061.075-.021.015-.049.046-.08.046-.029.014-.059.014-.09.014h-.045c-.029 0-.059-.014-.09-.029-.029-.016-.061-.03-.074-.061-.016-.029-
								 .045-.061-.061-.09s-.031-.06-.031-.09v-.359c-.014-.209-.029-.425-.059-.639-.016-.146-.045-.284-.061-.42 0-.074-.016-.146-.029-.209l-.029-.15-.038
								 -.141-.016-.09-.045-.15c-.029-.12-.074-.24-.119-.36-.029-.091-.061-.165-.105-.239l-.029-.076-.135-.27-.031-.045c-.061-.135-.135-.27-.225-.391l-.0
								 45-.074h-.201l-.064-.091c-.055-.089-.114-.165-.18-.239l-.125-.15-.015-.016-.046-.057-.035-.045-.075-.074-.015-.03-.07-.06-.045-.046-.083-.075-.04-
								 .037-.046-.045-.015-.016c-.016-.015-.045-.045-.075-.06l-.076-.062-.03-.015-.061-.046-.074-.06-.045-.036-.03-.016-.06-.053c0-.016-.016-.016-.031-.0
								 16l-.029-.029-.015-.016v-.013l-.03-.014-.061-.037-.044-.031-.075-.045-.06-.045-.029-.016-.032-.013h-.09l-.019-.016-.065-.035-.009-.014-.03-.016-.0
								 45-.021h-.012l-.045-.016-.025-.015-.045-.015-.01-.011-.03-.016-.053-.029-.03-.015-.09-.03-.074-.029-.137-.016-.044-.029c-.015-.01-.03-.016-.046-.0
								 16l-.029-.015c-.029-.011-.045-.016-.075-.03l-.03-.016h-.029l-.061-.029-.029-.016-.045-.015h-.092c-.008 0-.019-.005-.03-.007h-.09l-.045-.016h-.015l
								 -.045-.016h-.041c-.025-.014-.045-.014-.07-.014l-.01-.016-.06-.015c-.03-.016-.056-.016-.084-.016l-.045-.015-.05-.016-.045-.014-.061-.016h-.061l-.17
								 9-.022h-.09l-.116-.015h-.076l-.068-.008h-.03l-.054-.016h-.285l-.01-.015h-.061c-.03 0-.064-.015-.09-.03-.03-.016-.061-.029-.081-.06l-.03-.046c-.029
								 -.029-.029-.06-.045-.09-.014-.028-.014-.059-.014-.089s0-.06.015-.09c.016-.029.029-.06.061-.075.015-.03.044-.044.074-.06.029-.016.061-.03.09-.03h.
								 061l.015.066zm.554 1.574l.037.003.061.006c.008 0 .018 0 .029.003.022 0 .045.004.075.006l.06.008.024.016.045.015.048.015.045.016h.03l.042.015.07.01
								 5.056.016.026.014h.073l.119.028.046.015.045.015.045.016s.015 0 .015.015l.046.015.044.016.045.016c.015 0 .03.014.046.014.007 0 .014.016.025.016l.0
								 64.03h.029l.09.03.05.029.046.03.108.045.06.015.031.031c.045.014.09.044.135.059l.048.03.048.03.049.029c.045.03.082.046.121.076l.029.014.041.031.02
								 2.015.075.045.037.03.065.043.029.015.03.015.046.03.06.046c.015.014.022.014.034.029.01.015.016.015.025.03l.033.03.036.029.03.03.046.046.029.03.016
								 .016.09.089.016.016c0 .015.015.03.029.03l.016.013.045.046.029.045.03.03.045.06.046.046.09.119.014.029.061.076.016.029.015.031.015.029.016.03c.016
								 .015.016.03.029.06l.043.076.016.015.029.061.031.044c.014.015.014.029.029.045l.03.045.03.061.029.059.016.046c.015.044.045.075.06.12 0 .015.015.029
								 .015.045l.045.119.061.195c0 .016.015.045.015.061l.046.135.044.18.046.24c.014.074.014.135.029.211.016.119.03.238.03.359l.015.21v.165c0 .016 0 .029
								 -.015.045l-.044.043c-.029.023-.045.045-.074.061-.03.015-.061.029-.09.04-.031.016-.075.016-.105.016-.029 0-.061-.016-.09-.03-.016 0-.03-.016-.045
								 -.021-.031-.014-.061-.039-.075-.065-.03-.03-.046-.06-.046-.091l-.014-.044v-.313c0-.133-.016-.256-.031-.385-.015-.135-.044-.285-.074-.42-.029-.09
								 -.045-.18-.075-.26l-.03-.091-.029-.075-.016-.03-.045-.12-.045-.09-.075-.149-.069-.12v-.019l-.029-.047-.03-.038-.045-.075-.046-.061-.089-.119c-.0
								 46-.061-.09-.12-.142-.178-.014-.015-.029-.029-.029-.045l-.03-.029-.017-.016-.03-.014-.03-.027v-.146l-.119-.113-.075-.068v-.014l-.03-.031-.038-.0
								 29-.015-.016c0-.015-.016-.015-.029-.015l-.046-.016-.015-.015-.061-.045-.014-.016-.016-.015c-.012-.015-.023-.015-.03-.015l-.06-.045-.016-.016-.06
								 -.029-.011-.016-.045-.029-.03-.016-.03-.029-.029-.031h-.016c-.029-.029-.06-.044-.105-.06l-.044-.03-.03-.014-.016-.016-.045-.03-.044-.015-.06-.03
								 -.046-.015-.015-.016-.056-.014v-.012l-.091-.03-.06-.03-.03-.015h-.06c-.03-.015-.045-.015-.075-.03H13.2l-.045-.016h-.044l-.046-.014-.029-.016h-.0
								 61l-.061-.015-.029-.016h-.165l-.069-.015H12.3l-.046-.016c-.029-.014-.06-.029-.09-.06-.014-.03-.045-.06-.06-.089-.015-.031-.03-.061-.03-.091v-.09
								 c.006-.046.016-.075.03-.105.008-.015.015-.03.03-.045.018-.03.045-.06.075-.075.015-.015.03-.015.044-.029.031-.016.061-.016.091-.016h.06l-.014.055
								 zm.454 1.629c.015 0 .03 0 .044.004.016 0 .031 0 .046.002l.052.005c.104.009.213.024.318.046l.104.023.026.008.114.029.059.02.046.016c.045.014.091.
								 045.135.06l.016.015.06.03.09.046.029.014c.016.016.031.016.046.03.015.016.045.03.06.045.061.03.105.075.15.105l.105.09.09.091.061.074.029.029.03.0
								 31.044.06.091.135.075.135.06.12.046.105c.044.104.06.195.09.299.029.091.045.196.06.285l.015.15.016.136V9.8c0 .045-.016.075-.03.105-.015.029-.046.
								 074-.075.09-.03.029-.061.045-.105.061-.029.014-.06.014-.09.014-.029 0-.06 0-.09-.014l-.104-.046c-.03-.03-.06-.045-.091-.091-.015-.029-.029-.06-.
								 045-.104v-.166l-.015-.105-.015-.119-.016-.105-.016-.06c0-.015-.014-.045-.014-.06-.03-.121-.09-.24-.15-.36l-.061-.06-.047-.06-.045-.045-.015-.03-
								 .075-.06-.061-.061-.059-.045c-.016-.015-.03-.015-.061-.029l-.09-.061-.061-.03-.029-.015h-.016l-.076-.031-.09-.03-.09-.015h-.075l-.044-.015-.035-
								 .007h-.045l-.06-.016h-.255l-.015-.075h-.039c-.03-.004-.055-.015-.08-.029-.035-.021-.064-.045-.09-.08-.018-.029-.034-.061-.045-.09-.008-.029-.012
								 -.06-.012-.09 0-.037 0-.075.015-.113.015-.039.03-.07.06-.1l.061-.045c.029-.016.061-.03.09-.03l.062-.075h.032z';
						$arr_buttons_code[] = '<' . $ssb_element_tag . ' class="simplesocial-viber-share ssb_viber-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Viber Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $viber_url . '"' . $ssb_ck_self . '>'
							. '<span class="icon"> <svg aria-labelledby="simpleicons-viber-icon" role="img"'
							. ' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'
							. '<title id="simpleicons-viber-icon">' . esc_html__( 'Viber icon', 'simple-social-buttons' ) . '</title>'
							. '<path d="' . $viber_svg_path . '"/></svg> </span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Viber', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $viber_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					} else {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$viber_url          = ssb_viber_share_link( $permalink );
						$arr_buttons_code[] = '<' . $ssb_element_tag . ' class="simplesocial-viber-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Viber Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $viber_url . '"' . $ssb_ck_self . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Viber', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter ssb_viber_counter">' . ssb_count_format( $viber_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					}
					break;
				case 'messenger':
						$messenger_share     = ( isset( $share_counts['messenger'] ) && $share_counts['messenger'] > 0 ) ? $share_counts['messenger'] : 0;
						$link                = rawurlencode( $permalink );
						$messenger_share_url = ssb_is_mobile() ? "fb-messenger://share/?link=$link?app_id=$this->fb_app_id" :
						"http://www.facebook.com/dialog/send?app_id=$this->fb_app_id&redirect_uri=$link&link=$link&display=popup";

					if ( 'simple-icons' === $theme ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$messenger_svg_path = 'M-880.5,1161c-5,0-9,3.8-9,8.5c0,2.4,1,4.5,2.7,6v4.5l3.8-2.3 c0.8,0.2,1.6,0.3,2.5,0.3c5,0,9-3.8,9-8.5S-875.5,1161-880.5,1161z
						 M-879.6,1172.2l-2.4-2.4l-4.3,2.4l4.7-5.2l2.4,2.4l4.2-2.4 L-879.6,1172.2z';
						$arr_buttons_code[] = '<' . $ssb_element_tag . ' class="simplesocial-viber-share ssb_msng-icon" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Facebook Messenger Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . esc_attr( $messenger_share_url ) . '"' . $ssb_ck_messenger . '>'
							. '<span class="icon"> <svg aria-hidden="true" focusable="false" version="1.1" id="Layer_1"'
							. ' xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"'
							. ' width="18px" height="19px" viewBox="-889.5 1161 18 19"'
							. ' enable-background="new -889.5 1161 18 19" xml:space="preserve">'
							. '<path opacity="0.99" fill="#FFFFFF" enable-background="new    "'
							. ' d="' . $messenger_svg_path . '"/>'
							. '</svg> </span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Messenger', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $messenger_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					} else {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$arr_buttons_code[] = '<' . $ssb_element_tag . ' class="simplesocial-msng-share" '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Facebook Messenger Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . esc_attr( $messenger_share_url ) . '"' . $ssb_ck_messenger . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Messenger', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter ssb_messenger_counter">' . ssb_count_format( $messenger_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '> ';
					}
					break;
				case 'email':
					$email_share = ( isset( $share_counts['email'] ) && $share_counts['email'] > 0 ) ? $share_counts['email'] : 0;
					// replace + sign with a space.
					$title = str_replace( '+', ' ', $title );
					if ( 'simple-icons' === $theme ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$email_url          = 'mailto:?subject=' . $title . '&body=' . $permalink;
						$email_svg_path     = 'M-1214.1,1565.2v1l8,4l8-4v-1c0-0.7-0.6-1.3-1.3-1.3h-13.4C-1213.5,1563.9-1214.1,1564.4-1214.1,
						1565.2z M-1214.1,1567.4v7.1c0,0.7,0.6,1.3,1.3,1.3h13.4c0.7,0,1.3-0.6,1.3-1.3v-7.1l-8,4L-1214.1,1567.4z';
						$arr_buttons_code[] = ' <' . $ssb_element_tag . ' class="ssb_email-icon simplesocial-email-share"'
							. ' aria-label="' . esc_attr__( 'Share through Email', 'simple-social-buttons' ) . '" '
							. $ssb_attr_html . ' '
							. $ssb_trigger_attr . '="' . $email_url . '"' . $ssb_ck_mailto . '>'
							. '<span class="icon"> <svg aria-hidden="true" focusable="false" version="1.1" id="Layer_1"'
							. ' xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"'
							. ' width="16px" height="11.9px" viewBox="-1214.1 1563.9 16 11.9"'
							. ' enable-background="new -1214.1 1563.9 16 11.9" xml:space="preserve">'
							. '<path  d="' . $email_svg_path . '"/> </svg> </span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Email', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $email_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					} else {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$email_url          = 'mailto:?subject=' . $title . '&body=' . $permalink;
						$arr_buttons_code[] = '<' . $ssb_element_tag . ' class="simplesocial-email-share" aria-label="' . esc_attr__( 'Share through Email', 'simple-social-buttons' ) . '" '
							. $ssb_attr_html . ' '
							. $ssb_trigger_attr . '="' . $email_url . '"' . $ssb_ck_mailto . '>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Email', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter ssb_email_counter">' . ssb_count_format( $email_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					}
					break;
				case 'print':
					$print_share = ( isset( $share_counts['print'] ) && $share_counts['print'] > 0 ) ? $share_counts['print'] : 0;
					if ( 'simple-icons' === $theme ) {
						$print_button_start = ' <' . $ssb_element_tag . $ssb_ck_print . ' aria-label="' . esc_attr__( 'Print The Post', 'simple-social-buttons' ) . '"'
							. ' class=" ssb_print-icon simplesocial-email-share" '
							. $ssb_attr_html . '>';
						$print_svg_icon     = '<span class="icon"> <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1"'
							. ' x="0px" y="0px" width="16px" height="13.7px"'
							. ' viewBox="-1296.9 1876.4 16 13.7"'
							. ' enable-background="new -1296.9 1876.4 16 13.7" xml:space="preserve"><g>';
						// SVG path data is too long to split further.
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$print_path_1 = '<path fill="#FFFFFF" d="M-1288.9,1879.7c2.3,0,4.6,0,6.9,0c0.4,0,0.7,0.1,0.9,0.5c0.1,0.2,0.1,0.4,0.1,0.6c0,1.7,0,3.4,0,5.1   c0,0.7-0.4,1.1-1.1,1c-0.6,0-1.2,0-1.8,0c-0.1,0-0.2,0-0.2,0.2c0,0.7,0,1.4,0,2c0,0.6-0.4,1-1,1c-0.1,0-0.3,0-0.4,0   c-2.5,0-4.9,0-7.4,0c-0.3,0-0.5,0-0.8-0.1c-0.3-0.2-0.5-0.5-0.5-0.9c0-0.7,0-1.4,0-2c0-0.2-0.1-0.2-0.2-0.2c-0.6,0-1.2,0-1.7,0   c-0.7,0-1-0.4-1-1c0-1.7,0-3.4,0-5.1c0-0.4,0.2-0.8,0.6-0.9c0.2-0.1,0.3-0.1,0.5-0.1C-1293.5,1879.7-1291.2,1879.7-1288.9,1879.7z    M-1288.9,1884.9C-1288.9,1884.9-1288.9,1884.9-1288.9,1884.9c-1.4,0-2.8,0-4.2,0c-0.1,0-0.2,0-0.2,0.2c0,0.3,0,0.7,0,1   c0,1,0,2,0,3c0,0.3,0.1,0.4,0.4,0.4c2.5,0,5.1,0,7.6,0c0.1,0,0.3,0,0.4,0c0.2,0,0.3-0.2,0.3-0.3c0-1.3,0-2.7,0-4   c0-0.2,0-0.2-0.2-0.2C-1286.1,1884.9-1287.5,1884.9-1288.9,1884.9z M-1284.2,1882.4c0.4,0,0.7-0.3,0.7-0.7c0-0.4-0.3-0.7-0.8-0.7   c-0.4,0-0.7,0.3-0.7,0.7C-1284.9,1882.1-1284.6,1882.4-1284.2,1882.4z"/>';
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$print_path_2 = '<path fill="#FFFFFF" d="M-1283.9,1879c-0.2,0-0.4,0-0.5,0c-3.1,0-6.2,0-9.3,0c-0.1,0-0.2,0-0.2-0.2c0-0.5,0-1,0-1.5   c0-0.5,0.4-1,0.9-1c0.1,0,0.2,0,0.3,0c2.6,0,5.2,0,7.8,0c0.6,0,1,0.4,1,1c0,0.5,0,0.9,0,1.4   C-1283.9,1878.9-1283.9,1879-1283.9,1879z"/>';
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$print_path_3 = '<path fill="#FFFFFF" d="M-1291.9,1886.9c0-0.2,0-0.4,0-0.6c2,0,4,0,6,0c0,0.2,0,0.4,0,0.6   C-1287.9,1886.9-1289.9,1886.9-1291.9,1886.9z"/>';
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$print_path_4       = '<path fill="#FFFFFF" d="M-1289.6,1888.2c-0.7,0-1.4,0-2.1,0c-0.1,0-0.2,0-0.2-0.2c0-0.1,0-0.2,0-0.3c0-0.1,0-0.2,0.2-0.2   c0.1,0,0.2,0,0.3,0c1.3,0,2.6,0,3.9,0c0.3,0,0.3,0,0.3,0.3c0,0.4,0,0.4-0.4,0.4C-1288.3,1888.2-1288.9,1888.2-1289.6,1888.2   C-1289.6,1888.2-1289.6,1888.2-1289.6,1888.2z"/>';
						$print_button_end   = '</g></svg></span>'
							. '<span class="simplesocialtxt">' . esc_html__( 'Print', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $print_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
						$arr_buttons_code[] = $print_button_start
							. $print_svg_icon
							. $print_path_1
							. $print_path_2
							. $print_path_3
							. $print_path_4
							. $print_button_end;
					} else {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
						$arr_buttons_code[] = '<' . $ssb_element_tag . $ssb_ck_print . ' '
							. $ssb_attr_html . ' aria-label="' . esc_attr__( 'Print Share', 'simple-social-buttons' ) . '"'
							. ' class="simplesocial-print-share" >'
							. '<span class="simplesocialtxt">' . esc_html__( 'Print', 'simple-social-buttons' ) . '</span>'
							. ( $show_count ? '<span class="ssb_counter ssb_print_counter">' . ssb_count_format( $print_share ) . '</span>' : '' )
							. '</' . $ssb_element_tag . '>';
					}
					break;
				case 'tumblr':
					$tumblr_score = ( isset( $share_counts['tumblr'] ) && $share_counts['tumblr'] > 0 ) ? $share_counts['tumblr'] : 0;

					$link             = rawurlencode( $permalink );
					$tumblr_share_url = esc_url( "http://tumblr.com/widgets/share/tool?canonicalUrl=$link" );
					if ( 'simple-icons' === $theme ) {
						$_html            = '<' . $ssb_element_tag . ' class="ssb_tumblr-icon" '
							. $ssb_attr_html
							. ' aria-label="' . esc_attr__( 'Tumblr Share', 'simple-social-buttons' ) . '" '
							. $ssb_trigger_attr . '="' . $tumblr_share_url . '"' . $ssb_ck_popup . '>';
						$tumblr_svg_start = '<span class="icon"> <svg aria-hidden="true" focusable="false" version="1.1" id="Layer_1"'
							. ' xmlns="http://www.w3.org/2000/svg"'
							. ' xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"'
							. ' width="12.6px" height="17.8px" viewBox="-299.1 388.3 12.6 17.8"'
							. ' style="enable-background:new -299.1 388.3 12.6 17.8;" xml:space="preserve"><g>';
						// SVG path data is too long to split further.
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$tumblr_path = ' <path fill="#FFFFFF" d="M-294.7,388.3c1.1,0,2.1,0,3.2,0c0,1.5,0,2.9,0,4.4c1.7,0,3.3,0,5,0c0,1.1,0,2.2,0,3.4c-1.7,0-3.3,0-5,0 c0,0.1,
						0,0.2,0,0.2c0,1.6,0,3.2,0,4.8c0,1.2,0.6,1.8,1.8,2c1.1,0.1,2.1,0,3-0.5c0.1,0,0.1-0.1,0.2-0.1c0,0.1,0,0.1,0,0.2 c0,0.8,0,1.5,0,2.3c0,0.1,0,0.2-0.2,
						0.3c-1.6,0.6-3.2,0.9-5,0.8c-1-0.1-2-0.3-2.9-0.8c-1.2-0.7-1.8-1.7-1.8-3.1c0-2.1,0-4.1,0-6.2 c0-0.1,0-0.2,0-0.3c-0.9,0-1.8,0-2.7,0c0-0.1,0-0.1,
						0-0.2c0-0.7,0-1.5,0-2.2c0-0.1,0-0.2,0.2-0.2c0.3-0.1,0.7-0.2,1-0.3 c1.6-0.6,2.6-1.8,3-3.5c0-0.1,0.1-0.3,0.1-0.4C-294.8,388.6-294.7,388.4-294.7,
						388.3z"/> </g> </svg> </span>';
						$_html      .= $tumblr_svg_start . $tumblr_path;
						$_html      .= '<span class="simplesocialtxt">' . esc_html__( 'Tumblr', 'simple-social-buttons' ) . ' </span>';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $tumblr_score ) . '</span>';
						}

						$_html .= '</' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . ' class="simplesocial-tumblr-share" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Tumblr Share', 'simple-social-buttons' ) . '" '
						. $ssb_trigger_attr . '="' . $tumblr_share_url . '"' . $ssb_ck_popup . ' ><span class="simplesocialtxt">' . esc_html__( 'Tumblr', 'simple-social-buttons' ) . '</span> ';

						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_tumblr_counter">' . ssb_count_format( $tumblr_score ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';

					}

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					$arr_buttons_code[] = $_html;
					break;
				case 'line':
					$line_share     = ( isset( $share_counts['line'] ) && $share_counts['line'] > 0 ) ? $share_counts['line'] : 0;
					$line_share_url = esc_url( 'https://social-plugins.line.me/lineit/share?url=' . rawurlencode( $permalink ) );

					if ( 'simple-icons' === $theme ) {
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$_html = '<' . $ssb_element_tag . ' class="ssb_line-icon" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Line Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr . '="' . $line_share_url . '"' . $ssb_ck_popup . '>
						<span class="icon"><svg aria-hidden="true" focusable="false" width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6.9C18 3.6 14.1 0 9 0
						4.03 0 0 2.925 0 6.9c0 3.667 3.142 6.697 7.564 7.144a.9.9 0 0 1 .67.397c.107.167.164.36.166.559q-.016.611-.135 1.211a.3.3 0 0 0 .412.341C10.797 15.615
						18 11.76 18 6.9" fill="#fff"/><path d="M5.7 8.55H4.35V5.4a.45.45 0 1 0-.9 0V9a.45.45 0 0 0 .45.45h1.8a.45.45 0 1 0 0-.9m1.2-3.6a.45.45 0 0
						0-.45.45V9a.45.45 0 1 0 .9 0V5.4a.45.45 0 0 0-.45-.45m4.2 0a.45.45 0 0 0-.45.45v2.25L8.775 5.13a.45.45 0 0 0-.81.27V9a.45.45 0 0 0 .885 0V6.75l1.875
						2.52a.45.45 0 0 0 .375.18.45.45 0 0 0 .45-.45V5.4a.45.45 0 0 0-.45-.45m3.3 2.7a.45.45 0 0 0 0-.9h-1.35v-.9h1.35a.45.45 0 0 0 0-.9h-1.8a.45.45 0 0
						0-.45.45V9a.45.45 0 0 0 .45.45h1.8a.45.45 0 0 0 0-.9h-1.35v-.9z" fill="#00cf2e"/></svg></span>
						<span class="simplesocialtxt">' . esc_html__( 'Line', 'simple-social-buttons' ) . '</span>';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter">' . ssb_count_format( $line_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					} else {
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						$_html = '<' . $ssb_element_tag . ' class="simplesocial-line-share" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Line Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr . '="' . $line_share_url . '"' . $ssb_ck_popup . ' ><span class="simplesocialtxt">' . esc_html__( 'Line', 'simple-social-buttons' ) . '</span> ';
						if ( $show_count ) {
							$_html .= '<span class="ssb_counter ssb_line_counter">' . ssb_count_format( $line_share ) . '</span>';
						}
						$_html .= '</' . $ssb_element_tag . '>';
					}

					$arr_buttons_code[] = $_html;
					break;
				case 'mastodon':
					$mastodon_share     = ( isset( $share_counts['mastodon'] ) && $share_counts['mastodon'] > 0 ) ? $share_counts['mastodon'] : 0;
					$mastodon_share_url = esc_url( 'https://mastodon.social/share?text=' . rawurlencode( get_the_title() ) . '&url=' . rawurlencode( $permalink ) );

					if ( 'simple-icons' === $theme ) {
						// phpcs:disable Generic.Files.LineLength.MaxExceeded
						$_html = '<' . $ssb_element_tag . ' class="ssb_mastodon-icon" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Mastodon Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr . '="' . $mastodon_share_url . '"' . $ssb_ck_popup . '>
						<span class="icon"><svg aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M14.6068 9.59183C14.3874 10.7205 12.6419 11.9557 10.6371 12.1951C9.59173 12.3198 8.56246 12.4345 7.46492 12.3841C5.67 12.3019 4.25368 11.9557 4.25368 11.9557C4.25368 12.1304 4.26446 12.2968 4.28601 12.4524C4.51936 14.2238 6.04249 14.3299 7.48527 14.3794C8.9415 14.4292 10.2382 14.0203 10.2382 14.0203L10.298 15.3368C10.298 15.3368 9.27942 15.8838 7.46492 15.9844C6.46436 16.0394 5.222 15.9592 3.775 15.5762C0.636686 14.7456 0.0969679 11.4003 0.0143852 8.00595C-0.0107861 6.99815 0.00473048 6.04784 0.00473048 5.25305C0.00473048 1.78216 2.27886 0.764794 2.27886 0.764794C3.42553 0.238179 5.39312 0.0167234 7.43863 0H7.48889C9.5344 0.0167234 11.5033 0.238179 12.6499 0.764794C12.6499 0.764794 14.9239 1.78216 14.9239 5.25305C14.9239 5.25305 14.9524 7.81389 14.6068 9.59183Z" fill="white"/>
						<path d="M12.2414 5.52235V9.72501H10.5764V5.64587C10.5764 4.786 10.2146 4.34955 9.49093 4.34955C8.69079 4.34955 8.28977 4.86729 8.28977 5.89104V8.12378H6.63459V5.89104C6.63459 4.86729 6.23348 4.34955 5.43335 4.34955C4.70967 4.34955 4.34788 4.786 4.34788 5.64587V9.72501H2.68286V5.52235C2.68286 4.66342 2.90156 3.98086 3.34085 3.47588C3.79385 2.9709 4.3871 2.71204 5.12353 2.71204C5.97556 2.71204 6.62079 3.03952 7.04741 3.69458L7.46214 4.38981L7.87695 3.69458C8.30348 3.03952 8.94871 2.71204 9.80083 2.71204C10.5372 2.71204 11.1304 2.9709 11.5835 3.47588C12.0227 3.98086 12.2414 4.66342 12.2414 5.52235Z" fill="#3088D4"/>
						</svg></span>
						<span class="simplesocialtxt">' . esc_html__( 'Mastodon', 'simple-social-buttons' ) . '</span>
						' . ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $mastodon_share ) . '</span>' : '' ) . '
						</' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . ' class="simplesocial-mastodon-share" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'Mastodon Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr . '="' . $mastodon_share_url . '"' . $ssb_ck_popup . '><span class="simplesocialtxt">' . esc_html__( 'Mastodon', 'simple-social-buttons' ) . '</span>
						' . ( $show_count ? '<span class="ssb_counter ssb_mastodon_counter">' . ssb_count_format( $mastodon_share ) . '</span>' : '' ) . '
						</' . $ssb_element_tag . '>';
					}

					$arr_buttons_code[] = $_html;
					break;
				case 'vk':
					$vk_share     = ( isset( $share_counts['vk'] ) && $share_counts['vk'] > 0 ) ? $share_counts['vk'] : 0;
					$vk_share_url = esc_url( 'https://vk.com/share.php?url=' . rawurlencode( $permalink ) . '&title=' . rawurlencode( get_the_title() ) );

					if ( 'simple-icons' === $theme ) {
						$_html = '<' . $ssb_element_tag . ' class="ssb_vk-icon" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'VK Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr . '="' . $vk_share_url . '"' . $ssb_ck_popup . '>
						<span class="icon"><svg aria-hidden="true" focusable="false" width="21" height="12" viewBox="0 0 21 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd"
							clip-rule="evenodd" d="M19.735.812c.14-.47 0-.812-.67-.812h-2.21c-.56 0-.82.297-.96.624 0 0-1.125 2.74-2.716
							4.52-.515.515-.749.679-1.03.679-.14 0-.343-.164-.343-.634V.812c0-.56-.164-.812-.63-.812H7.702c-.352 0-.564.26-.564.51 0 .533.797.654.88
							2.154V5.92c0 .712-.131.842-.41.842-.749 0-2.57-2.752-3.652-5.901C3.743.249 3.53.003 2.967.003H.757C.128.003 0 .3 0 .627c0 .585.749 3.49
							3.489 7.33C5.316 10.578 7.887 12 10.229 12c1.404 0 1.58-.315 1.58-.86V9.156c0-.63.133-.758.579-.758.327 0 .891.164 2.2 1.428 1.498 1.497 
							1.746 2.17 2.59 2.17h2.209c.63 0 .945-.315.764-.94-.2-.621-.916-1.521-1.864-2.591-.516-.61-1.289-1.264-1.522-1.592-.328-.42-.233-.609 0-.982-.003 
							0 2.688-3.791 2.97-5.08" fill="#fff"/></svg></span>
						<span class="simplesocialtxt">' . esc_html__( 'VK', 'simple-social-buttons' ) . '</span>
						' . ( $show_count ? '<span class="ssb_counter">' . ssb_count_format( $vk_share ) . '</span>' : '' ) . '
						</' . $ssb_element_tag . '>';
					} else {
						$_html = '<' . $ssb_element_tag . ' class="simplesocial-vk-share" ' . $ssb_attr_html . ' aria-label="' . esc_attr__( 'VK Share', 'simple-social-buttons' ) . '" ' . $ssb_trigger_attr . '="' . $vk_share_url . '"' . $ssb_ck_popup . '><span class="simplesocialtxt">' . esc_html__( 'VK', 'simple-social-buttons' ) . '</span>
						' . ( $show_count ? '<span class="ssb_counter ssb_vk_counter">' . ssb_count_format( $vk_share ) . '</span>' : '' ) . '
						</' . $ssb_element_tag . '>';
					}

					$arr_buttons_code[] = $_html;
					break;
				case 'snapchat':
					$snapchat_share_url = ssb_get_snapchat_web_share_url( $permalink );
					$snapchat_svg       = ssb_get_snapchat_icon_svg();
					$snapchat_kit_class = $ssb_is_amp ? '' : ' snapchat-share-button';
					$snapchat_amp_attr  = $ssb_is_amp
						? ' ' . $ssb_trigger_attr . '="' . esc_attr( $snapchat_share_url ) . '"' . $ssb_ck_popup
						: ' data-share-url="' . esc_attr( $snapchat_share_url ) . '"';
					if ( 'simple-icons' === $theme ) {
						$_html  = '<' . $ssb_element_tag . ' class="ssb_snapchat-icon' . esc_attr( $snapchat_kit_class ) . '" '
							. $ssb_attr_html
							. ' aria-label="' . esc_attr__( 'Snapchat Share', 'simple-social-buttons' ) . '"'
							. $snapchat_amp_attr . '>';
						$_html .= '<span class="icon">' . $snapchat_svg . '</span>';
						$_html .= '<span class="simplesocialtxt">' . esc_html__( 'Snapchat', 'simple-social-buttons' ) . '</span>';
						$_html .= ssb_get_button_counter_markup( 'snapchat', $share_counts, $show_count, $theme );
						$_html .= '</' . $ssb_element_tag . '>';
					} else {
						$_html  = '<' . $ssb_element_tag . ' class="simplesocial-snapchat-share' . esc_attr( $snapchat_kit_class ) . '" '
							. $ssb_attr_html
							. ' aria-label="' . esc_attr__( 'Snapchat Share', 'simple-social-buttons' ) . '"'
							. $snapchat_amp_attr . '>';
						$_html .= '<span class="simplesocialtxt">' . esc_html__( 'Snapchat', 'simple-social-buttons' ) . '</span>';
						$_html .= ssb_get_button_counter_markup( 'snapchat', $share_counts, $show_count, $theme );
						$_html .= '</' . $ssb_element_tag . '>';
					}
					$arr_buttons_code[] = $_html;
					break;
				default:
					// Custom buttons (custom_1, custom_2, ...) — Pro only.
					if ( ssb_is_pro_active() && ssb_is_custom_button_id( $button_name ) ) {
						$networks_option = self::ssb_get_networks_option();
						$custom_buttons  = $networks_option['custom_buttons'];
						if ( isset( $custom_buttons[ $button_name ] ) ) {
							$cb         = $custom_buttons[ $button_name ];
							$url        = isset( $cb['url'] ) ? esc_url( $cb['url'] ) : '#';
							$label      = isset( $cb['label'] ) ? esc_html( $cb['label'] ) : '';
							$icon_url   = isset( $cb['icon_url'] ) ? esc_url( $cb['icon_url'] ) : '';
							$bg         = isset( $cb['bg_color'] ) ? sanitize_hex_color( $cb['bg_color'] ) : '#0865ff';
							$hover_bg   = isset( $cb['hover_bg_color'] ) ? sanitize_hex_color( $cb['hover_bg_color'] ) : '#1557b0';
							$text_clr   = isset( $cb['text_color'] ) ? sanitize_hex_color( $cb['text_color'] ) : '#ffffff';
							$bg         = $bg ? $bg : '#0865ff';
							$hover_bg   = $hover_bg ? $hover_bg : '#1557b0';
							$text_clr   = $text_clr ? $text_clr : '#ffffff';
							$shadow     = ssb_darken_hex_color( $bg, 25 );
							$hover_sh   = ssb_darken_hex_color( $hover_bg, 25 );
							$style_attr = '--ssb-custom-bg:' . esc_attr( $bg )
								. ';--ssb-custom-color:' . esc_attr( $text_clr )
								. ';--ssb-custom-hover-bg:' . esc_attr( $hover_bg )
								. ';--ssb-custom-shadow:' . esc_attr( $shadow ? $shadow : $bg )
								. ';--ssb-custom-hover-shadow:' . esc_attr( $hover_sh ? $hover_sh : $hover_bg )
								. ';background-color:' . esc_attr( $bg )
								. ';color:' . esc_attr( $text_clr ) . ';';
							if ( 'simple-icons' === $theme ) {
								$_html = '<' . $ssb_element_tag . ' class="ssb_custom-icon ssb-custom-button simplesocial-custom" ' . $ssb_attr_html . ' style="' . $style_attr . '" ' . $ssb_trigger_attr . '="' . $url . '"' . $ssb_ck_custom_blank . '>'; // phpcs:ignore Generic.Files.LineLength.TooLong
								if ( $icon_url ) {
									$_html .= '<span class="icon"><img src="' . $icon_url . '" alt="" class="ssb-custom-icon" /></span>';
								}
								$_html .= '<span class="simplesocialtxt">' . $label . '</span>';
								$_html .= ssb_get_button_counter_markup( $button_name, $share_counts, $show_count, $theme );
								$_html .= '</' . $ssb_element_tag . '>';
							} else {
								$_html = '<' . $ssb_element_tag . ' class="ssb-custom-button simplesocial-custom" ' . $ssb_attr_html . ' style="' . $style_attr . '" ' . $ssb_trigger_attr . '="' . $url . '"' . $ssb_ck_custom_blank . '>'; // phpcs:ignore Generic.Files.LineLength.TooLong
								if ( $icon_url ) {
									$_html .= '<span class="ssb_custom_icon"> <img src="' . $icon_url . '" alt="" class="ssb-custom-icon" /> </span>';
								}
								$_html .= '<span class="simplesocialtxt">' . $label . '</span>';
								$_html .= ssb_get_button_counter_markup( $button_name, $share_counts, $show_count, $theme );
								$_html .= '</' . $ssb_element_tag . '>';
							}
						}
					}
					if ( '' !== $_html ) {
						$arr_buttons_code[] = $_html;
					}
					break;
			}
		}

		if ( count( $arr_buttons_code ) > 0 ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$arr_buttons_code = apply_filters( 'ssb_buttons_output', $arr_buttons_code );

			if ( ! empty( $extra_data['buttons_only'] ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return implode( "\n", $arr_buttons_code ) . "\n";
			}

			$class = isset( $extra_data['class'] ) ? $extra_data['class'] : '';
			if ( ! is_array( $extra_data ) ) {
				$class = $extra_data;
			}

			$position_style = 'simplesocialbuttons simplesocial-' . $theme . ' ' . $class;

			/**
			 * 'ssb_position_style' is the filter activated share network.
			 *
			 * @since 3.1.0
			 *
			 * @param string $position_style all classes.
			 * @param string $theme current theme.
			 * @param array $extra_data  meta data for position style classes.
			 * @param array $arr_buttons selected networks.
			 */
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$position_style = apply_filters( 'ssb_position_style', $position_style, $theme, $extra_data, $arr_buttons );

			$ssb_buttonscode .= '<div class="' . esc_attr( $position_style ) . '">' . "\n";
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase,WordPress.Security.EscapeOutput.OutputNotEscaped
			$ssb_buttonscode .= implode( "\n", $arr_buttons_code ) . "\n";
			$overflow_popup   = '';
			if ( ! empty( $arr_hidden_buttons ) ) {
				$overflow_parts   = $this->ssb_get_icon_limit_overflow_parts( $arr_hidden_buttons, $show_count, $extra_data );
				$ssb_buttonscode .= $overflow_parts['trigger'];
				if ( '' !== $deferred_totalshare_html ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$ssb_buttonscode .= $deferred_totalshare_html;
				}
				$overflow_popup = $overflow_parts['popup'];
			}
			$ssb_buttonscode .= '</div>' . "\n";
			if ( '' !== $overflow_popup ) {
				$ssb_buttonscode .= $overflow_popup;
			}
		}
		return $ssb_buttonscode;
	}

	/**
	 * Build trigger + popup markup for icon-limit overflow buttons.
	 *
	 * @param array   $arr_hidden_buttons Hidden network sort map.
	 * @param boolean $show_count         Whether share counts are enabled.
	 * @param array   $extra_data         Position context for theming.
	 * @return array{trigger:string,popup:string}
	 * @since 7.0.0
	 */
	private function ssb_get_icon_limit_overflow_parts( $arr_hidden_buttons, $show_count, $extra_data ) {
		$empty = array(
			'trigger' => '',
			'popup'   => '',
		);

		if ( empty( $arr_hidden_buttons ) || ! is_array( $arr_hidden_buttons ) ) {
			return $empty;
		}

		$overflow_extra = $extra_data;
		unset( $overflow_extra['icon_limit'] );
		$overflow_extra['buttons_only'] = true;

		$hidden_html = $this->ssb_generate_buttons_code( $arr_hidden_buttons, $show_count, false, $overflow_extra );
		$hidden_html = trim( $hidden_html );
		if ( '' === $hidden_html ) {
			return $empty;
		}

		/**
		 * Filter the label shown on the icon-limit overflow trigger button.
		 *
		 * @since 7.0.0
		 *
		 * @param string $more_label Overflow trigger label.
		 * @param array  $extra_data Position context.
		 */
		$more_label = apply_filters( 'ssb_icon_limit_more_label', '+', $extra_data );

		$theme             = isset( $extra_data['theme'] ) ? $extra_data['theme'] : $this->selected_theme;
		$popup_theme_class = 'simplesocialbuttons simplesocial-' . $theme;
		$position          = isset( $extra_data['position'] ) ? $extra_data['position'] : 'inline';
		$position          = 'sidebar' === $position ? 'simplesocialbuttons-float' : $position;
		$popup_id          = 'ssb-icon-limit-' . wp_unique_id();
		$counter_class     = $show_count ? ' ssb_counter-activate' : '';

		$trigger = '<button type="button" class="ssb_custom-button ssb_icon-limit-more" data-ssb-popup-target="'
			. esc_attr( $popup_id ) . '" aria-label="'
			. esc_attr__( 'More share options', 'simple-social-buttons' ) . '">';
		if ( '' !== $more_label && '+' !== $more_label ) {
			$trigger .= '<span class="ssb_icon-limit-more-label" aria-hidden="true">'
				. esc_html( $more_label ) . '</span>';
		}
		$trigger .= '</button>';

		$popup  = '<div id="' . esc_attr( $popup_id ) . '" class="ssb_icon-limit-popup ssb_icon-limit-popup--'
			. esc_attr( $position ) . ' simplesocialbuttons simplesocialbuttons_' . esc_attr( $position ) . ' simplesocial-' . esc_attr( $theme ) . $counter_class . '" role="dialog" aria-modal="true" aria-label="'
			. esc_attr__( 'More share options', 'simple-social-buttons' ) . '">';
		$popup .= '<div class="ssb_wrapped-button">';
		$popup .= '<span class="ssb_wrapper-closed" role="button" tabindex="0" aria-label="'
			. esc_attr__( 'Close', 'simple-social-buttons' ) . '"></span>';
		$popup .= '<p class="ssb_icon-limit-popup__title">' . esc_html__( 'Share', 'simple-social-buttons' ) . '</p>';
		$popup .= '<div class="' . esc_attr( $popup_theme_class ) . ' ssb_icon-limit-popup__buttons">';
		$popup .= $hidden_html;
		$popup .= '</div>';
		$popup .= '</div></div>';

		return array(
			'trigger' => $trigger,
			'popup'   => $popup,
		);
	}

	/**
	 * Backward compatibility: Pro and other code may call the unprefixed name.
	 *
	 * @deprecated Use ssb_generate_buttons_code() instead.
	 * @param  array|null   $order       Network order.
	 * @param  boolean      $show_count  Show count.
	 * @param  boolean      $show_total  Show total.
	 * @param  array        $extra_data  Extra data.
	 * @param  string|false $image      Image src.
	 * @return string
	 * @since 7.0.0
	 */
	public function generate_buttons_code( $order = null, $show_count = false, $show_total = false, $extra_data = array(), $image = false ) {
		return $this->ssb_generate_buttons_code( $order, $show_count, $show_total, $extra_data, $image );
	}

	/**
	 * Get the option value
	 *
	 * @param  string  $option Name of option.
	 * @param  boolean $default  Default value.
	 *
	 * @return mixed
	 * @access public
	 * @since 2.0
	 */
	public function get_option( $option, $default = false ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound
		if ( isset( $this->settings[ $option ] ) ) {
			return $this->settings[ $option ];
		} else {
			return $default;
		}
	}

	/**
	 * Get post type.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string
	 */
	public function get_post_type() {

		if ( is_home() || is_front_page() ) {
			return 'home';
		}
		return get_post_type();
	}

	/**
	 * Add Buttons on SideBar.
	 *
	 * @access public
	 * @since 2.0
	 * @return void
	 */
	public function ssb_include_sidebar() {

		// Return Content if hide ssb.
		if ( 'true' === get_post_meta( get_the_ID(), $this->hide_custom_meta_key, true ) ) {
			return;
		}

		if ( isset( $this->selected_position['sidebar'] ) && in_array( $this->get_post_type(), $this->get_settings( 'sidebar', 'posts', array() ), true ) ) {
			$show_total          = false;
			$show_count          = false;
			$show_hover_text     = false;
			$flat_button_sidebar = false;
			// Show Total at the end.
			if ( $this->sidebar_option['total_share'] ) {
				$show_total = true;
			}
			if ( isset( $this->sidebar_option['display_hover_text'] ) && $this->sidebar_option['display_hover_text'] ) {
				$show_hover_text = true;
			}
			if ( $this->sidebar_option['share_counts'] ) {
				$show_count = true;
			}
			if ( isset( $this->sidebar_option['flat_button_sidebar'] ) && $this->sidebar_option['flat_button_sidebar'] ) {
				$flat_button_sidebar = true;
			}
			if ( in_array( $this->get_post_type(), $this->sidebar_option['posts'], true ) ) {
				$class = 'simplesocialbuttons-float-' . $this->sidebar_option['orientation'] . '-center ' . $this->add_post_class(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- class string assembled for echo later.
				if ( $this->sidebar_option['hide_mobile'] ) {
					$class .= ' simplesocialbuttons-mobile-hidden';
				}
				if ( isset( $this->sidebar_option['sticky_mobile_bottom'] ) && $this->sidebar_option['sticky_mobile_bottom'] ) {
					$class .= ' simplesocialbuttons-bottom-sticky-mobile';
				}

				if ( $this->get_settings( 'sidebar', 'share_counts' ) ) {
					$class .= ' ssb_counter-activate';
				}
				if ( true === $show_hover_text ) {
					$class .= ' ssb_show_text_hover';
				}
				if ( isset( $flat_button_sidebar ) && true === $flat_button_sidebar ) {
					$class .= ' ssbflat_button_sidebar';
				}

				$class .= ' simplesocialbuttons-slide-' . $this->get_settings( 'sidebar', 'animation', 'no-animation' );
				/**
				 * The filter to modify Selected Networks of SSB in sidebar.
				 */
				$_selected_network = apply_filters( 'ssb_sidebar_social_networks', $this->selected_networks );
				/**
				 * The filter to add heading text in SSB sidebar.
				 */
				$before_text = apply_filters( 'ssb_sidebar_before_text', false );

				$extra_data = array(
					'class'       => $class,
					'position'    => 'sidebar',
					'before_text' => sanitize_text_field( $before_text ),
				);
				$icon_limit = $this->ssb_get_icon_limit_for_section( 'sidebar' );
				if ( $icon_limit > 0 ) {
					$extra_data['icon_limit'] = $icon_limit;
				}
				if (
					count( $_selected_network ) > 4
					&& isset( $this->sidebar_option['sticky_mobile_bottom'] )
					&& $this->sidebar_option['sticky_mobile_bottom']
					&& ! $this->ssb_is_setting_enabled( 'sidebar', 'icon_limit' )
				) {
					$buttons_code  = $this->ssb_generate_buttons_code( $_selected_network, $show_count, $show_total, $extra_data );
					$buttons_array = explode( '</button>', $buttons_code );

					// Ensure valid array position before inserting.
					if ( count( $buttons_array ) > 3 ) {
						$custom_button_html = '<button type="button" class="ssb_custom-button ssb_more-icon" aria-label="'
							. esc_attr__( 'More share options', 'simple-social-buttons' ) . '"></button>'
							. '<div class="ssb_wrapper_mobile">'
							. '<div class="ssb_wrapped-button">'
							. '<span class="ssb_wrapper-closed"></span>';
						array_splice( $buttons_array, 3, 0, $custom_button_html );
					}

					// Close the divs correctly after all buttons.
					$buttons_array[] = '</div></div>';

					// Reconstruct the buttons HTML.
					$fixed_buttons_code = implode( '</button>', $buttons_array );

					// Ensure the final structure does not break.
					$fixed_buttons_code = str_replace( '</button></button>', '</button>', $fixed_buttons_code );

					echo $fixed_buttons_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo $this->ssb_generate_buttons_code( $_selected_network, $show_count, $show_total, $extra_data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}

	/**
	 * User custoimzed CSS.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function css_file() {
		include_once __DIR__ . '/inc/ssb-custom-css.php';
	}

	/**
	 * Update option when user click on dismiss button.
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function ssb_review_update_notice() {

		if ( ! is_admin() ||
		! current_user_can( 'manage_options' ) ||
		! isset( $_GET['_wpnonce'] ) ||
		! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'ssb-update-nonce' ) ||
		! isset( $_GET['ssb_update_2_0_dismiss'] ) ) {

			return;
		}

		if ( isset( $_GET['ssb_update_2_0_dismiss'] ) ) {
			update_option( 'ssb_update_2_0_dismiss', 'yes' );
		}
	}

	/**
	 * Show 2.0 Update Notice.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_notice() {

		if ( get_option( 'ssb_update_2_0_dismiss' ) ) {
			return; }

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$scheme      = ( wp_parse_url( $request_uri, PHP_URL_QUERY ) ) ? '&' : '?';
		$url         = admin_url( 'admin.php?page=simple-social-buttons' ) . '&ssb_update_2_0_dismiss=yes';
		$dismiss_url = wp_nonce_url( $url, 'ssb-update-nonce' );

		?>
		<style media="screen">
		.ssb-update-notice { background: #dbf5ff; padding: 20px 20px; border: 1px solid #0085ba; border-radius: 5px; margin: 20px 20px 20px 0; }
		.ssb-update-notice:after { content: ''; display: table; clear: both; }
		.ssb-update-thumbnail { width: 114px; float: left; line-height: 80px; text-align: center; border-right: 4px solid transparent; }
		.ssb-update-thumbnail img { width: 100px; vertical-align: middle; }
		.ssb-update-text { overflow: hidden; }
		.ssb-update-text h3 { font-size: 24px; margin: 0 0 5px; font-weight: 400; line-height: 1.3; }
		.ssb-update-text p { font-size: 13px; margin: 0 0 5px; }
		.ssb_update_dismiss_button{
			padding: 7px 12px;
			background: #0085ba;
			border: 1px solid #006799;
			border-radius: 5px;
			display: inline-block;
			color: #fff;
			text-decoration: none;
			box-shadow: 0px 2px 0px 0px rgba(0, 103, 153, 1);
			position: relative;
			margin: 15px 10px 5px 0;
		}
		.ssb_update_dismiss_button:hover{ top: 2px; box-shadow: 0px 0px 0px 0px rgba(0, 103, 153, 1); color: #fff; background: #006799; }
		</style>
		<div class="ssb-update-notice">
			<div class="ssb-update-thumbnail">
				<img src="<?php echo esc_url( plugins_url( 'assets/images/social_button.svg', __FILE__ ) ); ?>" alt="">
			</div>
			<div class="ssb-update-text">
				<h3><?php esc_html_e( 'Simple Social Buttons 2.0 (Relaunched)', 'simple-social-buttons' ); ?></h3>
				<p>
				<?php
				esc_html_e(
					'Simple Social Buttons had 50,000 Active installs and It was abondoned and rarely updated since last 5 years.<br />
					We at <a href="https://WPBrigade.com/?utm_source=simple-social-buttons-lite&utm_medium=link-notice-2-0" target="_blank">WPBrigade</a>
					adopted this plugin and rewrote it completely from scratch.<br />
					<a href="https://simplesocialbuttons.com/pricing/?utm_source=simple-social-buttons-lite&utm_medium=link-notice-2-0&utm_campaign=pro-upgrade" target="_blank">
					Check out</a> What\'s new in 2.0 version.<br /> Pardon me, If there is anything broken. Please
					<a href="https://WPBrigade.com/contact/?utm_source=simple-social-buttons-lite" target="_blank">report</a> us any issue you see in the plugin.',
					'simple-social-buttons'
				);
				?>
				</p>
				<a href="<?php echo esc_url( $dismiss_url ); ?>" class="ssb_update_dismiss_button">Dismiss</a>
				<?php
				$learn_more_url = 'https://simplesocialbuttons.com/pricing/'
					. '?utm_source=simple-social-buttons-lite'
					. '&utm_medium=link-learn-more'
					. '&utm_campaign=pro-upgrade';
				?>
				<a href="<?php echo esc_url( $learn_more_url ); ?>" target="_blank" class="ssb_update_dismiss_button">Learn more</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Short code content.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @access public
	 * @since 2.0.2
	 * @version 7.1.0
	 * @return string
	 */
	public function ssb_short_code_content( $atts ) {
		/*
		 * counter = true,false
		 * show_total = true,false
		 * align = left ,right,centered,
		 * order = googleplus,twitter,pinterest,fbshare,linkedin,reddit,whatsapp,viber,fblike,messenger,email
		 * like_button_size = small or large , Default small
		 * theme
		 * theme1 =  sm-round
		 * theme2 =  simple-round
		 * theme3 =  round-txt
		 * theme4 =  round-btm-border
		 * Flat =  flat-button-border
		 * Circle =  round-icon
		 * Official =  simple-icons
		 */

		// Ensure front assets load for widget-only / no-position setups.
		$this->ssb_enqueue_front_assets();

		$selected_theme = shortcode_atts(
			array(
				'theme'            => '',
				'post_url'         => '',
				'order'            => null,
				'align'            => '',
				'counter'          => 'false',
				'show_total_count' => 'false',
				'like_button_size' => 'small',
			),
			$atts
		);

		$themes = array(
			'theme1'   => 'sm-round',
			'theme2'   => 'simple-round',
			'theme3'   => 'round-txt',
			'theme4'   => 'round-btm-border',
			'Flat'     => 'flat-button-border',
			'Circle'   => 'round-icon',
			'Official' => 'simple-icons',
		);

		if ( key_exists( $selected_theme['theme'], $themes ) ) {
			foreach ( $themes as $key => $value ) {

				if ( $selected_theme['theme'] === $key ) {

					$theme = $themes[ $key ];
				}
			}
		} else {
			$theme = $this->selected_theme;
		}

		if ( null !== $selected_theme['order'] && '' !== $selected_theme['order'] ) {
			$selected_theme['order'] = array_flip( array_merge( array( 0 ), explode( ',', $selected_theme['order'] ) ) );

		} else {
			$selected_theme['order'] = $this->selected_networks;
		}

		// Show Total at the end.
		if ( 'true' === $selected_theme['show_total_count'] ) {
			$show_total = true;
		} else {
			$show_total = false;
		}

		if ( empty( $selected_theme['align'] ) ) {
			$align_class = $this->get_settings( 'inline', 'icon_alignment', 'left' );
		} else {
			$align_class = $selected_theme['align'];
		}

			$extra_class = 'simplesocialbuttons_inline simplesocialbuttons-align-' . esc_html( $align_class );

		if ( 'true' === $selected_theme['counter'] ) {
			$show_count   = true;
			$extra_class .= ' ssb_counter-activate';
		} else {
			$show_count = false;
		}

			// set fb like button size.
			$like_button_size = $selected_theme['like_button_size'];
			$post_url         = esc_url_raw( sanitize_url( $selected_theme['post_url'] ) );
			$extra_class     .= ' simplesocialbuttons-inline-' . $this->get_settings( 'inline', 'animation', 'no-animation' );

			$extra_option = array(
				'class'            => $extra_class,
				'theme'            => $theme,
				'like-button-size' => esc_html( $like_button_size ),
				'position'         => 'shortcode',
			);

			if ( ! empty( $post_url ) ) {
				$extra_option['post_url'] = $post_url;
			}
			$ssb_buttons_code = $this->ssb_generate_buttons_code( $selected_theme['order'], $show_count, $show_total, $extra_option );
			// }

			return $ssb_buttons_code;
	}


	/**
	 * Add Meta Tags.
	 *
	 * @access public
	 * @since 2.0.9
	 * @version 7.0.1
	 * @return string
	 */
	public function ssb_add_meta_tags() {
		// Check is the page/post is a password protected.
		if ( post_password_required( get_the_ID() ) ) {
			return;
		}

		// Check og tags are off then return without creating the meta properties.
		if ( isset( $this->extra_option['ssb_og_tags'] ) && '1' !== $this->extra_option['ssb_og_tags'] ) {
			return;
		}

		$og_tag  = '';
		$og_tag .= PHP_EOL . '<!-- Open Graph Meta Tags generated by Simple Social Buttons ' . $this->plugin_version . ' -->' . PHP_EOL;
		if ( $this->og_get_title() ) {
			$og_tag .= '<meta property="og:title" content="' . esc_attr( get_the_title() . ' - ' . get_bloginfo( 'name' ) ) . '" />' . PHP_EOL;
		}

		// Add option for og type.
		$og_type = ( is_singular( 'post' ) ) ? 'article' : 'website';

		$og_tag .= '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . PHP_EOL;

		if ( $this->og_get_description() ) {
			$og_tag .= '<meta property="og:description" content="' . esc_attr( $this->og_get_description() ) . '" />' . PHP_EOL;
		}
		$og_tag .= '<meta property="og:url" content="' . esc_url( get_permalink() ) . '" />' . PHP_EOL;
		if ( $this->og_get_blog() ) {
			$og_tag .= '<meta property="og:site_name" content="' . esc_attr( $this->og_get_blog() ) . '" />' . PHP_EOL;
		}
		$og_tag .= $this->get_og_image();

		$og_tag .= '<meta name="twitter:card" content="summary_large_image" />' . PHP_EOL;
		if ( $this->og_get_description() ) {
			$og_tag .= '<meta name="twitter:description" content="' . esc_attr( $this->get_excerpt_by_id( get_the_ID() ) ) . '" />' . PHP_EOL;
		}

		if ( $this->og_get_title() ) {
			$og_tag .= '<meta name="twitter:title" content="' . esc_attr( get_the_title() . ' - ' . get_bloginfo( 'name' ) ) . '" />' . PHP_EOL;
		}
		$og_tag .= $this->generate_twitter_image();

		$snapchat_client_id = ssb_get_snapchat_client_id();
		if ( $snapchat_client_id && $this->ssb_has_snapchat_network() ) {
			$og_tag .= '<meta property="snapchat:app_id" content="' . esc_attr( $snapchat_client_id ) . '" />' . PHP_EOL;
		}

		echo apply_filters( 'ssb_og_tag', $og_tag ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Get title for open graph / meta description.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string
	 */
	public function og_get_title() {
		return get_the_title() . ' - ' . get_bloginfo( 'name' );
	}

	/**
	 * Get description for the Open Graph / meta descriptoin.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string
	 */
	public function og_get_description() {
		return $this->get_excerpt_by_id( get_the_ID() );
	}

	/**
	 * Get blog name for Open graph / meta descripton.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string
	 */
	public function og_get_blog() {
		return get_bloginfo( 'name' );
	}


	/**
	 * Get the excerpt
	 *
	 * @param int $post_id Post ID.
	 *
	 * @access public
	 * @since 2.0.9
	 * @version 6.1.0
	 * @return string
	 */
	public function get_excerpt_by_id( $post_id ) {

		if ( ! $post_id ) {
			return;
		}
			// Check if the post has an excerpt.
		if ( has_excerpt() ) {
				$excerpt_length = apply_filters( 'excerpt_length', 35 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				return trim( wp_strip_all_tags( strip_shortcodes( get_the_excerpt() ) ) );
		}

			$the_post    = get_post( $post_id ); // Gets post ID.
			$the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt.
			$the_excerpt = wp_trim_words( $the_excerpt, 60 );
			return trim( wp_strip_all_tags( strip_shortcodes( $the_excerpt ) ) );
	}

	/**
	 * Get meta tage Image from content.
	 *
	 * @param WP_Post|object $post Post object.
	 * @access public
	 * @since 2.0.10
	 * @version 7.0.1
	 * @return string
	 */
	public function get_content_images( $post ) {

		if ( ! $post ) {
			return;
		}

		$content = $post->post_content;
		$images  = '';
		if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {
			foreach ( $matches[0] as $img ) {
				if ( preg_match( '`src=(["\'])(.*?)\1`', $img, $match ) ) {
					$images .= '<meta property="og:image" content="' . esc_url( $match[2] ) . '" />' . PHP_EOL;
				}
			}
		}
		return $images;
	}


	/**
	 * Get the featured image / meta for open graph.
	 *
	 * @access public
	 * @since 2.0.10
	 * @version 7.0.1
	 * @return string Meta tag og:image for meta
	 */
	public function generate_og_image() {
		$_post_id = (int) get_the_ID();

		if ( has_post_thumbnail( $_post_id ) ) {
			return '<meta property="og:image" content="' . esc_url( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ) ) . '" />' . PHP_EOL;
		}

		return $this->get_content_images( get_post( $_post_id ) );
	}

	/**
	 * Get Open Graph image.
	 *
	 * @access public
	 * @since 2.0.10
	 * @return mixed
	 */
	public function get_og_image() {
		$image = $this->generate_og_image();

		if ( $image ) {
			return $image;
		}
	}

	/**
	 * Get the featured image for Twitter.
	 *
	 * @access public
	 * @since 2.0.10
	 * @version 7.0.1
	 * @return mixed
	 */
	public function generate_twitter_image() {
		$_post_id = (int) get_the_ID();

		if ( has_post_thumbnail( $_post_id ) ) {
			return '<meta property="twitter:image" content="' . esc_url( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ) ) . '" />' . PHP_EOL;
		}

		return $this->get_twitter_content_images( get_post( $_post_id ) );
	}

	/**
	 * Get Image from content for Twitter.
	 *
	 * @param WP_Post|object $post Post object.
	 * @access public
	 * @since 2.0.10
	 * @version 7.0.1
	 * @return string|false
	 */
	public function get_twitter_content_images( $post ) {

		if ( ! $post ) {
			return;
		}

		$content = $post->post_content;
		$images  = '';
		if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {
			foreach ( $matches[0] as $img ) {
				if ( preg_match( '`src=(["\'])(.*?)\1`', $img, $match ) ) {
					$images .= '<meta property="twitter:image" content="' . esc_url( $match[2] ) . '" />' . PHP_EOL;
				}
			}
		}
		return $images;
	}
	/**
	 * User to convert http to https or vice versa.
	 *
	 * @param string $url The URL to convert.
	 *
	 * @access public
	 * @since 2.0.12
	 * @return string|void
	 */
	public function http_or_https_resolve_url( $url ) {

		$arr_parsed_url = wp_parse_url( $url );
		if ( ! empty( $arr_parsed_url['scheme'] ) ) {
			if ( 'http' === $arr_parsed_url['scheme'] ) {
				$url = str_replace( 'http', 'https', $url );
				return $url;
			} elseif ( 'https' === $arr_parsed_url['scheme'] ) {
				$url = str_replace( 'https', 'http', $url );
				return $url;
			}
		}
	}



	/**
	 * Convert url http to https or vice versa.
	 *
	 * @param string $permalink Permalink URL.
	 * @access public
	 * @since 2.0.14
	 * @version 7.0.0
	 * @return array
	 */
	public function http_or_https_link_generate( $permalink ) {
		$_alt_share_links = array();

		foreach ( $this->arr_known_buttons as $social_name ) {
			if ( ! ssb_is_network_has_counts( $social_name ) ) {
				continue;
			}
			$callback = 'ssb_' . $social_name . '_generate_link';
			if ( ! is_callable( $callback ) ) {
				continue;
			}
			$url = $this->http_or_https_resolve_url( $permalink );
			// get alt url to cover http or https issue.
			$_alt_share_links[ $social_name ] = call_user_func( $callback, $url );
		}
		return $_alt_share_links;
	}

	/**
	 * Function use to remove button names on excerpt if above the content selected.
	 *
	 * @param string $text already excerpt set.
	 * @param int    $num_words word in excerpt.
	 * @param string $more more string append in last of the excerpt string.
	 * @param string $original_text original text of the string.
	 *
	 * @access public
	 * @since 2.0
	 * @version 5.0.1
	 * @return string
	 */
	public function ssb_on_excerpt_content( $text, $num_words, $more, $original_text ) {

		if ( empty( $text ) ) {
			return $text;
		}
		try {
			// this will not show any warning if html not valid .
			libxml_use_internal_errors( true );
			// xamp.
			$dom = new DOMDocument();

			if ( function_exists( 'mb_encode_numericentity' ) ) {
				$encoded_text = mb_encode_numericentity( $original_text, array( 0x80, 0xffff, 0, 0xffff ), 'UTF-8' );
				$dom->loadHTML( $encoded_text );
			} else {
				// Fallback for when mbstring functions are not available.
				$encoded_text = preg_replace_callback(
					'/[\x{80}-\x{10FFFF}]/u',
					function ( $match ) { // phpcs:ignore
						return '&#' . hexdec( bin2hex( $match[0] ) ) . ';';
					},
					$original_text
				);
				$dom->loadHTML( $encoded_text );
			}

			$xpath = new DOMXPath( $dom );
			$xpath->registerNamespace( 'h', 'http://www.w3.org/1999/xhtml' );
			// using a loop in case there are multiple occurrences.
			foreach ( $xpath->query( "//div[contains(@class, 'simplesocialbuttons')]" ) as $node ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$paragraph_node = $node->parentNode;
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$node->parentNode->removeChild( $node );
			}
			$text = wp_strip_all_tags( $dom->saveHTML() );
		} catch ( Exception $e ) {
			$text = $text;
		}

		/*
		 * wp_trim_words() for more detail.
		 * translators: If your word count is based on single characters (e.g. East Asian characters),
		 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
		 * Do not translate into your own language.
		 */
		if ( strpos( _x( 'words', 'Word count type. Do not translate!', 'simple-social-buttons' ), 'characters' ) === 0
		&& preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
			preg_match_all( '/./u', $text, $words_array );
			$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
			$sep         = '';
		} else {
			$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );

			$sep = ' ';
		}

		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

				return $text;
	}

	/**
	 * Check is ssb on with position and post type.
	 *
	 * @param string $position Location where you want to check ssb on/off.
	 *
	 * @access public
	 * @version 2.0.21
	 * @return bool
	 */
	public function is_ssb_on( $position ) {

		$is_ajax_callback = true;
		if ( ! in_array( $this->get_post_type(), $this->get_settings( $position, 'posts', array() ), true ) ) {
			$is_ajax_callback = false;
		}

		return $is_ajax_callback;
	}

	/**
	 * Register Block.
	 *
	 * @since 2.0.0
	 * @version 3.2.0
	 * @return void
	 */
	public function ssb_register_block() {

		load_plugin_textdomain( 'simple-social-buttons', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		if ( ! function_exists( 'register_block_type' ) ) { // Backward compitablity check.
			return;
		}
		register_block_type(
			'ssb/shortcode',
			array(
				'render_callback' => array( $this, 'ssb_shortcode_block_callback' ),
				'attributes'      => array(
					'theme'          => array(
						'type'    => 'string',
						'default' => 'simple-icons',
					),
					'counter'        => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'showTotalCount' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'align'          => array(
						'type'    => 'string',
						'default' => '',
					),
					'order'          => array(
						'type'    => 'string',
						'default' => 'fbshare,twitter,linkedin',
					),
					'likeButtonSize' => array(
						'type'    => 'string',
						'default' => 'small',
					),
					'alignment'      => array(
						'type'    => 'string',
						'default' => 'left',
					),
				),
			)
		);

		register_block_type(
			'ssb/click-to-tweet',
			array(
				'render_callback' => array( $this, 'ssb_click_to_tweet_block_callback' ),
				'attributes'      => array(
					'theme'           => array(
						'type'    => 'string',
						'default' => 'twitter-round',
					),
					'tweet'           => array(
						'type'    => 'string',
						'default' => '',
					),
					'front'           => array(
						'type'    => 'string',
						'default' => '',
					),
					'IncludePageLink' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'IncludeVia'      => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showTweetButton' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'align'           => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);
	}

	/**
	 * Call for ssb block
	 *
	 * @param array $attr register attributes.
	 * @since 3.0.0
	 * @version 7.0.1
	 * @return string
	 */
	public function ssb_shortcode_block_callback( $attr ) {

		$themes = array(
			'sm-round'           => 'theme1',
			'simple-round'       => 'theme2',
			'round-txt'          => 'theme3',
			'round-btm-border'   => 'theme4',
			'flat-button-border' => 'Flat',
			'round-icon'         => 'Circle',
			'simple-icons'       => 'Official',
		);

		if ( array_key_exists( $attr['theme'], $themes ) ) {
			foreach ( $themes as $key => $value ) {

				if ( $key === $attr['theme'] ) {

					$theme = $themes[ $key ];
				}
			}
		} else {
			$theme = $this->selected_theme;
		}

		$attr['counter']        = $attr['counter'] ? 'true' : 'false';
		$attr['showTotalCount'] = $attr['showTotalCount'] ? 'true' : 'false';
		$theme                  = "theme='" . esc_attr( $theme ) . "'";
		$order                  = "order='" . esc_attr( $attr['order'] ) . "'";
		$counter                = "counter='" . esc_attr( $attr['counter'] ) . "'";
		$alignemnt              = "align='" . esc_attr( $attr['alignment'] ) . "'";
		$like_button_size       = "like_button_size='" . esc_attr( $attr['likeButtonSize'] ) . "'";
		$show_total_count       = "show_total_count='" . esc_attr( $attr['showTotalCount'] ) . "'";
		$align                  = esc_attr( $attr['align'] );
		$shortcode_tags         = $this->ssb_get_shortcode_tags();
		$shortcode_tag          = $shortcode_tags[0];

		return "<div class='align$align'>  [{$shortcode_tag} $theme $order $counter $alignemnt  $like_button_size $show_total_count] </div>";
	}

	/**
	 * Call for click to tweet block.
	 *
	 * @param array $attr register attributes.
	 * @since 3.2.0
	 * @return string
	 */
	public function ssb_click_to_tweet_block_callback( $attr ) {
		$attr['IncludePageLink'] = ! $attr['IncludePageLink'] ? 'true' : 'false';
		$attr['showTweetButton'] = ! $attr['showTweetButton'] ? 'true' : 'false';
		$attr['IncludeVia']      = $attr['IncludeVia'] ? 'true' : 'false';

		$theme       = "theme='" . esc_attr( $attr['theme'] ) . "'";
		$front       = "front='" . esc_attr( $attr['front'] ) . "'";
		$tweet       = "tweet='" . esc_attr( $attr['tweet'] ) . "'";
		$hide_link   = "hide_link='" . esc_attr( $attr['IncludePageLink'] ) . "'";
		$hide_button = "hide_button='" . esc_attr( $attr['showTweetButton'] ) . "'";
		$include_via = "include_via='" . esc_attr( $attr['IncludeVia'] ) . "'";
		$align       = esc_attr( $attr['align'] );

		return "<div class='align" . $align . "'> [SBCTT $theme $front $tweet $hide_link $hide_button $include_via] </div>";
	}

	/**
	 * Clear plugin cron events on deactivation.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public static function ssb_plugin_deactivate() {
		wp_clear_scheduled_hook( 'ssb_flush_internal_share_queue' );
	}

	/**
	 * Define the uninstall callback function.
	 *
	 * @since 5.3.0
	 * @param string $slug Product slug from SDK after-uninstall hook, or empty when WordPress calls uninstall directly.
	 */
	public static function ssb_plugin_uninstall( $slug = '' ) {
		if ( is_string( $slug ) && '' !== $slug && 'simple-social-buttons' !== $slug ) {
			return;
		}
		// Do not use SSB_PLUGIN_DIR here — constants() may never run during uninstall.
		if ( ! defined( 'SSB_DOING_UNINSTALL' ) ) {
			define( 'SSB_DOING_UNINSTALL', true );
		}
		include_once __DIR__ . '/inc/ssb-uninstall.php';
	}
} // end class

// Uninstall: SDK register_uninstall_hook() sends telemetry then fires wp_wpb_sdk_after_uninstall
// (see hooks() above). Do not register a second uninstall hook — it overwrites the SDK callback.

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
global $_ssb_pr;
if ( is_admin() ) {
	include_once __DIR__ . '/classes/class-ssb-admin.php';
	include_once __DIR__ . '/classes/class-ssb-widget.php';
	// Include React admin if version compatibility is met. since 7.0.0.
	include_once __DIR__ . '/classes/class-ssb-react-admin.php';

	$_ssb_pr = new SimpleSocialButtonsPR_Admin(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
} else {
	include_once __DIR__ . '/classes/class-ssb-widget.php';

	$_ssb_pr = new SimpleSocialButtonsPR(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
}

if ( ! function_exists( 'get_ssb' ) ) {
	/**
	 * Legacy template helper (deprecated). Prefer the [SSB] shortcode.
	 *
	 * @param mixed $order Order parameter (unused, kept for backward compatibility).
	 * @return string
	 * @since 1.0.0
	 * @version 7.0.1
	 */
	function get_ssb( $order = null ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound,Universal.NamingConventions.NoReservedKeywordParameterNames.orderFound,Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Legacy public API.
		unset( $order );
		return '<!-- use shortcode instead of this function call - [SSB theme="theme1" aign="right" counter="true" order="twitter,pinterest,fbshare,linkedin" ] -->';
	}
}

?>
