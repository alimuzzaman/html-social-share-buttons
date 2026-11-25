#!/usr/bin/env php
<?php

require_once __DIR__ . '/php-compatibility-contract.php';

$tempDir = sys_get_temp_dir() . '/hssb-php-compat-selftest-' . bin2hex( random_bytes( 6 ) );
if ( ! mkdir( $tempDir, 0700, true ) ) {
	fwrite( STDERR, "Could not create temp directory for PHP compatibility self-test.\n" );
	exit( 1 );
}

try {
	$negativeFixtures = array(
		'nullable-param.php'       => "<?php\nfunction f(?string \$x) { return \$x; }\n",
		'return-void.php'          => "<?php\nfunction f(): void {}\n",
		'const-visibility.php'     => "<?php\nclass A { private const X = 1; }\n",
		'symmetric-destructure.php'=> "<?php\n[\$a, \$b] = array(1, 2);\n",
		'arrow-fn.php'             => "<?php\n\$f = fn(\$x) => \$x;\n",
		'typed-prop.php'           => "<?php\nclass B { private string \$name; }\n",
		'coalesce-equal.php'       => "<?php\n\$a = null; \$a ??= 1;\n",
		'nullsafe.php'             => "<?php\n\$x = \$obj?->prop;\n",
		'match-expr.php'           => "<?php\n\$y = match (\$x) { 1 => 'a', default => 'b' };\n",
		'trailing-comma-call.php'  => "<?php\nstrlen('abc',);\n",
		'array-spread.php'         => "<?php\n\$a = array(1); \$b = [...\$a, 2];\n",
		'multi-catch.php'          => "<?php\ntry { foo(); } catch (RuntimeException | LogicException \$e) {}\n",
		'unguarded-str-contains.php'=> "<?php\n\$ok = str_contains('abc', 'a');\n",
		'unguarded-array-key-first.php'=> "<?php\n\$k = array_key_first(array('a' => 1));\n",
		'implicit-nullable.php'    => "<?php\nfunction f(Foo \$x = null) { return \$x; }\n",
		'dollar-brace-interp.php'  => "<?php\n\$bar = 'hi'; \$s = \"\${bar}\";\n",
		'dynamic-property.php'     => "<?php\nclass DynPropTest { public function set() { \$this->undeclared = 1; } }\n",
		'non-canonical-cast.php'   => "<?php\n\$i = (integer) '5';\n",
		'switch-semicolon.php'     => "<?php\nswitch (\$x) { case 1; break; }\n",
		'deprecated-utf8.php'      => "<?php\n\$s = utf8_encode('abc');\n",
		'removed-create-fn.php'    => "<?php\n\$f = create_function('', 'return 1;');\n",
		'deprecated-e-strict.php'  => "<?php\n\$lvl = E_STRICT;\n",
		'numeric-separator.php'    => "<?php\n\$n = 1_000;\n",
		'typed-static-prop.php'    => "<?php\nclass C { private static string \$name; }\n",
		'attribute.php'            => "<?php\n#[AllowDynamicProperties]\nclass AttrTest {}\n",
		'readonly-class.php'       => "<?php\nreadonly class RoTest {}\n",
		'enum.php'                 => "<?php\nenum Status { case Open; }\n",
		'pipe-operator.php'        => "<?php\n\$out = 'hi' |> strlen(...);\n",
		'implicit-nullable-ref.php'=> "<?php\nfunction g(Foo & \$x = null) { return \$x; }\n",
	);

	foreach ( $negativeFixtures as $name => $source ) {
		$path = $tempDir . '/' . $name;
		file_put_contents( $path, $source );
		$hits = hssb_check_php_compatibility( array( $path ), $tempDir );
		if ( empty( $hits ) ) {
			throw new RuntimeException( "Negative PHP compatibility fixture {$name} unexpectedly passed." );
		}
	}

	// Clean PHP 7.0 syntax, array() trailing comma, stdClass dynamic prop, and guarded symbol must pass.
	$validPath = $tempDir . '/valid.php';
	file_put_contents(
		$validPath,
		"<?php\n" .
		"class ValidBase { protected \$declared = ''; }\n" .
		"class ValidChild extends ValidBase {\n" .
		"  public function run( \$items = null ) {\n" .
		"    \$this->declared = 'ok';\n" .
		"    \$bag = new stdClass();\n" .
		"    \$bag->dynamic = true;\n" .
		"    \$arr = array( 'a', 'b', );\n" .
		"    \$cond = ! empty( \$arr ) ? (int) '1' : 0;\n" .
		"    if ( function_exists( 'str_contains' ) ) { return str_contains( 'abc', 'a' ); }\n" .
		"    return false !== strpos( 'abc', 'a' ) && \$cond >= 0;\n" .
		"  }\n" .
		"}\n"
	);
	$validHits = hssb_check_php_compatibility( array( $validPath ), $tempDir );
	if ( ! empty( $validHits ) ) {
		throw new RuntimeException( 'Valid PHP 7.0 fixture failed compatibility checker: ' . implode( '; ', $validHits ) );
	}

	// Verify plugin-frame-scoped E_ALL diagnostic trapping catches plugin-file deprecations.
	$trapFile = $tempDir . '/plugin-trap.php';
	file_put_contents(
		$trapFile,
		"<?php\nfunction hssb_selftest_trigger_deprecation() { trigger_error( 'synthetic deprecation', E_USER_DEPRECATED ); }\n"
	);
	require_once $trapFile;
	$pluginRoot = realpath( $tempDir ) . DIRECTORY_SEPARATOR;
	$caught     = false;
	$prev       = error_reporting( E_ALL );
	set_error_handler(
		static function ( $severity, $message, $file, $line ) use ( $pluginRoot ) {
			if ( 0 === strpos( (string) $file, $pluginRoot ) ) {
				throw new ErrorException( $message, 0, $severity, $file, $line );
			}

			return true;
		}
	);
	try {
		hssb_selftest_trigger_deprecation();
	} catch ( ErrorException $e ) {
		$caught = false !== strpos( $e->getMessage(), 'synthetic deprecation' );
	} finally {
		restore_error_handler();
		error_reporting( $prev );
	}
	if ( ! $caught ) {
		throw new RuntimeException( 'E_ALL plugin-frame deprecation trap did not catch E_USER_DEPRECATED.' );
	}
} finally {
	foreach ( glob( $tempDir . '/*' ) ?: array() as $file ) {
		unlink( $file );
	}
	rmdir( $tempDir );
}

echo "PHP compatibility negative and positive self-tests passed.\n";
