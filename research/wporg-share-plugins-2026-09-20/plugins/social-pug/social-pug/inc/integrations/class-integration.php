<?php
namespace Mediavine\Grow\Integrations;

/**
 * Class Integration
 *
 * @package Mediavine\Grow\Integrations
 */
abstract class Integration extends Container {

	/** @var string[] The locations where this integration will perform an action. */
	public $locations = [];

	/**
	 *
	 */
	public function init() {

	}

	/**
	 * Add a hook for a given location.
	 *
	 * @param string $location
	 */
	public function add_hook( $location ) {
		$callback = method_exists( $this, $location ) ? [ $this, $location ] : '__return_false';
		\add_action( self::$hook_prefix . $location, $callback );
	}

	/**
	 * @return mixed
	 */
	abstract public function should_run();
}
