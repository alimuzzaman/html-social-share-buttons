<?php
/**
 * Tests for UrlUtils class
 *
 * @package Html_Social_Share_Buttons
 */

use PHPUnit\Framework\TestCase;
use HtmlSocialShare\Utils\UrlUtils;

class UrlUtilsTest extends TestCase
{
    /**
     * Test buildShareUrl method
     */
    public function testBuildShareUrl()
    {
        // Test basic URL building
        $template = 'https://example.com/share?url={{url}}&title={{title}}';
        $params = ['url' => 'https://test.com', 'title' => 'Test Title'];
        $expected = 'https://example.com/share?url=https%3A%2F%2Ftest.com&title=Test+Title';
        $this->assertEquals($expected, UrlUtils::buildShareUrl($template, $params));

        // Test missing parameters
        $template = 'https://example.com/share?url={{url}}&title={{missing}}';
        $params = ['url' => 'https://test.com'];
        $expected = 'https://example.com/share?url=https%3A%2F%2Ftest.com&title=';
        $this->assertEquals($expected, UrlUtils::buildShareUrl($template, $params));

        // Test no parameters
        $template = 'https://example.com/share';
        $this->assertEquals($template, UrlUtils::buildShareUrl($template, []));

        // Test empty template
        $this->assertEquals('', UrlUtils::buildShareUrl('', []));
    }

    /**
     * Test extractDomain method
     */
    public function testExtractDomain()
    {
        // Test basic domain extraction
        $this->assertEquals('example.com', UrlUtils::extractDomain('https://example.com/path'));
        $this->assertEquals('subdomain.example.com', UrlUtils::extractDomain('https://subdomain.example.com'));

        // Test with port
        $this->assertEquals('example.com', UrlUtils::extractDomain('https://example.com:8080/path'));

        // Test invalid URLs
        $this->assertEquals('', UrlUtils::extractDomain('not-a-url'));
        $this->assertEquals('', UrlUtils::extractDomain(''));

        // Test protocol-less URLs
        $this->assertEquals('', UrlUtils::extractDomain('example.com'));
    }

    /**
     * Test isValidUrl method
     */
    public function testIsValidUrl()
    {
        // Test valid URLs
        $this->assertTrue(UrlUtils::isValidUrl('https://example.com'));
        $this->assertTrue(UrlUtils::isValidUrl('http://example.com/path?query=value'));
        $this->assertTrue(UrlUtils::isValidUrl('https://subdomain.example.com:8080'));

        // Test invalid URLs
        $this->assertFalse(UrlUtils::isValidUrl('not-a-url'));
        $this->assertFalse(UrlUtils::isValidUrl('ftp://example.com'));
        $this->assertFalse(UrlUtils::isValidUrl(''));
        $this->assertFalse(UrlUtils::isValidUrl('javascript:alert("xss")'));
    }

    /**
     * Test isHttps method
     */
    public function testIsHttps()
    {
        // Test HTTPS URLs
        $this->assertTrue(UrlUtils::isHttps('https://example.com'));
        $this->assertTrue(UrlUtils::isHttps('https://example.com/path'));

        // Test HTTP URLs
        $this->assertFalse(UrlUtils::isHttps('http://example.com'));

        // Test invalid URLs
        $this->assertFalse(UrlUtils::isHttps('not-a-url'));
        $this->assertFalse(UrlUtils::isHttps(''));
    }

    /**
     * Test getScheme method
     */
    public function testGetScheme()
    {
        // Test various schemes
        $this->assertEquals('https', UrlUtils::getScheme('https://example.com'));
        $this->assertEquals('http', UrlUtils::getScheme('http://example.com'));
        $this->assertEquals('ftp', UrlUtils::getScheme('ftp://example.com'));

        // Test invalid URLs
        $this->assertEquals('', UrlUtils::getScheme('not-a-url'));
        $this->assertEquals('', UrlUtils::getScheme(''));
    }

    /**
     * Test getPath method
     */
    public function testGetPath()
    {
        // Test path extraction
        $this->assertEquals('/path/to/page', UrlUtils::getPath('https://example.com/path/to/page'));
        $this->assertEquals('/path', UrlUtils::getPath('https://example.com/path?query=value'));

        // Test root path
        $this->assertEquals('/', UrlUtils::getPath('https://example.com/'));
        $this->assertEquals('/', UrlUtils::getPath('https://example.com'));

        // Test invalid URLs
        $this->assertEquals('', UrlUtils::getPath('not-a-url'));
        $this->assertEquals('', UrlUtils::getPath(''));
    }

    /**
     * Test getQueryParams method
     */
    public function testGetQueryParams()
    {
        // Test basic query parsing
        $url = 'https://example.com?param1=value1&param2=value2';
        $expected = ['param1' => 'value1', 'param2' => 'value2'];
        $this->assertEquals($expected, UrlUtils::getQueryParams($url));

        // Test URL encoded values
        $url = 'https://example.com?url=https%3A%2F%2Ftest.com&title=Test+Title';
        $expected = ['url' => 'https://test.com', 'title' => 'Test Title'];
        $this->assertEquals($expected, UrlUtils::getQueryParams($url));

        // Test no query string
        $this->assertEquals([], UrlUtils::getQueryParams('https://example.com'));

        // Test empty values
        $url = 'https://example.com?empty=&param=value';
        $expected = ['empty' => '', 'param' => 'value'];
        $this->assertEquals($expected, UrlUtils::getQueryParams($url));

        // Test invalid URL
        $this->assertEquals([], UrlUtils::getQueryParams('not-a-url'));
    }

    /**
     * Test addQueryParams method
     */
    public function testAddQueryParams()
    {
        // Test adding to URL without query string
        $url = 'https://example.com';
        $params = ['param1' => 'value1', 'param2' => 'value2'];
        $result = UrlUtils::addQueryParams($url, $params);
        $this->assertStringContainsString('param1=value1', $result);
        $this->assertStringContainsString('param2=value2', $result);
        $this->assertStringStartsWith('https://example.com?', $result);

        // Test adding to URL with existing query string
        $url = 'https://example.com?existing=value';
        $params = ['new' => 'param'];
        $result = UrlUtils::addQueryParams($url, $params);
        $this->assertStringContainsString('existing=value', $result);
        $this->assertStringContainsString('new=param', $result);

        // Test URL encoding
        $url = 'https://example.com';
        $params = ['url' => 'https://test.com', 'title' => 'Test Title'];
        $result = UrlUtils::addQueryParams($url, $params);
        $this->assertStringContainsString('url=https%3A%2F%2Ftest.com', $result);
        $this->assertStringContainsString('title=Test+Title', $result);

        // Test empty params
        $url = 'https://example.com';
        $this->assertEquals($url, UrlUtils::addQueryParams($url, []));
    }

    /**
     * Test removeQueryParams method
     */
    public function testRemoveQueryParams()
    {
        // Test basic parameter removal
        $url = 'https://example.com?param1=value1&param2=value2&param3=value3';
        $result = UrlUtils::removeQueryParams($url, ['param2']);
        $this->assertStringContainsString('param1=value1', $result);
        $this->assertStringNotContainsString('param2=value2', $result);
        $this->assertStringContainsString('param3=value3', $result);

        // Test removing multiple parameters
        $url = 'https://example.com?param1=value1&param2=value2&param3=value3';
        $result = UrlUtils::removeQueryParams($url, ['param1', 'param3']);
        $this->assertStringNotContainsString('param1=value1', $result);
        $this->assertStringContainsString('param2=value2', $result);
        $this->assertStringNotContainsString('param3=value3', $result);

        // Test removing all parameters
        $url = 'https://example.com?param1=value1&param2=value2';
        $result = UrlUtils::removeQueryParams($url, ['param1', 'param2']);
        $this->assertEquals('https://example.com', $result);

        // Test removing non-existent parameters
        $url = 'https://example.com?param1=value1';
        $result = UrlUtils::removeQueryParams($url, ['nonexistent']);
        $this->assertEquals($url, $result);

        // Test URL without query string
        $url = 'https://example.com';
        $this->assertEquals($url, UrlUtils::removeQueryParams($url, ['param']));
    }

    /**
     * Test normalizeUrl method
     */
    public function testNormalizeUrl()
    {
        // Test basic normalization
        $this->assertEquals('https://example.com/', UrlUtils::normalizeUrl('https://example.com'));
        $this->assertEquals('https://example.com/path', UrlUtils::normalizeUrl('https://example.com/path'));

        // Test lowercasing domain
        $this->assertEquals('https://example.com/', UrlUtils::normalizeUrl('https://EXAMPLE.COM/'));

        // Test removing default ports
        $this->assertEquals('https://example.com/', UrlUtils::normalizeUrl('https://example.com:443/'));
        $this->assertEquals('http://example.com/', UrlUtils::normalizeUrl('http://example.com:80/'));

        // Test preserving non-default ports
        $this->assertEquals('https://example.com:8080/', UrlUtils::normalizeUrl('https://example.com:8080/'));

        // Test removing duplicate slashes
        $this->assertEquals('https://example.com/path/to/page', UrlUtils::normalizeUrl('https://example.com//path///to//page'));

        // Test removing trailing slash from paths
        $this->assertEquals('https://example.com/path', UrlUtils::normalizeUrl('https://example.com/path/'));

        // Test invalid URL
        $this->assertEquals('', UrlUtils::normalizeUrl('not-a-url'));
        $this->assertEquals('', UrlUtils::normalizeUrl(''));
    }

    /**
     * Test matchesDomainPattern method
     */
    public function testMatchesDomainPattern()
    {
        // Test exact domain match
        $this->assertTrue(UrlUtils::matchesDomainPattern('https://example.com', 'example.com'));

        // Test subdomain wildcard
        $this->assertTrue(UrlUtils::matchesDomainPattern('https://sub.example.com', '*.example.com'));
        $this->assertFalse(UrlUtils::matchesDomainPattern('https://example.com', '*.example.com'));

        // Test case insensitive matching
        $this->assertTrue(UrlUtils::matchesDomainPattern('https://EXAMPLE.COM', 'example.com'));

        // Test different schemes
        $this->assertTrue(UrlUtils::matchesDomainPattern('http://example.com', 'example.com'));
        $this->assertTrue(UrlUtils::matchesDomainPattern('https://example.com', 'example.com'));

        // Test with paths
        $this->assertTrue(UrlUtils::matchesDomainPattern('https://example.com/path', 'example.com'));

        // Test no match
        $this->assertFalse(UrlUtils::matchesDomainPattern('https://different.com', 'example.com'));

        // Test invalid URLs
        $this->assertFalse(UrlUtils::matchesDomainPattern('not-a-url', 'example.com'));
        $this->assertFalse(UrlUtils::matchesDomainPattern('', 'example.com'));
    }
}