<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

use HtmlSocialShare\Settings;

class LinkedInAdapter implements AdapterInterface
{
    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function fetch(string $url): int
    {
        // LinkedIn no longer provides a public share count API. Keep placeholder
        // for future integrations or third-party aggregators.
        error_log('HSS LinkedInAdapter: public share-counts unavailable, returning 0 for ' . $url);
        return 0;
    }
}
