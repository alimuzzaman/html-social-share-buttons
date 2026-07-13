<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('init', function(){
	if(is_admin())
		new zm_sh_settings;
}, 20);

class zm_sh_settings{
	private $options;
	private $zm_sh;
	private $iconsets;
	function __construct(){
		global $zm_sh, $zm_sh_default_options;
		$this->zm_sh	= &$zm_sh;
		$this->iconsets	= &$zm_sh->iconsets;
		$this->options = get_option("zm_shbt_fld", $zm_sh_default_options);

		// Runtime migration: Convert 'twitter' to 'x' for backward compatibility
		if (isset($this->options['icons']['twitter'])) {
			$this->options['icons']['x'] = $this->options['icons']['twitter'];
			unset($this->options['icons']['twitter']);
		}
		//adding menu item and page on admin
		add_action('admin_menu', array($this,'reg_admn_menu'));
		//registering settings/options for admin page
		add_action('admin_init', array($this,'zm_reg_sett'));
		//registering scripts and styles for admin
		add_action( 'admin_enqueue_scripts', array($this,'admin_scripts'),20 );
		add_action( 'wp_ajax_zm_sh_save_settings', array($this,'ajax_save_settings') );
		add_action( 'wp_ajax_zm_sh_search_content', array($this,'ajax_search_content') );
	}

	private function get_admin_color_scheme_tokens() {
		global $_wp_admin_css_colors;

		$tokens = array(
			'accent'        => '#2271b1',
			'accent_strong' => '#135e96',
			'accent_light'  => '#72aee6',
		);
		$scheme = get_user_option( 'admin_color' );

		if ( empty( $_wp_admin_css_colors[ $scheme ] ) ) {
			$scheme = 'modern';
		}

		$colors = ! empty( $_wp_admin_css_colors[ $scheme ]->colors ) ? $_wp_admin_css_colors[ $scheme ]->colors : array();
		$color_map = array(
			'accent'        => 2,
			'accent_strong' => 1,
			'accent_light'  => 3,
		);

		foreach ( $color_map as $token => $index ) {
			if ( isset( $colors[ $index ] ) && sanitize_hex_color( $colors[ $index ] ) ) {
				$tokens[ $token ] = sanitize_hex_color( $colors[ $index ] );
			}
		}

		return $tokens;
	}

	//registering menu item and page on admin
	function reg_admn_menu(){
		add_submenu_page('options-general.php', __("Html Social Share", "zm-sh"), __("Html Social Share", "zm-sh"), 'manage_options', 'zm_shbt_opt', array($this, 'zm_sh_opt'));
	}

	//registering scripts and styles for admin
	function admin_scripts($hook) {
		if ( 'settings_page_zm_shbt_opt' === $hook ) {
			$admin_style_path = __DIR__ . '/assets/admin.css';
			$admin_style_version = file_exists( $admin_style_path ) ? filemtime( $admin_style_path ) : '2.2.8';
			wp_enqueue_style( 'zm_sh_admin_styles', plugin_dir_url( __FILE__ ) . 'assets/admin.css', array(), $admin_style_version );
			$admin_color_tokens = $this->get_admin_color_scheme_tokens();
			wp_add_inline_style( 'zm_sh_admin_styles', sprintf(
				'.zmsh-settings-wrap{--zmsh-accent:%1$s;--zmsh-accent-strong:%2$s;--zmsh-accent-light:%3$s;}',
				esc_html( $admin_color_tokens['accent'] ),
				esc_html( $admin_color_tokens['accent_strong'] ),
				esc_html( $admin_color_tokens['accent_light'] )
			) );
			$admin_script = file_exists( __DIR__ . '/build/admin-react.js' ) ? 'build/admin-react.js' : 'assets/admin-react.js';
			$admin_script_path = __DIR__ . '/' . $admin_script;
			$admin_script_version = file_exists( $admin_script_path ) ? filemtime( $admin_script_path ) : '2.2.8';
			wp_enqueue_script('zm_sh_admin_scripts', plugin_dir_url( __FILE__ ) . $admin_script, array('jquery', 'wp-components', 'wp-element'), $admin_script_version, true  );

			$iconset_data = [];
			foreach ($this->iconsets->get_iconsets() as $iconset) {
				$icons = [];
				foreach ($iconset->get_icons() as $icon) {
					$icon_type = isset($iconset->types[0]) ? $iconset->types[0] : 'square';
					$icons[] = [
						'id' => (string) $icon['id'],
						'name' => esc_html($icon['name']),
						'preview_url' => isset($icon['image']) ? esc_url_raw($iconset->url . $icon_type . '/' . $icon['image']) : '',
					];
				}

				$iconset_data[] = [
					'id' => esc_attr($iconset->id),
					'name' => esc_html($iconset->name),
					'preview_img' => esc_url_raw($iconset->get_iconset_preview()),
					'types' => array_values((array) $iconset->types),
					'icons' => $icons,
				];
			}

			$defaulted_options = wp_parse_args((array) $this->options, array(
				'title' => __('Share this with your friends', 'zm-sh'),
				'iconset' => 'default',
				'show_in' => array(
					'show_left' => 0,
					'show_right' => 0,
					'show_before_post' => 0,
					'show_after_post' => 0,
				),
				'excludes' => '',
				'iconset_type' => 'square',
				'icons' => array(),
				'g_analytics' => 0,
				'auto_hide_btn' => 0,
				'use_port' => 0,
				'nofollow' => 0,
				'share_templates' => function_exists( 'zm_sh_get_share_templates' ) ? zm_sh_get_share_templates() : array(),
			));
			$exclude_items = $this->get_exclude_items( $defaulted_options['excludes'] );
			$share_template_defaults = function_exists( 'zm_sh_get_default_share_templates' ) ? zm_sh_get_default_share_templates() : array();
			$share_template_overrides = isset( $this->options['share_templates'] ) && is_array( $this->options['share_templates'] ) ? $this->options['share_templates'] : array();

			$legacy_twitter = isset($defaulted_options['icons']['twitter']);
			if ($legacy_twitter && ! isset($defaulted_options['icons']['x'])) {
				$defaulted_options['icons']['x'] = $defaulted_options['icons']['twitter'];
			}

			wp_localize_script(
				'zm_sh_admin_scripts',
				'zm_sh_react_settings',
				array(
					'ajax_url'       => admin_url('admin-ajax.php'),
					'nonce'          => wp_create_nonce('zm_sh_admin'),
					'assets_img'     => zm_sh_url_assets_img,
					'iconsets'       => $iconset_data,
					'options'        => $defaulted_options,
					'share_template_defaults' => $share_template_defaults,
					'share_template_overrides' => $share_template_overrides,
					'exclude_items'  => $exclude_items['items'],
					'exclude_custom' => $exclude_items['custom'],
					'defaultIconset' => 'default',
					'strings'        => array(
						'loading'   => __('Loading settings...', 'zm-sh'),
						'saving'    => __('Saving...', 'zm-sh'),
						'saved'     => __('Settings saved.', 'zm-sh'),
						'saveError' => __('Settings could not be saved. Try again.', 'zm-sh'),
					),
				)
			);
		}
		elseif ( 'widgets.php' == $hook ) {
			wp_enqueue_style( 'zm_sh_admin_styles_scripts', plugin_dir_url( __FILE__ ) . 'assets/admin-widget.css', array(), '2.2.4' );
		}
		elseif ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && function_exists( 'vc_map' ) ) {
			wp_enqueue_script('zm_sh_vc_admin_scripts', plugin_dir_url( __FILE__ ) . 'assets/vc_scripts.js', array( 'jquery' ), '2.2.4', true);
			wp_localize_script( 'zm_sh_vc_admin_scripts', 'zm_sh', array(
				'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			) );
		}
	}

	private function get_exclude_items( $excludes ) {
		$items = array();
		$custom = array();

		foreach ( zm_sh_get_excluded_post_identifiers( $excludes ) as $identifier ) {
			$post = null;
			if ( ctype_digit( $identifier ) ) {
				$post = get_post( absint( $identifier ) );
			} else {
				$matches = get_posts( array(
					'post_type' => array( 'post', 'page' ),
					'post_status' => 'publish',
					'posts_per_page' => 1,
					's' => $identifier,
				) );
				foreach ( $matches as $match ) {
					if ( 0 === strcasecmp( $identifier, $match->post_name ) || 0 === strcasecmp( $identifier, $match->post_title ) ) {
						$post = $match;
						break;
					}
				}
			}

			if ( $post && in_array( $post->post_type, array( 'post', 'page' ), true ) && 'publish' === $post->post_status ) {
				$items[] = array(
					'id' => (string) $post->ID,
					'token' => sprintf( '#%d - %s (%s)', $post->ID, get_the_title( $post ), $post->post_type ),
				);
			} else {
				$custom[] = $identifier;
			}
		}

		return array( 'items' => $items, 'has_custom' => ! empty( $custom ), 'custom' => $custom );
	}

	function ajax_search_content() {
		check_ajax_referer( 'zm_sh_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to search content.', 'zm-sh' ) ), 403 );
		}

		$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
		$posts = get_posts( array(
			'post_type' => array( 'post', 'page' ),
			'post_status' => 'publish',
			'posts_per_page' => 20,
			's' => $query,
			'orderby' => 'relevance',
		) );
		$items = array();

		foreach ( $posts as $post ) {
			$items[] = array(
				'id' => (string) $post->ID,
				'token' => sprintf( '#%d - %s (%s)', $post->ID, get_the_title( $post ), $post->post_type ),
			);
		}

		wp_send_json_success( $items );
	}

	function ajax_save_settings() {
		check_ajax_referer( 'zm_sh_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __('You are not allowed to change these settings.', 'zm-sh') ), 403 );
		}

		$serialized_settings = isset( $_POST['settings'] ) && is_string( $_POST['settings'] ) ? wp_kses_post( wp_unslash( $_POST['settings'] ) ) : '';
		$form_data = array();
		parse_str( $serialized_settings, $form_data );

		if ( empty( $form_data['zm_shbt_fld'] ) || ! is_array( $form_data['zm_shbt_fld'] ) ) {
			wp_send_json_error( array( 'message' => __('No settings were received.', 'zm-sh') ), 400 );
		}

		$sanitized = $this->sanitize( $form_data['zm_shbt_fld'] );
		update_option( 'zm_shbt_fld', $sanitized );
		$this->options = $sanitized;

		wp_send_json_success( array(
			'message' => __('Settings saved.', 'zm-sh'),
			'options' => $sanitized,
		) );
	}

	function add_new(){


	}

	function show_all(){


	}
	//option page content
	function zm_sh_opt(){
		?>
        <div class="wrap zmsh-settings-wrap">
            <div class="zm_settings_page_header">
                <h1 class="zm_options_page_heading"><?php esc_html_e("Html Social Share button", "zm-sh");?></h1>
                <p class="zm_settings_page_subtitle"><?php esc_html_e("Configure share buttons, placement, and output format from a single settings page.", "zm-sh");?></p>
            </div>
            <form id="zm-social-share-settings" class="zm_settings" method="post" action="options.php">
            <?php settings_fields( 'zm_shbt_opt' ); ?>
            <div id="zmsh-react-settings-root">
                <div class="zm_settings_loader zm_settings_loader--html" role="status" aria-live="polite">
                    <span class="zm_settings_loader_spinner" aria-hidden="true"></span>
                    <span><?php esc_html_e("Loading settings...", "zm-sh");?></span>
                </div>
            </div>
            <?php submit_button(); ?>
            <p class="desin_by">
			Designed By Hakan Ertan <a target="_blank" href="https://www.tonicons.com/" rel="follow">www.tonicons.com</a>
            </p>
		</form>
        </div>
        <?php
	}

	function zm_reg_sett(){
		register_setting( 'zm_shbt_opt', 'zm_shbt_fld',array($this,"sanitize"));
	}

	function sanitize( $input ){
        $new_input = array(); //get_option("zm_shbt_fld", $zm_sh_default_options);
		$keep_as_is = array( "title", "iconset", "excludes", "icons", "show_in", "show_left", "show_right", "show_before_post", "show_after_post", );

		foreach($input as $key =>$value){
			if( $key == "show_in" && is_array( $value ) ) {
				foreach($value as $key_1=>$value_1) {
					if ( $value_1 ) {
						$new_input["show_in"]["$key_1"] = '1';
					}
				}
			}
			elseif ( 'share_templates' === $key && is_array( $value ) ) {
				$defaults = function_exists( 'zm_sh_get_default_share_templates' ) ? zm_sh_get_default_share_templates() : array();
				$new_input['share_templates'] = array();
				foreach ( $defaults as $platform => $default ) {
					if ( isset( $value[ $platform ] ) && is_string( $value[ $platform ] ) ) {
						$new_input['share_templates'][ $platform ] = sanitize_textarea_field( $value[ $platform ] );
					}
				}
			}
			elseif ( 'title' === $key )
				$new_input[ $key ] = sanitize_text_field( $value );
			elseif ( 'excludes' === $key )
				$new_input[ $key ] = sanitize_textarea_field( $value );
			elseif ( 'iconset' === $key )
				$new_input[ $key ] = sanitize_key( $value );
			elseif ( in_array( $key, $keep_as_is, true ) )
				$new_input[ $key ] = $value;
			elseif(isset( $input[$key] ) and $input[$key] )
				$new_input[$key] = true;

		}
        return $new_input;
    }

}
