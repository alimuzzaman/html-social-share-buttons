<?php
namespace HtmlSocialShare;

class Settings implements SettingsInterface
{
    private array $store = [];

    public function get(string $key, $default = null)
    {
        return $this->store[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->store[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->store[$key]);
    }

    /**
     * Get all settings as an array.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->store;
    }
}
