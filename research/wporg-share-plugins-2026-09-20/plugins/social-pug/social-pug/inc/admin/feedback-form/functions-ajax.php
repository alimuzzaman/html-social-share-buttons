<?php

/**
 * AJAX callback to send the feedback
 *
 */
function dpsp_ajax_send_feedback() {
	$dpsp_token = filter_input( INPUT_POST, 'dpsp_token' );
	if ( empty( $dpsp_token ) || ! wp_verify_nonce( $dpsp_token, 'dpsp_feedback_form' ) ) {
		echo 0;
		wp_die();
	}

	$post = stripslashes_deep( $_POST );

	if ( empty( $post['user_email'] ) ) {
		echo 0;
		wp_die();
	}

	$email = $post['user_email']; // Input var okay; sanitization okay.

	// Set headers
	$headers = [
		'From: ' . sanitize_email( $email ),
		'Reply-To: ' . sanitize_email( $email ),
	];

	$type = $post['type']; // Input var okay; sanitization okay.

	// Message type
	$message  = 'Type:';
	$message .= "\n";
	$message .= '---------------------------------------------------------';
	$message .= "\n";
	$message .= sanitize_text_field( $type );

	$msg = $post['message'];  // Input var okay; sanitization okay.
	// Message content
	$message .= "\n\r";
	$message .= 'Message:';
	$message .= "\n";
	$message .= '---------------------------------------------------------';
	$message .= "\n";
	$message .= sanitize_text_field( $msg );

	// Message user email
	$message .= "\n\r";
	$message .= 'User email:';
	$message .= "\n";
	$message .= '---------------------------------------------------------';
	$message .= "\n";
	$message .= sanitize_text_field( $email );

	// Send the email
	$sent = wp_mail( 'support@morehubbub.com', 'Hubbub User Feedback', $message, $headers );

	// Return
	echo ( $sent ? 1 : 0 );
	wp_die();

}
