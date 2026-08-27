<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;

final class LegacySettingsMapperTest extends WP_UnitTestCase {
	public function testLegacySettingsMapToTheNewDomainWithoutDatabaseChanges(): void {
		$fixture = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/settings-schema-baseline.json' ),
			true
		);
		$stored  = $fixture['sanitization_case']['expected'];
		$mapper  = new OptionSettingsCodec();
		$before  = get_option( $fixture['option_name'], null );

		$settings = $mapper->fromArray( $stored );

		$this->assertSame( 'Compatibility title', $settings->title() );
		$this->assertSame( 'flat', $settings->iconSetId() );
		$this->assertTrue( $settings->placements()[ Placement::LEFT ] );
		$this->assertTrue( $settings->placements()[ Placement::BEFORE_CONTENT ] );
		$this->assertFalse( $settings->placements()[ Placement::RIGHT ] );
		$this->assertTrue( $settings->networkStates()['x'] );
		$this->assertSame( $before, get_option( $fixture['option_name'], null ) );
		$expected = $stored;
		$expected['button_appearance'] = 'legacy';
		$this->assertSame( $expected, $mapper->toArray( $settings, $stored ) );
	}

	public function testLegacyTwitterAliasIsRuntimeOnly(): void {
		$mapper = new OptionSettingsCodec();
		$stored = array(
			'icons' => array(
				'twitter' => '1',
			),
		);

		$settings = $mapper->fromArray( $stored );

		$this->assertTrue( $settings->networkStates()['x'] );
		$this->assertArrayHasKey( 'twitter', $settings->networkStates() );
		$this->assertSame( $stored['icons'], $mapper->toArray( $settings, $stored )['icons'] );
	}

	public function testEncodingCanDisableExistingEnabledNetworksWithoutRewritingFalseValues(): void {
		$mapper = new OptionSettingsCodec();
		$stored = array(
			'icons' => array(
				'facebook' => '1',
				'telegram' => '0',
				'twitter' => '1',
			),
		);
		$current = $mapper->fromArray( $stored );
		$states = $current->networkStates();
		$states['facebook'] = false;
		$states['x'] = false;
		$settings = new Settings(
			$current->title(),
			$current->iconSetId(),
			$current->defaultIconShape(),
			$current->placements(),
			$current->placementShapes(),
			$states,
			$current->shareTemplates(),
			$current->excludedContent(),
			$current->analyticsEnabled(),
			$current->autoHideEnabled(),
			$current->preserveUrlPort(),
			$current->noFollow()
		);

		$encoded = $mapper->toArray( $settings, $stored );

		$this->assertArrayNotHasKey( 'facebook', $encoded['icons'] );
		$this->assertArrayNotHasKey( 'twitter', $encoded['icons'] );
		$this->assertSame( '0', $encoded['icons']['telegram'] );
	}

	public function testEncodingCanEnableExistingFalseNetworkAndTwitterAliasValues(): void {
		$mapper = new OptionSettingsCodec();
		$stored = array(
			'icons' => array(
				'telegram' => '0',
				'twitter' => '0',
			),
		);
		$current = $mapper->fromArray( $stored );
		$states = $current->networkStates();
		$states['telegram'] = true;
		$states['x'] = true;
		$settings = new Settings(
			$current->title(),
			$current->iconSetId(),
			$current->defaultIconShape(),
			$current->placements(),
			$current->placementShapes(),
			$states,
			$current->shareTemplates(),
			$current->excludedContent(),
			$current->analyticsEnabled(),
			$current->autoHideEnabled(),
			$current->preserveUrlPort(),
			$current->noFollow()
		);

		$encoded = $mapper->toArray( $settings, $stored );

		$this->assertSame( '1', $encoded['icons']['telegram'] );
		$this->assertSame( '1', $encoded['icons']['twitter'] );
		$this->assertArrayNotHasKey( 'x', $encoded['icons'] );
	}

	public function testStoredLegacyValuesUseHistoricalPhpTruthiness(): void {
		$mapper = new OptionSettingsCodec();
		$settings = $mapper->fromArray(
			array(
				'show_in' => array(
					'show_left' => 'false',
					'show_right' => 0.0,
					'show_before_post' => array(),
					'show_after_post' => array( 'enabled' ),
				),
				'icons' => array(
					'facebook' => 'false',
					'linkedin' => 0.0,
					'mail' => array(),
					'telegram' => array( 'enabled' ),
				),
				'g_analytics' => 'false',
				'auto_hide_btn' => 0.0,
				'use_port' => array(),
				'nofollow' => array( 'enabled' ),
			)
		);

		$this->assertTrue( $settings->placements()[ Placement::LEFT ] );
		$this->assertFalse( $settings->placements()[ Placement::RIGHT ] );
		$this->assertFalse( $settings->placements()[ Placement::BEFORE_CONTENT ] );
		$this->assertTrue( $settings->placements()[ Placement::AFTER_CONTENT ] );
		$this->assertTrue( $settings->networkStates()['facebook'] );
		$this->assertFalse( $settings->networkStates()['linkedin'] );
		$this->assertFalse( $settings->networkStates()['mail'] );
		$this->assertTrue( $settings->networkStates()['telegram'] );
		$this->assertTrue( $settings->analyticsEnabled() );
		$this->assertFalse( $settings->autoHideEnabled() );
		$this->assertFalse( $settings->preserveUrlPort() );
		$this->assertTrue( $settings->noFollow() );
	}
}
