<?php
// Register the Elementor widget for HTML Social Share Buttons
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    if ( class_exists( '\HtmlSocialShare\Elementor\ShareButtonsWidget' ) ) {
        require_once __DIR__ . '/ShareButtonsWidget.php';
        $widgets_manager->register( new \HtmlSocialShare\Elementor\ShareButtonsWidget() );
    }
} );
