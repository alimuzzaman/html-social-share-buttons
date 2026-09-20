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
	private $requiresFrontendJs;
	private $frontendFeatures;

	public function __construct( $html, array $stylesheets, array $printedIcons, $requiresFrontendJs = false, array $frontendFeatures = array() ) {
		$this->html = (string) $html;
		$this->stylesheets = $stylesheets;
		$this->printedIcons = $printedIcons;
		$this->requiresFrontendJs = (bool) $requiresFrontendJs;
		$this->frontendFeatures = array_values( $frontendFeatures );
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

	public function requiresFrontendJs() {
		return $this->requiresFrontendJs;
	}

	public function frontendFeatures() {
		return $this->frontendFeatures;
	}
}
