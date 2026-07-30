<?php

final class WordPressSurfaceContractTest extends WP_UnitTestCase {
	private $surface;

	protected function setUp(): void {
		parent::setUp();
		$this->surface = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/wordpress-surface-baseline.json' ),
			true
		);
	}

	public function testShortcodeWidgetAndBuilderIdentifiersMatchTheContract(): void {
		global $shortcode_tags;

		$this->assertSame(
			$this->surface['shortcode']['callback'],
			$shortcode_tags[ $this->surface['shortcode']['tag'] ]
		);

		$widget = new zm_html_share_widget();
		$this->assertSame( $this->surface['widget']['id_base'], $widget->id_base );
		$this->assertSame(
			10,
			has_action(
				$this->surface['elementor']['hook'],
				$this->surface['elementor']['callback']
			)
		);
		$this->assertSame(
			10,
			has_action(
				$this->surface['wpbakery']['hook'],
				$this->surface['wpbakery']['callback']
			)
		);
	}

	public function testBlockRegistrationAndScriptDependenciesMatchTheContract(): void {
		$block = WP_Block_Type_Registry::get_instance()->get_registered(
			$this->surface['block']['name']
		);

		$this->assertInstanceOf( WP_Block_Type::class, $block );
		$this->assertSame( $this->surface['block']['script_handle'], $block->editor_script_handles[0] );

		$script = wp_scripts()->registered[ $this->surface['block']['script_handle'] ];
		$this->assertSame( $this->surface['block']['script_dependencies'], $script->deps );
		$this->assertStringEndsWith( '/build/social-share.js', $script->src );
		$this->assertStringContainsString(
			'var ' . $this->surface['block']['localized_object'] . ' =',
			$script->extra['data']
		);
	}

	public function testSettingsAssetsAndAjaxHooksMatchTheContract(): void {
		$settings = new zm_sh_settings();
		$settings->admin_scripts( 'settings_page_zm_shbt_opt' );

		$script = wp_scripts()->registered[ $this->surface['settings']['script_handle'] ];
		$this->assertSame( $this->surface['settings']['script_dependencies'], $script->deps );
		$this->assertStringEndsWith( '/' . $this->surface['settings']['script'], $script->src );
		$this->assertStringContainsString(
			'var ' . $this->surface['settings']['localized_object'] . ' =',
			$script->extra['data']
		);
		$this->assertTrue( wp_style_is( $this->surface['settings']['style_handle'], 'enqueued' ) );

		foreach ( $this->surface['ajax_actions'] as $action ) {
			$this->assertNotFalse(
				has_action( 'wp_ajax_' . $action ),
				'Missing AJAX action: ' . $action
			);
		}
	}

	public function testLegacySourcesStillDeclareEveryPersistedIdentifier(): void {
		$root = dirname( __DIR__, 2 );
		$sources = array(
			'metabox' => (string) file_get_contents(
				$root . '/src/Compatibility/Legacy/Integration/MetaboxAdapter.php'
			),
			'settings' => (string) file_get_contents(
				$root . '/src/Compatibility/Legacy/Global/settings-page.php'
			),
			'wpbakery_map' => (string) file_get_contents(
				$root . '/src/Compatibility/Legacy/Integration/WpBakeryAdapter.php'
			),
			'wpbakery_assets' => (string) file_get_contents(
				$root . '/src/Compatibility/Legacy/Admin/LegacySettingsAssetEnqueuer.php'
			),
		);

		foreach (
			array(
				$this->surface['metabox']['id'],
				$this->surface['metabox']['nonce_action'],
				$this->surface['metabox']['nonce_field'],
				$this->surface['metabox']['meta_key'],
			) as $identifier
		) {
			$this->assertStringContainsString( $identifier, $sources['metabox'] );
		}

		foreach (
			array(
				$this->surface['settings']['group'],
				$this->surface['settings']['option'],
				$this->surface['settings']['page_slug'],
				$this->surface['settings']['parent'],
			) as $identifier
		) {
			$this->assertStringContainsString( $identifier, $sources['settings'] );
		}

		$this->assertStringContainsString(
			$this->surface['wpbakery']['base'],
			$sources['wpbakery_map']
		);
		$this->assertStringContainsString(
			$this->surface['wpbakery']['script'],
			$sources['wpbakery_assets']
		);
	}
}
