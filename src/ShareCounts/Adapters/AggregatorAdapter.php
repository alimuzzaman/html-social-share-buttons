<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

use HtmlSocialShare\Settings;

class AggregatorAdapter implements AdapterInterface
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function fetch(string $url): int
    {
        $endpoint = $this->settings->get('share_counts_aggregator_endpoint', '');
        $apiKey = $this->settings->get('share_counts_aggregator_key', '');

        if (empty($endpoint)) {
            error_log('HSS AggregatorAdapter: No aggregator endpoint configured');
            return 0;
        }

        $query = [
            'url' => $url,
            'key' => $apiKey
        ];

        $endpointUrl = $endpoint . (strpos($endpoint, '?') === false ? '?' : '&') . http_build_query($query);

        if (!function_exists('wp_remote_get')) {
            error_log('HSS AggregatorAdapter: wp_remote_get not available in environment');
            return 0;
        }

        $response = wp_remote_get($endpointUrl, ['timeout' => 10]);
        if (is_wp_error($response)) {
            error_log('HSS AggregatorAdapter: HTTP error: ' . $response->get_error_message());
            return 0;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (is_array($data) && isset($data['count'])) {
            return (int) $data['count'];
        }

        // Some aggregators may return {counts: {facebook: 1, x: 2}}; sum if present
        if (is_array($data) && isset($data['counts']) && is_array($data['counts'])) {
            return array_sum(array_map('intval', $data['counts']));
        }

        return 0;
    }
}
