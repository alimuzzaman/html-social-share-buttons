<?php
require_once 'tests/bootstrap-minimal.php';

$result = HtmlSocialShare\Utils\SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World');
echo 'Result: ' . var_export($result, true) . PHP_EOL;
echo 'Expected: ' . var_export('Hello World', true) . PHP_EOL;
echo 'Equal: ' . ($result === 'Hello World' ? 'YES' : 'NO') . PHP_EOL;
