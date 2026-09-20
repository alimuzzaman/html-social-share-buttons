<?php
/**
 * Just for Lite when registered
 */

// DO NOT include in `composer.json` section for `"files"`.
$lite_plus_tools = [
    'tools/follow-widget/class-dpsp-social-media-follow-buttons.php',
    'tools/follow-widget/follow-widget.php',
    'tools/follow-widget/submenu-page-follow-widget.php',
    'class-shortcodes.php',
];

foreach ( $lite_plus_tools as $file ) {
    require_once( __DIR__ . '/' . $file );
}