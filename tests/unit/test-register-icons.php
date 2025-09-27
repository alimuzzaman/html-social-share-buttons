<?php
// Run the register_icons script and ensure it exits 0 or 2 (no iconset available)
$cmd = 'php ' . __DIR__ . '/../../scripts/register_icons.php default';
exec($cmd, $out, $rc);
if ($rc !== 0 && $rc !== 2) {
    echo "FAIL: register_icons returned $rc\n";
    exit(1);
}

echo "Register icons PASS\n";
exit(0);
