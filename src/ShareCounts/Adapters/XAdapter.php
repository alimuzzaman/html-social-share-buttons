<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

class XAdapter implements AdapterInterface
{
    public function fetch(string $url): int
    {
        // Twitter/X removed public share count endpoint. Returning 0 by default
        // This adapter exists so a future implementation can call an external
        // aggregator or internal tracking service.
        error_log('HSS XAdapter: public share-counts unavailable, returning 0 for ' . $url);
        return 0;
    }
}
