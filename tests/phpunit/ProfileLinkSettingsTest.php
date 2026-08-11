<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;

final class ProfileLinkSettingsTest extends WP_UnitTestCase {
	private function schema() {
		return new SettingsSchema(
			( new BuiltInNetworkProvider() )->createRegistry()->ids(),
			array( 'default', 'flat', 'long-shadows', 'prajin' ),
			array( 'square', 'circle' )
		);
	}

	public function testCanonicalSanitizerAcceptsOnlyHttpsProfilesAndHeaderFreeMailto(): void {
		$settings = ( new SettingsRequestSanitizer( $this->schema() ) )->sanitize(
			array(
				'profile_links' => array(
					'facebook' => ' https://www.facebook.com/example?ref=profile ',
					'x' => 'http://x.com/example',
					'linkedin' => 'javascript:alert(1)',
					'pinterest' => '//pinterest.com/example',
					'telegram' => array( 'https://t.me/example' ),
					'bluesky' => 'data:text/html,bad',
					'mail' => 'mailto:hello@example.com',
					'unknown' => 'https://example.com/profile',
				),
			)
		);

		$this->assertSame(
			array(
				'facebook' => 'https://www.facebook.com/example?ref=profile',
				'mail' => 'mailto:hello@example.com',
			),
			$settings->profileLinks()
		);
	}

	public function testCanonicalSanitizerRejectsMailHeadersAndInvalidAddresses(): void {
		$sanitizer = new SettingsRequestSanitizer( $this->schema() );
		$invalid = array(
			'mailto:hello@example.com?subject=Hello',
			'mailto:hello@example.com%0ABcc:bad@example.com',
			"mailto:hello@example.com\r\nBcc:bad@example.com",
			'mailto:not-an-email',
			'https://example.com/contact',
		);

		foreach ( $invalid as $value ) {
			$settings = $sanitizer->sanitize(
				array( 'profile_links' => array( 'mail' => $value ) )
			);
			$this->assertSame( array(), $settings->profileLinks(), $value );
		}
	}

	public function testStoredRequestMappingKeepsProfileMapStructuredAndSanitized(): void {
		$mapper = new OptionSettingsRequestMapper();
		$sanitizer = new SettingsRequestSanitizer( $this->schema() );
		$input = array(
			'profile_links' => array(
				'twitter' => 'https://x.com/example',
				'facebook' => 'http://facebook.com/example',
				'mail' => 'mailto:hello@example.com',
			),
		);

		$settings = $sanitizer->sanitize( $mapper->toCanonical( $input ) );
		$this->assertEquals(
			array(
				'profile_links' => array(
					'twitter' => 'https://x.com/example',
					'mail' => 'mailto:hello@example.com',
				),
			),
			$mapper->toStoredSubmission( $settings, $input )
		);
	}

	public function testLegacyCodecPreservesTwitterAliasAndOmitsAbsentProfiles(): void {
		$mapper = new OptionSettingsCodec();
		$stored = array(
			'profile_links' => array(
				'twitter' => 'https://x.com/example',
			),
		);
		$settings = $mapper->fromArray( $stored );

		$this->assertSame( array( 'x' => 'https://x.com/example' ), $settings->profileLinks() );
		$this->assertSame( $stored['profile_links'], $mapper->toArray( $settings, $stored )['profile_links'] );

		$defaults = SettingsDefaults::create();
		$this->assertSame( array(), $defaults->profileLinks() );
		$this->assertArrayNotHasKey( 'profile_links', $mapper->toArray( $defaults, array() ) );
	}

	public function testAdminPayloadMapsStoredTwitterProfileToCanonicalXField(): void {
		update_option(
			'zm_shbt_fld',
			array(
				'profile_links' => array( 'twitter' => 'https://x.com/example' ),
			)
		);
		LegacyApi::plugin()->admin()->enqueueAssets( 'settings_page_zm_shbt_opt' );
		$data = wp_scripts()->registered['zm_sh_admin_scripts']->extra['data'];
		$this->assertSame( 1, preg_match( '/^var zm_sh_react_settings = (.+);$/', $data, $matches ) );
		$payload = json_decode( $matches[1], true );

		$this->assertSame( 'https://x.com/example', $payload['options']['profile_links']['x'] );
		$this->assertArrayNotHasKey( 'twitter', $payload['options']['profile_links'] );
	}

	public function testClearingProfilesRemovesTheCompatibilityStorageKey(): void {
		$mapper = new OptionSettingsCodec();
		$current = $mapper->fromArray(
			array( 'profile_links' => array( 'facebook' => 'https://facebook.com/example' ) )
		);
		$cleared = new Settings(
			$current->title(),
			$current->iconSetId(),
			$current->defaultIconShape(),
			$current->placements(),
			$current->placementShapes(),
			$current->networkStates(),
			$current->shareTemplates(),
			$current->excludedContent(),
			$current->analyticsEnabled(),
			$current->autoHideEnabled(),
			$current->preserveUrlPort(),
			$current->noFollow(),
			array()
		);

		$this->assertArrayNotHasKey(
			'profile_links',
			$mapper->toArray(
				$cleared,
				array( 'profile_links' => array( 'facebook' => 'https://facebook.com/example' ) )
			)
		);
	}
}
