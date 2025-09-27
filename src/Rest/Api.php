<?php
namespace HtmlSocialShare\Rest;

class Api
{
    protected $routes = [];

    public function register(string $method, string $path, callable $handler)
    {
        $this->routes[strtoupper($method)][$path] = $handler;
    }

    public function dispatch(string $method, string $path, $payload = null)
    {
        $method = strtoupper($method);
        if (!isset($this->routes[$method][$path])) {
            return [ 'status' => 404, 'body' => 'Not Found' ];
        }

        $handler = $this->routes[$method][$path];
        return call_user_func($handler, $payload);
    }
}
