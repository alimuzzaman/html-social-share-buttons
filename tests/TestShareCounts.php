<?php
use PHPUnit\Framework\TestCase;

class TestShareCounts extends TestCase
{
    public function testFetchCountFromNetworkReturnsInt()
    {
        if (!class_exists('HtmlSocialShare\\ShareCounts\\ShareCountManager')) {
            $this->markTestSkipped('ShareCountManager not available');
        }

        // Minimal test to ensure method exists and returns integer for a URL
        $settingsMock = $this->createMock(\HtmlSocialShare\Settings::class);
        $settingsMock->method('get')->willReturn(false);
        $cacheMock = $this->createMock(\HtmlSocialShare\CacheInterface::class);
        $cacheMock->method('get')->willReturn(null);
        $cacheMock->method('set')->willReturn(null);

        $manager = new \HtmlSocialShare\ShareCounts\ShareCountManager($cacheMock, $settingsMock);
        $count = $manager->fetchCountFromNetwork('https://example.com', 'facebook');
        $this->assertIsInt($count);
    }
}
