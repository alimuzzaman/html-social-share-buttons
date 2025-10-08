<?php
require_once 'tests/bootstrap-minimal.php';
require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use HtmlSocialShare\Utils\SecurityUtils;

class SingleTest extends TestCase
{
    public function testSanitizeTextField()
    {
        // Test HTML tag removal
        $this->assertEquals('Hello World', SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World'));
    }
}

$suite = new PHPUnit\Framework\TestSuite();
$suite->addTest(new SingleTest('testSanitizeTextField'));

$runner = new PHPUnit\TextUI\TestRunner();
$result = $runner->run($suite, ['verbose' => false]);

echo 'Tests run: ' . $result->count() . PHP_EOL;
echo 'Failures: ' . $result->failureCount() . PHP_EOL;
if ($result->failureCount() > 0) {
    foreach ($result->failures() as $failure) {
        echo 'Failure: ' . $failure->getMessage() . PHP_EOL;
    }
}
