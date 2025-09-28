<?php
namespace HtmlSocialShare\Telemetry;

interface TelemetryInterface
{
    /**
     * Track an event for observability and diagnostics.
     * Implementations may be no-op, log to file, or send telemetry to external systems.
     *
     * @param string $event
     * @param array $payload
     */
    public function track(string $event, array $payload = []): void;
}
