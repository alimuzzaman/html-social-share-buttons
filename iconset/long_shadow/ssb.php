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
								'url' => "http://www.facebook.com/sharer.php?u=%%permalink%%&amp;t=%%title%%",
							),
				'x'=>array(
								'id' => 'x',
								'name' => "X (formerly Twitter)",
								'class' => 'twitter',
								'image' => 'twitter.png',
								'url' => "https://x.com/intent/tweet?url=%%permalink%%&amp;text=%%title%%",
							),
				'linkedin'=>array(
								'id' => 'linkedin',
								'name' => "Linkedin",
								'class' => 'linkedin',
								'image' => 'linkedin.png',
								'url' => "http://www.linkedin.com/shareArticle?mini=true&url=%%permalink%%&amp;title=%%title%%",
							),
				'pinterest'=>array(
								'id' => 'pinterest',
								'name' => "Pinterest",
								'class' => 'pinterest',
								'image' => 'pinterest.png',
								'url' => "http://pinterest.com/pin/create/button/?url=%%permalink%%&amp;media=%%imageurl%%&amp;description=%%title%%",
							),
				'mail'=>array(
								'id' => 'mail',
								'name' => "Email",
								'class' => 'mail',
								'image' => 'mail.png',
								'url' => "mailto:?subject=I wanted you to see this site&amp;body=This is about %%title%% %%permalink%%",
							),
				);


}
