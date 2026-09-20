<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface Kiwi_Social_Share_Social (Bars)
 */
interface Kiwi_Social_Share_Interface_Frontend {
	/**
	 * Returns a string containing HTML code of the bars
	 *
	 * @return mixed
	 */
	public function generate_frontend_bar();

	/**
	 * Outputs the string on frontend
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public function display_bar($content = '');
}