<?php
namespace HtmlSocialShare\ShareCounts\Adapters;

class GenericAdapter implements AdapterInterface
{
    public function fetch(string $url): int
    {
        // Generic fallback when no network-specific API is available.
        return 0;
    }
}
