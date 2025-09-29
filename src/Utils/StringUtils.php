<?php
namespace HtmlSocialShare\Utils;

/**
 * Pure string and text processing functions
 * 
 * This class contains only pure functions for string manipulation,
 * text processing, and formatting operations without side effects.
 * 
 * @since 3.0.0
 */
class StringUtils
{
    /**
     * Truncate string to specified length with ellipsis
     * 
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $ellipsis Ellipsis string
     * @param bool $breakWords Whether to break words
     * @return string Truncated text
     */
    public static function truncate(string $text, int $length, string $ellipsis = '...', bool $breakWords = false): string
    {
        if (empty($text) || $length <= 0) {
            return '';
        }
        
        // If text is shorter than target length, return as-is
        if (mb_strlen($text) < $length) {
            return $text;
        }
        
        // If text length equals target and no ellipsis needed
        if (mb_strlen($text) == $length && empty($ellipsis)) {
            return $text;
        }
        
        // Calculate available space for text after ellipsis
        $availableLength = $length - mb_strlen($ellipsis);
        
        // If no space available for text, return ellipsis or empty
        if ($availableLength <= 0) {
            return $availableLength == 0 ? $ellipsis : '';
        }
        
        $truncated = mb_substr($text, 0, $availableLength);
        
        if (!$breakWords) {
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }
        }
        
        return $truncated . $ellipsis;
    }

    /**
     * Convert string to slug format
     * 
     * @param string $text Text to convert
     * @param string $separator Separator character
     * @return string Slug format string
     */
    public static function toSlug(string $text, string $separator = '-'): string
    {
        // Convert to lowercase
        $text = mb_strtolower($text);
        
        // Remove accents and special characters
        $text = self::removeAccents($text);
        
        // Replace spaces and special characters with separator
        $text = preg_replace('/[^a-z0-9]+/', $separator, $text);
        
        // Remove leading/trailing separators
        return trim($text, $separator);
    }

    /**
     * Remove accents from text
     * 
     * @param string $text Text with accents
     * @return string Text without accents
     */
    public static function removeAccents(string $text): string
    {
        $accents = [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ý' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
            'Ñ' => 'N', 'ñ' => 'n',
            'Ç' => 'C', 'ç' => 'c'
        ];
        
        return strtr($text, $accents);
    }

    /**
     * Convert string to camelCase
     * 
     * @param string $text Text to convert
     * @param string $separator Current separator
     * @return string CamelCase string
     */
    public static function toCamelCase(string $text, string $separator = '-'): string
    {
        $words = explode($separator, $text);
        $result = array_shift($words); // First word stays lowercase
        
        foreach ($words as $word) {
            $result .= ucfirst(strtolower($word));
        }
        
        return $result;
    }

    /**
     * Convert string to PascalCase
     * 
     * @param string $text Text to convert
     * @param string $separator Current separator
     * @return string PascalCase string
     */
    public static function toPascalCase(string $text, string $separator = '-'): string
    {
        $words = explode($separator, $text);
        $result = '';
        
        foreach ($words as $word) {
            $result .= ucfirst(strtolower($word));
        }
        
        return $result;
    }

    /**
     * Convert camelCase/PascalCase to snake_case
     * 
     * @param string $text Text to convert
     * @return string Snake case string
     */
    public static function toSnakeCase(string $text): string
    {
        // Convert spaces to underscores first
        $text = str_replace(' ', '_', $text);
        // Convert camelCase to snake_case
        $result = preg_replace('/([A-Z])/', '_$1', $text);
        $result = strtolower(ltrim($result, '_'));
        // Remove multiple underscores
        return preg_replace('/_+/', '_', $result);
    }

    /**
     * Extract hashtags from text
     * 
     * @param string $text Text containing hashtags
     * @return array Array of hashtags (without #)
     */
    public static function extractHashtags(string $text): array
    {
        $pattern = '/#([a-zA-Z0-9_]+)/';
        $matches = [];
        preg_match_all($pattern, $text, $matches);
        // Return unique hashtags only
        return array_unique($matches[1] ?? []);
    }

    /**
     * Extract mentions from text
     * 
     * @param string $text Text containing mentions
     * @return array Array of mentions (without @)
     */
    public static function extractMentions(string $text): array
    {
        $pattern = '/@([a-zA-Z0-9_]+)/';
        $matches = [];
        preg_match_all($pattern, $text, $matches);
        return $matches[1] ?? [];
    }

    /**
     * Clean text by removing extra whitespace and line breaks
     * 
     * @param string $text Text to clean
     * @param bool $preserveLineBreaks Whether to preserve line breaks
     * @return string Cleaned text
     */
    public static function cleanText(string $text, bool $preserveLineBreaks = false): string
    {
        // Remove control characters first
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
        
        if ($preserveLineBreaks) {
            // Replace multiple spaces with single space, but preserve line breaks
            $text = preg_replace('/[ \t]+/', ' ', $text);
            $text = preg_replace('/\n[ \t]*/', "\n", $text);
            $text = preg_replace('/\n{3,}/', "\n\n", $text);
        } else {
            // Replace all whitespace with single spaces
            $text = preg_replace('/\s+/', ' ', $text);
        }
        
        return trim($text);
    }

    /**
     * Wrap long words in text
     * 
     * @param string $text Text to wrap
     * @param int $maxWordLength Maximum word length
     * @param string $break Break character
     * @return string Text with long words wrapped
     */
    public static function wrapLongWords(string $text, int $maxWordLength = 20, string $break = "\u{200B}"): string
    {
        $words = explode(' ', $text);
        $result = [];
        
        foreach ($words as $word) {
            if (mb_strlen($word) > $maxWordLength) {
                $wrapped = '';
                $length = mb_strlen($word);
                for ($i = 0; $i < $length; $i += $maxWordLength) {
                    if ($i > 0) {
                        $wrapped .= $break;
                    }
                    $wrapped .= mb_substr($word, $i, $maxWordLength);
                }
                $result[] = $wrapped;
            } else {
                $result[] = $word;
            }
        }
        
        return implode(' ', $result);
    }

    /**
     * Count words in text
     * 
     * @param string $text Text to count
     * @return int Word count
     */
    public static function wordCount(string $text): int
    {
        $text = self::cleanText($text);
        if (empty($text)) {
            return 0;
        }
        return count(explode(' ', $text));
    }

    /**
     * Calculate reading time estimate
     * 
     * @param string $text Text to analyze
     * @param int $wordsPerMinute Average reading speed
     * @return int Reading time in minutes
     */
    public static function readingTime(string $text, int $wordsPerMinute = 200): int
    {
        $wordCount = self::wordCount($text);
        return max(1, (int) ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Generate excerpt from text
     * 
     * @param string $text Full text
     * @param int $length Maximum length in characters
     * @param string $more More text indicator
     * @return string Excerpt
     */
    public static function excerpt(string $text, int $length = 150, string $more = '...'): string
    {
        $text = self::cleanText($text);
        return self::truncate($text, $length, $more, false);
    }

    /**
     * Check if string contains only ASCII characters
     * 
     * @param string $text Text to check
     * @return bool True if ASCII only
     */
    public static function isAscii(string $text): bool
    {
        return mb_check_encoding($text, 'ASCII');
    }

    /**
     * Check if string is valid UTF-8
     * 
     * @param string $text Text to check
     * @return bool True if valid UTF-8
     */
    public static function isValidUtf8(string $text): bool
    {
        return mb_check_encoding($text, 'UTF-8');
    }

    /**
     * Convert string to title case
     * 
     * @param string $text Text to convert
     * @return string Title case text
     */
    public static function toTitleCase(string $text): string
    {
        // Simple title case - capitalize first letter of each word
        return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Parse template variables in text
     * 
     * @param string $template Template text with variables
     * @param array $variables Variable values
     * @param string $prefix Variable prefix (e.g., '{{')
     * @param string $suffix Variable suffix (e.g., '}}')
     * @return string Parsed text
     */
    public static function parseTemplate(string $template, array $variables, string $prefix = '{{', string $suffix = '}}'): string
    {
        foreach ($variables as $key => $value) {
            $placeholder = $prefix . $key . $suffix;
            $template = str_replace($placeholder, (string) $value, $template);
        }
        
        return $template;
    }

    /**
     * Extract template variables from text
     * 
     * @param string $template Template text
     * @param string $prefix Variable prefix
     * @param string $suffix Variable suffix
     * @return array Variable names found
     */
    public static function extractTemplateVariables(string $template, string $prefix = '{{', string $suffix = '}}'): array
    {
        $escapedPrefix = preg_quote($prefix, '/');
        $escapedSuffix = preg_quote($suffix, '/');
        $pattern = '/' . $escapedPrefix . '([a-zA-Z0-9_]+)' . $escapedSuffix . '/';
        
        $matches = [];
        preg_match_all($pattern, $template, $matches);
        
        return array_unique($matches[1] ?? []);
    }

    /**
     * Mask sensitive information in string
     * 
     * @param string $text Text to mask
     * @param string $pattern Pattern to match (regex)
     * @param string $replacement Replacement pattern
     * @return string Masked text
     */
    public static function maskSensitive(string $text, string $pattern, string $replacement = '***'): string
    {
        return preg_replace($pattern, $replacement, $text);
    }

    /**
     * Generate random string
     * 
     * @param int $length String length
     * @param string $chars Character set
     * @return string Random string
     */
    public static function random(int $length = 10, string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
    {
        $result = '';
        $charsLength = strlen($chars);
        
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $charsLength - 1)];
        }
        
        return $result;
    }

    /**
     * Check if string starts with prefix
     * 
     * @param string $text Text to check
     * @param string $prefix Prefix to check for
     * @param bool $caseSensitive Whether comparison is case sensitive
     * @return bool True if starts with prefix
     */
    public static function startsWith(string $text, string $prefix, bool $caseSensitive = true): bool
    {
        if (!$caseSensitive) {
            $text = mb_strtolower($text);
            $prefix = mb_strtolower($prefix);
        }
        
        return mb_substr($text, 0, mb_strlen($prefix)) === $prefix;
    }

    /**
     * Check if string ends with suffix
     * 
     * @param string $text Text to check
     * @param string $suffix Suffix to check for
     * @param bool $caseSensitive Whether comparison is case sensitive
     * @return bool True if ends with suffix
     */
    public static function endsWith(string $text, string $suffix, bool $caseSensitive = true): bool
    {
        if (!$caseSensitive) {
            $text = mb_strtolower($text);
            $suffix = mb_strtolower($suffix);
        }
        
        return mb_substr($text, -mb_strlen($suffix)) === $suffix;
    }

    /**
     * Check if string contains substring
     * 
     * @param string $text Text to check
     * @param string $substring Substring to look for
     * @param bool $caseSensitive Whether comparison is case sensitive
     * @return bool True if contains substring
     */
    public static function contains(string $text, string $substring, bool $caseSensitive = true): bool
    {
        if (!$caseSensitive) {
            $text = mb_strtolower($text);
            $substring = mb_strtolower($substring);
        }
        
        return mb_strpos($text, $substring) !== false;
    }

    /**
     * Split string by delimiter with limit and trim
     * 
     * @param string $text Text to split
     * @param string $delimiter Delimiter to split by
     * @param int $limit Maximum parts (0 = no limit)
     * @param bool $trimParts Whether to trim each part
     * @return array Array of parts
     */
    public static function smartSplit(string $text, string $delimiter = ',', int $limit = 0, bool $trimParts = true): array
    {
        if ($limit > 0) {
            $parts = explode($delimiter, $text, $limit);
        } else {
            $parts = explode($delimiter, $text);
        }
        
        if ($trimParts) {
            $parts = array_map('trim', $parts);
        }
        
        return array_filter($parts, function($part) {
            return $part !== '';
        });
    }

    /**
     * Pad string to specified length with character
     * 
     * @param string $text Text to pad
     * @param int $length Target length
     * @param string $char Padding character
     * @param string $side Side to pad ('left', 'right', 'both')
     * @return string Padded string
     */
    public static function pad(string $text, int $length, string $char = ' ', string $side = 'right'): string
    {
        $currentLength = mb_strlen($text);
        if ($currentLength >= $length) {
            return $text;
        }
        
        $padLength = $length - $currentLength;
        
        switch ($side) {
            case 'left':
                return str_repeat($char, $padLength) . $text;
            case 'both':
                $leftPad = floor($padLength / 2);
                $rightPad = $padLength - $leftPad;
                return str_repeat($char, $leftPad) . $text . str_repeat($char, $rightPad);
            case 'right':
            default:
                return $text . str_repeat($char, $padLength);
        }
    }
}