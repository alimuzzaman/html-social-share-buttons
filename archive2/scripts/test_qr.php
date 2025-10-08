<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $qr = \Endroid\QrCode\QrCode::create('https://example.com')->setSize(120);
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    $result = $writer->write($qr);
    $dataUri = method_exists($result, 'getDataUri') ? $result->getDataUri() : null;
    echo 'dataUri: ' . ($dataUri ? substr($dataUri, 0, 30) . '...' : 'unavailable') . PHP_EOL;
} catch (Throwable $e) {
    echo 'QR generation failed: ' . $e->getMessage() . PHP_EOL;
}
