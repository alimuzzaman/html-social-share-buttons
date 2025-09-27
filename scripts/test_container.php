<?php
try {
    $c = require __DIR__ . '/../src/bootstrap.php';
    if ($c === null) {
        echo "bootstrap returned null\n";
        exit(1);
    }

    echo 'Container class: ' . get_class($c) . "\n";
    $info = $c->get('info');
    echo 'Info message: ' . $info::message() . "\n";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
