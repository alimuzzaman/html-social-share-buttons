<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*add_action('zm_sh_add_iconset', 'zm_sh_iconset_default');
function zm_sh_iconset_default(){
	global $zm_sh;

	$zm_sh->iconsets->add_iconset(new zm_sh_iconset_default);

}*/

class zm_sh_iconset_default extends __iconset_parent_class{
		public $id			= 'default';				//Required
		public $name		= 'Default';				//Required
		public $__FILE__	= __FILE__;					//Required, the path of file.
		//public $stylesheet	= "style.css";			//Optional, Use if that not named as style.css
		//public $preview_img	= "preview.png";		//Optional, Use if that not named as preview.png
		public $types		= array("square");
		public $icons		= array(
				'facebook'=>array(
								'id' => 'facebook',
								'name' => "Facebook",
								'class' => 'facebook',
								'image' => 'facebook.png',
							),
				'x'=>array(
								'id' => 'x',
								'name' => "X (formerly Twitter)",
								'class' => 'twitter',
								'image' => 'twitter.png',
							),
				'linkedin'=>array(
								'id' => 'linkedin',
								'name' => "Linkedin",
								'class' => 'linkedin',
								'image' => 'linkedin.png',
							),
				'pinterest'=>array(
								'id' => 'pinterest',
								'name' => "Pinterest",
								'class' => 'pinterest',
								'image' => 'pinterest.png',
							),
				'mail'=>array(
								'id' => 'mail',
								'name' => "Email",
								'class' => 'mail',
								'image' => 'mail.png',
							),
				);




}

