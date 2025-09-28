<?php
use PHPUnit\Framework\TestCase;

class TestWeChatQr extends TestCase
{
    public function testWeChatRendererOutputsQrImageDataOrFallback()
    {
        if (!class_exists('HtmlSocialShare\\ShareRenderer')) {
            $this->markTestSkipped('ShareRenderer not available');
        }

        // If Endroid is present but no image extension is available locally, skip
        $endroidAvailable = class_exists('Endroid\\QrCode\\QrCode');
        $gdAvailable = extension_loaded('gd');
        $imagickAvailable = extension_loaded('imagick');

        if ($endroidAvailable && !($gdAvailable || $imagickAvailable)) {
            $this->markTestSkipped('Endroid available but no image extension present (GD/Imagick); skipping local data-uri assertion');
        }

        // Create mocks for IconRegistryInterface and Settings
        $iconRegistryMock = $this->createMock(\HtmlSocialShare\IconRegistryInterface::class);
        $iconRegistryMock->method('getIcon')->willReturn('<svg class="hss-icon">...</svg>');

        $settingsMock = $this->createMock(\HtmlSocialShare\Settings::class);
        $settingsMock->method('get')->willReturnMap([
            ['share_counts_enabled', false, false]
        ]);

        $renderer = new \HtmlSocialShare\ShareRenderer($iconRegistryMock, $settingsMock, null);

        $html = $renderer->render('wechat', ['network' => 'wechat', 'handle' => '@example'], 'https://example.com', 'Example Title');

        $this->assertStringContainsString('<img', $html, 'Rendered HTML should include an <img> for the QR');

        if ($endroidAvailable && ($gdAvailable || $imagickAvailable)) {
            $this->assertStringContainsString('data:image', $html, 'When Endroid and GD/Imagick are available expect a data URI');
        } else {
            $this->assertStringContainsString('chart.googleapis.com', $html, 'When local QR generation unavailable expect external chart API fallback');
        }
    }
}
