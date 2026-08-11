<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginFactory;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApiRegistrar;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;

final class HssbPreSchemaCommunityIconSet {
	public $id = 'community';
	public $name = 'Community';
	public $stylesheet = 'style.css';
	public $preview_img = 'preview.png';
	public $dir = '/tmp/hssb-community-assets';
	public $url = 'https://assets.example.test/community';
	public $types = array( 'diamond' );
	public $icons = array(
		'facebook' => array(
			'id' => 'facebook',
			'name' => 'Facebook',
			'class' => 'facebook',
			'image' => 'facebook.png',
			'url' => 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
		),
	);
}

final class LegacyPreSchemaIconSetImportTest extends WP_UnitTestCase {
	public function testLegacyIconSetActionRunsBeforeTheSettingsSchemaIsBuilt(): void {
		$callback = static function () {
			$GLOBALS['zm_sh_iconset_classes']['HssbPreSchemaCommunityIconSet'] =
				'HssbPreSchemaCommunityIconSet';
		};
		add_action( 'zm_sh_add_iconset', $callback );

		$root = dirname( __DIR__, 2 );
		LegacyApiRegistrar::prepare( $root . '/html-social-share.php' );
		$settings = new class() implements SettingsStateStore {
			public function load() { return SettingsDefaults::create(); }
			public function save( Settings $settings ) { return $settings; }
			public function readStored( $fallback = array() ) { return $fallback; }
			public function replace( Settings $settings, array $storageBase ) { return $storageBase; }
			public function replaceStored( array $stored ) { return $stored; }
		};

		$plugin = ( new PluginFactory() )->create(
			$root,
			$settings,
			array(),
			null,
			array( LegacyApiRegistrar::class, 'importThirdPartyIconSets' )
		);
		remove_action( 'zm_sh_add_iconset', $callback );

		$this->assertTrue( $plugin->iconSets()->has( 'community' ) );
		$iconSet = $plugin->iconSets()->get( 'community' );
		$this->assertSame(
			'https://assets.example.test/community/style.css',
			$plugin->assets()->stylesheetUrl( $iconSet )
		);
		$this->assertSame(
			'https://assets.example.test/community/preview.png',
			$plugin->assets()->previewUrl( $iconSet )
		);
		$this->assertSame(
			'https://assets.example.test/community/diamond/facebook.png',
			$plugin->assets()->iconUrl( $iconSet, 'diamond', 'facebook' )
		);
		$this->assertSame(
			'community',
			$plugin->service( 'admin' )->sanitize(
				array(
					'iconset' => 'community',
					'iconset_type' => 'diamond',
					'icons' => array( 'facebook' => '1' ),
				)
			)['iconset']
		);

		$output = $plugin->renderer()->render(
			array(
				'iconset' => 'community',
				'iconset_type' => 'diamond',
				'icons' => array( 'facebook' => 'on' ),
				'class' => 'in_php_function',
			)
		)->html();
		$this->assertStringContainsString( 'zmshbt in_php_function community diamond', $output );
		$this->assertStringContainsString( "class='facebook'", $output );
	}
}
