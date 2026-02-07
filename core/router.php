<?php
class Router {
    private $routes = [];

    public function get($path, $controller, $method) {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post($path, $controller, $method) {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Base path'i kaldır
        $basePath = '/video-portal/public';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // index.php'yi kaldır
        $uri = str_replace('/index.php', '', $uri);
        
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        // Debug için
        error_log("Requested URI: " . $uri);
        error_log("Method: " . $method);

        if (isset($this->routes[$method][$uri])) {
            $this->callController($this->routes[$method][$uri], []);
            return;
        }

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->callController($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 - Sayfa Bulunamadı</h1>';
        echo '<p><strong>Aranan URI:</strong> ' . htmlspecialchars($uri) . '</p>';
        echo '<p><strong>Method:</strong> ' . $method . '</p>';
        echo '<h3>Kayıtlı Rotalar:</h3>';
        echo '<pre>' . print_r($this->routes[$method] ?? [], true) . '</pre>';
    }

    private function callController($handler, $params) {
        $controllerName = $handler['controller'];
        $method = $handler['method'];
        
        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            die("Controller bulunamadı: {$controllerName}");
        }

        require_once $controllerFile;
        $controller = new $controllerName();
        
        if (!method_exists($controller, $method)) {
            die("Method bulunamadı: {$method}");
        }

        call_user_func_array([$controller, $method], $params);
    }
}