<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Elementor;

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;

final class ElementorRegistrar {
	private $renderer;
	private $settings;
	private $iconSets;
	private $networks;
	private $assets;
	private $assetResolver;
	private $config;

	public function __construct(
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		AssetCollector $assets,
		IconSetAssetResolver $assetResolver,
		PluginConfig $config
	) {
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
		$this->assetResolver = $assetResolver;
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( $this->config->elementorHook(), array( $this, 'registerWidget' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueueEditorAssets' ) );
	}

	public function enqueueEditorAssets() {
		$assetPath = $this->config->paths()->directory() . 'build/vc-scripts.asset.php';
		$asset = file_exists( $assetPath ) ? require $assetPath : array();
		$version = isset( $asset['version'] ) ? $asset['version'] : $this->config->version();
		wp_enqueue_script(
			$this->config->adminElementorScriptHandle(),
			plugins_url( 'build/vc-scripts.js', $this->config->paths()->file() ),
			array( 'jquery', 'elementor-editor' ),
			$version,
			true
		);
		wp_localize_script(
			$this->config->adminElementorScriptHandle(),
			$this->config->adminWpBakeryObject(),
			array(
				'nonce'           => wp_create_nonce( $this->config->adminNonceAction() ),
				'elementorWidget' => $this->config->elementorWidgetName(),
				'legacyIconsets'  => array(
					'default' => __( 'Default (legacy)', 'html-social-share-buttons' ),
				),
			)
		);
	}

	public function registerWidget( $widgetsManager ) {
		if ( ! class_exists( '\\Elementor\\Widget_Base' ) || ! is_object( $widgetsManager ) ) {
			return;
		}
		$this->registerStyles();
		ElementorShareWidget::configureDependencies(
			$this->renderer,
			$this->settings,
			$this->iconSets,
			$this->networks,
			$this->assets,
			$this->config
		);
		$widget = new ElementorShareWidget(
			$this->renderer,
			$this->settings,
			$this->iconSets,
			$this->networks,
			$this->assets,
			$this->config
		);

		if ( method_exists( $widgetsManager, 'register' ) ) {
			$widgetsManager->register( $widget );
			return;
		}

		if ( method_exists( $widgetsManager, 'register_widget_type' ) ) {
			$widgetsManager->register_widget_type( $widget );
		}
	}

	/** Historical bridge entry point. */
	public function register( $widgetsManager ) {
		return $this->registerWidget( $widgetsManager );
	}

	private function registerStyles() {
		foreach ( $this->iconSets->all() as $iconSet ) {
			wp_register_style(
				'social-share-' . sanitize_key( $iconSet->id() ),
				$this->assetResolver->stylesheetUrl( $iconSet ),
				array(),
				$this->config->version()
			);
		}
	}
}
