<?php

final class SettingsContractTest extends WP_UnitTestCase {
	private $settings;
	private $schema;

	protected function setUp(): void {
		parent::setUp();
		$this->settings = new zm_sh_settings();
		$this->schema = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/settings-schema-baseline.json' ),
			true
		);
	}

	public function testDefaultSettingsMatchThePersistedSchemaContract(): void {
		global $zm_sh_default_options;

		$this->assertIsArray( $this->schema );
		$this->assertSame( 'zm_shbt_fld', $this->schema['option_name'] );
		$this->assertTrue( $this->schema['autoload']['new_option'] );
		$this->assertSame( 'preserved', $this->schema['autoload']['existing_option'] );
		$this->assertSame( $this->schema['default_options'], $zm_sh_default_options );
	}

	public function testCompleteSettingsSanitizationMatchesTheSchemaContract(): void {
		$case = $this->schema['sanitization_case'];

		$this->assertSame( $case['expected'], $this->settings->sanitize( $case['input'] ) );
	}

	public function testShareTemplateMapMatchesTheSchemaContract(): void {
		$this->assertSame(
			$this->schema['share_template_defaults'],
			zm_sh_get_default_share_templates()
		);
	}

	public function testLegacyTwitterSettingMapsAtRuntimeWithoutChangingStoredData(): void {
		$stored = array(
			'iconset' => 'default',
			'icons'   => array(
				'twitter'  => '1',
				'facebook' => '1',
			),
		);
		update_option( $this->schema['option_name'], $stored );

		$renderer = new zm_social_share();

		$this->assertSame( $stored, get_option( $this->schema['option_name'] ) );
		$this->assertArrayNotHasKey( 'twitter', $renderer->options['icons'] );
		$this->assertSame( '1', $renderer->options['icons']['x'] );
	}

	public function testLegacySettingsSanitizeWithoutChangingTheSavedContract(): void {
		$input = array(
			'title' => 'Legacy title',
			'iconset' => 'flat',
			'excludes' => '42,legacy-slug',
			'show_in' => array(
				'show_left' => '1',
				'show_before_post' => '1',
			),
			'show_left' => 'circle',
			'show_right' => 'square',
			'show_before_post' => 'circle',
			'show_after_post' => 'square',
			'icons' => array(
				'facebook' => '1',
				'x' => '1',
			),
			'use_port' => true,
		);

		$this->assertSame( $input, $this->settings->sanitize( $input ) );
	}

	public function testCanonicalSanitizerBridgePreservesLegacyExtensionValues(): void {
		$input = array(
			'title' => 'Custom <script>bad</script> title',
			'iconset' => 'Community Pack',
			'excludes' => "42,\ncommunity-page",
			'show_in' => array(
				'show_left' => '1',
				'community_slot' => 'yes',
			),
			'show_left' => 'diamond',
			'icons' => array(
				'mastodon' => 'custom-enabled-value',
				'facebook' => '0',
			),
			'community_extension' => array( 'truthy' ),
		);

		$this->assertSame(
			array(
				'title' => 'Custom title',
				'iconset' => 'communitypack',
				'excludes' => "42,\ncommunity-page",
				'show_in' => array(
					'show_left' => '1',
					'community_slot' => '1',
				),
				'show_left' => 'diamond',
				'icons' => array(
					'mastodon' => 'custom-enabled-value',
					'facebook' => '0',
				),
				'community_extension' => true,
			),
			$this->settings->sanitize( $input )
		);
	}

	public function testLegacySanitizerUsesHistoricalPhpTruthinessForMixedTypes(): void {
		$case = $this->schema['mixed_type_truthiness_case'];

		$this->assertSame( $case['expected'], $this->settings->sanitize( $case['input'] ) );
	}

	public function testShareTemplateDefaultsRemainAvailableForLegacyOptions(): void {
		$defaults = zm_sh_get_default_share_templates();

		$this->assertSame(
			'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
			$defaults['facebook']
		);
		$this->assertArrayHasKey( 'x', $defaults );
		$this->assertArrayHasKey( 'telegram', $defaults );
		$this->assertArrayHasKey( 'bluesky', $defaults );
	}

	public function testSerializedSettingsAreSanitizedBeforeParsing(): void {
		$serialized = 'zm_shbt_fld%5Btitle%5D=Share+%3Cscript%3Ebad%3C%2Fscript%3E';
		parse_str( wp_kses_post( wp_unslash( $serialized ) ), $form_data );

		$sanitized = $this->settings->sanitize( $form_data['zm_shbt_fld'] );

		$this->assertSame( 'Share', $sanitized['title'] );
	}

	public function testPluginUsesCanonicalTextDomainAndShipsMigratedCatalogs(): void {
		$plugin_data = get_plugin_data( dirname( __DIR__, 2 ) . '/html-social-share.php', false, false );

		$this->assertSame( 'html-social-share-buttons', $plugin_data['TextDomain'] );
		$this->assertFileExists( dirname( __DIR__, 2 ) . '/languages/html-social-share-buttons.pot' );
		$this->assertFileExists( dirname( __DIR__, 2 ) . '/languages/html-social-share-buttons-fr_FR.mo' );
	}

	public function testCurrentPageUrlKeepsTheExistingShareUrlShape(): void {
		$original_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/';
		$_SERVER['REQUEST_URI'] = '/privacy-policy/?preview=true';

		$this->assertSame(
			esc_url_raw( home_url( '/privacy-policy/?preview=true' ) ),
			zm_sh_curentPageURL()
		);

		$_SERVER['REQUEST_URI'] = $original_request_uri;
	}

	public function testShortcodeWithoutAttributesUsesTheDefaultNetworks(): void {
		$output = do_shortcode( '[zm_sh_btn]' );

		$this->assertStringContainsString( 'zmshbt', $output );
		$this->assertStringContainsString( 'facebook', $output );
	}
}
