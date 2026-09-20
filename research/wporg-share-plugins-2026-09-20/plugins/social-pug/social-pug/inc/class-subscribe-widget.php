<?php
namespace Mediavine\Grow;

/**
 * Class Subscribe_Widget
 * This Class handles interactions with the Grow Subscribe Widget
 * @package Mediavine\Grow
 */
class Subscribe_Widget {

	/** @var null|Subscribe_Widget */
	private static $instance = null;

	/** @var string Content body Class */
	public const CONTENT_BODY_CLASS = 'grow-content-body';

	/**
	 * Get the instance of the class
	 *
	 * @return Subscribe_Widget
	 */
	public static function get_instance(): Subscribe_Widget {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Init function in case it is needed in the future.
	 */
	public function init(): void {
		add_filter( 'post_class', [ $this, 'content_body_class' ] );
	}

	/**
	 * Add the Grow Content Body Class
	 * @param mixed $classes Current post classes
	 *
	 * @return mixed
	 */
	public function content_body_class( $classes ) {
		global $post;
		$current_post = dpsp_get_current_post();
		// Certain Themes reuse the Posts Loop for Other purpose which causes duplicate content body classes
		if ( is_singular() && is_array( $classes ) && $post instanceof \WP_Post && $current_post instanceof \WP_Post && $post->ID === $current_post->ID ) {
			$classes[] = self::CONTENT_BODY_CLASS;
		}
		return $classes;
	}
}
