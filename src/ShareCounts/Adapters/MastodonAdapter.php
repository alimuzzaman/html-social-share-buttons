<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

class MastodonAdapter implements AdapterInterface
{
    public function fetch(string $url): int
    {
        // Mastodon has no central share count API; counts are instance-specific and not aggregated.
        // Keep a placeholder implementation that returns 0 for now and logs a note for future work.
        error_log('HSS MastodonAdapter: public share-counts not available, returning 0 for ' . $url);
        return 0;
    }
}
