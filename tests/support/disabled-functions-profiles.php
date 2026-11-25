<?php

/**
 * Canonical PHP disable_functions profiles modeled after shared/managed
 * WordPress hosts and stricter hardened environments.
 *
 * Must remain PHP 7.0-compatible so every matrix cell can parse and load it.
 */

if ( ! function_exists( 'hssb_disabled_functions_shared_host' ) ) {
	/**
	 * Functions commonly disabled by shared and managed WordPress hosts.
	 *
	 * @return array
	 */
	function hssb_disabled_functions_shared_host() {
		return array(
			'exec',
			'shell_exec',
			'system',
			'passthru',
			'popen',
			'proc_open',
			'proc_close',
			'proc_get_status',
			'proc_nice',
			'proc_terminate',
			'pcntl_alarm',
			'pcntl_fork',
			'pcntl_waitpid',
			'pcntl_wait',
			'pcntl_wifexited',
			'pcntl_wifstopped',
			'pcntl_wifsignaled',
			'pcntl_wifcontinued',
			'pcntl_wexitstatus',
			'pcntl_wtermsig',
			'pcntl_wstopsig',
			'pcntl_signal',
			'pcntl_signal_dispatch',
			'pcntl_get_last_error',
			'pcntl_strerror',
			'pcntl_sigprocmask',
			'pcntl_sigwaitinfo',
			'pcntl_sigtimedwait',
			'pcntl_exec',
			'pcntl_getpriority',
			'pcntl_setpriority',
			'pcntl_async_signals',
			'pcntl_unshare',
			'posix_kill',
			'posix_mkfifo',
			'posix_setpgid',
			'posix_setsid',
			'posix_setuid',
			'posix_getpwuid',
			'posix_uname',
			'posix_ctermid',
			'posix_getegid',
			'posix_geteuid',
			'posix_getgid',
			'posix_getgrgid',
			'posix_getgrnam',
			'posix_getgroups',
			'posix_getlogin',
			'posix_getpgid',
			'posix_getpgrp',
			'posix_getpid',
			'posix_getppid',
			'posix_getpwnam',
			'posix_getrlimit',
			'posix_getsid',
			'posix_getuid',
			'posix_isatty',
			'posix_setegid',
			'posix_seteuid',
			'posix_setgid',
			'posix_times',
			'posix_ttyname',
			'dl',
			'show_source',
			'highlight_file',
			'phpinfo',
			'php_uname',
			'putenv',
			'apache_setenv',
			'apache_child_terminate',
			'symlink',
			'link',
			'readlink',
			'linkinfo',
			'disk_free_space',
			'disk_total_space',
			'parse_ini_file',
			'escapeshellarg',
			'escapeshellcmd',
			'curl_exec',
			'curl_multi_exec',
			'fsockopen',
			'pfsockopen',
			'stream_socket_server',
			'mail',
			'set_time_limit',
			'ignore_user_abort',
			'ini_set',
			'ini_alter',
			'ini_restore',
			'ini_get_all',
			'chmod',
			'chown',
			'chgrp',
			'lchgrp',
			'lchown',
			'fpassthru',
			'readfile',
			'glob',
			'getmyuid',
			'getmypid',
			'getrusage',
			'get_current_user',
		);
	}
}

if ( ! function_exists( 'hssb_disabled_functions_hardened_extra' ) ) {
	/**
	 * Additional functions restricted on ultra-hardened hosts or stripped builds.
	 *
	 * @return array
	 */
	function hssb_disabled_functions_hardened_extra() {
		return array(
			'scandir',
			'error_log',
			'ini_get',
			'lstat',
			'filemtime',
			'mb_substr',
			'mb_strlen',
			'mb_strtolower',
			'ctype_digit',
			'iconv',
		);
	}
}

if ( ! function_exists( 'hssb_disabled_functions_guarded_optional' ) ) {
	/**
	 * Functions allowed in production ONLY when guarded by function_exists().
	 *
	 * @return array
	 */
	function hssb_disabled_functions_guarded_optional() {
		return array(
			'scandir',
			'error_log',
			'ini_get',
			'lstat',
			'filemtime',
			'file_get_contents',
			'ctype_digit',
			'mb_substr',
			'mb_strlen',
			'mb_strtolower',
			'iconv',
			'json_encode',
			'json_decode',
		);
	}
}

if ( ! function_exists( 'hssb_disabled_functions_hardened_host' ) ) {
	/**
	 * Full combined disabled-functions list for strict standalone verification.
	 *
	 * @return array
	 */
	function hssb_disabled_functions_hardened_host() {
		return array_values(
			array_unique(
				array_merge(
					hssb_disabled_functions_shared_host(),
					hssb_disabled_functions_hardened_extra()
				)
			)
		);
	}
}

if ( ! function_exists( 'hssb_disabled_functions_wp_bootstrap_required' ) ) {
	/**
	 * Functions WordPress core itself calls unguarded during wp-load.php boot.
	 *
	 * The plugin itself calls neither function unguarded; excluding these two
	 * lets wp-load.php boot so the rest of the shared_host list can be enforced
	 * at WordPress runtime while standalone tests still enforce the full list.
	 *
	 * @return array
	 */
	function hssb_disabled_functions_wp_bootstrap_required() {
		return array(
			'ini_set',
			'glob',
		);
	}
}

if ( ! function_exists( 'hssb_disabled_functions_wordpress_runtime' ) ) {
	/**
	 * Shared/managed host disable_functions profile compatible with WordPress core boot.
	 *
	 * @return array
	 */
	function hssb_disabled_functions_wordpress_runtime() {
		return array_values(
			array_diff(
				hssb_disabled_functions_shared_host(),
				hssb_disabled_functions_wp_bootstrap_required()
			)
		);
	}
}

if ( ! function_exists( 'hssb_collect_production_php_paths' ) ) {
	/**
	 * Production PHP files shipped in the plugin archive.
	 *
	 * @param string $root Repository root directory.
	 * @return array
	 */
	function hssb_collect_production_php_paths( $root ) {
		$root  = rtrim( (string) $root, '/\\' );
		$paths = array(
			$root . '/html-social-share.php',
			$root . '/src',
			$root . '/resources',
		);
		$assetFiles = glob( $root . '/build/*.asset.php' );
		if ( is_array( $assetFiles ) ) {
			sort( $assetFiles );
			foreach ( $assetFiles as $assetFile ) {
				$paths[] = $assetFile;
			}
		}

		return $paths;
	}
}

if ( ! function_exists( 'hssb_expand_php_files' ) ) {
	/**
	 * Expand files and directories into sorted .php file paths.
	 *
	 * @param array $paths Candidate files or directories.
	 * @return array
	 */
	function hssb_expand_php_files( array $paths ) {
		$files = array();
		foreach ( $paths as $path ) {
			if ( is_file( $path ) ) {
				if ( 'php' === strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
					$files[] = $path;
				}
				continue;
			}
			if ( ! is_dir( $path ) ) {
				continue;
			}
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS )
			);
			foreach ( $iterator as $entry ) {
				if ( $entry->isFile() && 'php' === strtolower( $entry->getExtension() ) ) {
					$files[] = $entry->getPathname();
				}
			}
		}
		$files = array_values( array_unique( $files ) );
		sort( $files );

		return $files;
	}
}
