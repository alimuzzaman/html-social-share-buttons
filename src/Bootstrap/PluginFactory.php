<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\MigrationRunner;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\WordPressMigrationStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader;

final class PluginFactory {
	public function create( $pluginRoot, SettingsRepository $settings ) {
		$pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
		$extensions = new ExtensionHooks();
		$networks = $extensions->networks(
			( new BuiltInNetworkProvider() )->createRegistry()
		);
		$iconSets = ( new ManifestIconSetProvider(
			$pluginRoot . '/resources/iconsets'
		) )->createRegistry( $networks );
		$iconSets = $extensions->iconSets( $iconSets );

		return new Plugin(
			$settings,
			$networks,
			$iconSets,
			new MigrationRunner( new WordPressMigrationStateStore(), array() ),
			new ExcludedContentPolicy(),
			new ContentPlacementComposer(),
			new FloatingPlacementPlanner(),
			new TranslationLoader(
				$pluginRoot . '/html-social-share.php',
				'html-social-share-buttons'
			),
			new IconSetAssetResolver(
				$pluginRoot . '/assets/iconsets',
				plugins_url(
					'assets/iconsets',
					$pluginRoot . '/html-social-share.php'
				)
			),
			$extensions
		);
	}
}
