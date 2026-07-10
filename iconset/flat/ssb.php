<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class	zm_sh_iconset_flat extends __iconset_parent_class{
		public $id = 'flat';
		public $name = 'Flat';
		public $__FILE__	= __FILE__;					//Required, the path of file.
		//public $stylesheet	= "style.css";			//Optional, Use if that not named as style.css
		//public $preview_img	= "preview.png";		//Optional, Use if that not named as preview.png
		public $types = array("square", "circle");
		public $icons = array(
				'facebook'=>array(
								'id' => 'facebook',
								'name' => "Facebook",
								'class' => 'facebook',
								'image' => 'Facebook.png',
							),
				'x'=>array(
								'id' => 'x',
								'name' => "X (formerly Twitter)",
								'class' => 'twitter',
								'image' => 'Twitter.png',
							),
				'linkedin'=>array(
								'id' => 'linkedin',
								'name' => "Linkedin",
								'class' => 'linkedin',
								'image' => 'Linkedin.png',
							),
				'pinterest'=>array(
								'id' => 'pinterest',
								'name' => "Pinterest",
								'class' => 'pinterest',
								'image' => 'Pinterest.png',
							),
				'mail'=>array(
								'id' => 'mail',
								'name' => "Email",
								'class' => 'mail',
								'image' => 'Mail.png',
							),
				);

}

