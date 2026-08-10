<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering;

final class CurrentPostPermalink {
	public function resolve( $contextPostId = 0 ) {
		$postId = absint( $contextPostId );

		if ( ! $postId ) {
			global $post;

			if ( is_object( $post ) && ! empty( $post->ID ) ) {
				$postId = absint( $post->ID );
			}
		}

		if ( ! $postId && function_exists( 'get_queried_object' ) ) {
			$queriedObject = get_queried_object();
			if ( $queriedObject instanceof \WP_Post ) {
				$postId = absint( $queriedObject->ID );
			}
		}

		if ( ! $postId && $this->isAjaxRequest() ) {
			foreach ( array( 'post_id', 'editor_post_id', 'post' ) as $requestKey ) {
				if ( ! isset( $_REQUEST[ $requestKey ] ) || ! is_scalar( $_REQUEST[ $requestKey ] ) ) {
					continue;
				}

				$postId = absint( wp_unslash( $_REQUEST[ $requestKey ] ) );
				if ( $postId ) {
					break;
				}
			}
		}

		if ( $postId ) {
			$permalink = get_permalink( $postId );
			if ( is_string( $permalink ) && '' !== $permalink ) {
				return esc_url_raw( $permalink );
			}
		}

		$requestUri = isset( $_SERVER['REQUEST_URI'] )
			? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )
			: '/';

		return esc_url_raw( home_url( $requestUri ) );
	}

	private function isAjaxRequest() {
		return wp_doing_ajax();
	}
}
