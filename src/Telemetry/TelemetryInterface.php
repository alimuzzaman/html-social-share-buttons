<?php
namespace HtmlSocialShare\Telemetry;

/**
 * Interface for telemetry and analytics tracking
 * 
 * Provides methods for tracking events, errors, and performance metrics.
 * Implementations may be no-op, log to file, or send telemetry to external systems.
 * 
 * @package HtmlSocialShare\Telemetry
 * @since 3.0.0
 */
interface TelemetryInterface
{
    /**
     * Track an event for observability and diagnostics.
     * Implementations may be no-op, log to file, or send telemetry to external systems.
     *
     * @param string $event Event name (e.g., 'button_rendered', 'share_clicked')
     * @param array $payload Optional event data
     * @return void
     */
    public function track(string $event, array $payload = []): void;

    /**
     * Track an error event with context.
     *
     * @param string $error Error identifier
     * @param \Throwable|string $exception Exception or error message
     * @param array $context Additional error context
     * @return void
     */
    public function trackError(string $error, $exception, array $context = []): void;

    /**
     * Track a performance metric.
     *
     * @param string $metric Metric name (e.g., 'render_time', 'api_response_time')
     * @param float $value Metric value
     * @param string $unit Unit of measurement (e.g., 'ms', 'seconds', 'bytes')
     * @param array $tags Optional metric tags
     * @return void
     */
    public function trackMetric(string $metric, float $value, string $unit = '', array $tags = []): void;

    /**
     * Start timing a code block.
     *
     * @param string $name Timer name
     * @return string Timer ID for stopping
     */
    public function startTimer(string $name): string;

    /**
     * Stop timing a code block and track the metric.
     *
     * @param string $timerId Timer ID from startTimer()
     * @return float Elapsed time in milliseconds
     */
    public function stopTimer(string $timerId): float;

    /**
     * Set user context for subsequent events.
     *
     * @param array $context User context (user_id, role, etc.)
     * @return void
     */
    public function setUserContext(array $context): void;

    /**
     * Set request context for subsequent events.
     *
     * @param array $context Request context (url, method, user_agent, etc.)
     * @return void
     */
    public function setRequestContext(array $context): void;

    /**
     * Clear all context data.
     *
     * @return void
     */
    public function clearContext(): void;

    /**
     * Check if telemetry is enabled.
     *
     * @return bool True if enabled
     */
    public function isEnabled(): bool;

    /**
     * Enable or disable telemetry.
     *
     * @param bool $enabled Whether to enable telemetry
     * @return void
     */
    public function setEnabled(bool $enabled): void;
}
