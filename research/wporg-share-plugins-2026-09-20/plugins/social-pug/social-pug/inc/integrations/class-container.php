<?php
namespace Mediavine\Grow\Integrations;

class Container extends \Social_Pug {

	/** @var null  */
	private static $instance = null;

	/** @var string Prefix to use for all integration hook names. */
	public static $hook_prefix = 'mv_grow_integration_hook_';

	/** @var Integration[] Array of integration classes. */
	private $integrations = [];

	/**
	 *
	 *
	 * @return Container|\Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 *
	 */
	public function init() {

	}

	/**
	 * Add a set of integrations to this class.
	 *
	 * @param Integration[] $integration
	 * @return bool
	 */
	public function add_integrations( $integration = [] ) {
		if ( empty( $integration ) ) {
			return false;
		}
		$this->integrations = array_merge( $this->integrations, $integration );
		return true;
	}

	/**
	 * Get all the integrations registered with this class.
	 *
	 * @return Integration[]
	 */
	public function get_integrations() {
		return $this->integrations;
	}

	/**
	 * Determine if any integrations are currently active for a given integration location.
	 *
	 * @param string $location
	 * @return bool
	 */
	public static function has_location( $location ) {
		$container  = self::get_instance();
		$should_run = false;
		foreach ( $container->integrations as $integration ) {
			if ( $integration->should_run() && in_array( $location, $integration->locations, true ) ) {
				$should_run = true;
				$integration->add_hook( $location );
			}
		}

		return $should_run;
	}

	/**
	 * Run integration actions for a given location.
	 *
	 * @param string $location
	 * @param array $args
	 * @return bool
	 */
	public static function do_location( $location, $args = [] ) {
		if ( empty( $location ) ) {
			return false;
		}
		\do_action( self::$hook_prefix . $location, $args );

		return true;
	}
}
