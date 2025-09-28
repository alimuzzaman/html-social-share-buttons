<?php
use PHPUnit\Framework\TestCase;

class TestShareCountsCron extends TestCase
{
    public function testRefreshCountsDoesNotThrow()
    {
        if (!class_exists('HtmlSocialShare\\ShareCounts\\ShareCountManager')) {
            $this->markTestSkipped('ShareCountManager not available');
        }

        $settingsMock = $this->createMock(\HtmlSocialShare\Settings::class);
        $settingsMock->method('get')->willReturn([]);
        $cacheMock = $this->createMock(\HtmlSocialShare\CacheInterface::class);
        $cacheMock->method('get')->willReturn(null);
        $cacheMock->method('set')->willReturn(null);

        $manager = new \HtmlSocialShare\ShareCounts\ShareCountManager($cacheMock, $settingsMock);

        // Should not raise any exceptions
        $manager->refreshCounts([]);
        $this->assertTrue(true);
    }
}
