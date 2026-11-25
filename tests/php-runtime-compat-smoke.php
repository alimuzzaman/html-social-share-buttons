#!/usr/bin/env php
<?php

require_once __DIR__ . '/support/standalone-runtime-smoke.php';

$previousReporting = error_reporting( E_ALL );
try {
	hssb_run_standalone_runtime_smoke( dirname( __DIR__ ) );
} finally {
	error_reporting( $previousReporting );
}

echo 'Standalone PHP E_ALL runtime compatibility smoke passed on PHP ' . PHP_VERSION . ".\n";
