<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;

final class ExcludedContentLookup {
	private $policy;

	public function __construct( ExcludedContentPolicy $policy ) {
		$this->policy = $policy;
	}

	public function resolve( $excludedContent ) {
		$items = array();
		$custom = array();

		foreach ( $this->policy->identifiers( $excludedContent ) as $identifier ) {
			$post = $this->findPublishedPost( $identifier );
			if ( $post ) {
				$items[] = $this->toItem( $post );
			} else {
				$custom[] = $identifier;
			}
		}

		return array(
			'items'      => $items,
			'has_custom' => ! empty( $custom ),
			'custom'     => $custom,
		);
	}

	public function search( $query ) {
		$posts = get_posts(
			array(
				'post_type'              => array( 'post', 'page' ),
				'post_status'            => 'publish',
				'posts_per_page'         => 20,
				's'                      => $query,
				'orderby'                => 'relevance',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		return array_map( array( $this, 'toItem' ), $posts );
	}

	public function toItem( $post ) {
		return array(
			'id'    => (string) $post->ID,
			'token' => sprintf( '#%d - %s (%s)', $post->ID, get_the_title( $post ), $post->post_type ),
		);
	}

	private function findPublishedPost( $identifier ) {
		if ( ctype_digit( (string) $identifier ) ) {
			$post = get_post( absint( $identifier ) );

			return $this->isSupportedPublishedPost( $post ) ? $post : null;
		}

		$matches = get_posts(
			array(
				'post_type'      => array( 'post', 'page' ),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				's'              => $identifier,
			)
		);
		foreach ( $matches as $match ) {
			if ( 0 === strcasecmp( (string) $identifier, $match->post_name ) || 0 === strcasecmp( (string) $identifier, $match->post_title ) ) {
				return $this->isSupportedPublishedPost( $match ) ? $match : null;
			}
		}

		return null;
	}

	private function isSupportedPublishedPost( $post ) {
		return $post && in_array( $post->post_type, array( 'post', 'page' ), true ) && 'publish' === $post->post_status;
	}
}
