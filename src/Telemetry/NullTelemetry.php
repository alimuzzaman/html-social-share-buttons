<?php
namespace HtmlSocialShare\Telemetry;

class NullTelemetry implements TelemetryInterface
{
    public function track(string $event, array $payload = []): void
    {
        // no-op default implementation for environments without telemetry
    }
}
