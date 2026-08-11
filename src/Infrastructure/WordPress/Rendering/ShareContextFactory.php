<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

/**
 * WordPress request-state adapter. It is intentionally outside Application so
 * rendering remains independent of globals and WordPress query functions.
 */
final class ShareContextFactory {
	private $permalinks;
	private $extensions;

	public function __construct(
		CurrentPostPermalink $permalinks = null,
		ExtensionHooks $extensions = null
	) {
		$this->permalinks = $permalinks ? $permalinks : new CurrentPostPermalink();
		$this->extensions = $extensions ? $extensions : new ExtensionHooks();
	}

	public function create( $contextPostId = 0 ) {
		$postId = $this->postId( $contextPostId );
		$title = $postId ? get_the_title( $postId ) : get_the_title();

		return new ShareContext(
			$this->permalink( $postId ),
			$this->extensions->shareTitle( $title ),
			get_bloginfo( 'description' ),
			$this->imageUrl( $postId )
		);
	}

	public function permalink( $contextPostId = 0 ) {
		return $this->permalinks->resolve( $contextPostId );
	}

	private function postId( $contextPostId ) {
		$postId = absint( $contextPostId );
		if ( ! $postId ) {
			global $post;
			if ( is_object( $post ) && ! empty( $post->ID ) ) {
				$postId = absint( $post->ID );
			}
		}

		return $postId;
	}

	private function imageUrl( $postId ) {
		if ( ! $postId ) {
			return '';
		}
		$thumbnailId = get_post_thumbnail_id( $postId );
		$imageUrl = $thumbnailId ? wp_get_attachment_url( $thumbnailId ) : '';

		return is_string( $imageUrl ) ? $imageUrl : '';
	}
}
