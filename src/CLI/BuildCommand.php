<?php
/**
 * WP-CLI Build Command
 *
 * Provides WP-CLI command to rebuild iconset CSS files.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\CLI;

use HtmlSocialShare\Build\IconsetBuilder;
use WP_CLI;

/**
 * Class BuildCommand
 *
 * WP-CLI command for building iconset CSS files.
 */
class BuildCommand {
	/**
	 * Build iconset CSS files
	 *
	 * Scans assets/iconset/ directories and generates CSS files for each iconset.
	 *
	 * ## EXAMPLES
	 *
	 *     # Build all iconsets
	 *     $ wp html-social-share build-iconsets
	 *     Success: Built 8 iconsets
	 *
	 * @when after_wp_load
	 */
	public function build_iconsets( $args, $assoc_args ) {
		WP_CLI::log( 'Building iconset CSS files...' );

		$builder = new IconsetBuilder();
		$results = $builder->buildAll();

		if ( empty( $results ) ) {
			WP_CLI::warning( 'No iconsets found in ' . $builder->getAssetsDir() );
			return;
		}

		$successCount = 0;
		$failCount = 0;

		foreach ( $results as $iconsetKey => $result ) {
			if ( $result['success'] ) {
				$successCount++;
				WP_CLI::success(
					sprintf(
						'Built %s (%d networks)',
						$iconsetKey,
						$result['networks']
					)
				);
			} else {
				$failCount++;
				WP_CLI::error(
					sprintf(
						'Failed to build %s: %s',
						$iconsetKey,
						$result['error']
					),
					false
				);
			}
		}

		WP_CLI::log( '' );
		
		if ( $successCount > 0 ) {
			WP_CLI::success(
				sprintf(
					'Built %d iconset%s successfully',
					$successCount,
					$successCount === 1 ? '' : 's'
				)
			);
		}

		if ( $failCount > 0 ) {
			WP_CLI::error(
				sprintf(
					'Failed to build %d iconset%s',
					$failCount,
					$failCount === 1 ? '' : 's'
				)
			);
		}
	}

	/**
	 * Register WP-CLI commands
	 *
	 * @return void
	 */
	public static function register() {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return;
		}

		WP_CLI::add_command( 'html-social-share', __CLASS__ );
	}
}
