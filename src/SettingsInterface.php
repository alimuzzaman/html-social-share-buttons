<?php
namespace HtmlSocialShare;

interface SettingsInterface
{
    /**
     * Get a setting value by key, with optional default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Delete a setting.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
