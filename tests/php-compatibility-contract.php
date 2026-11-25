#!/usr/bin/env php
<?php

require_once __DIR__ . '/support/disabled-functions-profiles.php';

if ( ! function_exists( 'hssb_compat_prev_meaningful' ) ) {
	function hssb_compat_prev_meaningful( array $tokens, $index ) {
		for ( $pos = $index - 1; $pos >= 0; $pos-- ) {
			if ( ! is_array( $tokens[ $pos ] ) || ! in_array( $tokens[ $pos ][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
				return array( $pos, $tokens[ $pos ] );
			}
		}

		return array( -1, null );
	}
}

if ( ! function_exists( 'hssb_compat_next_meaningful' ) ) {
	function hssb_compat_next_meaningful( array $tokens, $index ) {
		$count = count( $tokens );
		for ( $pos = $index + 1; $pos < $count; $pos++ ) {
			if ( ! is_array( $tokens[ $pos ] ) || ! in_array( $tokens[ $pos ][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
				return array( $pos, $tokens[ $pos ] );
			}
		}

		return array( $count, null );
	}
}

if ( ! function_exists( 'hssb_compat_matching_paren' ) ) {
	function hssb_compat_matching_paren( array $tokens, $openIndex ) {
		$count = count( $tokens );
		$depth = 0;
		for ( $pos = $openIndex; $pos < $count; $pos++ ) {
			$tok = $tokens[ $pos ];
			if ( '(' === $tok ) {
				$depth++;
			} elseif ( ')' === $tok ) {
				$depth--;
				if ( 0 === $depth ) {
					return $pos;
				}
			}
		}

		return $count - 1;
	}
}

if ( ! function_exists( 'hssb_compat_matching_bracket' ) ) {
	function hssb_compat_matching_bracket( array $tokens, $openIndex ) {
		$count = count( $tokens );
		$depth = 0;
		for ( $pos = $openIndex; $pos < $count; $pos++ ) {
			$tok = $tokens[ $pos ];
			if ( '[' === $tok ) {
				$depth++;
			} elseif ( ']' === $tok ) {
				$depth--;
				if ( 0 === $depth ) {
					return $pos;
				}
			}
		}

		return $count - 1;
	}
}

if ( ! function_exists( 'hssb_compat_unquote' ) ) {
	function hssb_compat_unquote( $literal ) {
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

if ( ! function_exists( 'hssb_compat_is_ampersand' ) ) {
	function hssb_compat_is_ampersand( $token ) {
		if ( '&' === $token ) {
			return true;
		}
		if ( ! is_array( $token ) ) {
			return false;
		}
		if ( defined( 'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG' ) && T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG === $token[0] ) {
			return true;
		}

		return defined( 'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG' ) && T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG === $token[0];
	}
}

if ( ! function_exists( 'hssb_compat_index_classes' ) ) {
	/**
	 * Collect declared class properties, parents, and __set definitions across files.
	 *
	 * @param array $parsedFiles List of array( relative => string, tokens => array ).
	 * @return array
	 */
	function hssb_compat_index_classes( array $parsedFiles ) {
		$classes = array();
		foreach ( $parsedFiles as $entry ) {
			$tokens        = $entry['tokens'];
			$count         = count( $tokens );
			$namespace     = '';
			$depth         = 0;
			$classDepth    = -1;
			$currentClass  = '';
			$inMethodDepth = -1;

			for ( $i = 0; $i < $count; $i++ ) {
				$tok = $tokens[ $i ];
				if ( ! is_array( $tok ) ) {
					if ( '{' === $tok ) {
						$depth++;
					} elseif ( '}' === $tok ) {
						if ( $inMethodDepth === $depth ) {
							$inMethodDepth = -1;
						}
						if ( $classDepth === $depth ) {
							$classDepth   = -1;
							$currentClass = '';
						}
						$depth = max( 0, $depth - 1 );
					}
					continue;
				}

				if ( defined( 'T_CURLY_OPEN' ) && T_CURLY_OPEN === $tok[0] ) {
					$depth++;
					continue;
				}
				if ( defined( 'T_DOLLAR_OPEN_CURLY_BRACES' ) && T_DOLLAR_OPEN_CURLY_BRACES === $tok[0] ) {
					$depth++;
					continue;
				}

				if ( T_NAMESPACE === $tok[0] ) {
					list( , $next ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( is_array( $next ) && ( T_STRING === $next[0] || ( defined( 'T_NAME_QUALIFIED' ) && T_NAME_QUALIFIED === $next[0] ) ) ) {
						$nsParts = array();
						$cursor  = $i + 1;
						while ( $cursor < $count ) {
							$part = $tokens[ $cursor ];
							if ( is_array( $part ) && T_WHITESPACE === $part[0] ) {
								$cursor++;
								continue;
							}
							if ( is_array( $part ) && ( T_STRING === $part[0] || T_NS_SEPARATOR === $part[0] || ( defined( 'T_NAME_QUALIFIED' ) && T_NAME_QUALIFIED === $part[0] ) ) ) {
								$nsParts[] = $part[1];
								$cursor++;
								continue;
							}
							break;
						}
						$namespace = trim( implode( '', $nsParts ), '\\' );
					}
					continue;
				}

				if ( in_array( $tok[0], array( T_CLASS, T_TRAIT ), true ) && -1 === $classDepth ) {
					list( , $prev ) = hssb_compat_prev_meaningful( $tokens, $i );
					if ( is_array( $prev ) && ( T_DOUBLE_COLON === $prev[0] || T_NEW === $prev[0] ) ) {
						continue;
					}
					list( $nameIdx, $nameTok ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( ! is_array( $nameTok ) || T_STRING !== $nameTok[0] ) {
						continue;
					}
					$fqcn = '' !== $namespace ? strtolower( $namespace . '\\' . $nameTok[1] ) : strtolower( $nameTok[1] );
					$parent = '';
					$cursor = $nameIdx + 1;
					while ( $cursor < $count && '{' !== $tokens[ $cursor ] ) {
						if ( is_array( $tokens[ $cursor ] ) && T_EXTENDS === $tokens[ $cursor ][0] ) {
							list( $pIdx, $pTok ) = hssb_compat_next_meaningful( $tokens, $cursor );
							$parentParts = array();
							$pCursor     = $pIdx;
							while ( $pCursor < $count && is_array( $tokens[ $pCursor ] ) ) {
								$pt = $tokens[ $pCursor ];
								if ( T_STRING === $pt[0] || T_NS_SEPARATOR === $pt[0] || ( defined( 'T_NAME_QUALIFIED' ) && T_NAME_QUALIFIED === $pt[0] ) || ( defined( 'T_NAME_FULLY_QUALIFIED' ) && T_NAME_FULLY_QUALIFIED === $pt[0] ) ) {
									$parentParts[] = $pt[1];
									$pCursor++;
									continue;
								}
								break;
							}
							$rawParent = implode( '', $parentParts );
							if ( '' !== $rawParent ) {
								$parent = 0 === strpos( $rawParent, '\\' )
									? strtolower( ltrim( $rawParent, '\\' ) )
									: ( '' !== $namespace ? strtolower( $namespace . '\\' . $rawParent ) : strtolower( $rawParent ) );
							}
							break;
						}
						$cursor++;
					}
					if ( ! isset( $classes[ $fqcn ] ) ) {
						$classes[ $fqcn ] = array(
							'parent'     => $parent,
							'properties' => array(),
							'hasSet'     => false,
						);
					}
					while ( $cursor < $count && '{' !== $tokens[ $cursor ] ) {
						$cursor++;
					}
					if ( $cursor < $count && '{' === $tokens[ $cursor ] ) {
						$depth++;
						$classDepth   = $depth;
						$currentClass = $fqcn;
						$i            = $cursor;
					}
					continue;
				}

				if ( '' !== $currentClass && $depth === $classDepth ) {
					if ( T_FUNCTION === $tok[0] ) {
						list( , $fnNameTok ) = hssb_compat_next_meaningful( $tokens, $i );
						if ( is_array( $fnNameTok ) && T_STRING === $fnNameTok[0] && '__set' === strtolower( $fnNameTok[1] ) ) {
							$classes[ $currentClass ]['hasSet'] = true;
						}
						$cursor = $i + 1;
						while ( $cursor < $count && '{' !== $tokens[ $cursor ] && ';' !== $tokens[ $cursor ] ) {
							$cursor++;
						}
						if ( $cursor < $count && '{' === $tokens[ $cursor ] ) {
							$depth++;
							$inMethodDepth = $depth;
							$i             = $cursor;
						}
						continue;
					}

					if ( T_VARIABLE === $tok[0] ) {
						list( , $prev ) = hssb_compat_prev_meaningful( $tokens, $i );
						// Property declarations follow visibility/var/static/type tokens, not `=`.
						if ( is_array( $prev ) && in_array( $prev[0], array( T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR, T_STATIC ), true ) ) {
							$classes[ $currentClass ]['properties'][ substr( $tok[1], 1 ) ] = true;
						} elseif ( ',' === $prev ) {
							$classes[ $currentClass ]['properties'][ substr( $tok[1], 1 ) ] = true;
						}
					}
				}
			}
		}

		return $classes;
	}
}

if ( ! function_exists( 'hssb_compat_resolve_class_info' ) ) {
	function hssb_compat_resolve_class_info( array $classes, $fqcn ) {
		$props        = array();
		$hasSet       = false;
		$closedChain  = true;
		$visited      = array();
		$cursor       = strtolower( (string) $fqcn );

		while ( '' !== $cursor && ! isset( $visited[ $cursor ] ) ) {
			$visited[ $cursor ] = true;
			if ( ! isset( $classes[ $cursor ] ) ) {
				$closedChain = false;
				break;
			}
			foreach ( $classes[ $cursor ]['properties'] as $propName => $flag ) {
				$props[ $propName ] = $flag;
			}
			if ( $classes[ $cursor ]['hasSet'] ) {
				$hasSet = true;
			}
			if ( 'stdclass' === strtolower( $classes[ $cursor ]['parent'] ) ) {
				$hasSet = true;
				break;
			}
			$cursor = $classes[ $cursor ]['parent'];
		}

		return array(
			'properties'  => $props,
			'hasSet'      => $hasSet,
			'closedChain' => $closedChain,
		);
	}
}

if ( ! function_exists( 'hssb_check_php_compatibility' ) ) {
	/**
	 * Static compatibility and deprecation check across PHP 7.0 through 8.5.
	 *
	 * @param array  $paths   Files/directories to scan.
	 * @param string $baseDir Base directory stripped from diagnostics.
	 * @return array
	 */
	function hssb_check_php_compatibility( array $paths, $baseDir ) {
		$baseDir = rtrim( (string) $baseDir, '/\\' ) . '/';
		$files   = hssb_expand_php_files( $paths );

		$parsedFiles = array();
		foreach ( $files as $file ) {
			$relative      = 0 === strpos( $file, $baseDir ) ? substr( $file, strlen( $baseDir ) ) : $file;
			$parsedFiles[] = array(
				'relative' => $relative,
				'tokens'   => token_get_all( (string) file_get_contents( $file ) ),
			);
		}

		$classIndex = hssb_compat_index_classes( $parsedFiles );

		$typeTokens = array( T_STRING, T_ARRAY, T_CALLABLE );
		foreach ( array( 'T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED', 'T_NAME_RELATIVE' ) as $tokenName ) {
			if ( defined( $tokenName ) ) {
				$typeTokens[] = constant( $tokenName );
			}
		}
		$nameTokenTypes = array( T_STRING );
		foreach ( array( 'T_NAME_FULLY_QUALIFIED', 'T_NAME_QUALIFIED' ) as $tokenName ) {
			if ( defined( $tokenName ) ) {
				$nameTokenTypes[] = constant( $tokenName );
			}
		}

		$newerFunctions = array_flip(
			array(
				'is_iterable',
				'is_countable',
				'array_key_first',
				'array_key_last',
				'hrtime',
				'str_contains',
				'str_starts_with',
				'str_ends_with',
				'fdiv',
				'get_debug_type',
				'get_resource_id',
				'preg_last_error_msg',
				'array_is_list',
				'enum_exists',
				'json_validate',
				'array_find',
				'array_find_key',
				'array_any',
				'array_all',
				'array_first',
				'array_last',
			)
		);
		$newerClasses = array_flip(
			array(
				'weakmap',
				'fiber',
				'stringable',
				'valueerror',
				'unhandledmatcherror',
				'curlhandle',
				'random\\randomizer',
			)
		);
		$newerConstants = array_flip(
			array(
				'PHP_FLOAT_DIG',
				'PHP_FLOAT_EPSILON',
				'PHP_FLOAT_MIN',
				'PHP_FLOAT_MAX',
				'JSON_THROW_ON_ERROR',
				'FILTER_FLAG_GLOBAL_RANGE',
			)
		);
		$deprecatedFunctions = array_flip(
			array(
				'create_function',
				'each',
				'ereg',
				'eregi',
				'ereg_replace',
				'eregi_replace',
				'split',
				'spliti',
				'mysql_connect',
				'mysql_pconnect',
				'mysql_query',
				'mysql_real_escape_string',
				'get_magic_quotes_gpc',
				'get_magic_quotes_runtime',
				'money_format',
				'restore_include_path',
				'utf8_encode',
				'utf8_decode',
				'strftime',
				'gmstrftime',
				'imagedestroy',
				'curl_close',
				'xml_set_object',
			)
		);
		$deprecatedConstants = array_flip(
			array(
				'FILTER_SANITIZE_STRING',
				'FILTER_SANITIZE_STRIPPED',
				'E_STRICT',
			)
		);
		$unsupportedReturnKeywords = array_flip(
			array( 'void', 'iterable', 'object', 'mixed', 'never', 'static', 'true', 'false', 'null' )
		);

		$failures = array();

		foreach ( $parsedFiles as $entry ) {
			$relative = $entry['relative'];
			$tokens   = $entry['tokens'];
			$count    = count( $tokens );

			$namespace      = '';
			$braceDepth     = 0;
			$classStack     = array();
			$anonClassProps = array();
			$fnDepths       = array( 0 );
			$fnStarts       = array( 0 );
			$pendingFnDepth = false;
			$guards         = array();
			$parenStack     = array();
			$bracketStack   = array();
			$switchDepths   = array();

			for ( $i = 0; $i < $count; $i++ ) {
				$tok = $tokens[ $i ];

				if ( ! is_array( $tok ) ) {
					if ( '{' === $tok ) {
						$braceDepth++;
						if ( $pendingFnDepth ) {
							$fnDepths[]     = $braceDepth;
							$fnStarts[]     = $i;
							$pendingFnDepth = false;
						}
					} elseif ( '}' === $tok ) {
						if ( count( $fnDepths ) > 1 && $braceDepth === end( $fnDepths ) ) {
							array_pop( $fnDepths );
							array_pop( $fnStarts );
						}
						if ( ! empty( $classStack ) && $braceDepth === end( $classStack )['depth'] ) {
							array_pop( $classStack );
						}
						if ( ! empty( $switchDepths ) && $braceDepth === end( $switchDepths ) ) {
							array_pop( $switchDepths );
						}
						$braceDepth = max( 0, $braceDepth - 1 );
					} elseif ( '(' === $tok ) {
						list( $prevIdx, $prev ) = hssb_compat_prev_meaningful( $tokens, $i );
						$isArrayConstruct       = is_array( $prev ) && T_ARRAY === $prev[0];
						$isFnList               = is_array( $prev ) && ( T_FUNCTION === $prev[0] || T_USE === $prev[0] || ( defined( 'T_FN' ) && T_FN === $prev[0] ) );
						if ( is_array( $prev ) && T_STRING === $prev[0] ) {
							list( , $beforePrev ) = hssb_compat_prev_meaningful( $tokens, $prevIdx );
							if ( is_array( $beforePrev ) && T_FUNCTION === $beforePrev[0] ) {
								$isFnList = true;
							}
						}
						$parenStack[] = array(
							'index'   => $i,
							'isArray' => $isArrayConstruct,
							'isCall'  => ! $isArrayConstruct && ( $isFnList || ( is_array( $prev ) && in_array( $prev[0], $nameTokenTypes, true ) ) || T_VARIABLE === ( is_array( $prev ) ? $prev[0] : 0 ) || ')' === $prev || ']' === $prev ),
						);
					} elseif ( ')' === $tok ) {
						$ctx = array_pop( $parenStack );
						if ( is_array( $ctx ) && ! $ctx['isArray'] && $ctx['isCall'] ) {
							list( $beforeCloseIdx, $beforeClose ) = hssb_compat_prev_meaningful( $tokens, $i );
							if ( ',' === $beforeClose ) {
								list( , $commaContextPrev ) = hssb_compat_prev_meaningful( $tokens, $beforeCloseIdx );
								$line = is_array( $commaContextPrev ) ? $commaContextPrev[2] : 0;
								$failures[] = $relative . ( $line ? ':' . $line : '' ) . ' uses trailing comma before ")" (requires PHP 7.3+/8.0+)';
							}
						}
					} elseif ( '[' === $tok ) {
						list( , $prev ) = hssb_compat_prev_meaningful( $tokens, $i );
						$isIndexAccess = ( is_array( $prev ) && in_array( $prev[0], array( T_VARIABLE, T_STRING, T_CONSTANT_ENCAPSED_STRING ), true ) ) || ']' === $prev || ')' === $prev || '}' === $prev;
						$bracketStack[] = array(
							'index'     => $i,
							'isLiteral' => ! $isIndexAccess,
						);
					} elseif ( ']' === $tok ) {
						$bCtx = array_pop( $bracketStack );
						if ( is_array( $bCtx ) && $bCtx['isLiteral'] ) {
							list( , $afterBracket ) = hssb_compat_next_meaningful( $tokens, $i );
							if ( '=' === $afterBracket ) {
								list( , $nextIn ) = hssb_compat_next_meaningful( $tokens, $bCtx['index'] );
								$line = is_array( $nextIn ) ? $nextIn[2] : 0;
								$failures[] = $relative . ( $line ? ':' . $line : '' ) . ' uses symmetric array destructuring "[...] =" (requires PHP 7.1+)';
							}
						}
					} elseif ( '?' === $tok ) {
						if ( isset( $tokens[ $i + 1 ] ) && is_array( $tokens[ $i + 1 ] ) && T_OBJECT_OPERATOR === $tokens[ $i + 1 ][0] ) {
							$failures[] = $relative . ':' . $tokens[ $i + 1 ][2] . ' uses nullsafe operator "?->" (requires PHP 8.0+)';
						}
					} elseif ( '|' === $tok ) {
						if ( isset( $tokens[ $i + 1 ] ) && '>' === $tokens[ $i + 1 ] ) {
							list( , $prevPipe ) = hssb_compat_prev_meaningful( $tokens, $i );
							$line = is_array( $prevPipe ) ? $prevPipe[2] : 0;
							$failures[] = $relative . ( $line ? ':' . $line : '' ) . ' uses pipe operator "|>" (requires PHP 8.5+)';
						}
					}
					continue;
				}

				if ( defined( 'T_CURLY_OPEN' ) && T_CURLY_OPEN === $tok[0] ) {
					$braceDepth++;
					continue;
				}
				if ( defined( 'T_DOLLAR_OPEN_CURLY_BRACES' ) && T_DOLLAR_OPEN_CURLY_BRACES === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses deprecated "${var}" string interpolation (PHP 8.2+)';
					$braceDepth++;
					continue;
				}

				if ( T_NAMESPACE === $tok[0] ) {
					list( , $next ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( is_array( $next ) && ( T_STRING === $next[0] || ( defined( 'T_NAME_QUALIFIED' ) && T_NAME_QUALIFIED === $next[0] ) ) ) {
						$nsParts = array();
						$cursor  = $i + 1;
						while ( $cursor < $count ) {
							$part = $tokens[ $cursor ];
							if ( is_array( $part ) && T_WHITESPACE === $part[0] ) {
								$cursor++;
								continue;
							}
							if ( is_array( $part ) && ( T_STRING === $part[0] || T_NS_SEPARATOR === $part[0] || ( defined( 'T_NAME_QUALIFIED' ) && T_NAME_QUALIFIED === $part[0] ) ) ) {
								$nsParts[] = $part[1];
								$cursor++;
								continue;
							}
							break;
						}
						$namespace = trim( implode( '', $nsParts ), '\\' );
					}
					continue;
				}

				// Track class / anonymous class scopes for property & constant visibility rules.
				if ( in_array( $tok[0], array( T_CLASS, T_TRAIT, T_INTERFACE ), true ) ) {
					list( , $prev ) = hssb_compat_prev_meaningful( $tokens, $i );
					if ( is_array( $prev ) && T_DOUBLE_COLON === $prev[0] ) {
						continue;
					}
					$isAnon = is_array( $prev ) && T_NEW === $prev[0];
					$fqcn   = '';
					if ( ! $isAnon ) {
						list( , $nameTok ) = hssb_compat_next_meaningful( $tokens, $i );
						if ( is_array( $nameTok ) && T_STRING === $nameTok[0] ) {
							$fqcn = '' !== $namespace ? strtolower( $namespace . '\\' . $nameTok[1] ) : strtolower( $nameTok[1] );
						}
					}
					$cursor = $i + 1;
					$anonProps = array();
					if ( $isAnon ) {
						// Find opening `{` after optional constructor `( ... )`.
						while ( $cursor < $count && '{' !== $tokens[ $cursor ] ) {
							if ( '(' === $tokens[ $cursor ] ) {
								$cursor = hssb_compat_matching_paren( $tokens, $cursor );
							}
							$cursor++;
						}
					} else {
						while ( $cursor < $count && '{' !== $tokens[ $cursor ] ) {
							$cursor++;
						}
					}
					if ( $cursor < $count && '{' === $tokens[ $cursor ] ) {
						$braceDepth++;
						$classStack[] = array(
							'fqcn'   => $fqcn,
							'isAnon' => $isAnon,
							'depth'  => $braceDepth,
						);
						$i = $cursor;
					}
					continue;
				}

				// Class constant visibility & typed properties (PHP 7.1+ / 7.4+).
				$inClassMemberLevel = ! empty( $classStack ) && $braceDepth === end( $classStack )['depth'];
				if ( $inClassMemberLevel && in_array( $tok[0], array( T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR, T_STATIC ), true ) ) {
					list( $afterVisIdx, $afterVis ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( is_array( $afterVis ) && T_CONST === $afterVis[0] && T_VAR !== $tok[0] && T_STATIC !== $tok[0] ) {
						$failures[] = $relative . ':' . $tok[2] . ' uses class constant visibility modifier (requires PHP 7.1+)';
					}
					if ( is_array( $afterVis ) && T_STATIC === $afterVis[0] ) {
						list( , $afterStatic ) = hssb_compat_next_meaningful( $tokens, $afterVisIdx );
						$afterVis = $afterStatic;
					}
					if ( '?' === $afterVis || ( is_array( $afterVis ) && in_array( $afterVis[0], $typeTokens, true ) && T_FUNCTION !== $afterVis[0] && T_CONST !== $afterVis[0] ) ) {
						$scan = $i + 1;
						while ( $scan < $count && ';' !== $tokens[ $scan ] && '=' !== $tokens[ $scan ] && '{' !== $tokens[ $scan ] ) {
							if ( is_array( $tokens[ $scan ] ) && in_array( $tokens[ $scan ][0], array( T_FUNCTION, T_CONST ), true ) ) {
								break;
							}
							if ( is_array( $tokens[ $scan ] ) && T_VARIABLE === $tokens[ $scan ][0] ) {
								$failures[] = $relative . ':' . $tokens[ $scan ][2] . ' uses typed class property (requires PHP 7.4+)';
								break;
							}
							$scan++;
						}
					}
					if ( end( $classStack )['isAnon'] && is_array( $afterVis ) && T_VARIABLE === $afterVis[0] ) {
						$anonProps[ substr( $afterVis[1], 1 ) ] = true;
					}
				}

				// Syntax tokens introduced after PHP 7.0.
				if ( defined( 'T_FN' ) && T_FN === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses arrow function "fn()" (requires PHP 7.4+)';
				} elseif ( T_STRING === $tok[0] && 'fn' === strtolower( $tok[1] ) ) {
					list( , $prevFn ) = hssb_compat_prev_meaningful( $tokens, $i );
					list( $openIdx, $afterFn ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( '(' === $afterFn && ( ! is_array( $prevFn ) || ! in_array( $prevFn[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_CONST ), true ) ) ) {
						$closeIdx = hssb_compat_matching_paren( $tokens, $openIdx );
						list( , $afterClose ) = hssb_compat_next_meaningful( $tokens, $closeIdx );
						if ( is_array( $afterClose ) && T_DOUBLE_ARROW === $afterClose[0] ) {
							$failures[] = $relative . ':' . $tok[2] . ' uses arrow function "fn()" (requires PHP 7.4+)';
						}
					}
				}

				if ( defined( 'T_COALESCE_EQUAL' ) && T_COALESCE_EQUAL === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses null coalescing assignment "??=" (requires PHP 7.4+)';
				} elseif ( defined( 'T_COALESCE' ) && T_COALESCE === $tok[0] && isset( $tokens[ $i + 1 ] ) && '=' === $tokens[ $i + 1 ] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses null coalescing assignment "??=" (requires PHP 7.4+)';
				}

				if ( ( T_LNUMBER === $tok[0] || T_DNUMBER === $tok[0] ) && false !== strpos( $tok[1], '_' ) ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses numeric literal separator "_" (requires PHP 7.4+)';
				} elseif ( ( T_LNUMBER === $tok[0] || T_DNUMBER === $tok[0] ) && isset( $tokens[ $i + 1 ] ) && is_array( $tokens[ $i + 1 ] ) && T_STRING === $tokens[ $i + 1 ][0] && 0 === strpos( $tokens[ $i + 1 ][1], '_' ) ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses numeric literal separator "_" (requires PHP 7.4+)';
				}

				if ( T_ELLIPSIS === $tok[0] && ! empty( $bracketStack ) && end( $bracketStack )['isLiteral'] ) {
					list( , $prevSpread ) = hssb_compat_prev_meaningful( $tokens, $i );
					if ( '[' === $prevSpread || ',' === $prevSpread ) {
						$failures[] = $relative . ':' . $tok[2] . ' uses array spread "[...$arr]" (requires PHP 7.4+)';
					}
				}

				if ( defined( 'T_MATCH' ) && T_MATCH === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses "match" expression (requires PHP 8.0+)';
				} elseif ( T_STRING === $tok[0] && 'match' === strtolower( $tok[1] ) ) {
					list( , $prevM ) = hssb_compat_prev_meaningful( $tokens, $i );
					list( $mOpen, $afterM ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( '(' === $afterM && ( ! is_array( $prevM ) || ! in_array( $prevM[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION ), true ) ) ) {
						$mClose = hssb_compat_matching_paren( $tokens, $mOpen );
						list( , $afterClose ) = hssb_compat_next_meaningful( $tokens, $mClose );
						if ( '{' === $afterClose ) {
							$failures[] = $relative . ':' . $tok[2] . ' uses "match" expression (requires PHP 8.0+)';
						}
					}
				}

				if ( defined( 'T_NULLSAFE_OBJECT_OPERATOR' ) && T_NULLSAFE_OBJECT_OPERATOR === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses nullsafe operator "?->" (requires PHP 8.0+)';
				}

				if ( defined( 'T_ATTRIBUTE' ) && T_ATTRIBUTE === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses PHP 8 attribute "#[...]" (comment on PHP 7.x)';
				} elseif ( T_COMMENT === $tok[0] && 1 === preg_match( '/^#\[[A-Za-z_\\\\]/', $tok[1] ) ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses PHP 8 attribute "#[...]" (comment on PHP 7.x)';
				}
				if ( defined( 'T_READONLY' ) && T_READONLY === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses "readonly" modifier (requires PHP 8.1+/8.2+)';
				} elseif ( T_STRING === $tok[0] && 'readonly' === strtolower( $tok[1] ) ) {
					list( , $afterRo ) = hssb_compat_next_meaningful( $tokens, $i );
					$isRoClass = is_array( $afterRo ) && T_CLASS === $afterRo[0];
					$isRoProp  = $inClassMemberLevel && ( '?' === $afterRo || ( is_array( $afterRo ) && ( T_VARIABLE === $afterRo[0] || in_array( $afterRo[0], array( T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC ), true ) || in_array( $afterRo[0], $typeTokens, true ) ) && T_FUNCTION !== $afterRo[0] && T_CONST !== $afterRo[0] ) );
					if ( $isRoClass || $isRoProp ) {
						$failures[] = $relative . ':' . $tok[2] . ' uses "readonly" modifier (requires PHP 8.1+/8.2+)';
					}
				}
				if ( defined( 'T_ENUM' ) && T_ENUM === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses "enum" (requires PHP 8.1+)';
				} elseif ( T_STRING === $tok[0] && 'enum' === strtolower( $tok[1] ) ) {
					list( , $prevEnum ) = hssb_compat_prev_meaningful( $tokens, $i );
					$prevOk             = null === $prevEnum || ';' === $prevEnum || '{' === $prevEnum || '}' === $prevEnum || ( is_array( $prevEnum ) && T_OPEN_TAG === $prevEnum[0] );
					list( $enumNameIdx, $enumName ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( $prevOk && is_array( $enumName ) && T_STRING === $enumName[0] ) {
						list( , $afterEnumName ) = hssb_compat_next_meaningful( $tokens, $enumNameIdx );
						if ( '{' === $afterEnumName || ':' === $afterEnumName ) {
							$failures[] = $relative . ':' . $tok[2] . ' uses "enum" (requires PHP 8.1+)';
						}
					}
				}
				if ( defined( 'T_PIPE' ) && T_PIPE === $tok[0] ) {
					$failures[] = $relative . ':' . $tok[2] . ' uses pipe operator "|>" (requires PHP 8.5+)';
				}

				// Multi-catch (PHP 7.1+).
				if ( T_CATCH === $tok[0] ) {
					list( $cOpen, $afterC ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( '(' === $afterC ) {
						$cClose = hssb_compat_matching_paren( $tokens, $cOpen );
						for ( $c = $cOpen + 1; $c < $cClose; $c++ ) {
							if ( '|' === $tokens[ $c ] ) {
								$failures[] = $relative . ':' . $tok[2] . ' uses multi-catch "catch (A | B)" (requires PHP 7.1+)';
								break;
							}
						}
					}
				}

				// PHP 8.5 deprecated non-canonical scalar casts.
				if ( in_array( $tok[0], array( T_BOOL_CAST, T_INT_CAST, T_DOUBLE_CAST, T_STRING_CAST ), true ) ) {
					if ( 1 === preg_match( '/^\(\s*(boolean|integer|double|binary)\s*\)$/i', $tok[1], $castMatch ) ) {
						$failures[] = $relative . ':' . $tok[2] . ' uses non-canonical cast ' . $tok[1] . ' (deprecated in PHP 8.5)';
					}
				}

				// PHP 8.5 deprecated switch case semicolon terminators.
				if ( T_SWITCH === $tok[0] ) {
					list( $sOpen, $afterS ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( '(' === $afterS ) {
						$sClose = hssb_compat_matching_paren( $tokens, $sOpen );
						list( , $afterClose ) = hssb_compat_next_meaningful( $tokens, $sClose );
						if ( '{' === $afterClose ) {
							$switchDepths[] = $braceDepth + 1;
						}
					}
				}
				if ( ( T_CASE === $tok[0] || T_DEFAULT === $tok[0] ) && ! empty( $switchDepths ) && $braceDepth === end( $switchDepths ) ) {
					$cIdx     = $i + 1;
					$exprNest = 0;
					while ( $cIdx < $count ) {
						$ct = $tokens[ $cIdx ];
						if ( '(' === $ct || '[' === $ct || '{' === $ct ) {
							$exprNest++;
						} elseif ( ')' === $ct || ']' === $ct || '}' === $ct ) {
							if ( 0 === $exprNest ) {
								break;
							}
							$exprNest--;
						} elseif ( 0 === $exprNest && ( ':' === $ct || ';' === $ct ) ) {
							if ( ';' === $ct ) {
								$failures[] = $relative . ':' . $tok[2] . ' uses ";" after switch case/default (deprecated in PHP 8.5)';
							}
							break;
						}
						$cIdx++;
					}
				}

				// Dynamic property writes `$this->prop = ...` on plugin classes without declared prop or __set (PHP 8.2+).
				if ( T_VARIABLE === $tok[0] && '$this' === $tok[1] && ! empty( $classStack ) ) {
					list( $opIdx, $opTok ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( is_array( $opTok ) && T_OBJECT_OPERATOR === $opTok[0] ) {
						list( $propIdx, $propTok ) = hssb_compat_next_meaningful( $tokens, $opIdx );
						if ( is_array( $propTok ) && T_STRING === $propTok[0] ) {
							$afterPropIdx = $propIdx + 1;
							while ( $afterPropIdx < $count ) {
								list( $nextIdx, $nextTok ) = hssb_compat_next_meaningful( $tokens, $afterPropIdx - 1 );
								if ( '[' === $nextTok ) {
									$afterPropIdx = hssb_compat_matching_bracket( $tokens, $nextIdx ) + 1;
									continue;
								}
								break;
							}
							list( , $assignTok ) = hssb_compat_next_meaningful( $tokens, $afterPropIdx - 1 );
							$isWrite = '=' === $assignTok || ( is_array( $assignTok ) && in_array( $assignTok[0], array( T_PLUS_EQUAL, T_MINUS_EQUAL, T_MUL_EQUAL, T_DIV_EQUAL, T_CONCAT_EQUAL, T_MOD_EQUAL, T_AND_EQUAL, T_OR_EQUAL, T_XOR_EQUAL, T_SL_EQUAL, T_SR_EQUAL, T_INC, T_DEC ), true ) );
							if ( $isWrite ) {
								$currentScope = end( $classStack );
								$propName     = $propTok[1];
								if ( $currentScope['isAnon'] ) {
									if ( ! isset( $anonProps[ $propName ] ) ) {
										$failures[] = $relative . ':' . $propTok[2] . ' writes undeclared dynamic property $this->' . $propName . ' (deprecated in PHP 8.2+)';
									}
								} elseif ( '' !== $currentScope['fqcn'] ) {
									$info = hssb_compat_resolve_class_info( $classIndex, $currentScope['fqcn'] );
									if ( $info['closedChain'] && ! $info['hasSet'] && ! isset( $info['properties'][ $propName ] ) ) {
										$failures[] = $relative . ':' . $propTok[2] . ' writes undeclared dynamic property $this->' . $propName . ' (deprecated in PHP 8.2+)';
									}
								}
							}
						}
					}
				}

				// Inspect function parameter lists, nullable types, return types, and closures.
				if ( T_FUNCTION === $tok[0] ) {
					list( , $afterFn ) = hssb_compat_next_meaningful( $tokens, $i );
					if ( '{' === $afterFn || '(' === $afterFn || is_array( $afterFn ) ) {
						$pendingFnDepth = true;
					}
					$pScan = $i + 1;
					while ( $pScan < $count && '(' !== $tokens[ $pScan ] && ';' !== $tokens[ $pScan ] ) {
						$pScan++;
					}
					if ( $pScan < $count && '(' === $tokens[ $pScan ] ) {
						$pClose = hssb_compat_matching_paren( $tokens, $pScan );
						// Check parameter list between $pScan and $pClose.
						for ( $p = $pScan + 1; $p < $pClose; $p++ ) {
							$pt = $tokens[ $p ];
							if ( '?' === $pt ) {
								list( , $prevP ) = hssb_compat_prev_meaningful( $tokens, $p );
								list( , $nextP ) = hssb_compat_next_meaningful( $tokens, $p );
								if ( ( '(' === $prevP || ',' === $prevP ) && is_array( $nextP ) && in_array( $nextP[0], $typeTokens, true ) ) {
									$failures[] = $relative . ':' . $nextP[2] . ' uses nullable parameter type "?Type" (requires PHP 7.1+)';
								}
							}
							if ( ! is_array( $pt ) || T_VARIABLE !== $pt[0] ) {
								continue;
							}
							list( $prevPIdx, $prevP ) = hssb_compat_prev_meaningful( $tokens, $p );
							if ( hssb_compat_is_ampersand( $prevP ) ) {
								list( , $prevP ) = hssb_compat_prev_meaningful( $tokens, $prevPIdx );
							}
							if ( is_array( $prevP ) && in_array( $prevP[0], $typeTokens, true ) ) {
								list( $eqIdx, $eq ) = hssb_compat_next_meaningful( $tokens, $p );
								list( , $def )      = hssb_compat_next_meaningful( $tokens, $eqIdx );
								if ( '=' === $eq && is_array( $def ) && T_STRING === $def[0] && 'null' === strtolower( $def[1] ) ) {
									$failures[] = $relative . ':' . $pt[2] . ' uses an implicitly nullable typed parameter (deprecated in PHP 8.4+)';
								}
							}
						}
						// Check return type between $pClose and `{` or `;`.
						$rScan = $pClose + 1;
						list( $uIdx, $afterParen ) = hssb_compat_next_meaningful( $tokens, $pClose );
						if ( is_array( $afterParen ) && T_USE === $afterParen[0] ) {
							list( $useOpen, $useParen ) = hssb_compat_next_meaningful( $tokens, $uIdx );
							if ( '(' === $useParen ) {
								$rScan = hssb_compat_matching_paren( $tokens, $useOpen ) + 1;
							}
						}
						list( $colonIdx, $maybeColon ) = hssb_compat_next_meaningful( $tokens, $rScan - 1 );
						if ( ':' === $maybeColon ) {
							for ( $r = $colonIdx + 1; $r < $count && '{' !== $tokens[ $r ] && ';' !== $tokens[ $r ]; $r++ ) {
								$rt = $tokens[ $r ];
								if ( '?' === $rt ) {
									$failures[] = $relative . ':' . ( is_array( $tokens[ $r + 1 ] ) ? $tokens[ $r + 1 ][2] : 0 ) . ' uses nullable return type (requires PHP 7.1+)';
									break;
								}
								if ( '|' === $rt || hssb_compat_is_ampersand( $rt ) || '(' === $rt ) {
									$failures[] = $relative . ' uses union/intersection/DNF return type (requires PHP 8.0+)';
									break;
								}
								if ( is_array( $rt ) && ( T_STRING === $rt[0] || T_ARRAY === $rt[0] || T_CALLABLE === $rt[0] || T_STATIC === $rt[0] ) ) {
									$retName = strtolower( $rt[1] );
									if ( isset( $unsupportedReturnKeywords[ $retName ] ) ) {
										$failures[] = $relative . ':' . $rt[2] . ' uses return type ":' . $retName . '" (unsupported on PHP 7.0)';
										break;
									}
								}
							}
						}
					}
					continue;
				}

				// Inspect function calls, constants, class references, and reflection checks.
				if ( in_array( $tok[0], $nameTokenTypes, true ) ) {
					list( $prevIdx, $prev ) = hssb_compat_prev_meaningful( $tokens, $i );
					list( $nextIdx, $next ) = hssb_compat_next_meaningful( $tokens, $i );

					// Record guards (`function_exists('...')`, `class_exists('...')`, `interface_exists('...')`, `defined('...')`).
					$lowerRaw = strtolower( ltrim( $tok[1], '\\' ) );
					if ( in_array( $lowerRaw, array( 'function_exists', 'class_exists', 'interface_exists', 'defined' ), true ) && '(' === $next ) {
						$isGlobalCheck = ! is_array( $prev ) || ! in_array( $prev[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION ), true );
						if ( $isGlobalCheck ) {
							list( $argIdx, $argTok ) = hssb_compat_next_meaningful( $tokens, $nextIdx );
							if ( is_array( $argTok ) && T_CONSTANT_ENCAPSED_STRING === $argTok[0] ) {
								$guardedSymbol = ltrim( hssb_compat_unquote( $argTok[1] ), '\\' );
								if ( '' !== $guardedSymbol ) {
									$key = 'defined' === $lowerRaw ? $guardedSymbol : strtolower( $guardedSymbol );
									$guards[ $key ][] = $i;
								}
							}
						}
						continue;
					}

					// Function call checks.
					if ( '(' === $next && ( ! is_array( $prev ) || ! in_array( $prev[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW ), true ) ) ) {
						$isNsLocal = is_array( $prev ) && T_NS_SEPARATOR === $prev[0];
						if ( $isNsLocal ) {
							list( , $beforeNs ) = hssb_compat_prev_meaningful( $tokens, $prevIdx );
							if ( is_array( $beforeNs ) && in_array( $beforeNs[0], array( T_STRING, T_NAMESPACE ), true ) ) {
								continue;
							}
						}
						if ( false !== strpos( $lowerRaw, '\\' ) ) {
							continue;
						}
						$scopeStart = end( $fnStarts );
						$isGuarded  = false;
						if ( isset( $guards[ $lowerRaw ] ) ) {
							foreach ( $guards[ $lowerRaw ] as $gIdx ) {
								if ( $gIdx >= $scopeStart && $gIdx < $i ) {
									$isGuarded = true;
									break;
								}
							}
						}
						if ( isset( $newerFunctions[ $lowerRaw ] ) && ! $isGuarded ) {
							$failures[] = $relative . ':' . $tok[2] . ' calls unguarded post-PHP-7.0 function ' . $lowerRaw . '()';
							continue;
						}
						if ( isset( $deprecatedFunctions[ $lowerRaw ] ) ) {
							$failures[] = $relative . ':' . $tok[2] . ' calls deprecated/removed PHP function ' . $lowerRaw . '()';
							continue;
						}
						if ( function_exists( $lowerRaw ) ) {
							$rf = new ReflectionFunction( $lowerRaw );
							if ( $rf->isInternal() && $rf->isDeprecated() ) {
								$failures[] = $relative . ':' . $tok[2] . ' calls function ' . $lowerRaw . '() deprecated in PHP ' . PHP_VERSION;
								continue;
							}
						}
					}

					// Class / interface checks (`new X`, `instanceof X`, `X::`).
					$isClassRef = is_array( $prev ) && in_array( $prev[0], array( T_NEW, T_INSTANCEOF ), true );
					if ( ! $isClassRef && is_array( $next ) && T_DOUBLE_COLON === $next[0] ) {
						$isClassRef = true;
					}
					if ( $isClassRef && isset( $newerClasses[ $lowerRaw ] ) ) {
						$scopeStart = end( $fnStarts );
						$isGuarded  = false;
						if ( isset( $guards[ $lowerRaw ] ) ) {
							foreach ( $guards[ $lowerRaw ] as $gIdx ) {
								if ( $gIdx >= $scopeStart && $gIdx < $i ) {
									$isGuarded = true;
									break;
								}
							}
						}
						if ( ! $isGuarded ) {
							$failures[] = $relative . ':' . $tok[2] . ' references unguarded post-PHP-7.0 class/interface ' . $tok[1];
						}
					}

					// Constant checks (`FILTER_SANITIZE_STRING`, `JSON_THROW_ON_ERROR`, `E_STRICT`, etc.).
					$exactName = ltrim( $tok[1], '\\' );
					if ( '(' !== $next && ( ! is_array( $prev ) || ! in_array( $prev[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_CONST, T_FUNCTION ), true ) ) ) {
						if ( isset( $deprecatedConstants[ $exactName ] ) ) {
							$failures[] = $relative . ':' . $tok[2] . ' uses deprecated/removed constant ' . $exactName;
						} elseif ( isset( $newerConstants[ $exactName ] ) ) {
							$scopeStart = end( $fnStarts );
							$isGuarded  = false;
							if ( isset( $guards[ $exactName ] ) ) {
								foreach ( $guards[ $exactName ] as $gIdx ) {
									if ( $gIdx >= $scopeStart && $gIdx < $i ) {
										$isGuarded = true;
										break;
									}
								}
							}
							if ( ! $isGuarded ) {
								$failures[] = $relative . ':' . $tok[2] . ' uses unguarded post-PHP-7.0 constant ' . $exactName;
							}
						}
					}
				}
			}
		}

		return array_values( array_unique( $failures ) );
	}
}

if (
	isset( $_SERVER['SCRIPT_FILENAME'] ) &&
	realpath( (string) $_SERVER['SCRIPT_FILENAME'] ) === realpath( __FILE__ )
) {
	$root     = dirname( __DIR__ );
	$failures = hssb_check_php_compatibility( hssb_collect_production_php_paths( $root ), $root );
	if ( $failures ) {
		fwrite( STDERR, "PHP compatibility contract failed:\n - " . implode( "\n - ", $failures ) . "\n" );
		exit( 1 );
	}
	echo 'PHP compatibility contract passed on PHP ' . PHP_VERSION . " (7.0 floor + 8.1-8.5 deprecation & reflection checks).\n";
}
