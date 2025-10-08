<?php
require __DIR__ . '/../src/bootstrap.php';

use HtmlSocialShare\Iconset\PreviewGenerator;

$set = $argv[1] ?? 'default';
$base = __DIR__ . '/../iconset';
$gen = new PreviewGenerator($base);

echo "<html><head><meta charset=\"utf-8\"><title>Iconset preview: " . htmlspecialchars($set) . "</title></head><body>\n";
echo $gen->generatePreviewHtml($set);
echo "\n</body></html>";
