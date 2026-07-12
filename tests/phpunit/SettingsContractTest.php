<?php

use PHPUnit\Framework\TestCase;

final class SettingsContractTest extends TestCase {
	private $settings;

	protected function setUp(): void {
		$reflection = new ReflectionClass( 'zm_sh_settings' );
		$this->settings = $reflection->newInstanceWithoutConstructor();
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
}
