<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Settings;

final class Settings {
	private $title;
	private $iconSetId;
	private $defaultIconShape;
	private $placements;
	private $placementShapes;
	private $networkStates;
	private $shareTemplates;
	private $excludedContent;
	private $analyticsEnabled;
	private $autoHideEnabled;
	private $preserveUrlPort;
	private $noFollow;

	public function __construct(
		$title,
		$iconSetId,
		$defaultIconShape,
		array $placements,
		array $placementShapes,
		array $networkStates,
		array $shareTemplates,
		$excludedContent,
		$analyticsEnabled,
		$autoHideEnabled,
		$preserveUrlPort,
		$noFollow
	) {
		$this->title             = (string) $title;
		$this->iconSetId         = (string) $iconSetId;
		$this->defaultIconShape  = (string) $defaultIconShape;
		$this->placements        = $placements;
		$this->placementShapes   = $placementShapes;
		$this->networkStates     = $networkStates;
		$this->shareTemplates    = $shareTemplates;
		$this->excludedContent   = (string) $excludedContent;
		$this->analyticsEnabled  = (bool) $analyticsEnabled;
		$this->autoHideEnabled   = (bool) $autoHideEnabled;
		$this->preserveUrlPort   = (bool) $preserveUrlPort;
		$this->noFollow          = (bool) $noFollow;
	}

	public function title() {
		return $this->title;
	}

	public function iconSetId() {
		return $this->iconSetId;
	}

	public function defaultIconShape() {
		return $this->defaultIconShape;
	}

	public function placements() {
		return $this->placements;
	}

	public function placementShapes() {
		return $this->placementShapes;
	}

	public function networkStates() {
		return $this->networkStates;
	}

	public function shareTemplates() {
		return $this->shareTemplates;
	}

	public function excludedContent() {
		return $this->excludedContent;
	}

	public function analyticsEnabled() {
		return $this->analyticsEnabled;
	}

	public function autoHideEnabled() {
		return $this->autoHideEnabled;
	}

	public function preserveUrlPort() {
		return $this->preserveUrlPort;
	}

	public function noFollow() {
		return $this->noFollow;
	}
}
