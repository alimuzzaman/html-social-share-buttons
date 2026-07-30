<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;

final class SettingsSchemaTest extends WP_UnitTestCase {
	private function schema() {
		return new SettingsSchema(
			( new BuiltInNetworkProvider() )->createRegistry()->ids(),
			array( 'default', 'flat', 'long-shadows', 'prajin' ),
			array( 'square', 'circle' )
		);
	}

	public function testCanonicalSchemaUsesOnlyNewStableIdentifiers(): void {
		$schema = $this->schema();

		$this->assertSame(
			array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			$schema->networkIds()
		);
		$this->assertSame(
			array( Placement::LEFT, Placement::RIGHT, Placement::BEFORE_CONTENT, Placement::AFTER_CONTENT ),
			$schema->placementIds()
		);
		$this->assertTrue( $schema->supportsIconSet( 'long-shadows' ) );
		$this->assertFalse( $schema->supportsIconSet( 'long_shadow' ) );
	}

	public function testWordPressRequestSanitizerProducesValidatedSettings(): void {
		$settings = ( new SettingsRequestSanitizer( $this->schema() ) )->sanitize(
			array(
				'title' => '<b>Canonical title</b>',
				'icon_set' => 'flat',
				'icon_shape' => 'circle',
				'placements' => array( Placement::LEFT => '1' ),
				'placement_shapes' => array( Placement::LEFT => 'invalid' ),
				'networks' => array( 'facebook' => '1', 'unknown' => '1' ),
				'share_templates' => array(
					'facebook' => "https://example.test/\n%%permalink%%",
					'unknown' => 'ignored',
				),
				'excluded_content' => "42\nexample",
				'no_follow' => '1',
			)
		);

		$this->assertSame( 'Canonical title', $settings->title() );
		$this->assertSame( 'flat', $settings->iconSetId() );
		$this->assertSame( 'circle', $settings->placementShapes()[ Placement::LEFT ] );
		$this->assertTrue( $settings->placements()[ Placement::LEFT ] );
		$this->assertFalse( $settings->placements()[ Placement::RIGHT ] );
		$this->assertTrue( $settings->networkStates()['facebook'] );
		$this->assertArrayNotHasKey( 'unknown', $settings->networkStates() );
		$this->assertTrue( $settings->noFollow() );
	}

	public function testCanonicalBooleanInputsUseExplicitSemanticValues(): void {
		$settings = ( new SettingsRequestSanitizer( $this->schema() ) )->sanitize(
			array(
				'placements' => array(
					Placement::LEFT => 'false',
					Placement::RIGHT => 'off',
					Placement::BEFORE_CONTENT => array(),
					Placement::AFTER_CONTENT => array( 'enabled' ),
				),
				'analytics_enabled' => 0.0,
				'auto_hide_enabled' => 'no',
				'preserve_url_port' => 'yes',
				'no_follow' => '1',
			)
		);

		$this->assertFalse( $settings->placements()[ Placement::LEFT ] );
		$this->assertFalse( $settings->placements()[ Placement::RIGHT ] );
		$this->assertFalse( $settings->placements()[ Placement::BEFORE_CONTENT ] );
		$this->assertTrue( $settings->placements()[ Placement::AFTER_CONTENT ] );
		$this->assertFalse( $settings->analyticsEnabled() );
		$this->assertFalse( $settings->autoHideEnabled() );
		$this->assertTrue( $settings->preserveUrlPort() );
		$this->assertTrue( $settings->noFollow() );
	}
}
