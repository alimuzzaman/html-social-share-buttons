<?php
namespace HtmlSocialShare;

class Cache implements CacheInterface
{
    private array $store = [];

    public function set(string $key, $value, ?int $ttl = null): void
    {
        $expire = null;
        if ($ttl !== null) {
            $expire = microtime(true) + $ttl;
        }
        $this->store[$key] = ['value' => $value, 'expire' => $expire];
    }

    public function get(string $key)
    {
        if (! isset($this->store[$key])) {
            return null;
        }
        $entry = $this->store[$key];
        if ($entry['expire'] !== null && microtime(true) > $entry['expire']) {
            unset($this->store[$key]);
            return null;
        }
        return $entry['value'];
    }

    public function delete(string $key): void
    {
        unset($this->store[$key]);
    }

    public function clear(): void
    {
        $this->store = [];
    }
}
