<?php
use PHPUnit\Framework\TestCase;

class TestVkAdapter extends TestCase
{
    public function testVkAdapterFetchReturnsIntOrSkips()
    {
        if (!class_exists('HtmlSocialShare\\ShareCounts\\Adapters\\VkAdapter')) {
            $this->markTestSkipped('VkAdapter not available');
        }

        if (!function_exists('wp_remote_get')) {
            $this->markTestSkipped('WordPress HTTP functions not available in test environment');
        }

        $adapter = new \HtmlSocialShare\ShareCounts\Adapters\VkAdapter();
        $count = $adapter->fetch('https://example.com');
        $this->assertIsInt($count);
    }
}
