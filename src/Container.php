<?php
namespace HtmlSocialShare;

/**
 * Very small service container for initial bootstrap.
 */
class Container
{
    private array $services = [];
    private array $instances = [];

    public function set(string $id, $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (! array_key_exists($id, $this->services)) {
            throw new \RuntimeException("Service '$id' not found");
        }

        $definition = $this->services[$id];

        if (is_callable($definition)) {
            $instance = $definition($this);
        } else {
            $instance = $definition;
        }

        $this->instances[$id] = $instance;

        return $instance;
    }
}
