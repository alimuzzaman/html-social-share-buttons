<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\ShareTemplatePreview;

final class ShareTemplatePreviewTest extends WP_UnitTestCase {
	public function testEveryDefaultMatchesTheExistingFinalDestinationTreatment(): void {
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$preview = new ShareTemplatePreview( $networks );
		$sample = ShareTemplatePreview::samples();
		$context = new ShareContext( $sample['permalink'], $sample['title'], $sample['description'], $sample['imageurl'] );
		foreach ( $networks->all() as $network ) {
			$url = ( new ResolveShareUrl() )->resolve( $network, $context );
			if ( 'bluesky' === $network->id() ) {
				$url = str_ireplace( '%0A', '%20', $url );
			}
			$result = $preview->preview( $network->id(), '' );
			$this->assertTrue( $result['uses_default'] );
			$this->assertSame( html_entity_decode( esc_url( $url ), ENT_QUOTES, 'UTF-8' ), $result['resolved_url'], $network->id() );
		}
	}

	public function testAdvisoryDiagnosticsDoNotRejectTemplates(): void {
		$preview = new ShareTemplatePreview( ( new BuiltInNetworkProvider() )->createRegistry() );
		$cases = array(
			'https://example.com/?q=%%typo%%' => 'unknown_token',
			'https://example.com/?q=%25%25title%25%25' => 'encoded_token',
			'https://user:pass@example.com/' => 'credentials',
			'//example.com/' => 'relative_url',
			'https:///path' => 'missing_host',
			'javascript:alert(1)' => 'empty_destination',
			'<b>https://example.com/</b>' => 'sanitized',
			'https://example.com/' => 'no_content_tokens',
		);
		foreach ( $cases as $template => $code ) {
			$result = $preview->preview( 'facebook', $template );
			$this->assertContains( $code, array_column( $result['diagnostics'], 'code' ), $template );
		}
	}

	public function testAllSupportedTokensUseFixedSamples(): void {
		$preview = new ShareTemplatePreview( ( new BuiltInNetworkProvider() )->createRegistry() );
		$result = $preview->preview( 'facebook', 'https://example.com/?u=%%permalink%%&t=%%title%%&d=%%description%%&i=%%imageurl%%' );
		foreach ( ShareTemplatePreview::samples() as $sample ) {
			$this->assertStringContainsString( rawurlencode( $sample ), $result['resolved_url'] );
		}
		$this->assertFalse( $result['uses_default'] );
		$this->assertNotContains( 'unresolved_token', array_column( $result['diagnostics'], 'code' ) );
	}

	public function testMalformedAndOversizedRequestsAreRejected(): void {
		$preview = new ShareTemplatePreview( ( new BuiltInNetworkProvider() )->createRegistry() );
		foreach ( array( array( 'missing', '' ), array( array(), '' ), array( 'facebook', array() ), array( 'facebook', str_repeat( 'a', 8193 ) ) ) as $input ) {
			try {
				$preview->preview( $input[0], $input[1] );
				$this->fail( 'Malformed request accepted.' );
			} catch ( InvalidArgumentException $exception ) {
				$this->assertSame( 'Invalid preview request.', $exception->getMessage() );
			}
		}
	}
}
