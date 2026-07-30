<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class zm_sh_iconset{
	public	$options;
	private	$iconsets;
	private	$iconsetId;
	private	$curr_iconset;

	function __get($var){
		if($var == 'curr_iconset')
			return $this->get_current_iconset();
		if($var == 'private')
			return $this->get_current_iconset();
		elseif(isset($this->iconsets->$var))
			return $this->iconsets->$var;
	}

	function __construct(){
		global $zm_sh, $zm_sh_default_options;
		global $zm_sh_iconset_classes;
		$zm_sh_iconset_classes = is_array( $zm_sh_iconset_classes ) ? $zm_sh_iconset_classes : array();
		$this->iconsets	= new stdClass;
		//var_dump($this->iconsets);
		$this->options =
			\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::settings()
				->stored( $zm_sh_default_options );
		if ( ! is_array( $this->options ) ) {
			$this->options = is_array( $zm_sh_default_options ) ? $zm_sh_default_options : array();
		}
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, '__iconset_parent_class')) {
				$zm_sh_iconset_classes[$class] = $class;
			}
		}
		//print_r($zm_sh_iconset_classes);
		foreach($zm_sh_iconset_classes as $iconset)
			$this->add_iconset(new $iconset);
		\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Hook\LegacyExtensionHookBridge::registerIconSets();
		add_action( 'wp_ajax_get_iconset', array($this, 'wp_ajax_get_iconset') );
		add_action( 'wp_ajax_get_iconset_preview', array($this, 'wp_ajax_get_iconset_preview') );
	}

	function add_iconset($iconset){
		$id = $iconset->id;
		if(empty($id)) return;
		$this->iconsets->$id = $iconset;
		return $this->iconsets->$id;
	}

	function get_current_iconset(){
		$this->iconsetId = isset($this->options['iconset']) ? $this->options['iconset'] : 'default';
		$this->curr_iconset = $this->get_iconset($this->iconsetId);
		return $this->curr_iconset;
	}

	function set_current_iconset($iconset_name){
		$this->curr_iconset = $iconset_name;
		return true;
	}

	function get_iconset($iconset = "default", $setAsCurrent = false){
		//print_r(debug_backtrace());
		//if(empty($iconset)) return false;
		//echo '"><pre>';
		//print_r(debug_backtrace ());
		if($setAsCurrent && isset($this->iconsets->$iconset))
			$this->curr_iconset = $this->iconsets->$iconset;
		if(isset($this->iconsets->$iconset))
			return $this->iconsets->$iconset;
		elseif(isset($this->iconsets->default))
			return  $this->iconsets->default;
		else
			return false;
	}

	function get_iconsets(){
		return $this->iconsets;
	}

	function get_iconset_list(){
		$iocnsets = array();
		foreach($this->iconsets as $iconset){
			$id = $iconset->id;
			$iocnsets[$id] = $iconset->name;
		}
		return $iocnsets;
	}

	public function remove_iconset($id){
		unset($this->iconsets->$id);
		return $id;
	}


	function wp_ajax_get_iconset_preview(){
		check_ajax_referer( 'zm_sh_admin', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		if (!isset($_POST['iconsetId'])) {
			wp_die('Missing iconset ID');
		}
		$iconset_id	= sanitize_key(wp_unslash($_POST['iconsetId']));
		$iconset = $this->get_iconset($iconset_id);
		if ( ! is_object( $iconset ) ) wp_die( 'Invalid iconset' );
		$preview	= $iconset->get_iconset_preview();
		wp_die( esc_url( $preview ) );
	}

	function wp_ajax_get_iconset(){
		check_ajax_referer( 'zm_sh_admin', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		if (!isset($_POST['iconsetId'])) {
			wp_die('Missing iconset ID');
		}
		$iconset_id	= sanitize_key(wp_unslash($_POST['iconsetId']));
		$iconset	= $this->get_iconset($iconset_id);
		if ( ! is_object( $iconset ) ) wp_die( 'Invalid iconset' );
		wp_send_json( $iconset );
	}

}


abstract class __iconset_parent_class implements interface_iconset{
	public		$__FILE__;
	public		$id;
	public		$name;
	public		$types;
	public		$icons;
	public		$inTheme	= false;
	public		$inChildTheme = false;
	public		$dir;
	public		$url;
	public		$stylesheet_url;
	public		$preview_img_url;
	public		$preview_img_dir;

	public		$stylesheet		= "style.css";
	public		$preview_img	= "preview.png";

	function __construct(){
		if (
			false !== strpos(
				str_replace( '\\', '/', (string) $this->__FILE__ ),
				'/src/Compatibility/Legacy/IconSet/Definitions/'
			)
		) {
			$asset_directory = 'long-shadows' === $this->id ? 'long_shadow' : sanitize_key( $this->id );
			$this->__FILE__ =
				\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::pluginRoot() .
				'/iconset/' . $asset_directory . '/ssb.php';
		}
		$this->set_dir_and_url($this->__FILE__);
		foreach ( (array) $this->icons as $id => $icon ) {
			$this->icons[ $id ]['url'] = zm_sh_get_share_template( $id, isset( $icon['url'] ) ? $icon['url'] : '' );
		}

	}


	function set_dir_and_url($__FILE__){
		if(isset($this->inTheme) and $this->inTheme){
			$this->dir				= get_template_directory(). $this->inTheme;
			$this->url				= get_template_directory_uri(). $this->inTheme;
		}
		elseif(isset($this->inChildTheme) and $this->inChildTheme){
			$this->dir				= get_stylesheet_directory(). $this->inChildTheme;
			$this->url				= get_stylesheet_directory_uri(). $this->inChildTheme;
		}
		else{
			$this->dir				= plugin_dir_path( $__FILE__ );
			$this->url				= plugins_url( "/", $__FILE__ );
		}
		$this->stylesheet_url	= $this->url . $this->stylesheet;
		$this->preview_img_url	= $this->url . $this->preview_img;
		$this->preview_img_dir	= $this->dir . $this->preview_img;
	}

	public function get_icons(){
		return $this->icons;
	}

	public function get_icons_id_name(){
		$new	= array();
		foreach( $this->icons as $id=>$icon)
			$new[$id]	= $icon['name'];
		return $new;
	}

	public function push_icon($icon){
		$this->icons[]	= $icon;
	}
	public function get_iconset_preview(){
		return $this->url . $this->preview_img;
	}


}

// Bundled legacy definitions are explicit; add-ons still register through the
// declared-class scan and zm_sh_add_iconset hook above.
$bundled_iconset_classes = array(
	'zm_sh_iconset_default',
	'zm_sh_iconset_flat',
	'zm_sh_iconset_long_shadows',
	'zm_sh_iconset_prajin',
);
foreach ( $bundled_iconset_classes as $bundled_iconset_class ) {
	require_once dirname( __DIR__ ) . '/IconSet/Definitions/' .
		str_replace( 'zm_sh_iconset_', '', $bundled_iconset_class ) .
		'.php';
	if ( class_exists( $bundled_iconset_class ) ) {
		$zm_sh_iconset_classes[ $bundled_iconset_class ] = $bundled_iconset_class;
	}
}

$legacy_iconset_root =
	\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::pluginRoot() .
	'/iconset';
if ( is_dir( $legacy_iconset_root ) ) {
	foreach ( scandir( $legacy_iconset_root ) as $legacy_iconset_directory ) {
		if ( '.' === $legacy_iconset_directory || '..' === $legacy_iconset_directory ) {
			continue;
		}
		$legacy_iconset_file =
			$legacy_iconset_root . '/' . $legacy_iconset_directory . '/ssb.php';
		if ( file_exists( $legacy_iconset_file ) ) {
			require_once $legacy_iconset_file;
			$legacy_iconset_class = 'zm_sh_iconset_' . $legacy_iconset_directory;
			if ( class_exists( $legacy_iconset_class ) ) {
				$zm_sh_iconset_classes[ $legacy_iconset_class ] = $legacy_iconset_class;
			}
		}
	}
}


add_action( 'wp_ajax_get_iconset_details', 'wp_ajax_get_iconset_details' );

function wp_ajax_get_iconset_details() {
	check_ajax_referer( 'zm_sh_admin', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
	if (!isset($_POST['iconset'])) {
		wp_die('Missing iconset');
	}
	$iconset_class	= new zm_sh_iconset;
	$iconset = $iconset_class->get_iconset(sanitize_key(wp_unslash($_POST['iconset'])));
	if ( ! is_object( $iconset ) ) wp_die( 'Invalid iconset' );
	wp_send_json( $iconset->get_icons_id_name() );
}
