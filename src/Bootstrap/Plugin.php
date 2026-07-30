<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\MigrationRunner;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader;

final class Plugin {
	private $settings;
	private $networks;
	private $iconSets;
	private $migrations;
	private $excludedContent;
	private $contentPlacement;
	private $floatingPlacement;
	private $translations;
	private $assets;
	private $extensions;
	private $booted = false;

	public function __construct(
		SettingsRepository $settings,
		NetworkRegistry $networks,
		IconSetRegistry $iconSets,
		MigrationRunner $migrations,
		ExcludedContentPolicy $excludedContent,
		ContentPlacementComposer $contentPlacement,
		FloatingPlacementPlanner $floatingPlacement,
		TranslationLoader $translations,
		IconSetAssetResolver $assets,
		ExtensionHooks $extensions
	) {
		$this->settings = $settings;
		$this->networks = $networks;
		$this->iconSets = $iconSets;
		$this->migrations = $migrations;
		$this->excludedContent = $excludedContent;
		$this->contentPlacement = $contentPlacement;
		$this->floatingPlacement = $floatingPlacement;
		$this->translations = $translations;
		$this->assets = $assets;
		$this->extensions = $extensions;
	}

	public function boot() {
		if ( $this->booted ) {
			return;
		}

		$this->migrations->run();
		$this->booted = true;
	}

	public function isBooted() {
		return $this->booted;
	}

	public function settings() {
		return $this->settings;
	}

	public function networks() {
		return $this->networks;
	}

	public function iconSets() {
		return $this->iconSets;
	}

	public function excludedContent() {
		return $this->excludedContent;
	}

	public function contentPlacement() {
		return $this->contentPlacement;
	}

	public function floatingPlacement() {
		return $this->floatingPlacement;
	}

	public function translations() {
		return $this->translations;
	}

	public function assets() {
		return $this->assets;
	}

	public function extensions() {
		return $this->extensions;
	}
}
