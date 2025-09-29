<?php
namespace HtmlSocialShare\Telemetry;

use HtmlSocialShare\Utils\SecurityUtils;
use HtmlSocialShare\Utils\StringUtils;
use Exception;

/**
 * Enhanced Null Telemetry implementation with comprehensive tracking capabilities.
 * 
 * Provides a safe no-op telemetry service that can optionally log events for debugging
 * while maintaining production safety with:
 * - Event validation and sanitization
 * - Optional debug logging when enabled
 * - Performance metrics collection capability
 * - Error tracking and context management
 * - Memory usage optimization
 */
class NullTelemetry implements TelemetryInterface
{
    /** @var bool Whether debug logging is enabled */
    private bool $debugLogging;
    
    /** @var array<string, mixed> Telemetry configuration */
    private array $config;
    
    /** @var array<string, int> Event counters for basic metrics */
    private array $eventCounters = [];
    
    /** @var int Maximum events to track in memory */
    private const MAX_EVENTS_IN_MEMORY = 100;

    /**
     * Initialize null telemetry with optional debug capabilities.
     *
     * @param array $config Optional configuration array
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'debug_logging' => false,
            'log_errors' => false,
            'track_performance' => false,
            'max_payload_size' => 1024,
        ], $config);
        
        $this->debugLogging = (bool) ($this->config['debug_logging'] ?? false);
    }
    
    /**
     * Track telemetry event with validation but no actual transmission.
     *
     * @param string $event Event name to track
     * @param array $payload Event data payload
     * @param array $context Optional context information
     * @return bool Always returns true for null implementation
     */
    public function track(string $event, array $payload = [], array $context = []): bool
    {
        try {
            // Validate event name
            if (!$this->isValidEventName($event)) {
                if ($this->config['log_errors']) {
                    error_log("HSS Telemetry: Invalid event name: {$event}");
                }
                return false;
            }
            
            // Sanitize and validate payload
            $sanitizedPayload = $this->sanitizePayload($payload);
            if (empty($sanitizedPayload) && !empty($payload)) {
                if ($this->config['log_errors']) {
                    error_log("HSS Telemetry: Payload sanitization failed for event: {$event}");
                }
            }
            
            // Track event counter for basic metrics
            $this->incrementEventCounter($event);
            
            // Optional debug logging
            if ($this->debugLogging) {
                $this->logEventForDebug($event, $sanitizedPayload, $context);
            }
            
            // Optional performance tracking
            if ($this->config['track_performance']) {
                $this->trackPerformanceMetrics($event, $context);
            }
            
            return true;
            
        } catch (Exception $e) {
            if ($this->config['log_errors']) {
                error_log("HSS Telemetry: Event tracking failed for {$event}: {$e->getMessage()}");
            }
            return false;
        }
    }
    
    /**
     * Track error events with enhanced context.
     *
     * @param string $error Error message or identifier
     * @param array $context Error context information
     * @param string $level Error level (error, warning, info)
     * @return bool Success status
     */
    public function trackError(string $error, array $context = [], string $level = 'error'): bool
    {
        $payload = [
            'error' => SecurityUtils::sanitizeInput($error),
            'level' => $this->validateErrorLevel($level),
            'timestamp' => time(),
            'memory_usage' => memory_get_usage(true),
        ];
        
        return $this->track('error_occurred', $payload, $context);
    }
    
    /**
     * Track performance timing events.
     *
     * @param string $operation Operation name
     * @param float $duration Duration in seconds
     * @param array $context Additional context
     * @return bool Success status
     */
    public function trackTiming(string $operation, float $duration, array $context = []): bool
    {
        $payload = [
            'operation' => SecurityUtils::sanitizeInput($operation),
            'duration_seconds' => round($duration, 4),
            'memory_peak' => memory_get_peak_usage(true),
        ];
        
        return $this->track('performance_timing', $payload, $context);
    }
    
    /**
     * Start timing measurement for an operation.
     *
     * @param string $operation Operation identifier
     * @return float Start time for use with endTiming()
     */
    public function startTiming(string $operation): float
    {
        return microtime(true);
    }
    
    /**
     * End timing measurement and track the result.
     *
     * @param string $operation Operation identifier
     * @param float $startTime Start time from startTiming()
     * @param array $context Additional context
     * @return bool Success status
     */
    public function endTiming(string $operation, float $startTime, array $context = []): bool
    {
        $duration = microtime(true) - $startTime;
        return $this->trackTiming($operation, $duration, $context);
    }
    
    /**
     * Get basic telemetry statistics.
     *
     * @return array Statistics about tracked events
     */
    public function getStats(): array
    {
        return [
            'total_events' => array_sum($this->eventCounters),
            'event_types' => count($this->eventCounters),
            'event_counters' => $this->eventCounters,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];
    }
    
    /**
     * Clear telemetry statistics and counters.
     *
     * @return bool Success status
     */
    public function clearStats(): bool
    {
        $this->eventCounters = [];
        return true;
    }
    
    // PURE FUNCTIONS FOR VALIDATION AND PROCESSING
    
    /**
     * Validate event name format (pure function).
     *
     * @param string $event Event name to validate
     * @return bool True if valid
     */
    private function isValidEventName(string $event): bool
    {
        // Event names should be non-empty, reasonable length, alphanumeric with underscores
        return !empty($event) 
            && strlen($event) <= 100 
            && preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $event);
    }
    
    /**
     * Sanitize event payload data (pure function).
     *
     * @param array $payload Raw payload data
     * @return array Sanitized payload
     */
    private function sanitizePayload(array $payload): array
    {
        $sanitized = [];
        $maxSize = $this->config['max_payload_size'];
        $currentSize = 0;
        
        foreach ($payload as $key => $value) {
            // Sanitize key
            $sanitizedKey = SecurityUtils::sanitizeInput((string) $key);
            if (empty($sanitizedKey)) {
                continue;
            }
            
            // Sanitize value based on type
            $sanitizedValue = $this->sanitizePayloadValue($value);
            
            // Check size limit
            $entrySize = strlen(json_encode([$sanitizedKey => $sanitizedValue]));
            if ($currentSize + $entrySize > $maxSize) {
                break;
            }
            
            $sanitized[$sanitizedKey] = $sanitizedValue;
            $currentSize += $entrySize;
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize individual payload value (pure function).
     *
     * @param mixed $value Value to sanitize
     * @return mixed Sanitized value
     */
    private function sanitizePayloadValue($value)
    {
        if (is_string($value)) {
            return SecurityUtils::sanitizeInput($value);
        } elseif (is_numeric($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return $value;
        } elseif (is_array($value) && count($value) <= 10) {
            // Recursively sanitize small arrays
            return array_map([$this, 'sanitizePayloadValue'], $value);
        }
        
        // For complex types, convert to string
        return SecurityUtils::sanitizeInput((string) $value);
    }
    
    /**
     * Validate error level (pure function).
     *
     * @param string $level Error level to validate
     * @return string Valid error level
     */
    private function validateErrorLevel(string $level): string
    {
        $validLevels = ['error', 'warning', 'info', 'debug'];
        $level = strtolower(trim($level));
        
        return in_array($level, $validLevels, true) ? $level : 'error';
    }
    
    // SIDE EFFECT FUNCTIONS FOR LOGGING AND TRACKING
    
    /**
     * Increment event counter for basic metrics.
     *
     * @param string $event Event name
     */
    private function incrementEventCounter(string $event): void
    {
        $this->eventCounters[$event] = ($this->eventCounters[$event] ?? 0) + 1;
        
        // Prevent memory issues by limiting tracked events
        if (count($this->eventCounters) > self::MAX_EVENTS_IN_MEMORY) {
            // Remove oldest entries (simple approach)
            $this->eventCounters = array_slice($this->eventCounters, -50, null, true);
        }
    }
    
    /**
     * Log event for debugging purposes.
     *
     * @param string $event Event name
     * @param array $payload Event payload
     * @param array $context Event context
     */
    private function logEventForDebug(string $event, array $payload, array $context): void
    {
        $logData = [
            'event' => $event,
            'payload_size' => count($payload),
            'context_keys' => array_keys($context),
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        error_log('HSS Debug Telemetry: ' . json_encode($logData));
    }
    
    /**
     * Track performance metrics if enabled.
     *
     * @param string $event Event name
     * @param array $context Event context
     */
    private function trackPerformanceMetrics(string $event, array $context): void
    {
        // Simple performance tracking
        $metrics = [
            'event' => $event,
            'memory_current' => memory_get_usage(),
            'memory_peak' => memory_get_peak_usage(),
            'time' => microtime(true),
        ];
        
        // In a real implementation, this would be stored or transmitted
        // For null implementation, we just track it internally
        $this->incrementEventCounter('performance_' . $event);
    }
}
