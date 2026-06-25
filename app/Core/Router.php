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

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || $route['path'] !== $uri) {
                continue;
            }

            $controllerClass = $route['controller'];

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                return ['error' => "Controller $controllerClass not found"];
            }

            $controller = new $controllerClass();
            $action = $route['action'];

            if (!method_exists($controller, $action)) {
                http_response_code(500);
                return ['error' => "Method $action not found in $controllerClass"];
            }

            return $controller->$action($request);
        }

        http_response_code(404);
        return ['error' => 'Route not found'];
    }
}
