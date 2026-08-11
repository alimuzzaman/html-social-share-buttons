<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;

final class MetaboxController {
	private $config;

	public function __construct( PluginConfig $config ) {
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( 'load-post.php', array( $this, 'registerPostHooks' ) );
		add_action( 'load-post-new.php', array( $this, 'registerPostHooks' ) );
	}

	public function registerPostHooks() {
		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	public function addMetaBox( $postType ) {
		if ( ! in_array( $postType, array( 'post', 'page' ), true ) ) {
			return;
		}
		add_meta_box(
			$this->config->metaboxId(),
			__( 'Html Social Share', 'html-social-share-buttons' ),
			array( $this, 'render' ),
			$postType,
			'side',
			'high'
		);
	}

	/**
	 * Historical controller entry point retained for integrations that obtained
	 * the service directly. The legacy global facade delegates here as well.
	 */
	public function add_meta_box( $postType ) {
		return $this->addMetaBox( $postType );
	}

	public function save( $postId ) {
		if ( ! isset( $_POST[ $this->config->metaboxNonceField() ] ) ) {
			return $postId;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST[ $this->config->metaboxNonceField() ] ) );
		if ( ! wp_verify_nonce( $nonce, $this->config->metaboxNonceAction() ) ) {
			return $postId;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $postId;
		}

		$postType = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : '';
		if ( 'page' === $postType ) {
			if ( ! current_user_can( 'edit_page', $postId ) ) {
				return $postId;
			}
		} elseif ( ! current_user_can( 'edit_post', $postId ) ) {
			return $postId;
		}

		$value = isset( $_POST[ $this->config->disabledMetaKey() ] )
			? sanitize_text_field( wp_unslash( $_POST[ $this->config->disabledMetaKey() ] ) )
			: false;
		update_post_meta( $postId, $this->config->disabledMetaKey(), $value );

		return $postId;
	}

	public function render( $post ) {
		wp_nonce_field( $this->config->metaboxNonceAction(), $this->config->metaboxNonceField() );
		$value = get_post_meta( $post->ID, $this->config->disabledMetaKey(), true );
		$checked = checked( $value, 'on', false );

		echo '<input type="checkbox" id="' . esc_attr( $this->config->disabledMetaKey() ) . '" name="' . esc_attr( $this->config->disabledMetaKey() ) . '" ' . wp_kses_post( $checked ) . ' />';
		echo '<label for="' . esc_attr( $this->config->disabledMetaKey() ) . '">';
		esc_html_e( 'Disable Social share for this page', 'html-social-share-buttons' );
		echo '</label> ';
	}

	/**
	 * Historical meta-box callback name.
	 */
	public function render_meta_box_content( $post ) {
		return $this->render( $post );
	}
}
