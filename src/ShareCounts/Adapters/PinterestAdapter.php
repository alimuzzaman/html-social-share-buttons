<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

use HtmlSocialShare\Settings;

class PinterestAdapter implements AdapterInterface
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function fetch(string $url): int
    {
        // Pinterest previously exposed a public count endpoint but it's unreliable.
        // Try a legacy endpoint as best-effort; fall back to 0.
        $endpoint = sprintf('https://api.pinterest.com/v1/urls/count.json?url=%s', rawurlencode($url));
        $response = wp_remote_get($endpoint, ['timeout' => 10]);
        if (is_wp_error($response)) {
            error_log('HSS PinterestAdapter: HTTP error: ' . $response->get_error_message());
            return 0;
        }

        $body = wp_remote_retrieve_body($response);
        // Legacy response wrapped in 'receiveCount({...})' - try to extract JSON
        $json = preg_replace('/^receiveCount\((.*)\)$/', '\1', trim($body));
        $data = json_decode($json, true);
        if (is_array($data) && isset($data['count'])) {
            return (int) $data['count'];
        }

        return 0;
    }
}
