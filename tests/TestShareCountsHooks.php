<?php
use PHPUnit\Framework\TestCase;

class TestShareCountsHooks extends TestCase
{
    public function testCronAndAjaxHooksRegistered()
    {
        // These tests assume bootstrap.php ran (it will during plugin bootstrap in integration tests)
        $this->assertTrue(has_action('hss_refresh_share_counts') !== false, 'Cron refresh hook should be registered');
        $this->assertTrue(has_action('wp_ajax_hss_refresh_counts') !== false, 'Admin AJAX refresh hook should be registered');
        $this->assertTrue(has_action('wp_ajax_hss_flush_share_counts') !== false, 'Admin AJAX flush hook should be registered');
    }
}
