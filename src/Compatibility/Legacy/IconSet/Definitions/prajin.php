<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class zm_sh_iconset_prajin extends __iconset_parent_class {
	public $id = 'prajin';
	public $__FILE__ = __FILE__;

	function __construct() {
		\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::builtInIconSets()
			->hydrate( $this, $this->id, __FILE__ );
		parent::__construct();
	}
}
