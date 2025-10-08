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

    /** @var array<string, array{start:float,name:string}> Active timers */
    private array $timers = [];

    /** @var array<string,mixed> User context for subsequent events */
    private array $userContext = [];

    /** @var array<string,mixed> Request/contextual data for subsequent events */
    private array $requestContext = [];

    /** @var bool Whether telemetry is enabled */
    private bool $enabled;

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
            'enabled' => true,
        ], $config);
        
        $this->debugLogging = (bool) ($this->config['debug_logging'] ?? false);
        $this->enabled = (bool) ($this->config['enabled'] ?? true);
    }
    
    /**
     * Track telemetry event with validation but no actual transmission.
     *
     * Implements TelemetryInterface::track
     *
     * @param string $event Event name to track
     * @param array $payload Event data payload
     * @return void
     */
    public function track(string $event, array $payload = []): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            // Extract any explicit context passed in the payload
            $context = [];
            if (isset($payload['_context']) && is_array($payload['_context'])) {
                $context = $payload['_context'];
                unset($payload['_context']);
            } elseif (isset($payload['context']) && is_array($payload['context'])) {
                $context = $payload['context'];
                unset($payload['context']);
            } else {
                // Merge user/request context if no explicit context provided
                $context = array_merge($this->userContext, $this->requestContext);
            }

            // Validate event name
            if (!$this->isValidEventName($event)) {
                if ($this->config['log_errors']) {
                    error_log("HSS Telemetry: Invalid event name: {$event}");
                }
                return;
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

            // Note: null implementation intentionally does not transmit events externally
        } catch (Exception $e) {
            if ($this->config['log_errors']) {
                error_log("HSS Telemetry: Event tracking failed for {$event}: {$e->getMessage()}");
            }
            // Swallow exceptions to avoid interfering with primary application flow
        }
    }
    
    /**
     * Track an error event with context.
     *
     * Implements TelemetryInterface::trackError
     *
     * @param string $error Error identifier or message
     * @param \Throwable|string $exception Exception or error message
     * @param array $context Additional error context
     * @return void
     */
    public function trackError(string $error, $exception, array $context = []): void
    {
        $exceptionStr = $exception instanceof \Throwable ? $exception->getMessage() : (string) $exception;
        $payload = [
            'error' => SecurityUtils::sanitizeInput($error),
            'exception' => SecurityUtils::sanitizeInput($exceptionStr),
            'level' => isset($context['level']) ? $this->validateErrorLevel((string) $context['level']) : 'error',
            'timestamp' => time(),
            'memory_usage' => memory_get_usage(true),
        ];

        if (!empty($context)) {
            $payload['context'] = $context;
        }

        $this->track('error_occurred', $payload);
    }
    
    /**
     * Track a performance metric.
     *
     * Implements TelemetryInterface::trackMetric
     *
     * @param string $metric Metric name (e.g., 'render_time', 'api_response_time')
     * @param float $value Metric value
     * @param string $unit Unit of measurement (e.g., 'ms', 'seconds', 'bytes')
     * @param array $tags Optional metric tags
     * @return void
     */
    public function trackMetric(string $metric, float $value, string $unit = '', array $tags = []): void
    {
        $payload = [
            'metric' => SecurityUtils::sanitizeInput($metric),
            'value' => $value,
            'unit' => SecurityUtils::sanitizeInput($unit),
            'tags' => $tags,
            'timestamp' => microtime(true),
        ];

        $this->track('metric_recorded', $payload);
    }

    /**
     * Start a named timer and return a timer ID.
     *
     * Implements TelemetryInterface::startTimer
     *
     * @param string $name Timer name
     * @return string Timer ID for stopping
     */
    public function startTimer(string $name): string
    {
        $id = bin2hex(random_bytes(8));
        $this->timers[$id] = ['start' => microtime(true), 'name' => $name];
        return $id;
    }

    /**
     * Stop timing identified by timer ID and return elapsed milliseconds.
     *
     * Implements TelemetryInterface::stopTimer
     *
     * @param string $timerId Timer ID from startTimer()
     * @return float Elapsed time in milliseconds
     */
    public function stopTimer(string $timerId): float
    {
        if (!isset($this->timers[$timerId])) {
            return 0.0;
        }

        $meta = $this->timers[$timerId];
        $start = $meta['start'];
        $name = $meta['name'] ?? 'timer';
        $elapsedMs = (microtime(true) - $start) * 1000.0;

        // Clean up timer
        unset($this->timers[$timerId]);

        // Optionally record metric
        $this->trackMetric($name, $elapsedMs, 'ms');

        return $elapsedMs;
    }

    /**
     * Set user context for subsequent events.
     *
     * Implements TelemetryInterface::setUserContext
     *
     * @param array $context User context (user_id, role, etc.)
     * @return void
     */
    public function setUserContext(array $context): void
    {
        $this->userContext = $context;
    }

    /**
     * Set request context for subsequent events.
     *
     * Implements TelemetryInterface::setRequestContext
     *
     * @param array $context Request context (url, method, user_agent, etc.)
     * @return void
     */
    public function setRequestContext(array $context): void
    {
        $this->requestContext = $context;
    }

    /**
     * Clear all context data.
     *
     * Implements TelemetryInterface::clearContext
     *
     * @return void
     */
    public function clearContext(): void
    {
        $this->userContext = [];
        $this->requestContext = [];
    }

    /**
     * Check if telemetry is enabled.
     *
     * Implements TelemetryInterface::isEnabled
     *
     * @return bool True if enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enable or disable telemetry.
     *
     * Implements TelemetryInterface::setEnabled
     *
     * @param bool $enabled Whether to enable telemetry
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Backwards-compatible convenience method: track an error with level.
     *
     * Kept for compatibility with older callers in the class; simply forwards
     * to the interface-compatible trackError implementation.
     *
     * @param string $error
     * @param array $context
     * @param string $level
     * @return void
     */
    public function trackErrorWithLevel(string $error, array $context = [], string $level = 'error'): void
    {
        $context['level'] = $level;
        $this->trackError($error, (string) ($context['exception'] ?? ''), $context);
    }

    /**
     * Backwards-compatible convenience method: track a timing event.
     *
     * @param string $operation
     * @param float $duration
     * @param array $context
     * @return void
     */
    public function trackTiming(string $operation, float $duration, array $context = []): void
    {
        $payload = [
            'operation' => SecurityUtils::sanitizeInput($operation),
            'duration_seconds' => round($duration, 4),
            'memory_peak' => memory_get_peak_usage(true),
        ];

        if (!empty($context)) {
            $payload['context'] = $context;
        }

        $this->track('performance_timing', $payload);
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
     * @return void
     */
    public function clearStats(): void
    {
        $this->eventCounters = [];
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
