<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\ShareUrlPresentation;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\ShareTemplateSanitizer;
use InvalidArgumentException;

/** Advisory analysis only: no persistence, content lookup or runtime share hooks. */
final class ShareTemplatePreview {
	private $networks;

	public function __construct( NetworkRegistry $networks ) {
		$this->networks = $networks;
	}

	public static function samples() {
		return array(
			'permalink'   => 'https://example.com/sample-post/?a=1&b=2',
			'title'       => 'Example title & café',
			'description' => 'Example site description',
			'imageurl'    => 'https://example.com/sample-image.jpg',
		);
	}

	public function preview( $networkId, $input ) {
		if ( ! is_string( $networkId ) || ! is_string( $input ) || strlen( $input ) > 8192 || ! $this->networks->has( $networkId ) ) {
			throw new InvalidArgumentException( 'Invalid preview request.' );
		}
		$network = $this->networks->get( $networkId );
		$template = ShareTemplateSanitizer::sanitize( $input );
		$usesDefault = '' === trim( $template );
		$diagnostics = array();
		if ( $template !== $input ) {
			$diagnostics[] = $this->diagnostic( 'sanitized', 'warning', __( 'Saving removes or changes some characters in this template.', 'html-social-share-buttons' ) );
		}
		$effective = $usesDefault ? $network->defaultShareTemplate() : $template;
		$samples = self::samples();
		$context = new ShareContext( $samples['permalink'], $samples['title'], $samples['description'], $samples['imageurl'] );
		$resolved = ( new ResolveShareUrl() )->resolve( $network, $context, $template );
		if ( preg_match( '/%%(?:permalink|title|description|imageurl)%%/', $effective ) ) {
			$diagnostics[] = $this->diagnostic( 'content_tokens', 'info', __( 'Supported content tokens were replaced with sample values.', 'html-social-share-buttons' ) );
		} else {
			$diagnostics[] = $this->diagnostic( 'no_content_tokens', 'info', __( 'This destination contains no supported content token.', 'html-social-share-buttons' ) );
		}
		if ( preg_match( '/%%(?!(?:permalink|title|description|imageurl)%%)[^%\s]+%%/', $effective ) ) {
			$diagnostics[] = $this->diagnostic( 'unknown_token', 'warning', __( 'The template contains an unknown token.', 'html-social-share-buttons' ) );
		}
		if ( preg_match( '/%25%25[^\s]*?%25%25/i', $input ) ) {
			$diagnostics[] = $this->diagnostic( 'encoded_token', 'warning', __( 'Percent-encoded tokens are not replaced. Use the literal %%token%% form.', 'html-social-share-buttons' ) );
		}
		if ( preg_match( '/%%[^%]+%%/', $resolved ) ) {
			$diagnostics[] = $this->diagnostic( 'unresolved_token', 'warning', __( 'The resolved destination still contains an unresolved token.', 'html-social-share-buttons' ) );
		}
		$parts = wp_parse_url( $resolved );
		$scheme = preg_match( '/^([a-z][a-z0-9+.-]*):/i', $resolved, $schemeMatch ) ? strtolower( $schemeMatch[1] ) : '';
		if ( '' === $scheme ) {
			$diagnostics[] = $this->diagnostic( 'relative_url', 'warning', __( 'Use an absolute URL with a scheme for a predictable share destination.', 'html-social-share-buttons' ) );
		} elseif ( in_array( $scheme, array( 'http', 'https' ), true ) ) {
			if ( empty( $parts['host'] ) ) {
				$diagnostics[] = $this->diagnostic( 'missing_host', 'error', __( 'The HTTP or HTTPS destination needs a host.', 'html-social-share-buttons' ) );
			}
		} elseif ( 'mailto' !== $scheme ) {
			$diagnostics[] = $this->diagnostic( 'unusual_scheme', 'warning', __( 'This destination uses an unusual URL scheme.', 'html-social-share-buttons' ) );
		}
		if ( is_array( $parts ) && ( isset( $parts['user'] ) || isset( $parts['pass'] ) ) ) {
			$diagnostics[] = $this->diagnostic( 'credentials', 'warning', __( 'This destination contains URL credentials.', 'html-social-share-buttons' ) );
		}
		$escaped = ShareUrlPresentation::escape( $networkId, $resolved );
		if ( '' === $escaped ) {
			$diagnostics[] = $this->diagnostic( 'empty_destination', 'error', __( 'WordPress URL escaping leaves an empty destination.', 'html-social-share-buttons' ) );
		}

		return array(
			'network'      => $networkId,
			'resolved_url' => html_entity_decode( $escaped, ENT_QUOTES, 'UTF-8' ),
			'diagnostics'  => $diagnostics,
			'uses_default' => $usesDefault,
		);
	}

	private function diagnostic( $code, $severity, $message ) {
		return array(
			'code'     => $code,
			'severity' => $severity,
			'message'  => $message,
		);
	}
}
