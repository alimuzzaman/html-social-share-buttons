<?php
/*
 * Plugin Name: Kiwi Social Share - Social Media Share Buttons & Icons
 * Version: 2.1.9.1
 * Description: Really beautiful & simple social media & share buttons + icons. Simplicity & speed is key with this social media share plugin.
 * Author: WPKube
 * Author URI: https://www.wpkube.com/
 * Requires at least: 4.0
 * Tested up to: 7.0
 *
 * Text Domain: kiwi-social-share
 * Domain Path: /languages/
 *
 * Copyright 2018-2019    MachoThemes     office@machothemes.com
 * Copyright 2019         WPKube          wpkube@gmail.com
 *
 * NOTE:
 * MachoThemes ownership rights were ceased on: 02/02/2019 when ownership was turned over to WPKube
 * WPKube ownership started on: 02/02/2019
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package WordPress
 * @author Macho Themes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KIWI_SOCIAL_SHARE_BASE', plugin_dir_path( __FILE__ ) );
define( 'KIWI_SOCIAL_SHARE_URL', plugin_dir_url( __FILE__ ) );
define( 'KIWI_SOCIAL_SHARE_SITE', rtrim(ABSPATH, '\\/') );

// Load plugin class files
require_once 'includes/class-kiwi-social-share.php';
require_once 'includes/lib/helpers/class-kiwi-social-share-helper.php';

require_once 'includes/class-kiwi-social-share-autoloader.php';

/**
 * Returns the main instance of Kiwi_Social_Share to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Kiwi_Social_Share
 */
function Kiwi_Social_Share() {
	$instance = Kiwi_Social_Share::instance( __FILE__, '2.1.8' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Kiwi_Social_Share_Settings::instance( $instance );
	}

	return $instance;
}

Kiwi_Social_Share();