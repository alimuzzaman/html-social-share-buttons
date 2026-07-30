<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Network;

use InvalidArgumentException;

final class Network {
	private $id;
	private $label;
	private $cssClass;
	private $defaultShareTemplate;
	private $placeholders;
	private $enabledByDefault;

	public function __construct(
		$id,
		$label,
		$cssClass,
		$defaultShareTemplate,
		array $placeholders,
		$enabledByDefault
	) {
		$id = (string) $id;
		$label = (string) $label;
		$cssClass = (string) $cssClass;
		$defaultShareTemplate = (string) $defaultShareTemplate;

		if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $id ) ) {
			throw new InvalidArgumentException( 'Network IDs must be lowercase slugs.' );
		}
		if ( '' === trim( $label ) ) {
			throw new InvalidArgumentException( 'Network labels cannot be empty.' );
		}
		if ( ! preg_match( '/^[a-z][a-z0-9_-]*$/', $cssClass ) ) {
			throw new InvalidArgumentException( 'Network CSS classes must be safe identifiers.' );
		}
		$normalizedPlaceholders = array();
		foreach ( $placeholders as $placeholder ) {
			$placeholder = (string) $placeholder;
			if ( ! preg_match( '/^%%[a-z]+%%$/', $placeholder ) ) {
				throw new InvalidArgumentException( 'Network placeholders must use the %%name%% form.' );
			}
			$normalizedPlaceholders[ $placeholder ] = $placeholder;
		}

		preg_match_all( '/%%[a-z]+%%/', $defaultShareTemplate, $templatePlaceholders );
		foreach ( array_unique( $templatePlaceholders[0] ) as $placeholder ) {
			if ( ! isset( $normalizedPlaceholders[ $placeholder ] ) ) {
				throw new InvalidArgumentException( 'The share template contains an undeclared placeholder.' );
			}
		}

		$this->id = $id;
		$this->label = $label;
		$this->cssClass = $cssClass;
		$this->defaultShareTemplate = $defaultShareTemplate;
		$this->placeholders = array_values( $normalizedPlaceholders );
		$this->enabledByDefault = (bool) $enabledByDefault;
	}

	public function id() {
		return $this->id;
	}

	public function label() {
		return $this->label;
	}

	public function cssClass() {
		return $this->cssClass;
	}

	public function defaultShareTemplate() {
		return $this->defaultShareTemplate;
	}

	public function placeholders() {
		return $this->placeholders;
	}

	public function enabledByDefault() {
		return $this->enabledByDefault;
	}
}
