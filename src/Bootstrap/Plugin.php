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
	private $paths;
	private $config;
	private $hooks;
	private $renderer;
	private $services;
	private $booted = false;
	private $booting = false;

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
		ExtensionHooks $extensions,
		PluginPaths $paths = null,
		PluginConfig $config = null,
		HookRegistrar $hooks = null,
		$renderer = null,
		array $services = array()
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
		$this->paths = $paths;
		$this->config = $config;
		$this->hooks = $hooks ? $hooks : new HookRegistrar();
		$this->renderer = $renderer;
		$this->services = $services;
	}

	public function boot() {
		if ( $this->booted || $this->booting ) {
			return;
		}

		$this->booting = true;
		try {
			$this->migrations->run();
			$this->hooks->registerHooks();
			$this->booted = true;
		} finally {
			$this->booting = false;
		}
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

	public function paths() {
		return $this->paths;
	}

	public function config() {
		return $this->config;
	}

	public function hooks() {
		return $this->hooks;
	}

	/**
	 * The canonical render facade.  The loose type deliberately permits the
	 * bootstrap package to be loaded before WordPress-facing presentation
	 * classes are assembled, while callers still receive one explicit service.
	 */
	public function renderer() {
		return $this->renderer;
	}

	/**
	 * Explicit access to optional WordPress integration services.
	 */
	public function service( $name ) {
		$name = (string) $name;

		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	public function frontend() {
		return $this->service( 'frontend' );
	}

	public function shortcode() {
		return $this->service( 'shortcode' );
	}

	public function block() {
		return $this->service( 'block' );
	}

	public function widgets() {
		return $this->service( 'widgets' );
	}

	public function elementor() {
		return $this->service( 'elementor' );
	}

	public function wpBakery() {
		return $this->service( 'wpBakery' );
	}

	public function admin() {
		return $this->service( 'admin' );
	}

	public function metabox() {
		return $this->service( 'metabox' );
	}
}
