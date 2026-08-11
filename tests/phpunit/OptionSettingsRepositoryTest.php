<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRepository;

final class OptionSettingsRepositoryTest extends WP_UnitTestCase {
	private $optionName = 'hssb_test_settings_repository';

	protected function tearDown(): void {
		delete_option( $this->optionName );
		parent::tearDown();
	}

	public function testMissingOptionLoadsDefaultsWithoutWriting(): void {
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$settings = $repository->load();

		$this->assertTrue( $settings->placements()[ Placement::LEFT ] );
		$this->assertTrue( $settings->placements()[ Placement::AFTER_CONTENT ] );
		$this->assertTrue( $settings->networkStates()['x'] );
		$this->assertFalse( get_option( $this->optionName, false ) );
	}

	public function testPartialStoredOptionRetainsItsLiteralShapeAtRuntime(): void {
		update_option(
			$this->optionName,
			array(
				'title' => 'Partial',
				'icons' => array( 'facebook' => '1' ),
			)
		);
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$settings = $repository->load();

		$this->assertSame( 'Partial', $settings->title() );
		$this->assertTrue( $settings->networkStates()['facebook'] );
		$this->assertFalse( $settings->placements()[ Placement::LEFT ] );
	}

	public function testSavePreservesUnknownAndLegacyAliasValues(): void {
		$stored = array(
			'title' => 'Before',
			'unknown_extension_key' => array( 'keep' => true ),
			'icons' => array( 'twitter' => 'custom-truthy-value' ),
		);
		update_option( $this->optionName, $stored );
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$repository->save( $repository->load() );
		$saved = get_option( $this->optionName );

		$this->assertSame( array( 'keep' => true ), $saved['unknown_extension_key'] );
		$this->assertSame( 'custom-truthy-value', $saved['icons']['twitter'] );
		$this->assertArrayNotHasKey( 'x', $saved['icons'] );
	}

	public function testStoredReplacementUsesTheSubmittedShapeWithoutMergingOldKeys(): void {
		update_option(
			$this->optionName,
			array(
				'title' => 'Before',
				'stale_extension_key' => true,
			)
		);
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );
		$replacement = array(
			'title' => 'After',
			'icons' => array( 'facebook' => '1' ),
		);

		$result = $repository->replaceStored( $replacement );

		$this->assertSame( $replacement, $result );
		$this->assertSame( $replacement, get_option( $this->optionName ) );
	}

	public function testFirstSaveKeepsTheHistoricalAutoloadedOptionBehavior(): void {
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$repository->replaceStored( array( 'title' => 'Autoload contract' ) );
		wp_cache_delete( 'alloptions', 'options' );

		$this->assertArrayHasKey( $this->optionName, wp_load_alloptions() );
	}

	public function testSavePreservesAnExistingNonAutoloadedDecision(): void {
		add_option( $this->optionName, array( 'title' => 'Before' ), '', 'no' );
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$repository->replaceStored( array( 'title' => 'After' ) );
		wp_cache_delete( 'alloptions', 'options' );

		$this->assertArrayNotHasKey( $this->optionName, wp_load_alloptions() );
	}
}
