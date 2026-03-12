<?php
declare(strict_types=1);
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $middleware = [];

    public function get(string $path, string $controller, string $method = 'index', string $name = ''): self
    {
        return $this->addRoute('GET', $path, $controller, $method, $name);
    }

    public function post(string $path, string $controller, string $method, string $name = ''): self
    {
        return $this->addRoute('POST', $path, $controller, $method, $name);
    }

    public function put(string $path, string $controller, string $method, string $name = ''): self
    {
        return $this->addRoute('PUT', $path, $controller, $method, $name);
    }

    public function delete(string $path, string $controller, string $method, string $name = ''): self
    {
        return $this->addRoute('DELETE', $path, $controller, $method, $name);
    }

    private function addRoute(string $httpMethod, string $path, string $controller, string $method, string $name): self
    {
        $pattern = $this->pathToRegex($path);
        $route = [
            'method'     => $httpMethod,
            'path'       => $path,
            'pattern'    => $pattern,
            'controller' => $controller,
            'action'     => $method,
            'middleware' => [],
        ];
        $this->routes[] = $route;
        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
        return $this;
    }

    private function pathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): void
    {
        $httpMethod = $request->method();
        $uri = $request->uri();

        // Handle method override (_method hidden input)
        if ($httpMethod === 'POST' && $request->post('_method')) {
            $httpMethod = strtoupper($request->post('_method'));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $httpMethod) continue;
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                $controllerClass = 'App\\Controllers\\' . $route['controller'];
                if (!class_exists($controllerClass)) {
                    error_log('[TENIKO ROUTER] Controller not found: ' . $controllerClass);
                    $this->abort(500, "Controller not found: {$controllerClass}");
                    return;
                }
                $controller = new $controllerClass();
                $action = $route['action'];
                if (!method_exists($controller, $action)) {
                    error_log('[TENIKO ROUTER] Method not found: ' . $action . ' in ' . $controllerClass);
                    $this->abort(500, "Method {$action} not found.");
                    return;
                }
                $controller->$action($request);
                return;
            }
        }

        $this->abort(404, 'Page not found.');
    }

    public function route(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            return '/';
        }
        $url = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', (string)$value, $url);
        }
        return $url;
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        try {
            $view = new View();
            $view->render('errors/error', ['code' => $code, 'message' => $message]);
        } catch (\Throwable $e) {
            // Fallback if even the error view fails
            echo '<h1>' . $code . ' — ' . htmlspecialchars($message) . '</h1>';
            echo '<p><a href="/">Go Home</a></p>';
        }
    }
}
