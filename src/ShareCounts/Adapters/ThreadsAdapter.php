<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

class ThreadsAdapter implements AdapterInterface
{
    public function fetch(string $url): int
    {
        // Threads does not provide a public share-count endpoint for external URLs.
        // Returning 0 and logging for transparency; a centralized aggregator would be needed.
        error_log('HSS ThreadsAdapter: public share-counts not available, returning 0 for ' . $url);
        return 0;
    }
}
