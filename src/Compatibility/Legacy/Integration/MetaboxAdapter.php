<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration;

class MetaboxAdapter {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	public function add_meta_box( $postType ) {
		if ( ! in_array( $postType, array( 'post', 'page' ) ) ) {
			return;
		}

		add_meta_box(
			'zm_sh_metabox',
			'Html Social Share',
			array( $this, 'render_meta_box_content' ),
			$postType,
			'side',
			'high'
		);
	}

	public function save( $postId ) {
		if ( ! isset( $_POST['zm_sh_mtbox'] ) ) {
			return $postId;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['zm_sh_mtbox'] ) );
		if ( ! wp_verify_nonce( $nonce, 'zm_sh_metabox' ) ) {
			return $postId;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $postId;
		}

		$postType = isset( $_POST['post_type'] )
			? sanitize_key( wp_unslash( $_POST['post_type'] ) )
			: '';
		if ( 'page' == $postType ) {
			if ( ! current_user_can( 'edit_page', $postId ) ) {
				return $postId;
			}
		} elseif ( ! current_user_can( 'edit_post', $postId ) ) {
			return $postId;
		}

		$value = isset( $_POST['_zm_sh_disable_share'] )
			? sanitize_text_field( wp_unslash( $_POST['_zm_sh_disable_share'] ) )
			: false;
		update_post_meta( $postId, '_zm_sh_disable_share', $value );
	}

	public function render_meta_box_content( $post ) {
		wp_nonce_field( 'zm_sh_metabox', 'zm_sh_mtbox' );
		$value = get_post_meta( $post->ID, '_zm_sh_disable_share', true );
		$checked = checked( $value, 'on', false );

		echo '<input type="checkbox" id="_zm_sh_disable_share" name="_zm_sh_disable_share" ' .
			wp_kses_post( $checked ) .
			' />';
		echo '<label for="_zm_sh_disable_share">';
		esc_html_e( 'Disable Social share for this page', 'html-social-share-buttons' );
		echo '</label> ';
	}
}
