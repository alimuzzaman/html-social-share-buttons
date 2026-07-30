<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings\LegacySettingsService;

final class LegacySettingsAjaxController {
	private $settings;
	private $content;

	public function __construct(
		LegacySettingsService $settings,
		LegacyExcludedContentLookup $content
	) {
		$this->settings = $settings;
		$this->content = $content;
	}

	public function search() {
		check_ajax_referer( 'zm_sh_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to search content.', 'html-social-share-buttons' ) ),
				403
			);
		}

		$query = isset( $_POST['query'] )
			? sanitize_text_field( wp_unslash( $_POST['query'] ) )
			: '';
		$query = trim( $query );
		if ( strlen( $query ) < 2 ) {
			wp_send_json_success( array() );
		}

		$query = function_exists( 'mb_substr' )
			? mb_substr( $query, 0, 100 )
			: substr( $query, 0, 100 );
		wp_send_json_success( $this->content->search( $query ) );
	}

	public function save() {
		check_ajax_referer( 'zm_sh_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to change these settings.', 'html-social-share-buttons' ) ),
				403
			);
		}

		$serializedSettings = isset( $_POST['settings'] ) && is_string( $_POST['settings'] )
			? wp_unslash( $_POST['settings'] )
			: '';
		$formData = array();
		parse_str( $serializedSettings, $formData );

		if ( empty( $formData['zm_shbt_fld'] ) || ! is_array( $formData['zm_shbt_fld'] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'No settings were received.', 'html-social-share-buttons' ) ),
				400
			);
		}

		$sanitized = $this->settings->save( $formData['zm_shbt_fld'] );
		wp_send_json_success(
			array(
				'message' => __( 'Settings saved.', 'html-social-share-buttons' ),
				'options' => $sanitized,
			)
		);
	}
}
