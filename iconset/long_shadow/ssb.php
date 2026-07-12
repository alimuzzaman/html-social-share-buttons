<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $zm_sh_iconset_classes;
$zm_sh_iconset_classes[]	= 'zm_sh_iconset_long_shadows';

class	zm_sh_iconset_long_shadows extends __iconset_parent_class{
		public $id			= 'long-shadows';
		public $name		= 'Long Shadows';
		public $__FILE__	= __FILE__;
		public $stylesheet	= "style.css";
		public $preview_img	= "preview.png";
		public $types		= array("square", "circle");
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
				'telegram'=>array(
								'id' => 'telegram',
								'name' => "Telegram",
								'class' => 'telegram',
								'image' => 'telegram.svg',
							),
				'bluesky'=>array(
								'id' => 'bluesky',
								'name' => "Bluesky",
								'class' => 'bluesky',
								'image' => 'bluesky.svg',
							),
				'mail'=>array(
								'id' => 'mail',
								'name' => "Email",
								'class' => 'mail',
								'image' => 'mail.png',
							),
				);


}
