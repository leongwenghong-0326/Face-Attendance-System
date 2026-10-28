<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): void
    {
        // Convert route pattern with {param} into regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(Request $request): void
    {
        $uri = $request->uri();
        $method = $request->method();
        $matchMethod = ($method === 'HEAD') ? 'GET' : $method;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $matchMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Execute middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $result = $middleware->handle($request);
                    if ($result === false) {
                        return;
                    }
                }

                // Execute handler
                $handler = $route['handler'];
                $positionalParams = array_values($params);
                if (is_callable($handler)) {
                    call_user_func($handler, $request, ...$positionalParams);
                    return;
                }

                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    $controller->$action($request, ...$positionalParams);
                    return;
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        if ($request->isAjax()) {
            (new Response())->json(['error' => 'Route not found'], 404);
        } else {
            View::render('errors/404', ['title' => 'Page Not Found'], 'layouts/auth');
        }
    }
}
