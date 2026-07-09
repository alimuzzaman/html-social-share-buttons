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
	}

	//registering menu item and page on admin
	function reg_admn_menu(){
		add_menu_page(__("Html Social Share", "zm-sh"), __("Html Social Share", "zm-sh"),"administrator", "zm_shbt_opt",array($this,"zm_sh_opt"),"","59.679861");
	}

	//registering scripts and styles for admin
	function admin_scripts($hook) {
		if ( 'toplevel_page_zm_shbt_opt' == $hook ) {
			wp_enqueue_style( 'zm_sh_admin_styles',  plugin_dir_url( __FILE__ ) . 'assets/admin.css', array(), '2.2.4' );
			wp_enqueue_script('zm_sh_admin_scripts', plugin_dir_url( __FILE__ ) . 'assets/admin-react.js', array('jquery', 'wp-element'), '2.2.4', true  );

			$iconset_data = [];
			foreach ($this->iconsets->get_iconsets() as $iconset) {
				$icons = [];
				foreach ($iconset->get_icons() as $icon) {
					$icons[] = [
						'id' => (string) $icon['id'],
						'name' => esc_html($icon['name']),
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
			));

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
					'defaultIconset' => 'default',
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
            <div id="zmsh-react-settings-root"></div>
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
			if( $key == "show_in")
				foreach($value as $key_1=>$value_1)
					$new_input["show_in"]["$key_1"] = $input[$key_1];
			elseif( in_array($key, $keep_as_is))
				$new_input[$key] = $value;
			elseif(isset( $input[$key] ) and $input[$key] )
				$new_input[$key] = true;

		}
        return $new_input;
    }

}
