#!/usr/bin/env php
<?php

require_once __DIR__ . '/support/disabled-functions-profiles.php';

if ( ! function_exists( 'hssb_df_prev_meaningful' ) ) {
	function hssb_df_prev_meaningful( array $tokens, $index ) {
		for ( $pos = $index - 1; $pos >= 0; $pos-- ) {
			if ( ! is_array( $tokens[ $pos ] ) || ! in_array( $tokens[ $pos ][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
				return array( $pos, $tokens[ $pos ] );
			}
		}

		return array( -1, null );
	}
}

if ( ! function_exists( 'hssb_df_next_meaningful' ) ) {
	function hssb_df_next_meaningful( array $tokens, $index ) {
		$count = count( $tokens );
		for ( $pos = $index + 1; $pos < $count; $pos++ ) {
			if ( ! is_array( $tokens[ $pos ] ) || ! in_array( $tokens[ $pos ][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
				return array( $pos, $tokens[ $pos ] );
			}
		}

		return array( $count, null );
	}
}

if ( ! function_exists( 'hssb_df_unquote_literal' ) ) {
	function hssb_df_unquote_literal( $literal ) {
		if ( strlen( $literal ) < 2 ) {
			return '';
		}
		$first = $literal[0];
		$last  = $literal[ strlen( $literal ) - 1 ];
		if ( ( "'" === $first && "'" === $last ) || ( '"' === $first && '"' === $last ) ) {
			return substr( $literal, 1, -1 );
		}

		return '';
	}
}

if ( ! function_exists( 'hssb_df_is_method_or_declaration' ) ) {
	function hssb_df_is_method_or_declaration( array $tokens, $index ) {
		list( $prevIndex, $prev ) = hssb_df_prev_meaningful( $tokens, $index );
		if ( ! is_array( $prev ) ) {
			return false;
		}
		if ( in_array( $prev[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW ), true ) ) {
			return true;
		}
		if ( defined( 'T_NULLSAFE_OBJECT_OPERATOR' ) && T_NULLSAFE_OBJECT_OPERATOR === $prev[0] ) {
			return true;
		}
		if ( T_NS_SEPARATOR === $prev[0] ) {
			list( , $beforeNs ) = hssb_df_prev_meaningful( $tokens, $prevIndex );
			// A leading backslash (`\exec()`) targets the global namespace; `Foo\exec()` does not.
			if ( is_array( $beforeNs ) && in_array( $beforeNs[0], array( T_STRING, T_NAMESPACE ), true ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'hssb_df_has_preceding_guard' ) ) {
	function hssb_df_has_preceding_guard( array $guardIndexes, $scopeStartIndex, $callIndex ) {
		foreach ( $guardIndexes as $guardIndex ) {
			if ( $guardIndex >= $scopeStartIndex && $guardIndex < $callIndex ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'hssb_scan_disabled_functions' ) ) {
	/**
	 * Statically audit PHP files for host-disabled functions and unguarded optional calls.
	 *
	 * @param array  $paths   Files/directories to scan.
	 * @param string $baseDir Base path stripped in reported failures.
	 * @return array
	 */
	function hssb_scan_disabled_functions( array $paths, $baseDir ) {
		$baseDir         = rtrim( (string) $baseDir, '/\\' ) . '/';
		$sharedHostMap   = array_flip( hssb_disabled_functions_shared_host() );
		$guardedMap      = array_flip( hssb_disabled_functions_guarded_optional() );
		$nameTokenTypes  = array( T_STRING );
		foreach ( array( 'T_NAME_FULLY_QUALIFIED', 'T_NAME_QUALIFIED' ) as $constantName ) {
			if ( defined( $constantName ) ) {
				$nameTokenTypes[] = constant( $constantName );
			}
		}

		$failures = array();
		foreach ( hssb_expand_php_files( $paths ) as $file ) {
			$relative = 0 === strpos( $file, $baseDir ) ? substr( $file, strlen( $baseDir ) ) : $file;
			$contents = (string) file_get_contents( $file );
			$tokens   = token_get_all( $contents );
			$count    = count( $tokens );

			$braceDepth        = 0;
			$functionDepths    = array( 0 );
			$functionStartIdx  = array( 0 );
			$pendingFunction   = false;
			$guardsByFunction  = array();

			for ( $i = 0; $i < $count; $i++ ) {
				$token = $tokens[ $i ];
				if ( '`' === $token ) {
					$line       = isset( $tokens[ $i + 1 ] ) && is_array( $tokens[ $i + 1 ] ) ? $tokens[ $i + 1 ][2] : 0;
					$failures[] = $relative . ( $line ? ':' . $line : '' ) . ' uses backtick shell execution operator';
					continue;
				}
				if ( ! is_array( $token ) ) {
					if ( '{' === $token ) {
						$braceDepth++;
						if ( $pendingFunction ) {
							$functionDepths[]   = $braceDepth;
							$functionStartIdx[] = $i;
							$pendingFunction    = false;
						}
					} elseif ( '}' === $token ) {
						if ( count( $functionDepths ) > 1 && $braceDepth === end( $functionDepths ) ) {
							array_pop( $functionDepths );
							array_pop( $functionStartIdx );
						}
						$braceDepth = max( 0, $braceDepth - 1 );
					}
					continue;
				}

				if ( T_FUNCTION === $token[0] ) {
					list( , $afterFn ) = hssb_df_next_meaningful( $tokens, $i );
					if ( '{' === $afterFn || '(' === $afterFn || is_array( $afterFn ) ) {
						$pendingFunction = true;
					}
					continue;
				}

				if ( '{' === ( defined( 'T_CURLY_OPEN' ) && T_CURLY_OPEN === $token[0] ? '{' : '' ) ) {
					$braceDepth++;
					continue;
				}

				if ( ! in_array( $token[0], $nameTokenTypes, true ) ) {
					continue;
				}

				list( $openIndex, $after ) = hssb_df_next_meaningful( $tokens, $i );
				if ( '(' !== $after ) {
					continue;
				}
				if ( hssb_df_is_method_or_declaration( $tokens, $i ) ) {
					continue;
				}

				$rawName  = strtolower( ltrim( $token[1], '\\' ) );
				$funcName = false === strpos( $rawName, '\\' ) ? $rawName : '';
				if ( '' === $funcName ) {
					continue;
				}

				$line = $token[2];

				// Record `function_exists('foo')` guards before subsequent calls in the same function scope.
				if ( 'function_exists' === $funcName ) {
					list( $argIndex, $argToken ) = hssb_df_next_meaningful( $tokens, $openIndex );
					if ( is_array( $argToken ) && T_CONSTANT_ENCAPSED_STRING === $argToken[0] ) {
						list( , $closeParen ) = hssb_df_next_meaningful( $tokens, $argIndex );
						if ( ')' === $closeParen ) {
							$guardedName = strtolower( ltrim( hssb_df_unquote_literal( $argToken[1] ), '\\' ) );
							if ( '' !== $guardedName ) {
								$guardsByFunction[ $guardedName ][] = $i;
							}
						}
					}
					continue;
				}

				// Inspect direct string-literal dynamic calls via call_user_func / call_user_func_array.
				$calledName = $funcName;
				if ( 'call_user_func' === $funcName || 'call_user_func_array' === $funcName ) {
					list( , $argToken ) = hssb_df_next_meaningful( $tokens, $openIndex );
					if ( is_array( $argToken ) && T_CONSTANT_ENCAPSED_STRING === $argToken[0] ) {
						$calledName = strtolower( ltrim( hssb_df_unquote_literal( $argToken[1] ), '\\' ) );
					}
				}

				if ( isset( $sharedHostMap[ $calledName ] ) ) {
					$failures[] = $relative . ':' . $line . ' calls host-disabled function ' . $calledName . '()';
					continue;
				}

				if ( isset( $guardedMap[ $calledName ] ) ) {
					$scopeStart   = end( $functionStartIdx );
					$guardIndexes = isset( $guardsByFunction[ $calledName ] ) ? $guardsByFunction[ $calledName ] : array();
					if ( ! hssb_df_has_preceding_guard( $guardIndexes, $scopeStart, $i ) ) {
						$failures[] = $relative . ':' . $line . ' calls unguarded hardened/optional function ' . $calledName . '() without function_exists()';
					}
				}
			}
		}

		return $failures;
	}
}

if (
	isset( $_SERVER['SCRIPT_FILENAME'] ) &&
	realpath( (string) $_SERVER['SCRIPT_FILENAME'] ) === realpath( __FILE__ )
) {
	$root     = dirname( __DIR__ );
	$failures = hssb_scan_disabled_functions( hssb_collect_production_php_paths( $root ), $root );
	if ( $failures ) {
		fwrite( STDERR, "Disabled-functions static contract failed:\n - " . implode( "\n - ", $failures ) . "\n" );
		exit( 1 );
	}
	echo 'Disabled-functions static contract passed (' . count( hssb_disabled_functions_hardened_host() ) . " restricted functions verified).\n";
}
