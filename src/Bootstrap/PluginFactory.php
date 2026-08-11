<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\MigrationRunner;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\WordPressMigrationStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering\ShareContextFactory;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ExcludedContentLookup;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\FormPresenter;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\IconSetPayloadBuilder;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\MetaboxController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsAjaxController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsAssetEnqueuer;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPageController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPayloadBuilder;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Block\BlockRegistrar;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Elementor\ElementorRegistrar;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Shortcode\ShortcodeController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget\WidgetRegistrar;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\WpBakery\WpBakeryRegistrar;

final class PluginFactory {
	public function create(
		$pluginRoot,
		SettingsRepository $settings,
		array $subscribers = array(),
		$renderer = null,
		$configureRegistries = null
	) {
		$pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
		$paths = PluginPaths::fromPluginFile( $pluginRoot . '/html-social-share.php' );
		$config = new PluginConfig( $paths );
		$extensions = new ExtensionHooks();
		$networks = $extensions->networks(
			( new BuiltInNetworkProvider() )->createRegistry()
		);
		$iconSets = ( new ManifestIconSetProvider(
			$pluginRoot . '/resources/iconsets'
		) )->createRegistry( $networks );
		$iconSets = $extensions->iconSets( $iconSets );
		$assets = new IconSetAssetResolver(
			$paths->directory(),
			$paths->url(),
			$paths->file()
		);
		if ( is_callable( $configureRegistries ) ) {
			call_user_func( $configureRegistries, $iconSets, $networks, $assets );
		}

		$translations = new TranslationLoader( $paths->file(), $config->textDomain() );
		$facade = $renderer instanceof RenderFacade
			? $renderer
			: new RenderFacade(
				$networks,
				$iconSets,
				$assets,
				$extensions,
				new ShareContextFactory( null, $extensions )
			);
		$assetCollector = new AssetCollector(
			$assets->stylesheetUrl( $iconSets->get( 'default' ) )
		);
		$excludedContent = new ExcludedContentPolicy();
		$contentPlacement = new ContentPlacementComposer();
		$floatingPlacement = new FloatingPlacementPlanner();
		$services = array(
			'forms'     => new FormPresenter( $iconSets, $networks, $config, $assets ),
			'frontend'  => new FrontendController(
				$settings,
				$facade,
				$contentPlacement,
				$floatingPlacement,
				$excludedContent,
				$translations,
				$assetCollector,
				$config->disabledMetaKey(),
				null,
				$config->legacyTextDomain()
			),
			'shortcode' => new ShortcodeController(
				$facade,
				$settings,
				$iconSets,
				$assetCollector,
				$config
			),
			'block'     => new BlockRegistrar(
				$paths->directory(),
				$facade,
				$settings,
				$iconSets,
				$assets,
				$networks,
				$assetCollector,
				$config
			),
			'widgets'   => new WidgetRegistrar(
				$facade,
				$settings,
				$iconSets,
				$networks,
				$assetCollector,
				$config
			),
			'elementor' => new ElementorRegistrar(
				$facade,
				$settings,
				$iconSets,
				$networks,
				$assetCollector,
				$assets,
				$config
			),
			'wpBakery'  => new WpBakeryRegistrar(
				$facade,
				$settings,
				$iconSets,
				$networks,
				$assetCollector,
				$config
			),
			'metabox'   => new MetaboxController( $config ),
		);

		if ( $settings instanceof SettingsStateStore ) {
			$shapes = $this->iconShapes( $iconSets );
			$schema = $extensions->settingsSchema(
				new SettingsSchema( $networks->ids(), $iconSets->ids(), $shapes )
			);
			$iconSetPayloads = new IconSetPayloadBuilder( $iconSets, $networks, $assets );
			$contentLookup = new ExcludedContentLookup( $excludedContent );
			$settingsAjax = new SettingsAjaxController(
				$settings,
				new SettingsRequestSanitizer( $schema ),
				new OptionSettingsRequestMapper(),
				$contentLookup,
				$iconSetPayloads,
				$config
			);
			$services['admin'] = new SettingsPageController(
				$settingsAjax,
				new SettingsAssetEnqueuer(
					$paths->directory(),
					$paths->file(),
					new SettingsPayloadBuilder(
						$settings,
						$contentLookup,
						$iconSetPayloads,
						$networks,
						$paths->file(),
						$config
					),
					$config
				),
				$config
			);
		}

		$hookSubscribers = array();
		foreach ( $services as $service ) {
			if ( method_exists( $service, 'registerHooks' ) ) {
				$hookSubscribers[] = $service;
			}
		}
		foreach ( $subscribers as $subscriber ) {
			$hookSubscribers[] = $subscriber;
		}

		return new Plugin(
			$settings,
			$networks,
			$iconSets,
			new MigrationRunner( new WordPressMigrationStateStore(), array() ),
			$excludedContent,
			$contentPlacement,
			$floatingPlacement,
			$translations,
			$assets,
			$extensions,
			$paths,
			$config,
			new HookRegistrar( $hookSubscribers ),
			$facade,
			$services
		);
	}

	private function iconShapes( $iconSets ) {
		$shapes = array();
		foreach ( $iconSets->all() as $iconSet ) {
			foreach ( $iconSet->shapes() as $shape ) {
				$shapes[ $shape ] = $shape;
			}
		}

		return array_values( $shapes );
	}
}
