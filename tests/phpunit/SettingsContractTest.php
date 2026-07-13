<?php

final class SettingsContractTest extends WP_UnitTestCase {
	private $settings;

	protected function setUp(): void {
		parent::setUp();
		$this->settings = new zm_sh_settings();
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

	public function testCurrentPageUrlKeepsTheExistingShareUrlShape(): void {
		$_SERVER['REQUEST_URI'] = '/privacy-policy/?preview=true';

		$this->assertSame(
			esc_url_raw( home_url( '/privacy-policy/?preview=true' ) ),
			zm_sh_curentPageURL()
		);

		unset( $_SERVER['REQUEST_URI'] );
	}
}
