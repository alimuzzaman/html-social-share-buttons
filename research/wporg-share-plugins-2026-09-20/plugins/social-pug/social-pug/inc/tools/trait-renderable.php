<?php
namespace Mediavine\Grow\Tools;

trait Renderable {
	/** @var bool $has_rendered If this tool has rendered or not */
	private $has_rendered = false;

	/**
	 *
	 *
	 * @return bool Whether tool has rendered or not
	 */
	public function has_rendered() {
		return $this->has_rendered;
	}

	/**
	 *
	 *
	 * @return mixed The rendering action for this tool, must set $has_rendered to true;
	 */
	abstract public function render();
}
