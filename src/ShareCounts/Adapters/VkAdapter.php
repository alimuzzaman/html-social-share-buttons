<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

use HtmlSocialShare\Settings;

class VkAdapter implements AdapterInterface
{
    public function __construct()
    {
        // No settings required for public endpoint
    }

    public function fetch(string $url): int
    {
        // VK returns a JS callback like: VK.Share.count(1, 123);
        $endpoint = sprintf('https://vk.com/share.php?act=count&url=%s', rawurlencode($url));
        if (!function_exists('wp_remote_get')) {
            error_log('HSS VkAdapter: wp_remote_get not available (test environment)');
            return 0;
        }

        $response = wp_remote_get($endpoint, ['timeout' => 10]);
        if (is_wp_error($response)) {
            error_log('HSS VkAdapter: HTTP error: ' . $response->get_error_message());
            return 0;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return 0;
        }

        // Extract number from JS callback
        if (preg_match('/VK\.Share\.count\(\d+\s*,\s*(\d+)\)/', $body, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
