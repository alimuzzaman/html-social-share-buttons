<?php

/**
 * Operational hooks belong to canonical controllers.  The legacy API keeps
 * global callable names, but it must not be the plugin's hook composition
 * root or own WordPress lifecycle callbacks.
 */
final class HookRegistrationContractTest extends WP_UnitTestCase {
	public function testCanonicalControllersOwnTheOperationalHookSurface(): void {
		$this->assertCallback(
			'init',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController::class,
			'loadTranslations',
			2
		);
		$this->assertCallback(
			'init',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Block\BlockRegistrar::class,
			'registerBlocks',
			10
		);
		$this->assertCallback(
			'widgets_init',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget\WidgetRegistrar::class,
			'registerWidget',
			10
		);
		$this->assertCallback(
			'elementor/widgets/register',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Elementor\ElementorRegistrar::class,
			'registerWidget',
			10
		);
		$this->assertCallback(
			'vc_before_init',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\WpBakery\WpBakeryRegistrar::class,
			'registerElement',
			10
		);
		$this->assertCallback(
			'wp_footer',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController::class,
			'footer',
			10
		);
		$this->assertCallback(
			'wp',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController::class,
			'detectExclusion',
			10
		);
		$this->assertCallback(
			'admin_menu',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPageController::class,
			'registerMenu',
			10
		);
		$this->assertCallback(
			'admin_init',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPageController::class,
			'registerSettings',
			10
		);
		$this->assertCallback(
			'admin_enqueue_scripts',
			\Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsPageController::class,
			'enqueueAssets',
			20
		);
	}

	public function testCanonicalAjaxControllerOwnsAllPersistedAjaxActions(): void {
		$controller = \Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\SettingsAjaxController::class;
		foreach ( array(
			'zm_sh_save_settings' => 'save',
			'zm_sh_search_content' => 'search',
			'get_iconset' => 'iconSet',
			'get_iconset_preview' => 'iconSetPreview',
			'get_iconset_details' => 'iconSetDetails',
		) as $action => $method ) {
			$this->assertCallback( 'wp_ajax_' . $action, $controller, $method, 10 );
		}
	}

	public function testLegacyGlobalFunctionsRemainDelegatorsInsteadOfOperationalHooks(): void {
		global $shortcode_tags;

		foreach ( \Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig::SHORTCODE === 'zm_sh_btn'
			? array( 'zm_sh_btn', 'html-social-share-buttons' )
			: array() as $shortcode ) {
			$this->assertArrayHasKey( $shortcode, $shortcode_tags );
			$this->assertIsArray( $shortcode_tags[ $shortcode ] );
			$this->assertInstanceOf(
				\Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Shortcode\ShortcodeController::class,
				$shortcode_tags[ $shortcode ][0]
			);
			$this->assertSame( 'render', $shortcode_tags[ $shortcode ][1] );
		}

		$this->assertTrue( function_exists( 'zm_sh_shortcode_cb' ) );
		$this->assertTrue( function_exists( 'zm_sh_render_block' ) );
		$this->assertStringContainsString( "class='zmshbt in_shortcode", zm_sh_shortcode_cb( array( 'icons' => 'facebook' ) ) );
	}

	private function assertCallback( $hook, $class, $method, $priority ): void {
		global $wp_filter;
		$this->assertArrayHasKey( $hook, $wp_filter, 'Missing hook ' . $hook . '.' );
		$this->assertArrayHasKey( $priority, $wp_filter[ $hook ]->callbacks, 'Missing priority for ' . $hook . '.' );

		foreach ( $wp_filter[ $hook ]->callbacks[ $priority ] as $registration ) {
			$callback = $registration['function'];
			if (
				is_array( $callback ) &&
				isset( $callback[0], $callback[1] ) &&
				$callback[0] instanceof $class &&
				$method === $callback[1]
			) {
				$this->assertSame( 1, $registration['accepted_args'], $hook . ' accepted argument count changed.' );
				return;
			}
		}

		$this->fail( sprintf( 'Missing canonical callback %s::%s for %s.', $class, $method, $hook ) );
	}
}
