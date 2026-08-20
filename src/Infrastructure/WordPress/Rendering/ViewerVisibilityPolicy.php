<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

/**
 * Resolves the three mutually exclusive frontend viewer audiences.
 */
final class ViewerVisibilityPolicy {
	public function allows( Settings $settings, $contextPostId = 0 ) {
		if ( ! function_exists( 'is_user_logged_in' ) || ! is_user_logged_in() ) {
			return $settings->showForLoggedOutUser();
		}

		$currentUserId = function_exists( 'get_current_user_id' )
			? (int) get_current_user_id()
			: 0;
		$postId = $this->postId( $contextPostId );
		if (
			$currentUserId > 0 &&
			$postId > 0 &&
			function_exists( 'get_post_field' ) &&
			(int) get_post_field( 'post_author', $postId ) === $currentUserId
		) {
			return $settings->showForCurrentUser();
		}

		return $settings->showForLoggedInUser();
	}

	private function postId( $contextPostId ) {
		$postId = (int) $contextPostId;
		if ( $postId > 0 ) {
			return $postId;
		}
		if ( function_exists( 'get_queried_object_id' ) ) {
			$postId = (int) get_queried_object_id();
			if ( $postId > 0 ) {
				return $postId;
			}
		}

		return isset( $GLOBALS['post']->ID ) ? (int) $GLOBALS['post']->ID : 0;
	}
}
