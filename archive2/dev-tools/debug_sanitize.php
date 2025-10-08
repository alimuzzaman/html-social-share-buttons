<?php
require_once 'vendor/autoload.php';
require_once 'src/Utils/SecurityUtils.php';

$input = 'Hello <script>alert("xss")</script>World';
echo 'Input: ' . $input . PHP_EOL;

$step1 = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $input);
echo 'After script removal: ' . $step1 . PHP_EOL;

$step2 = strip_tags($step1);
echo 'After strip_tags: ' . $step2 . PHP_EOL;

$step3 = preg_replace('/[\r\n\t ]+/', ' ', $step2);
echo 'After whitespace: ' . $step3 . PHP_EOL;

$result = trim($step3);
echo 'Final result: ' . $result . PHP_EOL;

echo 'Expected: Hello World' . PHP_EOL;
echo 'Match: ' . ($result === 'Hello World' ? 'YES' : 'NO') . PHP_EOL;
