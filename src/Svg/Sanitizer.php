<?php
namespace HtmlSocialShare\Svg;

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use Exception;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Enhanced SVG Sanitizer with comprehensive security validation.
 * 
 * Provides robust SVG sanitization with:
 * - XSS protection through element and attribute filtering
 * - External reference validation and blocking
 * - Malicious pattern detection
 * - Size and complexity limits
 * - DOM-based parsing for accuracy
 * - Detailed error reporting and logging
 */
class Sanitizer implements SanitizerInterface
{
    /** @var array<string> Allowed SVG elements */
    private const ALLOWED_ELEMENTS = [
        'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'defs', 'linearGradient', 'radialGradient', 'stop', 'pattern',
        'mask', 'clipPath', 'marker', 'symbol', 'use', 'image'
    ];
    
    /** @var array<string> Allowed SVG attributes */
    private const ALLOWED_ATTRIBUTES = [
        'id', 'class', 'x', 'y', 'width', 'height', 'viewBox', 'xmlns', 'xmlns:xlink',
        'd', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin',
        'opacity', 'transform', 'cx', 'cy', 'r', 'rx', 'ry', 'x1', 'y1', 'x2', 'y2',
        'points', 'gradientUnits', 'offset', 'stop-color', 'stop-opacity',
        'preserveAspectRatio', 'style'
    ];
    
    /** @var array<string> Dangerous patterns to detect */
    private const DANGEROUS_PATTERNS = [
        '/javascript:/i',
        '/data:text\/html/i',
        '/vbscript:/i',
        '/<script/i',
        '/on\w+\s*=/i',
        '/expression\s*\(/i',
        '/url\s*\([^)]*javascript:/i',
        '/import\s/i',
        '/@import/i'
    ];
    
    /** @var array Configuration options */
    private array $config;
    
    /** @var int Maximum SVG file size in bytes */
    private const MAX_SVG_SIZE = 51200; // 50KB
    
    /** @var int Maximum number of elements */
    private const MAX_ELEMENTS = 1000;

    /**
     * Initialize SVG sanitizer with configuration.
     *
     * @param array $config Sanitizer configuration options
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'allow_external_refs' => false,
            'max_size' => self::MAX_SVG_SIZE,
            'max_elements' => self::MAX_ELEMENTS,
            'strict_mode' => true,
            'log_violations' => true,
        ], $config);
    }

    /**
     * Sanitize SVG content with comprehensive security validation.
     *
     * @param string $svg Raw SVG content
     * @return string Sanitized SVG content
     * @throws Exception If sanitization fails or SVG is malicious
     */
    public function sanitize(string $svg): string
    {
        try {
            // Initial validation
            if (!$this->isValidSvgInput($svg)) {
                throw new Exception('Invalid SVG input provided');
            }
            
            // Size validation
            if (strlen($svg) > $this->config['max_size']) {
                throw new Exception('SVG size exceeds maximum allowed size');
            }
            
            // Detect dangerous patterns
            $dangerousPatterns = $this->detectDangerousPatterns($svg);
            if (!empty($dangerousPatterns)) {
                if ($this->config['log_violations']) {
                    error_log("HSS SVG: Dangerous patterns detected: " . implode(', ', $dangerousPatterns));
                }
                throw new Exception('SVG contains dangerous patterns: ' . implode(', ', $dangerousPatterns));
            }
            
            // Use DOM parsing for accurate sanitization
            $sanitized = $this->sanitizeWithDom($svg);
            
            // Final validation
            if (empty($sanitized)) {
                throw new Exception('SVG sanitization resulted in empty content');
            }
            
            return $sanitized;
            
        } catch (Exception $e) {
            if ($this->config['log_violations']) {
                error_log("HSS SVG: Sanitization failed: {$e->getMessage()}");
            }
            throw $e;
        }
    }
    
    /**
     * Validate SVG using W3C standards compliance.
     *
     * @param string $svg SVG content to validate
     * @return array Validation result with success flag and errors
     */
    public function validate(string $svg): array
    {
        $errors = [];
        
        try {
            // Basic structure validation
            if (!$this->isValidSvgInput($svg)) {
                $errors[] = 'Invalid SVG structure';
            }
            
            // Size validation
            if (strlen($svg) > $this->config['max_size']) {
                $errors[] = 'SVG size exceeds maximum allowed size';
            }
            
            // Pattern validation
            $dangerousPatterns = $this->detectDangerousPatterns($svg);
            if (!empty($dangerousPatterns)) {
                $errors[] = 'Contains dangerous patterns: ' . implode(', ', $dangerousPatterns);
            }
            
            // DOM validation
            $domErrors = $this->validateWithDom($svg);
            $errors = array_merge($errors, $domErrors);
            
        } catch (Exception $e) {
            $errors[] = "Validation error: {$e->getMessage()}";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $this->generateWarnings($svg)
        ];
    }
    
    /**
     * Extract SVG dimensions and metadata safely.
     *
     * @param string $svg SVG content
     * @return array SVG metadata including dimensions
     */
    public function extractMetadata(string $svg): array
    {
        $metadata = [
            'width' => null,
            'height' => null,
            'viewBox' => null,
            'element_count' => 0,
            'has_animations' => false,
            'has_external_refs' => false,
        ];
        
        try {
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            
            if ($doc->loadXML($svg)) {
                $svgElement = $doc->documentElement;
                
                if ($svgElement && $svgElement->tagName === 'svg') {
                    // Extract dimensions
                    $metadata['width'] = $this->sanitizeDimension($svgElement->getAttribute('width'));
                    $metadata['height'] = $this->sanitizeDimension($svgElement->getAttribute('height'));
                    $metadata['viewBox'] = SecurityUtils::sanitizeInput($svgElement->getAttribute('viewBox'));
                    
                    // Count elements
                    $xpath = new DOMXPath($doc);
                    $allElements = $xpath->query('//*');
                    $metadata['element_count'] = $allElements ? $allElements->length : 0;
                    
                    // Check for animations
                    $animationElements = $xpath->query('//animate | //animateTransform | //animateMotion');
                    $metadata['has_animations'] = $animationElements && $animationElements->length > 0;
                    
                    // Check for external references
                    $metadata['has_external_refs'] = $this->hasExternalReferences($svg);
                }
            }
            
        } catch (Exception $e) {
            error_log("HSS SVG: Metadata extraction failed: {$e->getMessage()}");
        }
        
        return $metadata;
    }
    
    /**
     * Check if SVG has security violations.
     *
     * @param string $svg SVG content to check
     * @return array List of security violations found
     */
    public function scanForViolations(string $svg): array
    {
        $violations = [];
        
        // Check dangerous patterns
        $patterns = $this->detectDangerousPatterns($svg);
        if (!empty($patterns)) {
            $violations['dangerous_patterns'] = $patterns;
        }
        
        // Check external references
        if ($this->hasExternalReferences($svg)) {
            $violations['external_references'] = true;
        }
        
        // Check size limits
        if (strlen($svg) > $this->config['max_size']) {
            $violations['size_exceeded'] = strlen($svg);
        }
        
        // Check element count
        $elementCount = substr_count($svg, '<');
        if ($elementCount > $this->config['max_elements']) {
            $violations['element_count_exceeded'] = $elementCount;
        }
        
        return $violations;
    }
    
    // PURE FUNCTIONS FOR VALIDATION
    
    /**
     * Validate SVG input format (pure function).
     *
     * @param string $svg SVG content
     * @return bool True if valid input
     */
    private function isValidSvgInput(string $svg): bool
    {
        return !empty($svg)
            && is_string($svg)
            && strlen($svg) > 10
            && (strpos($svg, '<svg') !== false || strpos($svg, '<?xml') !== false);
    }
    
    /**
     * Detect dangerous patterns in SVG content (pure function).
     *
     * @param string $svg SVG content to check
     * @return array List of detected dangerous patterns
     */
    private function detectDangerousPatterns(string $svg): array
    {
        $detected = [];
        
        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $svg)) {
                $detected[] = trim($pattern, '/i');
            }
        }
        
        return $detected;
    }
    
    /**
     * Check if SVG has external references (pure function).
     *
     * @param string $svg SVG content
     * @return bool True if external references found
     */
    private function hasExternalReferences(string $svg): bool
    {
        // Check for various external reference patterns
        $externalPatterns = [
            '/href\s*=\s*["\']https?:\/\/[^"\']+["\']/i',
            '/xlink:href\s*=\s*["\']https?:\/\/[^"\']+["\']/i',
            '/src\s*=\s*["\']https?:\/\/[^"\']+["\']/i',
            '/url\s*\(\s*["\']?https?:\/\/[^)"\']+["\']?\s*\)/i',
        ];
        
        foreach ($externalPatterns as $pattern) {
            if (preg_match($pattern, $svg)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Sanitize dimension value (pure function).
     *
     * @param string $dimension Dimension string
     * @return string|null Sanitized dimension or null
     */
    private function sanitizeDimension(?string $dimension): ?string
    {
        if (empty($dimension)) {
            return null;
        }
        
        // Allow numeric values with optional units
        if (preg_match('/^(\d+(?:\.\d+)?)(px|em|rem|%)?$/', trim($dimension), $matches)) {
            return $matches[1] . ($matches[2] ?? '');
        }
        
        return null;
    }
    
    /**
     * Generate warnings for potentially problematic SVG content (pure function).
     *
     * @param string $svg SVG content
     * @return array List of warnings
     */
    private function generateWarnings(string $svg): array
    {
        $warnings = [];
        
        // Check for complex gradients
        if (substr_count($svg, 'gradient') > 10) {
            $warnings[] = 'SVG contains many gradients which may affect performance';
        }
        
        // Check for many paths
        if (substr_count($svg, '<path') > 100) {
            $warnings[] = 'SVG contains many path elements which may affect performance';
        }
        
        // Check for embedded images
        if (strpos($svg, '<image') !== false) {
            $warnings[] = 'SVG contains embedded images';
        }
        
        return $warnings;
    }
    
    // DOM-BASED SANITIZATION METHODS
    
    /**
     * Sanitize SVG using DOM parsing for accuracy.
     *
     * @param string $svg Raw SVG content
     * @return string Sanitized SVG content
     * @throws Exception If DOM parsing fails
     */
    private function sanitizeWithDom(string $svg): string
    {
        $doc = new DOMDocument();
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;
        
        // Suppress XML parsing errors
        libxml_use_internal_errors(true);
        
        if (!$doc->loadXML($svg)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new Exception('Failed to parse SVG XML: ' . $this->formatLibxmlErrors($errors));
        }
        
        $svgElement = $doc->documentElement;
        if (!$svgElement || $svgElement->tagName !== 'svg') {
            throw new Exception('Invalid SVG structure: root element must be <svg>');
        }
        
        // Sanitize recursively
        $this->sanitizeElement($svgElement);
        
        // Count elements after sanitization
        $xpath = new DOMXPath($doc);
        $elementCount = $xpath->query('//*')->length;
        if ($elementCount > $this->config['max_elements']) {
            throw new Exception('SVG contains too many elements after sanitization');
        }
        
        return $doc->saveXML($svgElement);
    }
    
    /**
     * Validate SVG using DOM parsing.
     *
     * @param string $svg SVG content
     * @return array Validation errors
     */
    private function validateWithDom(string $svg): array
    {
        $errors = [];
        
        try {
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            
            if (!$doc->loadXML($svg)) {
                $libxmlErrors = libxml_get_errors();
                foreach ($libxmlErrors as $error) {
                    $errors[] = "XML parsing error: {$error->message}";
                }
                libxml_clear_errors();
                return $errors;
            }
            
            $svgElement = $doc->documentElement;
            if (!$svgElement || $svgElement->tagName !== 'svg') {
                $errors[] = 'Root element must be <svg>';
            }
            
        } catch (Exception $e) {
            $errors[] = "DOM validation error: {$e->getMessage()}";
        }
        
        return $errors;
    }
    
    /**
     * Recursively sanitize DOM element and its children.
     *
     * @param DOMElement $element Element to sanitize
     */
    private function sanitizeElement(DOMElement $element): void
    {
        // Check if element is allowed
        if (!in_array($element->tagName, self::ALLOWED_ELEMENTS, true)) {
            $element->parentNode?->removeChild($element);
            return;
        }
        
        // Sanitize attributes
        $this->sanitizeAttributes($element);
        
        // Process child elements
        $children = [];
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $children[] = $child;
            }
        }
        
        foreach ($children as $child) {
            $this->sanitizeElement($child);
        }
    }
    
    /**
     * Sanitize element attributes.
     *
     * @param DOMElement $element Element to sanitize attributes for
     */
    private function sanitizeAttributes(DOMElement $element): void
    {
        $attributesToRemove = [];
        
        foreach ($element->attributes as $attribute) {
            $name = $attribute->name;
            $value = $attribute->value;
            
            // Check if attribute is allowed
            if (!in_array($name, self::ALLOWED_ATTRIBUTES, true)) {
                $attributesToRemove[] = $name;
                continue;
            }
            
            // Special handling for href attributes
            if (in_array($name, ['href', 'xlink:href'], true)) {
                if (!$this->isAllowedHref($value)) {
                    $attributesToRemove[] = $name;
                    continue;
                }
            }
            
            // Sanitize style attributes
            if ($name === 'style') {
                $sanitizedStyle = $this->sanitizeStyleAttribute($value);
                if (empty($sanitizedStyle)) {
                    $attributesToRemove[] = $name;
                } else {
                    $element->setAttribute($name, $sanitizedStyle);
                }
                continue;
            }
            
            // General value sanitization
            $sanitizedValue = SecurityUtils::sanitizeInput($value);
            if ($sanitizedValue !== $value) {
                $element->setAttribute($name, $sanitizedValue);
            }
        }
        
        // Remove disallowed attributes
        foreach ($attributesToRemove as $name) {
            $element->removeAttribute($name);
        }
    }
    
    /**
     * Check if href value is allowed.
     *
     * @param string $href Href attribute value
     * @return bool True if allowed
     */
    private function isAllowedHref(string $href): bool
    {
        // Allow internal references
        if (strpos($href, '#') === 0) {
            return true;
        }
        
        // Allow data URLs for SVG
        if (strpos($href, 'data:image/svg') === 0) {
            return $this->config['allow_external_refs'];
        }
        
        // Block external URLs unless explicitly allowed
        return $this->config['allow_external_refs'] && !preg_match('/^(javascript|data:text|vbscript):/i', $href);
    }
    
    /**
     * Sanitize CSS style attribute value.
     *
     * @param string $style Style attribute value
     * @return string Sanitized style value
     */
    private function sanitizeStyleAttribute(string $style): string
    {
        // Remove dangerous CSS properties and values
        $dangerousCssPatterns = [
            '/expression\s*\(/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/@import/i',
            '/behavior\s*:/i',
        ];
        
        foreach ($dangerousCssPatterns as $pattern) {
            if (preg_match($pattern, $style)) {
                return ''; // Remove entire style if dangerous
            }
        }
        
        return SecurityUtils::sanitizeInput($style);
    }
    
    /**
     * Format libxml errors for logging.
     *
     * @param array $errors Array of libxml errors
     * @return string Formatted error string
     */
    private function formatLibxmlErrors(array $errors): string
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = trim($error->message);
        }
        return implode('; ', $messages);
    }
}
