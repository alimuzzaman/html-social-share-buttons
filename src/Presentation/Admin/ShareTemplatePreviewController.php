<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use InvalidArgumentException;

final class ShareTemplatePreviewController {
	private $preview;
	private $config;

	public function __construct( ShareTemplatePreview $preview, PluginConfig $config ) {
		$this->preview = $preview;
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( 'wp_ajax_' . $this->config->shareTemplatePreviewAjaxAction(), array( $this, 'preview' ) );
	}

	public function preview() {
		check_ajax_referer( $this->config->adminNonceAction(), 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to preview share templates.', 'html-social-share-buttons' ) ), 403 );
		}
		if ( ! isset( $_POST['network'], $_POST['template'] ) || ! is_string( $_POST['network'] ) || ! is_string( $_POST['template'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Provide a registered network and a template string of at most 8192 bytes.', 'html-social-share-buttons' ) ), 400 );
		}
		try {
			$result = $this->preview->preview( wp_unslash( $_POST['network'] ), wp_unslash( $_POST['template'] ) );
		} catch ( InvalidArgumentException $exception ) {
			wp_send_json_error( array( 'message' => __( 'Provide a registered network and a template string of at most 8192 bytes.', 'html-social-share-buttons' ) ), 400 );
		}
		wp_send_json_success( $result );
	}
}
