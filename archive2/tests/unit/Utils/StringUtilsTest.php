<?php
/**
 * Tests for StringUtils class
 *
 * @package Html_Social_Share_Buttons
 */

use PHPUnit\Framework\TestCase;
use HtmlSocialShare\Utils\StringUtils;

class StringUtilsTest extends TestCase
{
    /**
     * Test truncate method
     */
    public function testTruncate()
    {
        // Test no truncation needed
        $this->assertEquals('Hello', StringUtils::truncate('Hello', 10));

        // Test basic truncation
        $this->assertEquals('Hello...', StringUtils::truncate('Hello World', 8));

        // Test word boundary preservation
        $this->assertEquals('Hello...', StringUtils::truncate('Hello World Test', 10, '...', false));

        // Test word breaking
        $this->assertEquals('Hello Wo...', StringUtils::truncate('Hello World', 11, '...', true));

        // Test custom ellipsis
        $this->assertEquals('Hell [more]', StringUtils::truncate('Hello World', 11, ' [more]'));

        // Test empty string
        $this->assertEquals('', StringUtils::truncate('', 10));

        // Test exact length
        $this->assertEquals('Hello', StringUtils::truncate('Hello', 5));
    }

    /**
     * Test toSlug method
     */
    public function testToSlug()
    {
        // Test basic conversion
        $this->assertEquals('hello-world', StringUtils::toSlug('Hello World'));

        // Test special characters
        $this->assertEquals('hello-world', StringUtils::toSlug('Hello @#$ World'));

        // Test custom separator
        $this->assertEquals('hello_world', StringUtils::toSlug('Hello World', '_'));

        // Test multiple spaces
        $this->assertEquals('hello-world', StringUtils::toSlug('Hello   World'));

        // Test leading/trailing spaces
        $this->assertEquals('hello-world', StringUtils::toSlug('  Hello World  '));

        // Test empty string
        $this->assertEquals('', StringUtils::toSlug(''));

        // Test numbers and valid characters
        $this->assertEquals('test-123-abc', StringUtils::toSlug('Test 123 ABC'));
    }

    /**
     * Test removeAccents method
     */
    public function testRemoveAccents()
    {
        // Test basic accent removal
        $this->assertEquals('aeiouc', StringUtils::removeAccents('àèìòùç'));
        $this->assertEquals('AEIOUC', StringUtils::removeAccents('ÀÈÌÒÙÇ'));

        // Test mixed accents
        $this->assertEquals('Resume', StringUtils::removeAccents('Résumé'));
        $this->assertEquals('naif', StringUtils::removeAccents('naïf'));

        // Test no accents
        $this->assertEquals('Hello World', StringUtils::removeAccents('Hello World'));

        // Test empty string
        $this->assertEquals('', StringUtils::removeAccents(''));
    }

    /**
     * Test toCamelCase method
     */
    public function testToCamelCase()
    {
        // Test basic conversion
        $this->assertEquals('helloWorld', StringUtils::toCamelCase('hello-world'));
        $this->assertEquals('helloWorldTest', StringUtils::toCamelCase('hello-world-test'));

        // Test custom separator
        $this->assertEquals('helloWorld', StringUtils::toCamelCase('hello_world', '_'));

        // Test single word
        $this->assertEquals('hello', StringUtils::toCamelCase('hello'));

        // Test empty string
        $this->assertEquals('', StringUtils::toCamelCase(''));

        // Test multiple separators
        $this->assertEquals('helloWorld', StringUtils::toCamelCase('hello--world'));
    }

    /**
     * Test toPascalCase method
     */
    public function testToPascalCase()
    {
        // Test basic conversion
        $this->assertEquals('HelloWorld', StringUtils::toPascalCase('hello-world'));
        $this->assertEquals('HelloWorldTest', StringUtils::toPascalCase('hello-world-test'));

        // Test custom separator
        $this->assertEquals('HelloWorld', StringUtils::toPascalCase('hello_world', '_'));

        // Test single word
        $this->assertEquals('Hello', StringUtils::toPascalCase('hello'));

        // Test empty string
        $this->assertEquals('', StringUtils::toPascalCase(''));
    }

    /**
     * Test toSnakeCase method
     */
    public function testToSnakeCase()
    {
        // Test basic conversion
        $this->assertEquals('hello_world', StringUtils::toSnakeCase('HelloWorld'));
        $this->assertEquals('hello_world_test', StringUtils::toSnakeCase('HelloWorldTest'));

        // Test camelCase
        $this->assertEquals('hello_world', StringUtils::toSnakeCase('helloWorld'));

        // Test spaces and special chars
        $this->assertEquals('hello_world', StringUtils::toSnakeCase('Hello World'));
        $this->assertEquals('hello_world', StringUtils::toSnakeCase('Hello-World'));

        // Test empty string
        $this->assertEquals('', StringUtils::toSnakeCase(''));
    }

    /**
     * Test extractHashtags method
     */
    public function testExtractHashtags()
    {
        // Test basic hashtag extraction
        $text = 'Hello #world and #test';
        $expected = ['world', 'test'];
        $this->assertEquals($expected, StringUtils::extractHashtags($text));

        // Test no hashtags
        $this->assertEquals([], StringUtils::extractHashtags('Hello world'));

        // Test duplicate hashtags
        $text = 'Hello #world and #world again';
        $expected = ['world'];
        $this->assertEquals($expected, StringUtils::extractHashtags($text));

        // Test hashtags with numbers
        $text = 'Test #html5 and #css3';
        $expected = ['html5', 'css3'];
        $this->assertEquals($expected, StringUtils::extractHashtags($text));

        // Test empty string
        $this->assertEquals([], StringUtils::extractHashtags(''));
    }

    /**
     * Test extractMentions method
     */
    public function testExtractMentions()
    {
        // Test basic mention extraction
        $text = 'Hello @user1 and @user2';
        $expected = ['user1', 'user2'];
        $this->assertEquals($expected, StringUtils::extractMentions($text));

        // Test no mentions
        $this->assertEquals([], StringUtils::extractMentions('Hello world'));

        // Test duplicate mentions
        $text = 'Hello @user1 and @user1 again';
        $expected = ['user1'];
        $this->assertEquals($expected, StringUtils::extractMentions($text));

        // Test mentions with numbers and underscores
        $text = 'Test @user_123 and @test_user';
        $expected = ['user_123', 'test_user'];
        $this->assertEquals($expected, StringUtils::extractMentions($text));

        // Test empty string
        $this->assertEquals([], StringUtils::extractMentions(''));
    }

    /**
     * Test cleanText method
     */
    public function testCleanText()
    {
        // Test basic cleaning
        $text = "  Hello   World  \n\r\t  ";
        $this->assertEquals('Hello World', StringUtils::cleanText($text));

        // Test line break preservation
        $text = "Hello\nWorld\n\nTest";
        $this->assertEquals("Hello\nWorld\n\nTest", StringUtils::cleanText($text, true));
        $this->assertEquals('Hello World Test', StringUtils::cleanText($text, false));

        // Test special characters removal
        $text = "Hello\x00\x01World";
        $this->assertEquals('HelloWorld', StringUtils::cleanText($text));

        // Test empty string
        $this->assertEquals('', StringUtils::cleanText(''));
    }

    /**
     * Test wrapLongWords method
     */
    public function testWrapLongWords()
    {
        // Test basic word wrapping
        $text = 'This is supercalifragilisticexpialidocious word';
        $result = StringUtils::wrapLongWords($text, 10);
        $this->assertStringContainsString("\u{200B}", $result);

        // Test no wrapping needed
        $text = 'Short words only';
        $this->assertEquals($text, StringUtils::wrapLongWords($text, 20));

        // Test custom break character
        $text = 'supercalifragilisticexpialidocious';
        $result = StringUtils::wrapLongWords($text, 10, '-');
        $this->assertStringContainsString('-', $result);

        // Test empty string
        $this->assertEquals('', StringUtils::wrapLongWords(''));
    }

    /**
     * Test wordCount method
     */
    public function testWordCount()
    {
        // Test basic word count
        $this->assertEquals(2, StringUtils::wordCount('Hello World'));
        $this->assertEquals(5, StringUtils::wordCount('This is a test sentence'));

        // Test single word
        $this->assertEquals(1, StringUtils::wordCount('Hello'));

        // Test empty string
        $this->assertEquals(0, StringUtils::wordCount(''));

        // Test multiple spaces
        $this->assertEquals(2, StringUtils::wordCount('Hello    World'));

        // Test with punctuation
        $this->assertEquals(5, StringUtils::wordCount('Hello, world! How are you?'));
    }

    /**
     * Test readingTime method
     */
    public function testReadingTime()
    {
        // Test basic reading time (default 200 WPM)
        $text = str_repeat('word ', 200); // 200 words
        $this->assertEquals(1, StringUtils::readingTime($text));

        $text = str_repeat('word ', 400); // 400 words
        $this->assertEquals(2, StringUtils::readingTime($text));

        // Test custom reading speed
        $text = str_repeat('word ', 100); // 100 words
        $this->assertEquals(1, StringUtils::readingTime($text, 100));

        // Test less than a minute
        $text = str_repeat('word ', 50); // 50 words
        $this->assertEquals(1, StringUtils::readingTime($text)); // Minimum 1 minute

        // Test empty string
        $this->assertEquals(1, StringUtils::readingTime(''));
    }

    /**
     * Test excerpt method
     */
    public function testExcerpt()
    {
        // Test basic excerpt
        $text = str_repeat('word ', 100);
        $result = StringUtils::excerpt($text, 50);
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual(53, strlen($result)); // 50 + '...'

        // Test no truncation needed
        $text = 'Short text';
        $this->assertEquals($text, StringUtils::excerpt($text, 50));

        // Test custom more text
        $text = str_repeat('word ', 100);
        $result = StringUtils::excerpt($text, 50, ' [more]');
        $this->assertStringEndsWith(' [more]', $result);

        // Test empty string
        $this->assertEquals('', StringUtils::excerpt(''));
    }

    /**
     * Test isAscii method
     */
    public function testIsAscii()
    {
        // Test ASCII text
        $this->assertTrue(StringUtils::isAscii('Hello World 123'));
        $this->assertTrue(StringUtils::isAscii('!@#$%^&*()'));

        // Test non-ASCII text
        $this->assertFalse(StringUtils::isAscii('Héllo'));
        $this->assertFalse(StringUtils::isAscii('世界'));

        // Test empty string
        $this->assertTrue(StringUtils::isAscii(''));
    }

    /**
     * Test isValidUtf8 method
     */
    public function testIsValidUtf8()
    {
        // Test valid UTF-8
        $this->assertTrue(StringUtils::isValidUtf8('Hello World'));
        $this->assertTrue(StringUtils::isValidUtf8('Héllo Wörld'));
        $this->assertTrue(StringUtils::isValidUtf8('世界'));

        // Test empty string
        $this->assertTrue(StringUtils::isValidUtf8(''));

        // Test invalid UTF-8 (this would need specific invalid byte sequences)
        // Note: It's hard to create invalid UTF-8 in PHP strings naturally
    }

    /**
     * Test toTitleCase method
     */
    public function testToTitleCase()
    {
        // Test basic title case
        $this->assertEquals('Hello World', StringUtils::toTitleCase('hello world'));
        $this->assertEquals('This Is A Test', StringUtils::toTitleCase('this is a test'));

        // Test mixed case
        $this->assertEquals('Hello World', StringUtils::toTitleCase('HELLO WORLD'));
        $this->assertEquals('Hello World', StringUtils::toTitleCase('hELLO wORLD'));

        // Test with articles (should not be capitalized in middle)
        $this->assertEquals('The Quick Brown Fox', StringUtils::toTitleCase('the quick brown fox'));

        // Test empty string
        $this->assertEquals('', StringUtils::toTitleCase(''));
    }

    /**
     * Test parseTemplate method
     */
    public function testParseTemplate()
    {
        // Test basic template parsing
        $template = 'Hello {{name}}, welcome to {{site}}!';
        $variables = ['name' => 'John', 'site' => 'Example'];
        $expected = 'Hello John, welcome to Example!';
        $this->assertEquals($expected, StringUtils::parseTemplate($template, $variables));

        // Test missing variables
        $template = 'Hello {{name}}, welcome to {{missing}}!';
        $variables = ['name' => 'John'];
        $expected = 'Hello John, welcome to {{missing}}!';
        $this->assertEquals($expected, StringUtils::parseTemplate($template, $variables));

        // Test custom delimiters
        $template = 'Hello {name}, welcome to {site}!';
        $variables = ['name' => 'John', 'site' => 'Example'];
        $expected = 'Hello John, welcome to Example!';
        $this->assertEquals($expected, StringUtils::parseTemplate($template, $variables, '{', '}'));

        // Test empty template
        $this->assertEquals('', StringUtils::parseTemplate('', []));
    }

    /**
     * Test extractTemplateVariables method
     */
    public function testExtractTemplateVariables()
    {
        // Test basic variable extraction
        $template = 'Hello {{name}}, welcome to {{site}}!';
        $expected = ['name', 'site'];
        $this->assertEquals($expected, StringUtils::extractTemplateVariables($template));

        // Test duplicate variables
        $template = 'Hello {{name}}, {{name}} welcome to {{site}}!';
        $expected = ['name', 'site'];
        $this->assertEquals($expected, StringUtils::extractTemplateVariables($template));

        // Test custom delimiters
        $template = 'Hello {name}, welcome to {site}!';
        $expected = ['name', 'site'];
        $this->assertEquals($expected, StringUtils::extractTemplateVariables($template, '{', '}'));

        // Test no variables
        $this->assertEquals([], StringUtils::extractTemplateVariables('Hello World'));

        // Test empty template
        $this->assertEquals([], StringUtils::extractTemplateVariables(''));
    }

    /**
     * Test maskSensitive method
     */
    public function testMaskSensitive()
    {
        // Test basic masking
        $text = 'My email is john@example.com';
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        $expected = 'My email is ***';
        $this->assertEquals($expected, StringUtils::maskSensitive($text, $pattern));

        // Test custom replacement
        $text = 'Credit card: 1234-5678-9012-3456';
        $pattern = '/\d{4}-\d{4}-\d{4}-\d{4}/';
        $expected = 'Credit card: [REDACTED]';
        $this->assertEquals($expected, StringUtils::maskSensitive($text, $pattern, '[REDACTED]'));

        // Test no matches
        $text = 'No sensitive data here';
        $pattern = '/password/i';
        $this->assertEquals($text, StringUtils::maskSensitive($text, $pattern));

        // Test empty string
        $this->assertEquals('', StringUtils::maskSensitive('', '/test/'));
    }

    /**
     * Test random method
     */
    public function testRandom()
    {
        // Test default length
        $result = StringUtils::random();
        $this->assertEquals(10, strlen($result));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $result);

        // Test custom length
        $result = StringUtils::random(5);
        $this->assertEquals(5, strlen($result));

        // Test custom character set
        $result = StringUtils::random(10, 'abc');
        $this->assertEquals(10, strlen($result));
        $this->assertMatchesRegularExpression('/^[abc]+$/', $result);

        // Test uniqueness
        $result1 = StringUtils::random();
        $result2 = StringUtils::random();
        $this->assertNotEquals($result1, $result2);

        // Test zero length
        $this->assertEquals('', StringUtils::random(0));
    }

    /**
     * Test startsWith method
     */
    public function testStartsWith()
    {
        // Test case sensitive
        $this->assertTrue(StringUtils::startsWith('Hello World', 'Hello'));
        $this->assertFalse(StringUtils::startsWith('Hello World', 'hello'));

        // Test case insensitive
        $this->assertTrue(StringUtils::startsWith('Hello World', 'hello', false));
        $this->assertTrue(StringUtils::startsWith('HELLO WORLD', 'hello', false));

        // Test empty prefix
        $this->assertTrue(StringUtils::startsWith('Hello World', ''));

        // Test prefix longer than text
        $this->assertFalse(StringUtils::startsWith('Hi', 'Hello'));

        // Test exact match
        $this->assertTrue(StringUtils::startsWith('Hello', 'Hello'));

        // Test empty text
        $this->assertFalse(StringUtils::startsWith('', 'test'));
        $this->assertTrue(StringUtils::startsWith('', ''));
    }
}