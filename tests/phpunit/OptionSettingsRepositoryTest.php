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
		$this->assertSame( 'bootstrap-solid', $settings->iconSetId() );
		$this->assertTrue( $settings->showForCurrentUser() );
		$this->assertTrue( $settings->showForLoggedInUser() );
		$this->assertTrue( $settings->showForLoggedOutUser() );
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
		$this->assertSame( 'default', $settings->iconSetId() );
		$this->assertTrue( $settings->networkStates()['facebook'] );
		$this->assertFalse( $settings->placements()[ Placement::LEFT ] );
		$this->assertTrue( $settings->showForCurrentUser() );
		$this->assertTrue( $settings->showForLoggedInUser() );
		$this->assertTrue( $settings->showForLoggedOutUser() );
	}

	public function testExplicitHistoricalDefaultRoundTripsWithoutMigration(): void {
		$stored = array(
			'iconset' => 'default',
			'iconset_type' => 'square',
			'extension_owned_state' => 'keep',
		);
		update_option( $this->optionName, $stored );
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$settings = $repository->load();
		$repository->save( $settings );

		$this->assertSame( 'default', $settings->iconSetId() );
		$saved = get_option( $this->optionName );
		$this->assertSame( 'default', $saved['iconset'] );
		$this->assertSame( 'square', $saved['iconset_type'] );
		$this->assertSame( 'keep', $saved['extension_owned_state'] );
	}

	public function testExplicitAudienceBooleansRoundTripWithoutLosingExtensionData(): void {
		$stored = array(
			'show_for_current_user' => false,
			'show_for_logged_in_user' => true,
			'show_for_logged_out_user' => false,
			'extension_owned_state' => array( 'keep' => true ),
		);
		update_option( $this->optionName, $stored );
		$repository = new OptionSettingsRepository( $this->optionName, new OptionSettingsCodec() );

		$settings = $repository->load();
		$repository->save( $settings );

		$this->assertFalse( $settings->showForCurrentUser() );
		$this->assertTrue( $settings->showForLoggedInUser() );
		$this->assertFalse( $settings->showForLoggedOutUser() );
		$saved = get_option( $this->optionName );
		$this->assertFalse( $saved['show_for_current_user'] );
		$this->assertTrue( $saved['show_for_logged_in_user'] );
		$this->assertFalse( $saved['show_for_logged_out_user'] );
		$this->assertSame( array( 'keep' => true ), $saved['extension_owned_state'] );
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
