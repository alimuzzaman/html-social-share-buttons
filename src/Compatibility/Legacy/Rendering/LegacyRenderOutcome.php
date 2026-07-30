<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering;

final class LegacyRenderOutcome {
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
