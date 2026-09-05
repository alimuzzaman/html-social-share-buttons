<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\ShareTemplateSanitizer;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ShareTemplatePreview;

final class ShareTemplateSanitizerTest extends WP_UnitTestCase {
	public function testSupportedTokensSurviveAlongsideUnicodeAndWhitespace(): void {
		$template = "https://example.com/?d=%%description%%&t=%%title%%&u=%%permalink%%&i=%%imageurl%%\n café  text";
		$this->assertSame( $template, ShareTemplateSanitizer::sanitize( $template ) );
		$this->assertSame( $template, ShareTemplateSanitizer::sanitize( ShareTemplateSanitizer::sanitize( $template ) ) );
	}

	public function testMarkupIsRemovedWithoutChangingSentinelLikeUserText(): void {
		$template = '<b>%%description%%</b> HSSBTOKEN000000000000000000000000000000000END HSSBTOKEN0END café';
		$this->assertSame( '%%description%% HSSBTOKEN000000000000000000000000000000000END HSSBTOKEN0END café', ShareTemplateSanitizer::sanitize( $template ) );
		$this->assertSame( sanitize_textarea_field( 'https://example.com/?x=%20%25%25title%25%25' ), ShareTemplateSanitizer::sanitize( 'https://example.com/?x=%20%25%25title%25%25' ) );
	}

	public function testSaveAndPreviewKeepDescriptionTokenWithTheSameCleanup(): void {
		$template = '<b>https://example.com/?d=%%description%%</b>';
		$sanitizer = new SettingsRequestSanitizer( new SettingsSchema( array( 'facebook' ), array( 'default' ), array( 'square' ) ) );
		$settings = $sanitizer->sanitize( array( 'share_templates' => array( 'facebook' => $template ) ) );
		$this->assertSame( 'https://example.com/?d=%%description%%', $settings->shareTemplates()['facebook'] );
		$preview = new ShareTemplatePreview( ( new BuiltInNetworkProvider() )->createRegistry() );
		$result = $preview->preview( 'facebook', $template );
		$this->assertSame( 'https://example.com/?d=Example%20site%20description', $result['resolved_url'] );
	}
}
