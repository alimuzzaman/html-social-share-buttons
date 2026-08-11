<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering;

/**
 * Presentation output plus the assets needed to display its icon anchors.
 * Asset emission stays in the frontend integration, never in an adapter.
 */
final class RenderOutcome {
	private $html;
	private $stylesheets;
	private $printedIcons;

	public function __construct( $html, array $stylesheets, array $printedIcons ) {
		$this->html = (string) $html;
		$this->stylesheets = $stylesheets;
		$this->printedIcons = $printedIcons;
	}

	public function html() {
		return $this->html;
	}

	public function stylesheets() {
		return $this->stylesheets;
	}

	public function printedIcons() {
		return $this->printedIcons;
	}
}
