<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

use HtmlSocialShare\Settings;

class FacebookAdapter implements AdapterInterface
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function fetch(string $url): int
    {
        // Requires a configured Facebook app token in settings
        $token = $this->settings->get('facebook_app_token', '');
        if (empty($token)) {
            error_log('HSS FacebookAdapter: No facebook_app_token configured; skipping fetch');
            return 0;
        }

        $endpoint = sprintf('https://graph.facebook.com/v12.0/?id=%s&fields=engagement&access_token=%s', rawurlencode($url), rawurlencode($token));

        $response = wp_remote_get($endpoint, ['timeout' => 10]);
        if (is_wp_error($response)) {
            error_log('HSS FacebookAdapter: HTTP error: ' . $response->get_error_message());
            return 0;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (!is_array($data) || empty($data['engagement']['share_count'])) {
            return 0;
        }

        return (int) $data['engagement']['share_count'];
    }
}
