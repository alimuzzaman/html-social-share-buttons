<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\SettingsRequestSanitizer;
use InvalidArgumentException;

final class SettingsAjaxController {
	private $settings;
	private $sanitizer;
	private $mapper;
	private $content;
	private $iconSets;
	private $config;

	public function __construct(
		SettingsStateStore $settings,
		SettingsRequestSanitizer $sanitizer,
		OptionSettingsRequestMapper $mapper,
		ExcludedContentLookup $content,
		IconSetPayloadBuilder $iconSets,
		PluginConfig $config
	) {
		$this->settings = $settings;
		$this->sanitizer = $sanitizer;
		$this->mapper = $mapper;
		$this->content = $content;
		$this->iconSets = $iconSets;
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( 'wp_ajax_' . $this->config->settingsAjaxSaveAction(), array( $this, 'save' ) );
		add_action( 'wp_ajax_' . $this->config->settingsAjaxSearchAction(), array( $this, 'search' ) );
		add_action( 'wp_ajax_' . $this->config->iconSetAjaxGetAction(), array( $this, 'iconSet' ) );
		add_action( 'wp_ajax_' . $this->config->iconSetAjaxPreviewAction(), array( $this, 'iconSetPreview' ) );
		add_action( 'wp_ajax_' . $this->config->iconSetAjaxDetailsAction(), array( $this, 'iconSetDetails' ) );
	}

	public function search() {
		$this->verifySettingsRequest(
			__( 'You are not allowed to search content.', 'html-social-share-buttons' )
		);
		$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
		$query = trim( $query );
		if ( strlen( $query ) < 2 ) {
			wp_send_json_success( array() );
		}
		$query = function_exists( 'mb_substr' ) ? mb_substr( $query, 0, 100 ) : substr( $query, 0, 100 );
		wp_send_json_success( $this->content->search( $query ) );
	}

	public function save() {
		$this->verifySettingsRequest(
			__( 'You are not allowed to change these settings.', 'html-social-share-buttons' )
		);
		$serialized = isset( $_POST['settings'] ) && is_string( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '';
		$formData = array();
		parse_str( $serialized, $formData );
		if ( empty( $formData[ $this->config->optionName() ] ) || ! is_array( $formData[ $this->config->optionName() ] ) ) {
			wp_send_json_error( array( 'message' => __( 'No settings were received.', 'html-social-share-buttons' ) ), 400 );
		}

		$stored = $this->persist( $formData[ $this->config->optionName() ] );
		wp_send_json_success(
			array(
				'message' => __( 'Settings saved.', 'html-social-share-buttons' ),
				'options' => $stored,
			)
		);
	}

	public function iconSet() {
		$this->verifyIconSetRequest( 'iconsetId' );
		$id = sanitize_key( wp_unslash( $_POST['iconsetId'] ) );
		try {
			wp_send_json( $this->iconSets->legacyObjectPayload( $id ) );
		} catch ( InvalidArgumentException $exception ) {
			wp_die( 'Invalid iconset' );
		}
	}

	public function iconSetPreview() {
		$this->verifyIconSetRequest( 'iconsetId' );
		$id = sanitize_key( wp_unslash( $_POST['iconsetId'] ) );
		try {
			wp_die( esc_url( $this->iconSets->previewUrl( $id ) ) );
		} catch ( InvalidArgumentException $exception ) {
			wp_die( 'Invalid iconset' );
		}
	}

	public function iconSetDetails() {
		$this->verifyIconSetRequest( 'iconset' );
		$id = sanitize_key( wp_unslash( $_POST['iconset'] ) );
		try {
			wp_send_json( $this->iconSets->iconNames( $id ) );
		} catch ( InvalidArgumentException $exception ) {
			wp_die( 'Invalid iconset' );
		}
	}

	/**
	 * Public so the Settings API callback and thin legacy wrapper can delegate
	 * without duplicating validation or persistence rules.
	 */
	public function sanitize( array $input ) {
		$settings = $this->sanitizer->sanitize( $this->mapper->toCanonical( $input ) );

		return $this->mapper->toStoredSubmission( $settings, $input );
	}

	public function persist( array $input ) {
		$settings = $this->sanitizer->sanitize( $this->mapper->toCanonical( $input ) );
		$submitted = $this->mapper->toStoredSubmission( $settings, $input );

		return $this->settings->replaceStored( $submitted );
	}

	private function verifySettingsRequest( $message ) {
		check_ajax_referer( $this->config->adminNonceAction(), 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => $message ), 403 );
		}
	}

	private function verifyIconSetRequest( $field ) {
		check_ajax_referer( $this->config->adminNonceAction(), 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		if ( ! isset( $_POST[ $field ] ) ) {
			wp_die( 'Missing iconset' . ( 'iconsetId' === $field ? ' ID' : '' ) );
		}
	}
}
