<?php
namespace HtmlSocialShare\Svg;

/**
 * Interface for SVG sanitization and security
 * 
 * Provides methods for safely processing SVG content by removing potentially
 * dangerous elements, attributes, and scripts while preserving visual appearance.
 * 
 * @package HtmlSocialShare\Svg
 * @since 3.0.0
 */
interface SanitizerInterface
{
    /**
     * Sanitize an SVG string and return a safe string.
     *
     * @param string $svg Raw SVG content
     * @return string Sanitized SVG content
     * @throws \InvalidArgumentException If SVG is invalid or empty
     */
    public function sanitize(string $svg): string;

    /**
     * Validate SVG content without sanitizing.
     *
     * @param string $svg SVG content to validate
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public function validate(string $svg): array;

    /**
     * Remove all script elements and event handlers from SVG.
     *
     * @param string $svg SVG content
     * @return string Cleaned SVG content
     */
    public function removeScripts(string $svg): string;

    /**
     * Remove dangerous attributes from SVG elements.
     *
     * @param string $svg SVG content
     * @return string Cleaned SVG content
     */
    public function removeDangerousAttributes(string $svg): string;

    /**
     * Get list of allowed SVG elements.
     *
     * @return array Array of allowed element names
     */
    public function getAllowedElements(): array;

    /**
     * Get list of allowed SVG attributes.
     *
     * @return array Array of allowed attribute names
     */
    public function getAllowedAttributes(): array;

    /**
     * Set custom allowed elements.
     *
     * @param array $elements Array of element names
     * @return void
     */
    public function setAllowedElements(array $elements): void;

    /**
     * Set custom allowed attributes.
     *
     * @param array $attributes Array of attribute names
     * @return void
     */
    public function setAllowedAttributes(array $attributes): void;

    /**
     * Check if SVG contains potentially dangerous content.
     *
     * @param string $svg SVG content
     * @return bool True if dangerous content detected
     */
    public function hasDangerousContent(string $svg): bool;

    /**
     * Extract dimensions from SVG if available.
     *
     * @param string $svg SVG content
     * @return array Array with 'width' and 'height' keys, null values if not found
     */
    public function extractDimensions(string $svg): array;
}
