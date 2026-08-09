<?php

$wordpress_root = getenv( 'WP_ROOT' );

if ( ! is_string( $wordpress_root ) || '' === $wordpress_root ) {
	throw new RuntimeException( 'WP_ROOT must point to the WordPress installation under test.' );
}

define( 'ABSPATH', rtrim( $wordpress_root, '/\\' ) . '/' );
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'wordpress' );
define( 'DB_PASSWORD', 'wordpress' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_';

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'HTML Social Share Buttons tests' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
