<?php

declare(strict_types=1);

final class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:callable|array}> */
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->compile($path),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        if (str_starts_with($path, '/admin')) {
            layout('admin', 'admin/404', ['title' => 'Not found']);
            return;
        }
        layout('store', 'store/404', ['title' => 'Page not found']);
    }

    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            $controller->{$action}(...array_values($params));
            return;
        }
        $handler(...array_values($params));
    }

    private function compile(string $path): string
    {
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            return '#^/$#';
        }
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }
}
