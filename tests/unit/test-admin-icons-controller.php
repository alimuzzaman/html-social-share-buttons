<?php
require __DIR__ . '/../../src/bootstrap.php';

$registry = new HtmlSocialShare\IconRegistry();
$ctrl = new HtmlSocialShare\Admin\IconsController($registry);
$html = $ctrl->index();

if (strpos($html, '<ul') === false) {
    echo "FAIL: Admin IconsController did not render list\n";
    exit(1);
}

echo "Admin IconsController PASS\n";
exit(0);
