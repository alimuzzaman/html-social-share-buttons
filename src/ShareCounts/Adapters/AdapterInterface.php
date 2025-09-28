<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

interface AdapterInterface
{
    /**
     * Fetch share count for a URL.
     *
     * @param string $url
     * @return int
     */
    public function fetch(string $url): int;
}
