<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Admin class
 *
 * Gets only initiated if this plugin is called inside the admin section.
 *
 * @package SimpleSocialButtons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! class_exists( 'SimpleSocialButtonsPR_Admin' ) ) :

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	/**
	 * Admin class for Simple Social Buttons
	 *
	 * @since 1.0.0
	 */
	class SimpleSocialButtonsPR_Admin extends SimpleSocialButtonsPR { //phpcs:ignore

		/**
		 * Automatically called when object created.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			include_once SSB_PLUGIN_DIR . '/classes/class-ssb-settings.php';
			include_once SSB_PLUGIN_DIR . '/classes/class-ssb-admin-bar-purge.php';
			new Ssb_Admin_Bar_Purge( $this );

			add_action( 'add_meta_boxes', array( $this, 'ssb_meta_box' ) );
			add_action( 'save_post', array( $this, 'ssb_save_meta' ), 10, 2 );

			add_filter( 'plugin_row_meta', array( $this, 'ssb_row_meta' ), 10, 2 );

			add_action( 'wp_ajax_ssb_deactivate', array( $this, 'ssb_deactivate' ) );
			add_action( 'admin_init', array( $this, 'ssb_review_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'ssb_admin_enqueue_scripts' ) );
			add_action( 'in_admin_header', array( $this, 'ssb_skip_notices' ), 100000 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'ssb_blocks_scripts' ) );
		}


		/**
		 * Admin side enqueued script.
		 *
		 * @param string $page Current admin page.
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function ssb_admin_enqueue_scripts( $page ) {
			$allowed_pages = array(
				'toplevel_page_simple-social-buttons',
				'social-buttons_page_ssb-help',
				'social-buttons_page_ssb-import-export',
				'widgets.php',
				'social-buttons_page_ssb-license',
			);

			if ( in_array( $page, $allowed_pages, true ) ) {
				wp_enqueue_style(
					'ssb-admin-cs',
					plugins_url( 'assets/css/admin.css', plugin_dir_path( __FILE__ ) ),
					false,
					SSB_VERSION
				);

				if (
					(
						class_exists( 'Ssb_React_Admin' ) &&
						! class_exists( 'Simple_Social_Buttons_Pro' )
					)
					||
					(
						class_exists( 'Ssb_React_Admin' ) &&
						class_exists( 'Ssb_Pro_React_Admin' )
					)
				) {

					// FREE React admin assets.
					wp_enqueue_style(
						'ssb-react-admin-styles',
						SSB_PLUGIN_URL . 'build/index.css',
						array(),
						SSB_VERSION
					);
				}
				wp_enqueue_script(
					'ssb-admin-js',
					plugins_url( 'assets/js/admin.js', plugin_dir_path( __FILE__ ) ),
					array( 'jquery', 'jquery-ui-sortable' ),
					SSB_VERSION,
					false
				);
				wp_localize_script(
					'ssb-admin-js',
					'ssb',
					array(
						'ssb_export_help_nonce' => wp_create_nonce( 'ssb-export-security-check' ),
					)
				);
			}
		}

		/**
		 * Ssb Block editor assets.
		 *
		 * @since 3.0.0
		 * @version 7.0.0
		 * @return void
		 */
		public function ssb_blocks_scripts() {
			$ssb_block_dependencies = array(
				'wp-blocks',
				'wp-block-editor',
				'wp-element',
				'wp-i18n',
				'wp-components',
			);
			wp_enqueue_script(
				'ssb-blocks-editor-js',
				plugins_url( 'assets/js/blocks.editor.js', plugin_dir_path( __FILE__ ) ),
				$ssb_block_dependencies,
				SSB_VERSION,
				false
			);
			wp_enqueue_style(
				'ssb-blocks-editor-css',
				plugins_url( 'assets/css/blocks.editor.css', plugin_dir_path( __FILE__ ) ),
				array(),
				SSB_VERSION
			);
			wp_enqueue_style(
				'ssb-front-css',
				plugins_url( 'assets/css/front.min.css', plugin_dir_path( __FILE__ ) ),
				false,
				SSB_VERSION
			);

			$is_pro = class_exists( 'Simple_Social_Buttons_Pro' )
				? rest_sanitize_boolean( true )
				: rest_sanitize_boolean( false );
			wp_localize_script(
				'ssb-blocks-editor-js',
				'SSB',
				array(
					'plugin_url' => SSB_PLUGIN_URL,
					'is_pro'     => $is_pro,
				)
			);
		}

		/**
		 * Register meta box to hide/show SSB plugin on single post or page.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void|false
		 */
		public function ssb_meta_box() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_id            = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
			$post_type          = get_post_type( $post_id );
			$ssb_positions      = get_option( 'ssb_positions' );
			$selected_post_type = array();

			// Check if a SSB position is selected or not.
			if ( isset( $ssb_positions['position'] ) && ! empty( $ssb_positions['position'] ) ) {
				foreach ( $ssb_positions['position'] as $key => $value ) {
					$options = get_option( 'ssb_' . $value );

					if ( isset( $options['posts'] ) && ! empty( $options['posts'] ) ) {
						foreach ( $options['posts'] as $allow_post_type ) {
							$selected_post_type[ $allow_post_type ] = $allow_post_type;
						}
					}
				}
			}

			$allow_post_type = apply_filters( 'ssb_cpt_visibility_mb', $selected_post_type );
			if ( ! in_array( $post_type, $allow_post_type, true ) ) {
				return false;
			}

			// Upon Editing or adding the post.
			$current_ssb_hide    = get_post_custom_values( $this->hide_custom_meta_key, $post_id );
			$current_ssb_hide[0] = isset( $current_ssb_hide ) ? $current_ssb_hide[0] : false;

			$checked = ( 'true' === $current_ssb_hide[0] );

			// Rendering meta box.
			if ( ! function_exists( 'add_meta_box' ) ) {
				include 'includes/template.php';
			}
			add_meta_box(
				'ssb_meta_box',
				__( 'SSB Settings', 'simple-social-buttons' ),
				array( $this, 'render_ssb_meta_box' ),
				$post_type,
				'side',
				'default',
				array(
					'type'    => $post_type,
					'checked' => $checked,
				)
			);
		}

		/**
		 * Showing custom meta field.
		 *
		 * @param WP_Post $post The post object.
		 * @param array   $metabox The metabox arguments.
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function render_ssb_meta_box( $post, $metabox ) {
			wp_nonce_field( plugin_basename( __FILE__ ), 'ssb_noncename' );
			$meta_key         = $this->hide_custom_meta_key;
			$escaped_meta_key = esc_attr( $meta_key );
			?>
			<label for="<?php echo esc_attr( $meta_key ); ?>">
				<input type="checkbox" id="<?php echo esc_attr( $meta_key ); ?>" 
					name="<?php echo esc_attr( $meta_key ); ?>" value="true"
					<?php if ( $metabox['args']['checked'] ) : ?>
					checked="checked"
				<?php endif; ?>/>
				&nbsp;<?php echo esc_html__( 'Hide Simple Social Buttons', 'simple-social-buttons' ); ?>
			</label>
			<?php
		}


		/**
		 * Saving custom meta value.
		 *
		 * @param int     $post_id The post ID.
		 * @param WP_Post $post The post object.
		 * @access public
		 * @since 1.0.0
		 * @version 7.0.1
		 * @return void
		 */
		public function ssb_save_meta( $post_id, $post ) {
			$post_id = (int) $post_id;
			// Verify if this is an auto save routine.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['ssb_noncename'] ) ) {
				return;
			}

			// Verify this came from the our screen and with proper authorization.
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( wp_unslash( $_POST['ssb_noncename'] ), plugin_basename( __FILE__ ) ) ) {
				return;
			}

			// Check permissions.
			if ( ! isset( $_POST['post_type'] ) ) {
				return;
			}

			$post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
			if ( 'page' === $post_type ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Saving data.
			$meta_key = $this->hide_custom_meta_key;
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$new_value = isset( $_POST[ $meta_key ] )
				? sanitize_text_field( wp_unslash( $_POST[ $meta_key ] ) )
				: 'false';

			update_post_meta( $post_id, $this->hide_custom_meta_key, $new_value );
		}


		/**
		 * Send the user response to api.
		 *
		 * @access public
		 * @since 1.9.0
		 * @return void
		 */
		public function ssb_deactivate() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to perform this action.', 'simple-social-buttons' ) );
				return;
			}

			$nonce = isset( $_POST['ssb_deactivate_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ssb_deactivate_nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'ssb_deactivate_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed.', 'simple-social-buttons' ) );
				return;
			}

			if ( ! isset( $_POST['reason'] ) || ! isset( $_POST['reason_detail'] ) ) {
				wp_die();
				return;
			}

			$email         = get_option( 'admin_email' );
			$_reason       = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';
			$reason_detail = isset( $_POST['reason_detail'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) ) : '';
			$reason        = '';

			if ( '1' === $_reason ) {
				$reason = 'I only needed the plugin for a short period';
			} elseif ( '2' === $_reason ) {
				$reason = 'I found a better plugin';
			} elseif ( '3' === $_reason ) {
				$reason = 'The plugin broke my site';
			} elseif ( '4' === $_reason ) {
				$reason = 'The plugin suddenly stopped working';
			} elseif ( '5' === $_reason ) {
				$reason = 'I no longer need the plugin';
			} elseif ( '6' === $_reason ) {
				$reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
			} elseif ( '7' === $_reason ) {
				$reason = 'Other';
			}
			$fields = array(
				'email'             => $email,
				'website'           => get_site_url(),
				'action'            => 'Deactivate',
				'reason'            => $reason,
				'reason_detail'     => $reason_detail,
				'blog_language'     => get_bloginfo( 'language' ),
				'wordpress_version' => get_bloginfo( 'version' ),
				'plugin_version'    => SSB_VERSION,
				'php_version'       => PHP_VERSION,
				'plugin_name'       => 'Simple Social Buttons',
			);

			$response = wp_remote_post(
				SSB_FEEDBACK_SERVER,
				array(
					'method'      => 'POST',
					'timeout'     => 5,
					'httpversion' => '1.0',
					'blocking'    => false,
					'headers'     => array(),
					'body'        => $fields,
				)
			);

			wp_die();
		}

		/**
		 * Check either to show notice or not.
		 *
		 * @access public
		 * @since 1.9.0
		 * @return void
		 */
		public function ssb_review_notice() {

			$this->ssb_review_dismissal();
			$this->ssb_review_prending();

			$review_dismissal = get_site_option( 'ssb_review_dismiss' );
			if ( 'yes' === $review_dismissal ) {
				return;
			}

			$activation_time = get_site_option( 'ssb_active_time' );
			if ( ! $activation_time ) {

				$activation_time = time();
				add_site_option( 'ssb_active_time', $activation_time );
			}

			// 1296000 = 15 Days in seconds.
			if ( time() - $activation_time > 1296000 ) {
				add_action( 'admin_notices', array( $this, 'ssb_review_notice_message' ) );
			}
		}

		/**
		 * Show review Message After 15 days.
		 *
		 * @access public
		 * @since 1.9.0
		 * @return void
		 */
		public function ssb_review_notice_message() {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
			$parsed_url  = wp_parse_url( $request_uri, PHP_URL_QUERY );
			$scheme      = $parsed_url ? '&' : '?';
			$url         = $request_uri . $scheme . 'ssb_review_dismiss=yes';
			$dismiss_url = wp_nonce_url( $url, 'ssb-review-nonce' );

			$later_link = $request_uri . $scheme . 'ssb_review_later=yes';
			$later_url  = wp_nonce_url( $later_link, 'ssb-review-nonce' );

			?>
			<style media="screen">
			.ssb-review-notice {
				padding: 15px 15px 15px 0;
				background-color: #fff;
				border-radius: 3px;
				margin: 20px 20px 0 0;
				border-left: 4px solid transparent;
			}
			.ssb-review-notice:after {
				content: '';
				display: table;
				clear: both;
			}
			.ssb-review-thumbnail {
				width: 114px;
				float: left;
				line-height: 80px;
				text-align: center;
				border-right: 4px solid transparent;
			}
			.ssb-review-thumbnail img {
				width: 74px;
				vertical-align: middle;
			}
			.ssb-review-text {
				overflow: hidden;
			}
			.ssb-review-text h3 {
				font-size: 24px;
				margin: 0 0 5px;
				font-weight: 400;
				line-height: 1.3;
			}
			.ssb-review-text p {
				font-size: 13px;
				margin: 0 0 5px;
			}
			.ssb-review-ul {
				margin: 0;
				padding: 0;
			}
			.ssb-review-ul li {
				display: inline-block;
				margin-right: 15px;
			}
			.ssb-review-ul li a {
				display: inline-block;
				color: #10738B;
				text-decoration: none;
				padding-left: 26px;
				position: relative;
			}
			.ssb-review-ul li a span {
				position: absolute;
				left: 0;
				top: -2px;
			}
			</style>
			<div class="ssb-review-notice">
			<div class="ssb-review-thumbnail">
			<img src="<?php echo esc_url( plugins_url( '../assets/images/ssb_grey_logo.png', __FILE__ ) ); ?>" alt="">
		</div>
		<div class="ssb-review-text">
		<h3><?php esc_html_e( 'Leave A Review?', 'simple-social-buttons' ); ?></h3>
		<p>
			<?php
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			$review_msg = __(
				'We hope you\'ve enjoyed using Simple Social Buttons! 
				Would you consider leaving us a review on WordPress.org?',
				'simple-social-buttons'
			);
			echo esc_html( $review_msg );
			?>
		</p>
		<ul class="ssb-review-ul">
			<li>
				<a href="https://wordpress.org/support/plugin/simple-social-buttons/reviews/?filter=5" target="_blank">
					<span class="dashicons dashicons-external"></span>
					<?php esc_html_e( 'Sure! I\'d love to!', 'simple-social-buttons' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( $dismiss_url ); ?>">
					<span class="dashicons dashicons-smiley"></span>
					<?php esc_html_e( 'I\'ve already left a review', 'simple-social-buttons' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( $later_url ); ?>">
					<span class="dashicons dashicons-calendar-alt"></span>
					<?php esc_html_e( 'Maybe Later', 'simple-social-buttons' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( $dismiss_url ); ?>">
					<span class="dashicons dashicons-dismiss"></span>
					<?php esc_html_e( 'Never show again', 'simple-social-buttons' ); ?>
				</a>
			</li>
		</ul>
		</div>
		</div>
			<?php
		}

		/**
		 * Set time to current so review notice will popup after 15 days
		 *
		 * @access public
		 * @since 1.9.0
		 * @return void
		 */
		public function ssb_review_prending() {
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			// delete_site_option( 'ssb_review_dismiss' );.
			if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'ssb-review-nonce' ) ||
			! isset( $_GET['ssb_review_later'] ) ) {

				return;
			}

			// Reset Time to current time.
			update_site_option( 'ssb_active_time', time() );
		}

		/**
		 * Check and Dismiss review message.
		 *
		 * @access private
		 * @since 1.9.0
		 * @return void
		 */
		private function ssb_review_dismissal() {
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			// delete_site_option( 'ssb_review_dismiss' );.
			if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'ssb-review-nonce' ) ||
			! isset( $_GET['ssb_review_dismiss'] ) ) {

				return;
			}

			add_site_option( 'ssb_review_dismiss', 'yes' );
		}


		/**
		 * Skip the all the notice from settings page.
		 *
		 * @access public
		 * @since 1.9.0
		 * @return void
		 */
		public function ssb_skip_notices() {

			if ( 'toplevel_page_simple-social-buttons' === get_current_screen()->id ) {

				global $wp_filter;

				if ( is_network_admin() && isset( $wp_filter['network_admin_notices'] ) ) {
					unset( $wp_filter['network_admin_notices'] );
				} elseif ( is_user_admin() && isset( $wp_filter['user_admin_notices'] ) ) {
					unset( $wp_filter['user_admin_notices'] );
				} elseif ( isset( $wp_filter['admin_notices'] ) ) {
					unset( $wp_filter['admin_notices'] );
				}

				if ( isset( $wp_filter['all_admin_notices'] ) ) {
					unset( $wp_filter['all_admin_notices'] );
				}
			}
		}



		/**
		 * Add Thumbs Up Icon.
		 *
		 * @param array  $links Plugin row meta links.
		 * @param string $file Plugin file path.
		 * @access public
		 * @since 1.9.0
		 * @version 2.1.5
		 * @return array
		 */
		// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore,Squiz.Commenting.FunctionComment.Missing
		public function ssb_row_meta( $links, $file ) {

			if ( strpos( $file, 'simple-social-buttons.php' ) !== false ) {

				$style  = '.ssb-rate-stars { display: inline-block; color: #ffb900; position: relative; top: 3px; }';
				$style .= '.ssb-rate-stars svg{ fill:#ffb900; } .ssb-rate-stars svg:hover{ fill:#ffb900 }';
				$style .= ' .ssb-rate-stars svg:hover ~ svg{ fill:none; }';
				echo '<style>' . esc_html( $style ) . '</style>';

				$plugin_rate   = 'https://wordpress.org/support/plugin/simple-social-buttons/reviews/?rate=5#new-post';
				$plugin_filter = 'https://wordpress.org/support/plugin/simple-social-buttons/reviews/?filter=5';
				$svg_xmlns     = 'https://www.w3.org/2000/svg';
				$svg_icon      = '';

				$svg_base  = "<svg xmlns='" . esc_url( $svg_xmlns ) . "' width='15' height='15' viewBox='0 0 24 24'";
				$svg_base .= " fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round'";
				$svg_base .= " stroke-linejoin='round' class='feather feather-star'>";
				$polygon   = "<polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 ";
				$polygon  .= "5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>";
				$svg_base .= $polygon;
				for ( $i = 0; $i < 5; $i++ ) {
					$svg_icon .= $svg_base;
				}
				// Set link for Reviews.
				$vote_text  = esc_html__( 'Vote!', 'simple-social-buttons' );
				$vote_link  = '<a href=' . esc_url( $plugin_filter ) . '  target="_blank">';
				$vote_link .= '<span class="dashicons dashicons-thumbs-up"></span> ' . $vote_text . '</a>';
				$links[]    = $vote_link;

				$rate_title = esc_attr__( 'Rate', 'simple-social-buttons' );
				$rate_url   = esc_url( $plugin_rate );
				$rate_link  = "<a href='" . $rate_url . "' target='_blank' title='" . $rate_title . "'>";
				$rate_link .= "<i class='ssb-rate-stars'>" . $svg_icon . '</i></a>';
				$links[]    = $rate_link;

			}

			return $links;
		}
	} // end SimpleSocialButtonsPR_Admin

endif;
?>
