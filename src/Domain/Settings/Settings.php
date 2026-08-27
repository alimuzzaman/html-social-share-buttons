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
	private $profileLinks;
	private $profileLinkPlacements;
	private $showForCurrentUser;
	private $showForLoggedInUser;
	private $showForLoggedOutUser;
	private $buttonAppearance;

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
		$noFollow,
		array $profileLinks = array(),
		array $profileLinkPlacements = array(),
		$showForCurrentUser = true,
		$showForLoggedInUser = true,
		$showForLoggedOutUser = true,
		$buttonAppearance = ButtonAppearance::LEGACY
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
		$this->profileLinks           = $profileLinks;
		$this->profileLinkPlacements  = $profileLinkPlacements;
		$this->showForCurrentUser     = (bool) $showForCurrentUser;
		$this->showForLoggedInUser    = (bool) $showForLoggedInUser;
		$this->showForLoggedOutUser   = (bool) $showForLoggedOutUser;
		$this->buttonAppearance       = ButtonAppearance::normalize( $buttonAppearance );
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

	public function profileLinks() {
		return $this->profileLinks;
	}

	/**
	 * An absent automatic-placement preference inherits global profile links.
	 * This keeps every pre-existing option value output-compatible.
	 */
	public function profileLinkMode( $placement ) {
		return isset( $this->profileLinkPlacements[ $placement ] )
			&& 'none' === $this->profileLinkPlacements[ $placement ]
			? 'none'
			: 'inherit';
	}

	public function profileLinkPlacements() {
		return $this->profileLinkPlacements;
	}

	public function showForCurrentUser() {
		return $this->showForCurrentUser;
	}

	public function showForLoggedInUser() {
		return $this->showForLoggedInUser;
	}

	public function showForLoggedOutUser() {
		return $this->showForLoggedOutUser;
	}

	public function buttonAppearance() {
		return $this->buttonAppearance;
	}
}
