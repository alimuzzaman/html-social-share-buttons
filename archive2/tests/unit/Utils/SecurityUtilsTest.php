<?php
/**
 * Tests for SecurityUtils class
 *
 * @package Html_Social_Share_Buttons
 */

use PHPUnit\Framework\TestCase;
use HtmlSocialShare\Utils\SecurityUtils;

class SecurityUtilsTest extends TestCase
{
    /**
     * Test sanitizeTextField method
     */
    public function testSanitizeTextField()
    {
        // Test basic sanitization
        $this->assertEquals('Hello World', SecurityUtils::sanitizeTextField('Hello World'));

        // Test HTML tag removal
        $this->assertEquals('Hello World', SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World'));

        // Test null byte removal
        $this->assertEquals('Hello World', SecurityUtils::sanitizeTextField("Hello\x00World"));

        // Test control character removal
        $this->assertEquals('Hello World', SecurityUtils::sanitizeTextField("Hello\x01\x02World"));

        // Test whitespace normalization
        $this->assertEquals('Hello World', SecurityUtils::sanitizeTextField("Hello\r\n\t   World"));

        // Test empty string
        $this->assertEquals('', SecurityUtils::sanitizeTextField(''));

        // Test only whitespace
        $this->assertEquals('', SecurityUtils::sanitizeTextField("   \r\n\t   "));
    }

    /**
     * Test sanitizeKey method
     */
    public function testSanitizeKey()
    {
        // Test basic key sanitization
        $this->assertEquals('hello-world', SecurityUtils::sanitizeKey('Hello-World'));

        // Test special character removal
        $this->assertEquals('hello_world', SecurityUtils::sanitizeKey('Hello@#$%World'));

        // Test trimming of dashes and underscores
        $this->assertEquals('hello', SecurityUtils::sanitizeKey('_-hello-_'));

        // Test empty result
        $this->assertEquals('', SecurityUtils::sanitizeKey('@#$%'));

        // Test valid characters preservation
        $this->assertEquals('test_key-123', SecurityUtils::sanitizeKey('test_key-123'));
    }

    /**
     * Test sanitizeHtmlClass method
     */
    public function testSanitizeHtmlClass()
    {
        // Test basic class sanitization
        $this->assertEquals('valid-class', SecurityUtils::sanitizeHtmlClass('valid-class'));

        // Test special character removal
        $this->assertEquals('class-name', SecurityUtils::sanitizeHtmlClass('class@#$name'));

        // Test space handling
        $this->assertEquals('multiple classes', SecurityUtils::sanitizeHtmlClass('multiple   classes'));

        // Test empty result
        $this->assertEquals('', SecurityUtils::sanitizeHtmlClass('@#$%'));
    }

    /**
     * Test escapeAttribute method
     */
    public function testEscapeAttribute()
    {
        // Test basic escaping
        $this->assertEquals('Hello World', SecurityUtils::escapeAttribute('Hello World'));

        // Test quote escaping
        $this->assertEquals('Hello &quot;World&quot;', SecurityUtils::escapeAttribute('Hello "World"'));

        // Test ampersand escaping
        $this->assertEquals('Hello &amp; World', SecurityUtils::escapeAttribute('Hello & World'));

        // Test less than escaping
        $this->assertEquals('Hello &lt; World', SecurityUtils::escapeAttribute('Hello < World'));

        // Test greater than escaping
        $this->assertEquals('Hello &gt; World', SecurityUtils::escapeAttribute('Hello > World'));
    }

    /**
     * Test escapeHtml method
     */
    public function testEscapeHtml()
    {
        // Test basic HTML escaping
        $this->assertEquals('Hello World', SecurityUtils::escapeHtml('Hello World'));

        // Test script tag escaping
        $this->assertEquals('&lt;script&gt;alert(\'xss\')&lt;/script&gt;', SecurityUtils::escapeHtml('<script>alert(\'xss\')</script>'));

        // Test quote and ampersand escaping
        $this->assertEquals('Hello &quot; &amp; World', SecurityUtils::escapeHtml('Hello " & World'));
    }

    /**
     * Test sanitizeUrl method
     */
    public function testSanitizeUrl()
    {
        // Test valid HTTP URL
        $this->assertEquals('https://example.com', SecurityUtils::sanitizeUrl('https://example.com'));

        // Test valid HTTPS URL
        $this->assertEquals('http://example.com', SecurityUtils::sanitizeUrl('http://example.com'));

        // Test javascript protocol rejection
        $this->assertEquals('', SecurityUtils::sanitizeUrl('javascript:alert("xss")'));

        // Test data protocol rejection
        $this->assertEquals('', SecurityUtils::sanitizeUrl('data:text/html,<script>alert("xss")</script>'));

        // Test vbscript protocol rejection
        $this->assertEquals('', SecurityUtils::sanitizeUrl('vbscript:msgbox("xss")'));

        // Test malformed URL
        $this->assertEquals('', SecurityUtils::sanitizeUrl('not-a-url'));

        // Test empty URL
        $this->assertEquals('', SecurityUtils::sanitizeUrl(''));
    }

    /**
     * Test isValidEmail method
     */
    public function testIsValidEmail()
    {
        // Test valid emails
        $this->assertTrue(SecurityUtils::isValidEmail('test@example.com'));
        $this->assertTrue(SecurityUtils::isValidEmail('user.name@domain.co.uk'));
        $this->assertTrue(SecurityUtils::isValidEmail('123@example.org'));

        // Test invalid emails
        $this->assertFalse(SecurityUtils::isValidEmail('invalid-email'));
        $this->assertFalse(SecurityUtils::isValidEmail('test@'));
        $this->assertFalse(SecurityUtils::isValidEmail('@example.com'));
        $this->assertFalse(SecurityUtils::isValidEmail(''));
        $this->assertFalse(SecurityUtils::isValidEmail('test space@example.com'));
    }

    /**
     * Test isAlphanumeric method
     */
    public function testIsAlphanumeric()
    {
        // Test basic alphanumeric
        $this->assertTrue(SecurityUtils::isAlphanumeric('abc123'));
        $this->assertTrue(SecurityUtils::isAlphanumeric('ABC123'));

        // Test with underscores
        $this->assertTrue(SecurityUtils::isAlphanumeric('abc_123', true));
        $this->assertFalse(SecurityUtils::isAlphanumeric('abc_123', false));

        // Test with dashes
        $this->assertTrue(SecurityUtils::isAlphanumeric('abc-123', false, true));
        $this->assertFalse(SecurityUtils::isAlphanumeric('abc-123', false, false));

        // Test invalid characters
        $this->assertFalse(SecurityUtils::isAlphanumeric('abc@123'));
        $this->assertFalse(SecurityUtils::isAlphanumeric('abc 123'));

        // Test empty string
        $this->assertFalse(SecurityUtils::isAlphanumeric(''));
    }

    /**
     * Test isValidToken method
     */
    public function testIsValidToken()
    {
        // Test valid tokens
        $this->assertTrue(SecurityUtils::isValidToken('abcdef123456'));
        $this->assertTrue(SecurityUtils::isValidToken('ABCDEF123456'));

        // Test minimum length
        $this->assertFalse(SecurityUtils::isValidToken('short'));
        $this->assertTrue(SecurityUtils::isValidToken('longenough', 8));

        // Test maximum length
        $longToken = str_repeat('a', 65);
        $this->assertFalse(SecurityUtils::isValidToken($longToken));

        // Test invalid characters
        $this->assertFalse(SecurityUtils::isValidToken('token@#$'));
        $this->assertFalse(SecurityUtils::isValidToken('token with spaces'));

        // Test empty token
        $this->assertFalse(SecurityUtils::isValidToken(''));
    }

    /**
     * Test generateRandomString method
     */
    public function testGenerateRandomString()
    {
        // Test default length
        $result = SecurityUtils::generateRandomString();
        $this->assertEquals(32, strlen($result));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $result);

        // Test custom length
        $result = SecurityUtils::generateRandomString(16);
        $this->assertEquals(16, strlen($result));

        // Test custom character set
        $result = SecurityUtils::generateRandomString(10, 'abc');
        $this->assertEquals(10, strlen($result));
        $this->assertMatchesRegularExpression('/^[abc]+$/', $result);

        // Test uniqueness (very low probability of collision)
        $result1 = SecurityUtils::generateRandomString();
        $result2 = SecurityUtils::generateRandomString();
        $this->assertNotEquals($result1, $result2);
    }

    /**
     * Test hashData method
     */
    public function testHashData()
    {
        // Test basic hashing
        $result = SecurityUtils::hashData('test data');
        $this->assertEquals(64, strlen($result)); // SHA-256 hex length
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $result);

        // Test with salt
        $result1 = SecurityUtils::hashData('test data', 'salt1');
        $result2 = SecurityUtils::hashData('test data', 'salt2');
        $this->assertNotEquals($result1, $result2);

        // Test consistency
        $result1 = SecurityUtils::hashData('test data', 'salt');
        $result2 = SecurityUtils::hashData('test data', 'salt');
        $this->assertEquals($result1, $result2);
    }

    /**
     * Test hasXssPatterns method
     */
    public function testHasXssPatterns()
    {
        // Test clean input
        $this->assertFalse(SecurityUtils::hasXssPatterns('Hello World'));

        // Test script tags
        $this->assertTrue(SecurityUtils::hasXssPatterns('<script>alert("xss")</script>'));
        $this->assertTrue(SecurityUtils::hasXssPatterns('<SCRIPT>alert("xss")</SCRIPT>'));

        // Test javascript protocol
        $this->assertTrue(SecurityUtils::hasXssPatterns('javascript:alert("xss")'));
        $this->assertTrue(SecurityUtils::hasXssPatterns('JAVASCRIPT:alert("xss")'));

        // Test on* event handlers
        $this->assertTrue(SecurityUtils::hasXssPatterns('onclick="alert(\'xss\')"'));
        $this->assertTrue(SecurityUtils::hasXssPatterns('onload="alert(\'xss\')"'));

        // Test data protocol
        $this->assertTrue(SecurityUtils::hasXssPatterns('data:text/html,<script>alert("xss")</script>'));
    }

    /**
     * Test hasSqlInjectionPatterns method
     */
    public function testHasSqlInjectionPatterns()
    {
        // Test clean input
        $this->assertFalse(SecurityUtils::hasSqlInjectionPatterns('Hello World'));

        // Test SQL injection patterns
        $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns("'; DROP TABLE users; --"));
        $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns('1 OR 1=1'));
        $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns('UNION SELECT'));
        $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns('/* comment */'));

        // Test case insensitive
        $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns('union select'));
        $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns('UNION SELECT'));
    }

    /**
     * Test isAllowedFileExtension method
     */
    public function testIsAllowedFileExtension()
    {
        $allowedExtensions = ['jpg', 'png', 'gif', 'svg'];

        // Test allowed extensions
        $this->assertTrue(SecurityUtils::isAllowedFileExtension('image.jpg', $allowedExtensions));
        $this->assertTrue(SecurityUtils::isAllowedFileExtension('image.PNG', $allowedExtensions));

        // Test disallowed extensions
        $this->assertFalse(SecurityUtils::isAllowedFileExtension('script.php', $allowedExtensions));
        $this->assertFalse(SecurityUtils::isAllowedFileExtension('document.pdf', $allowedExtensions));

        // Test no extension
        $this->assertFalse(SecurityUtils::isAllowedFileExtension('filename', $allowedExtensions));

        // Test empty filename
        $this->assertFalse(SecurityUtils::isAllowedFileExtension('', $allowedExtensions));
    }

    /**
     * Test sanitizeFilename method
     */
    public function testSanitizeFilename()
    {
        // Test basic filename
        $this->assertEquals('document.txt', SecurityUtils::sanitizeFilename('document.txt'));

        // Test dangerous characters removal
        $this->assertEquals('document.txt', SecurityUtils::sanitizeFilename('doc../ument.txt'));
        $this->assertEquals('document.txt', SecurityUtils::sanitizeFilename('doc\\ument.txt'));

        // Test path traversal prevention
        $this->assertEquals('document.txt', SecurityUtils::sanitizeFilename('../../../document.txt'));
        $this->assertEquals('document.txt', SecurityUtils::sanitizeFilename('..\\..\\document.txt'));

        // Test reserved names
        $this->assertEquals('_CON.txt', SecurityUtils::sanitizeFilename('CON.txt'));
        $this->assertEquals('_AUX.txt', SecurityUtils::sanitizeFilename('AUX.txt'));

        // Test length limit
        $longName = str_repeat('a', 300) . '.txt';
        $result = SecurityUtils::sanitizeFilename($longName);
        $this->assertLessThanOrEqual(255, strlen($result));
    }

    /**
     * Test isPrivateIp method
     */
    public function testIsPrivateIp()
    {
        // Test private IP ranges
        $this->assertTrue(SecurityUtils::isPrivateIp('192.168.1.1'));
        $this->assertTrue(SecurityUtils::isPrivateIp('10.0.0.1'));
        $this->assertTrue(SecurityUtils::isPrivateIp('172.16.0.1'));
        $this->assertTrue(SecurityUtils::isPrivateIp('127.0.0.1'));

        // Test public IPs
        $this->assertFalse(SecurityUtils::isPrivateIp('8.8.8.8'));
        $this->assertFalse(SecurityUtils::isPrivateIp('1.1.1.1'));

        // Test invalid IPs
        $this->assertFalse(SecurityUtils::isPrivateIp('not-an-ip'));
        $this->assertFalse(SecurityUtils::isPrivateIp(''));
    }

    /**
     * Test checkRateLimit method
     */
    public function testCheckRateLimit()
    {
        $currentTime = time();

        // Test no previous attempts
        $result = SecurityUtils::checkRateLimit([], 3, 60, $currentTime);
        $this->assertFalse($result['exceeded']);
        $this->assertEquals(1, count($result['attempts']));

        // Test within limit
        $attempts = [$currentTime - 30, $currentTime - 20];
        $result = SecurityUtils::checkRateLimit($attempts, 3, 60, $currentTime);
        $this->assertFalse($result['exceeded']);
        $this->assertEquals(3, count($result['attempts']));

        // Test exceeding limit
        $attempts = [$currentTime - 30, $currentTime - 20, $currentTime - 10];
        $result = SecurityUtils::checkRateLimit($attempts, 3, 60, $currentTime);
        $this->assertTrue($result['exceeded']);

        // Test old attempts cleanup
        $attempts = [$currentTime - 120, $currentTime - 30];
        $result = SecurityUtils::checkRateLimit($attempts, 3, 60, $currentTime);
        $this->assertFalse($result['exceeded']);
        $this->assertEquals(2, count($result['attempts'])); // Old attempt removed
    }

    /**
     * Test isValidCsrfTokenFormat method
     */
    public function testIsValidCsrfTokenFormat()
    {
        // Test valid token format
        $this->assertTrue(SecurityUtils::isValidCsrfTokenFormat('abcdef123456789'));

        // Test minimum length
        $this->assertFalse(SecurityUtils::isValidCsrfTokenFormat('short'));

        // Test maximum length
        $longToken = str_repeat('a', 129);
        $this->assertFalse(SecurityUtils::isValidCsrfTokenFormat($longToken));

        // Test invalid characters
        $this->assertFalse(SecurityUtils::isValidCsrfTokenFormat('token@#$'));
        $this->assertFalse(SecurityUtils::isValidCsrfTokenFormat('token with spaces'));

        // Test empty token
        $this->assertFalse(SecurityUtils::isValidCsrfTokenFormat(''));
    }

    /**
     * Test stripDangerousHtml method
     */
    public function testStripDangerousHtml()
    {
        // Test allowed tags preservation
        $input = '<p>Hello <strong>World</strong></p>';
        $this->assertEquals($input, SecurityUtils::stripDangerousHtml($input));

        // Test dangerous tag removal
        $input = '<p>Hello <script>alert("xss")</script> World</p>';
        $expected = '<p>Hello  World</p>';
        $this->assertEquals($expected, SecurityUtils::stripDangerousHtml($input));

        // Test custom allowed tags
        $input = '<p>Hello <span>World</span></p>';
        $expected = '<p>Hello World</p>';
        $this->assertEquals($expected, SecurityUtils::stripDangerousHtml($input, ['p']));

        // Test empty input
        $this->assertEquals('', SecurityUtils::stripDangerousHtml(''));
    }
}