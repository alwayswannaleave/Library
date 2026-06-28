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
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([0-9]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $paramNames = [];
                preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route['path'], $paramNames);
                $params = [];
                foreach ($paramNames[1] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }
                $request->setParams($params);

                $controller = new $route['controller']();
                $action = $route['action'];
                return $controller->$action($request);
            }
        }

        throw new \Exception('Route not found', 404);
    }
}
