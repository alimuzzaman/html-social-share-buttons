<?php

namespace Elementor;

/**
 * Minimal static-analysis surface for the optional Elementor dependency.
 */
class Widget_Base {
	public function __construct( array $data = array(), array $args = array() ) {
	}

	protected function start_controls_section( $id, array $arguments = array() ) {
	}

	protected function add_control( $id, array $arguments = array() ) {
	}

	protected function end_controls_section() {
	}

	protected function get_settings_for_display() {
		return array();
	}
}

class Controls_Manager {
	const TAB_CONTENT = 'content';
	const TEXT = 'text';
	const SELECT = 'select';
	const SELECT2 = 'select2';
}
