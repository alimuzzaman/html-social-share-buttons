<?php
use PHPUnit\Framework\TestCase;

class TestMissingAutoloadNotice extends TestCase
{
    public function testRegisterAutoloadNotice()
    {
        if (!function_exists('add_action') || !function_exists('do_action')) {
            $this->markTestSkipped('WordPress test functions not available in this environment');
        }

        // Ensure no admin_notices are registered initially (may vary by test harness)
        // Register a notice for a non-existent path
        $fakePath = '/nonexistent/vendor/autoload.php';
        html_social_share_register_autoload_notice($fakePath);

        // Capture output of admin_notices
        ob_start();
        do_action('admin_notices');
        $output = ob_get_clean();

        $this->assertStringContainsString('Composer autoloader not found', $output);
        $this->assertStringContainsString('composer install', $output);
        $this->assertStringContainsString(htmlspecialchars($fakePath, ENT_QUOTES, 'UTF-8'), $output);
    }
}
