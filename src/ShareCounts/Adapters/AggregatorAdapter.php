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

        // Use the overridable request method to allow tests to stub HTTP behaviour
        $body = $this->doRequest($endpointUrl);
        if ($body === false) {
            return 0;
        }

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

    /**
     * Perform HTTP GET request to aggregator endpoint.
     * Extracted into a protected method so tests can override it.
     *
     * @param string $endpointUrl
     * @return string|false Body string on success or false on failure
     */
    protected function doRequest(string $endpointUrl)
    {
        if (!function_exists('wp_remote_get')) {
            error_log('HSS AggregatorAdapter: wp_remote_get not available in environment');
            return false;
        }

        $response = wp_remote_get($endpointUrl, ['timeout' => 10]);
        if (is_wp_error($response)) {
            error_log('HSS AggregatorAdapter: HTTP error: ' . $response->get_error_message());
            return false;
        }

        return wp_remote_retrieve_body($response);
    }
}
