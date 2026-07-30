<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Content;

final class ExcludedContentPolicy {
	public function identifiers( $excludedContent ) {
		$identifiers = array_map( 'trim', explode( ',', (string) $excludedContent ) );

		return array_values( array_filter( $identifiers, 'strlen' ) );
	}

	public function matches( $contentId, $slug, $title, $excludedContent ) {
		$candidates = array_filter(
			array(
				(string) $contentId,
				trim( (string) $slug ),
				trim( (string) $title ),
			),
			'strlen'
		);

		foreach ( $this->identifiers( $excludedContent ) as $identifier ) {
			foreach ( $candidates as $candidate ) {
				if ( 0 === strcasecmp( $identifier, $candidate ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
