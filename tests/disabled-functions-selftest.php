#!/usr/bin/env php
<?php

require_once __DIR__ . '/disabled-functions-contract.php';

$tempDir = sys_get_temp_dir() . '/hssb-df-selftest-' . bin2hex( random_bytes( 6 ) );
if ( ! mkdir( $tempDir, 0700, true ) ) {
	fwrite( STDERR, "Could not create temp directory for disabled-functions self-test.\n" );
	exit( 1 );
}

try {
	// 1. Static negative cases: every restricted construct must fail.
	$negativeFixtures = array(
		'exec.php'           => "<?php\nexec('true');\n",
		'shell-exec.php'     => "<?php\nshell_exec('id');\n",
		'curl-exec.php'      => "<?php\n\$r = curl_exec(\$ch);\n",
		'ini-set.php'        => "<?php\nini_set('display_errors', '0');\n",
		'glob.php'           => "<?php\n\$f = glob('*.php');\n",
		'scandir.php'        => "<?php\n\$f = scandir('.');\n",
		'error-log.php'      => "<?php\nerror_log('test');\n",
		'ini-get.php'        => "<?php\n\$v = ini_get('memory_limit');\n",
		'lstat.php'          => "<?php\n\$s = lstat(__FILE__);\n",
		'ctype-digit.php'    => "<?php\n\$ok = ctype_digit('123');\n",
		'mb-substr.php'      => "<?php\n\$s = mb_substr('abc', 0, 1);\n",
		'filemtime.php'      => "<?php\n\$t = filemtime(__FILE__);\n",
		'file-get-contents.php' => "<?php\n\$c = file_get_contents(__FILE__);\n",
		'backtick.php'       => "<?php\n\$x = `uname -a`;\n",
		'call-user-func.php' => "<?php\ncall_user_func('exec', 'id');\n",
	);

	foreach ( $negativeFixtures as $name => $source ) {
		$path = $tempDir . '/' . $name;
		file_put_contents( $path, $source );
		$hits = hssb_scan_disabled_functions( array( $path ), $tempDir );
		if ( empty( $hits ) ) {
			throw new RuntimeException( "Negative fixture {$name} unexpectedly passed disabled-functions scanner." );
		}
	}

	// 2. Guarded and method-call positive cases must pass.
	$positivePath = $tempDir . '/valid.php';
	file_put_contents(
		$positivePath,
		"<?php\n" .
		"\$a = function_exists('mb_substr') ? mb_substr('hello', 0, 2) : substr('hello', 0, 2);\n" .
		"function hssb_safe_mtime(\$p) { if (function_exists('filemtime')) { return filemtime(\$p); } return 0; }\n" .
		"function hssb_method(\$obj) { return \$obj->exec(); }\n" .
		"class FooHelper { public static function mail() { return 'ok'; } }\n" .
		"FooHelper::mail();\n"
	);
	$positiveHits = hssb_scan_disabled_functions( array( $positivePath ), $tempDir );
	if ( ! empty( $positiveHits ) ) {
		throw new RuntimeException( 'Valid fixture failed disabled-functions scanner: ' . implode( '; ', $positiveHits ) );
	}

	// 3. Runtime negative check: prove the PHP binary actually enforces disable_functions.
	$cmd = escapeshellarg( PHP_BINARY ) .
		' -d disable_functions=exec,scandir,ini_get -r ' .
		escapeshellarg(
			'set_error_handler(function($s, $m, $f, $l) { throw new ErrorException($m, 0, $s, $f, $l); }); ' .
			'$caught = 0; ' .
			'foreach (array(function(){ exec("true"); }, function(){ scandir("."); }) as $fn) { ' .
			'  try { $fn(); } catch (Throwable $e) { $caught++; } ' .
			'} ' .
			'if (2 !== $caught || function_exists("ini_get")) { exit(2); } echo "ENFORCED";'
		) .
		' 2>&1';
	$out  = array();
	$code = 1;
	exec( $cmd, $out, $code );
	if ( 0 !== $code || false === strpos( implode( "\n", $out ), 'ENFORCED' ) ) {
		throw new RuntimeException( 'PHP binary disable_functions enforcement probe failed: ' . implode( "\n", $out ) );
	}
} finally {
	foreach ( glob( $tempDir . '/*' ) ?: array() as $file ) {
		unlink( $file );
	}
	rmdir( $tempDir );
}

echo "Disabled-functions negative and positive self-tests passed.\n";
