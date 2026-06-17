<?php

namespace App\Core;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(Request $request): array
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        error_log("Method: $method, URI: $uri");

        foreach ($this->routes as $route) {
            error_log('Comparing with: ' . $route['method'] . ' ' . $route['path']);

            if ($route['method'] === $method && $route['path'] === $uri) {
                $controller = new $route['controller']();
                $action = $route['action'];
                return $controller->$action($request);
            }
        }

        http_response_code(404);
        return ['error' => 'Route not found'];
    }
}
