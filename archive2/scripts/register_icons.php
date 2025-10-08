<?php
require __DIR__ . '/../src/bootstrap.php';

use HtmlSocialShare\IconRegistry;

$set = $argv[1] ?? 'default';
$base = __DIR__ . '/../iconset';
$dir = $base . DIRECTORY_SEPARATOR . $set;

if (!is_dir($dir)) {
    fwrite(STDERR, "Iconset not found: $set\n");
    exit(2);
}

$registry = new IconRegistry();

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if ($file->isDir()) continue;
    if (!preg_match('/\.svg$/i', $file->getFilename())) continue;

    $rel = ltrim(str_replace($dir, '', $file->getPathname()), DIRECTORY_SEPARATOR);
    $key = str_replace(DIRECTORY_SEPARATOR, '/', $rel);
    $svg = file_get_contents($file->getPathname());
    $registry->registerIcon($key, $svg);
    echo "Registered: $key\n";
}

echo "Done\n";
exit(0);
