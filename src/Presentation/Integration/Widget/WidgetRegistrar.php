<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget;

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;

final class WidgetRegistrar {
	private $renderer;
	private $settings;
	private $iconSets;
	private $networks;
	private $assets;
	private $config;

	public function __construct(
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		AssetCollector $assets,
		PluginConfig $config
	) {
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( $this->config->widgetHook(), array( $this, 'registerWidget' ) );
	}

	public function registerWidget() {
		register_widget( $this->widget() );
	}

	/** Historical bridge entry point. */
	public function register() {
		return $this->registerWidget();
	}

	/** Historical bridge entry point. */
	public function render( $arguments, $instance ) {
		return $this->widget()->widget( $arguments, $instance );
	}

	/** Historical bridge entry point. */
	public function update( $newInstance, $oldInstance ) {
		return $this->widget()->update( $newInstance, $oldInstance );
	}

	/** Historical bridge entry point. */
	public function form( $instance ) {
		return $this->widget()->form( $instance );
	}

	private function widget() {
		return new ShareWidget(
			$this->renderer,
			$this->settings,
			$this->iconSets,
			$this->networks,
			$this->assets,
			$this->config
		);
	}
}
