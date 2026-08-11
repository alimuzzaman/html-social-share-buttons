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

		$this->assertArrayHasKey( $this->surface['shortcode']['tag'], $shortcode_tags );
		$this->assertIsArray( $shortcode_tags[ $this->surface['shortcode']['tag'] ] );
		$this->assertInstanceOf(
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Shortcode\ShortcodeController::class,
			$shortcode_tags[ $this->surface['shortcode']['tag'] ][0]
		);

		$widget = new zm_html_share_widget();
		$this->assertSame( $this->surface['widget']['id_base'], $widget->id_base );
		$this->assertNotFalse( has_action( $this->surface['elementor']['hook'] ) );
		$this->assertNotFalse( has_action( $this->surface['wpbakery']['hook'] ) );
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

		$metadata = json_decode(
			(string) file_get_contents( dirname( __DIR__, 2 ) . '/block.json' ),
			true
		);
		$this->assertSame(
			$metadata['attributes'],
			array_intersect_key( $block->attributes, $metadata['attributes'] )
		);
		$this->assertSame( $metadata['usesContext'], $block->uses_context );
		$this->assertSame( array(), $block->script_handles );
		$this->assertSame( array(), $block->style_handles );
		$this->assertIsArray( $block->render_callback );
		$this->assertInstanceOf(
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Block\BlockRegistrar::class,
			$block->render_callback[0]
		);
		$this->assertSame( 'renderShareBlock', $block->render_callback[1] );
	}

	public function testSettingsAssetsAndAjaxHooksMatchTheContract(): void {
		$settings = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->admin();
		$settings->enqueueAssets( 'settings_page_zm_shbt_opt' );

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

	public function testCanonicalConfigOwnsEveryPersistedIdentifier(): void {
		$config = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->config();

		$this->assertSame( $this->surface['metabox']['id'], $config->metaboxId() );
		$this->assertSame( $this->surface['metabox']['nonce_action'], $config->metaboxNonceAction() );
		$this->assertSame( $this->surface['metabox']['nonce_field'], $config->metaboxNonceField() );
		$this->assertSame( $this->surface['metabox']['meta_key'], $config->disabledMetaKey() );
		$this->assertSame( $this->surface['settings']['group'], $config->settingsGroup() );
		$this->assertSame( $this->surface['settings']['option'], $config->optionName() );
		$this->assertSame( $this->surface['settings']['page_slug'], $config->settingsPage() );
		$this->assertSame( $this->surface['wpbakery']['base'], $config->wpBakeryBase() );
		$this->assertSame( $this->surface['wpbakery']['script_handle'], $config->adminWpBakeryScriptHandle() );
	}
}
