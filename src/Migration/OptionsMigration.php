<?php
/**
 * Options Migration
 *
 * Migrates options from v2.x format to v3.0 format
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\Migration;

/**
 * Class OptionsMigration
 *
 * Handles migration of plugin options from old format to new format.
 * Ensures zero data loss and is idempotent (safe to run multiple times).
 */
class OptionsMigration {
	/**
	 * Old option name
	 *
	 * @var string
	 */
	private $oldOptionName = 'zm_shbt_fld';

	/**
	 * New option name (same as old for backward compatibility)
	 *
	 * @var string
	 */
	private $newOptionName = 'zm_shbt_fld';

	/**
	 * Backup option name
	 *
	 * @var string
	 */
	private $backupOptionName = 'zm_shbt_fld_backup_v2';

	/**
	 * Migration complete flag
	 *
	 * @var string
	 */
	private $migrationFlag = 'zm_shbt_migration_v3_complete';

	/**
	 * Check if migration is needed
	 *
	 * @return bool True if migration needed, false if already done
	 */
	public function needsMigration() {
		// Check if migration already completed
		if ( get_option( $this->migrationFlag ) ) {
			return false;
		}

		// Check if old options exist
		$oldOptions = get_option( $this->oldOptionName );
		if ( empty( $oldOptions ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Run the migration
	 *
	 * @return array Result with 'success' boolean and optional 'message'
	 */
	public function migrate() {
		// Check if migration needed
		if ( ! $this->needsMigration() ) {
			return array(
				'success' => true,
				'message' => 'Migration not needed or already completed',
				'skipped' => true,
			);
		}

		// Load old options
		$oldOptions = get_option( $this->oldOptionName, array() );

		// Create backup
		$backupCreated = $this->createBackup( $oldOptions );
		if ( ! $backupCreated ) {
			return array(
				'success' => false,
				'message' => 'Failed to create backup of old options',
			);
		}

		// Migrate options
		$newOptions = $this->transformOptions( $oldOptions );

		// Validate new options
		$validated = $this->validateOptions( $newOptions );
		if ( ! $validated ) {
			return array(
				'success' => false,
				'message' => 'Validation failed for migrated options',
			);
		}

		// Save new options
		$saved = update_option( $this->newOptionName, $newOptions );
		if ( ! $saved && get_option( $this->newOptionName ) !== $newOptions ) {
			return array(
				'success' => false,
				'message' => 'Failed to save migrated options',
			);
		}

		// Mark migration as complete
		update_option( $this->migrationFlag, time() );

		return array(
			'success'     => true,
			'message'     => 'Migration completed successfully',
			'old_options' => count( $oldOptions ),
			'new_options' => count( $newOptions ),
		);
	}

	/**
	 * Create backup of old options
	 *
	 * @param array $options Options to backup
	 * @return bool True on success, false on failure
	 */
	private function createBackup( $options ) {
		// Don't overwrite existing backup
		if ( get_option( $this->backupOptionName ) !== false ) {
			return true;
		}

		return update_option( $this->backupOptionName, $options );
	}

	/**
	 * Transform old options format to new format
	 *
	 * @param array $oldOptions Old options array
	 * @return array Transformed options
	 */
	private function transformOptions( $oldOptions ) {
		$newOptions = array();

		// Title - stays the same
		if ( isset( $oldOptions['title'] ) ) {
			$newOptions['title'] = sanitize_text_field( $oldOptions['title'] );
		}

		// Excludes - stays the same
		if ( isset( $oldOptions['excludes'] ) ) {
			$newOptions['excludes'] = sanitize_text_field( $oldOptions['excludes'] );
		}

		// Google Analytics - stays the same
		if ( isset( $oldOptions['g_analytics'] ) ) {
			$newOptions['g_analytics'] = (bool) $oldOptions['g_analytics'];
		}

		// Auto-hide button - stays the same
		if ( isset( $oldOptions['auto_hide_btn'] ) ) {
			$newOptions['auto_hide_btn'] = (bool) $oldOptions['auto_hide_btn'];
		}

		// Use port - stays the same
		if ( isset( $oldOptions['use_port'] ) ) {
			$newOptions['use_port'] = (bool) $oldOptions['use_port'];
		}

		// Nofollow - stays the same
		if ( isset( $oldOptions['nofollow'] ) ) {
			$newOptions['nofollow'] = (bool) $oldOptions['nofollow'];
		}

		// Iconset - stays the same
		if ( isset( $oldOptions['iconset'] ) ) {
			$newOptions['iconset'] = sanitize_key( $oldOptions['iconset'] );
		}

		// Show in - transform from nested array to simple array
		if ( isset( $oldOptions['show_in'] ) && is_array( $oldOptions['show_in'] ) ) {
			$newOptions['show_in'] = array();
			
			foreach ( $oldOptions['show_in'] as $placement => $type ) {
				// Old format could be just the type string
				// New format is also just the type string
				if ( ! empty( $type ) ) {
					$cleanPlacement = sanitize_key( $placement );
					$cleanType = is_string( $type ) ? sanitize_key( $type ) : 'square';
					$newOptions['show_in'][ $cleanPlacement ] = $cleanType;
				}
			}
		}

		// Icons - stays the same
		if ( isset( $oldOptions['icons'] ) && is_array( $oldOptions['icons'] ) ) {
			$newOptions['icons'] = array();
			
			foreach ( $oldOptions['icons'] as $network => $enabled ) {
				if ( ! empty( $enabled ) ) {
					$cleanNetwork = sanitize_key( $network );
					$newOptions['icons'][ $cleanNetwork ] = '1';
				}
			}
		}

		// Set defaults for any missing options
		$newOptions = $this->applyDefaults( $newOptions );

		return $newOptions;
	}

	/**
	 * Apply default values for missing options
	 *
	 * @param array $options Options array
	 * @return array Options with defaults applied
	 */
	private function applyDefaults( $options ) {
		$defaults = array(
			'title'         => '',
			'excludes'      => '',
			'g_analytics'   => false,
			'auto_hide_btn' => false,
			'use_port'      => false,
			'nofollow'      => false,
			'iconset'       => 'default',
			'show_in'       => array(),
			'icons'         => array(
				'facebook'    => '1',
				'twitter'     => '1',
				'linkedin'    => '1',
				'pinterest'   => '1',
				'googlepluse' => '1',
				'mail'        => '1',
			),
		);

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * Validate migrated options
	 *
	 * @param array $options Options to validate
	 * @return bool True if valid, false otherwise
	 */
	private function validateOptions( $options ) {
		// Must be an array
		if ( ! is_array( $options ) ) {
			return false;
		}

		// Required keys must exist
		$requiredKeys = array( 'iconset', 'icons' );
		foreach ( $requiredKeys as $key ) {
			if ( ! isset( $options[ $key ] ) ) {
				return false;
			}
		}

		// Iconset must be a string
		if ( ! is_string( $options['iconset'] ) ) {
			return false;
		}

		// Icons must be an array
		if ( ! is_array( $options['icons'] ) ) {
			return false;
		}

		// Show_in must be an array if set
		if ( isset( $options['show_in'] ) && ! is_array( $options['show_in'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Restore from backup
	 *
	 * @return array Result with 'success' boolean and optional 'message'
	 */
	public function restoreBackup() {
		$backup = get_option( $this->backupOptionName );

		if ( empty( $backup ) ) {
			return array(
				'success' => false,
				'message' => 'No backup found',
			);
		}

		$restored = update_option( $this->oldOptionName, $backup );

		if ( ! $restored && get_option( $this->oldOptionName ) !== $backup ) {
			return array(
				'success' => false,
				'message' => 'Failed to restore backup',
			);
		}

		// Remove migration flag to allow re-migration
		delete_option( $this->migrationFlag );

		return array(
			'success' => true,
			'message' => 'Backup restored successfully',
		);
	}

	/**
	 * Get migration status
	 *
	 * @return array Status information
	 */
	public function getStatus() {
		return array(
			'migration_complete'  => (bool) get_option( $this->migrationFlag ),
			'migration_timestamp' => get_option( $this->migrationFlag, 0 ),
			'backup_exists'       => get_option( $this->backupOptionName ) !== false,
			'needs_migration'     => $this->needsMigration(),
		);
	}

	/**
	 * Run migration on plugin activation
	 *
	 * This should be called during plugin activation.
	 */
	public static function runOnActivation() {
		$migration = new self();
		
		if ( $migration->needsMigration() ) {
			$result = $migration->migrate();
			
			if ( $result['success'] ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'HTML Social Share: Migration completed successfully' );
				}
			} else {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'HTML Social Share: Migration failed - ' . $result['message'] );
				}
			}
		}
	}
}
