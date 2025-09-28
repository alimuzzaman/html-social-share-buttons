<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

class BlueskyAdapter implements AdapterInterface
{
    public function fetch(string $url): int
    {
        // Bluesky does not expose a public share-count API for web URLs as of now.
        // Placeholder returns 0 and logs the attempt; future implementations may call an aggregator.
        error_log('HSS BlueskyAdapter: public share-counts not available, returning 0 for ' . $url);
        return 0;
    }
}
