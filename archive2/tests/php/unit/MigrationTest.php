<?php
namespace HtmlSocialShare\Tests\Unit;

use PHPUnit\Framework\TestCase;
use HtmlSocialShare\Migration;
use HtmlSocialShare\Settings;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test migration from legacy zm_shbt_fld options to new hss_core schema
 * 
 * This test verifies that all 12 legacy keys are properly migrated:
 * 1. title → title
 * 2. excludes → exclude_pages
 * 3. g_analytics → google_analytics
 * 4. auto_hide_btn → auto_hide_buttons
 * 5. use_port → use_port_in_url
 * 6. nofollow → nofollow_links
 * 7. iconset → icon_style
 * 8. show_left → floating_left
 * 9. show_right → floating_right
 * 10. show_before_post → before_content
 * 11. show_after_post → after_content
 * 12. icons → enabled_networks
 *
 * @covers \HtmlSocialShare\Migration
 */
class MigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Test that all 12 legacy keys are properly mapped during migration
     */
    public function test_migrates_all_12_legacy_keys(): void
    {
        // Arrange: Create legacy options with all 12 keys
        $legacyOptions = [
            'title' => 'Custom Share Title',
            'excludes' => '1,2,about-us',
            'g_analytics' => true,
            'auto_hide_btn' => true,
            'use_port' => true,
            'nofollow' => true,
            'iconset' => 'flat',
            'show_left' => true,
            'show_right' => false,
            'show_before_post' => true,
            'show_after_post' => false,
            'icons' => [
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
                'googlepluse' => 1,
                'pinterest' => 1
            ]
        ];

        // Mock WordPress functions
        Functions\expect('get_option')
            ->with('zm_shbt_fld')
            ->andReturn($legacyOptions);

        Functions\expect('update_option')->andReturn(true);
        Functions\expect('current_time')->with('mysql')->andReturn('2025-10-05 10:00:00');

        // Create a mock Settings class
        $settingsMock = $this->createMock(Settings::class);
        
        // Track what gets set
        $capturedSettings = [];
        $settingsMock->method('set')
            ->willReturnCallback(function($key, $value) use (&$capturedSettings) {
                $capturedSettings[$key] = $value;
            });

        $settingsMock->method('getMigrationStatus')
            ->willReturn(['done' => false]);

        $settingsMock->method('setMigrationStatus')->willReturn(true);
        $settingsMock->method('setProfile')->willReturn(true);
        $settingsMock->method('setIcon')->willReturn(true);

        // Act: Run migration
        $migration = new Migration($settingsMock);
        $result = $migration->run();

        // Assert: Verify migration ran
        $this->assertTrue($result, 'Migration should return true');

        // Assert: Verify all 12 legacy keys were mapped correctly
        $this->assertArrayHasKey('title', $capturedSettings, '1. title should be migrated');
        $this->assertEquals('Custom Share Title', $capturedSettings['title']);

        $this->assertArrayHasKey('exclude_pages', $capturedSettings, '2. excludes → exclude_pages should be migrated');
        $this->assertEquals('1,2,about-us', $capturedSettings['exclude_pages']);

        $this->assertArrayHasKey('google_analytics', $capturedSettings, '3. g_analytics → google_analytics should be migrated');
        $this->assertTrue($capturedSettings['google_analytics']);

        $this->assertArrayHasKey('auto_hide_buttons', $capturedSettings, '4. auto_hide_btn → auto_hide_buttons should be migrated');
        $this->assertTrue($capturedSettings['auto_hide_buttons']);

        $this->assertArrayHasKey('use_port_in_url', $capturedSettings, '5. use_port → use_port_in_url should be migrated');
        $this->assertTrue($capturedSettings['use_port_in_url']);

        $this->assertArrayHasKey('nofollow_links', $capturedSettings, '6. nofollow → nofollow_links should be migrated');
        $this->assertTrue($capturedSettings['nofollow_links']);

        $this->assertArrayHasKey('icon_style', $capturedSettings, '7. iconset → icon_style should be migrated');
        $this->assertEquals('flat', $capturedSettings['icon_style']);

        $this->assertArrayHasKey('floating_left', $capturedSettings, '8. show_left → floating_left should be migrated');
        $this->assertTrue($capturedSettings['floating_left']);

        $this->assertArrayHasKey('floating_right', $capturedSettings, '9. show_right → floating_right should be migrated');
        $this->assertFalse($capturedSettings['floating_right']);

        $this->assertArrayHasKey('before_content', $capturedSettings, '10. show_before_post → before_content should be migrated');
        $this->assertTrue($capturedSettings['before_content']);

        $this->assertArrayHasKey('after_content', $capturedSettings, '11. show_after_post → after_content should be migrated');
        $this->assertFalse($capturedSettings['after_content']);

        $this->assertArrayHasKey('enabled_networks', $capturedSettings, '12. icons → enabled_networks should be migrated');
        $this->assertIsArray($capturedSettings['enabled_networks']);
        $this->assertContains('facebook', $capturedSettings['enabled_networks']);
        $this->assertContains('twitter', $capturedSettings['enabled_networks']);
        $this->assertContains('linkedin', $capturedSettings['enabled_networks']);
        $this->assertContains('googleplus', $capturedSettings['enabled_networks']); // Note: googlepluse → googleplus
        $this->assertContains('pinterest', $capturedSettings['enabled_networks']);
    }

    /**
     * Test migration handles missing legacy keys with defaults
     */
    public function test_handles_missing_legacy_keys_with_defaults(): void
    {
        // Arrange: Partial legacy options (only some keys present)
        $legacyOptions = [
            'title' => 'My Title',
            'iconset' => 'square',
            // Missing: excludes, g_analytics, auto_hide_btn, etc.
        ];

        Functions\expect('get_option')
            ->with('zm_shbt_fld')
            ->andReturn($legacyOptions);

        Functions\expect('update_option')->andReturn(true);
        Functions\expect('current_time')->with('mysql')->andReturn('2025-10-05 10:00:00');

        $settingsMock = $this->createMock(Settings::class);
        $capturedSettings = [];
        
        $settingsMock->method('set')
            ->willReturnCallback(function($key, $value) use (&$capturedSettings) {
                $capturedSettings[$key] = $value;
            });

        $settingsMock->method('getMigrationStatus')->willReturn(['done' => false]);
        $settingsMock->method('setMigrationStatus')->willReturn(true);
        $settingsMock->method('setProfile')->willReturn(true);
        $settingsMock->method('setIcon')->willReturn(true);

        // Act
        $migration = new Migration($settingsMock);
        $migration->run();

        // Assert: Missing boolean keys default to false
        $this->assertArrayHasKey('google_analytics', $capturedSettings);
        $this->assertFalse($capturedSettings['google_analytics'], 'Missing g_analytics should default to false');

        $this->assertArrayHasKey('auto_hide_buttons', $capturedSettings);
        $this->assertFalse($capturedSettings['auto_hide_buttons'], 'Missing auto_hide_btn should default to false');

        $this->assertArrayHasKey('use_port_in_url', $capturedSettings);
        $this->assertFalse($capturedSettings['use_port_in_url'], 'Missing use_port should default to false');

        $this->assertArrayHasKey('nofollow_links', $capturedSettings);
        $this->assertFalse($capturedSettings['nofollow_links'], 'Missing nofollow should default to false');

        // Missing placement booleans default to false
        $this->assertArrayHasKey('floating_left', $capturedSettings);
        $this->assertFalse($capturedSettings['floating_left']);

        $this->assertArrayHasKey('floating_right', $capturedSettings);
        $this->assertFalse($capturedSettings['floating_right']);

        $this->assertArrayHasKey('before_content', $capturedSettings);
        $this->assertFalse($capturedSettings['before_content']);

        $this->assertArrayHasKey('after_content', $capturedSettings);
        $this->assertFalse($capturedSettings['after_content']);

        // Missing string fields get defaults
        $this->assertArrayHasKey('exclude_pages', $capturedSettings);
        $this->assertEquals('', $capturedSettings['exclude_pages'], 'Missing excludes should default to empty string');
    }

    /**
     * Test migration handles legacy network name variations
     */
    public function test_normalizes_legacy_network_names(): void
    {
        // Arrange: Legacy icons with old naming conventions
        $legacyOptions = [
            'icons' => [
                'googlepluse' => 1,    // Legacy name
                'mail' => 1,            // Legacy name for email
                'bookmark' => 1,        // Legacy name
            ]
        ];

        Functions\expect('get_option')
            ->with('zm_shbt_fld')
            ->andReturn($legacyOptions);

        Functions\expect('update_option')->andReturn(true);
        Functions\expect('current_time')->with('mysql')->andReturn('2025-10-05 10:00:00');

        $settingsMock = $this->createMock(Settings::class);
        $capturedSettings = [];
        
        $settingsMock->method('set')
            ->willReturnCallback(function($key, $value) use (&$capturedSettings) {
                $capturedSettings[$key] = $value;
            });

        $settingsMock->method('getMigrationStatus')->willReturn(['done' => false]);
        $settingsMock->method('setMigrationStatus')->willReturn(true);
        $settingsMock->method('setProfile')->willReturn(true);
        $settingsMock->method('setIcon')->willReturn(true);

        // Act
        $migration = new Migration($settingsMock);
        $migration->run();

        // Assert: Legacy names are normalized
        $this->assertContains('googleplus', $capturedSettings['enabled_networks'], 'googlepluse should be normalized to googleplus');
        $this->assertContains('email', $capturedSettings['enabled_networks'], 'mail should be normalized to email');
        $this->assertContains('bookmark', $capturedSettings['enabled_networks'], 'bookmark should be preserved');
    }

    /**
     * Test migration doesn't run twice
     */
    public function test_migration_only_runs_once(): void
    {
        // Arrange: Migration already done
        $settingsMock = $this->createMock(Settings::class);
        
        $settingsMock->method('getMigrationStatus')
            ->willReturn([
                'done' => true,
                'date' => '2025-10-01 10:00:00'
            ]);

        // Act
        $migration = new Migration($settingsMock);
        $result = $migration->run();

        // Assert
        $this->assertFalse($result, 'Migration should return false when already done');
    }
}
