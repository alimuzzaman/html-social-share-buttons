<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSet;
use InvalidArgumentException;

final class RenderResult {
	private $iconSet;
	private $shape;
	private $placement;
	private $heading;
	private $relTokens;
	private $buttons;

	public function __construct(
		IconSet $iconSet,
		$shape,
		$placement,
		$heading,
		array $relTokens,
		array $buttons
	) {
		foreach ( $buttons as $button ) {
			if ( ! $button instanceof ResolvedButton ) {
				throw new InvalidArgumentException( 'Every render result button must be resolved.' );
			}
		}

		$this->iconSet = $iconSet;
		$this->shape = (string) $shape;
		$this->placement = (string) $placement;
		$this->heading = (string) $heading;
		$this->relTokens = array_values( $relTokens );
		$this->buttons = array_values( $buttons );
	}

	public function iconSet() {
		return $this->iconSet;
	}

	public function shape() {
		return $this->shape;
	}

	public function placement() {
		return $this->placement;
	}

	public function heading() {
		return $this->heading;
	}

	public function relTokens() {
		return $this->relTokens;
	}

	public function buttons() {
		return $this->buttons;
	}
}
