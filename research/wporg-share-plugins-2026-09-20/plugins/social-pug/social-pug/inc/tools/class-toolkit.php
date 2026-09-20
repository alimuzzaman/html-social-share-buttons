<?php
namespace Mediavine\Grow\Tools;

/**
 * Class Toolkit
 *
 * @package Mediavine\Grow\Tools
 */
class Toolkit {

	/** @var null */
	private static $instance = null;

	/** @var Tool[] Array of tool classes. */
	private $tools = [];

	/**
	 * Get instance of Tool Container.
	 *
	 * @return Toolkit
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Tasks to be run on init.
	 */
	public function init() {

	}

	/**
	 * Add a set of tools to this class.
	 *
	 * @param Tool[] $tools Array of Tools
	 * @return bool
	 */
	public function add( $tools = [] ) {
		if ( empty( $tools ) ) {
			return false;
		}
		$slugs = array_map(
			function( $tool ) {
			return $tool->get_slug();
			}, $tools
		);
		// Create an associative array by pulling slugs off of each tool;
		$keyed_tools = array_combine( $slugs, $tools );

		// Merge tools into new tools
		$this->tools = array_merge( $this->tools, $keyed_tools );

		return true;
	}

	/**
	 * Get all the tools registered with this class.
	 *
	 * @return Tool[]
	 */
	public function get_all() {
		return self::get_instance()->tools;
	}

	/**
	 * Get a tool by slug
	 *
	 * @param string $slug Slug for tool to get
	 * @return bool|Tool Tool if it exists, false if it doesn't exist or no slug passed in
	 */
	public function get( $slug = '' ) {
		if ( empty( $slug ) ) {
			return false;
		}
		$instance = self::get_instance();
		if ( ! isset( $instance->tools[ $slug ] ) ) {
			return false;
		}

		return $instance->tools[ $slug ];
	}

	/**
	 * Turn an array of Tool classes into an associative array where the key is the slug and the value is the name.
	 *
	 * @param Tool[] $tools
	 * @return array
	 */
	public function make_simple_array( $tools ) {
		$output = [];
		foreach ( $tools as $tool ) {
			$output[ $tool->get_slug() ] = $tool->get_name();
		}

		return $output;
	}
}
