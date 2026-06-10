<?php

    require_once("Core/config.php");


    class Router {

        private $routes = array();

        public function get(string $path, array $controller) {
            
            $path = $this->normalize($path);

            $this->routes["GET"][$path]= [
                "controller"=> $controller,
                ];
        }

        public function post(string $path, array $controller) {
            
            $path = $this->normalize($path);

            $this->routes["POST"][$path]= [
                "controller"=> $controller,
                ];

        }

        public function delete(string $path, array $controller) {
            
            $path = $this->normalize($path);

            $this->routes["DELETE"][$path]= [
                "controller"=> $controller,
                ];

        }

        public function patch(string $path, array $controller) {
            
            $path = $this->normalize($path);

            $this->routes["PATCH"][$path]= [
                "controller"=> $controller,
                ];

        }

        private function normalize(string $path) : string{
            
            $normalizedPath = trim($path, "/");

            $path = SITE_ROOT . "/" . $normalizedPath;
            return $path;
        }

        public function resolve()
        {
            $requestMethod = $_SERVER["REQUEST_METHOD"];
            $requestUri    = $_SERVER["REQUEST_URI"];
            $path          = parse_url($requestUri, PHP_URL_PATH);

            if (!isset($this->routes[$requestMethod])) {
                return $this->notFound();
            }

            foreach ($this->routes[$requestMethod] as $routePath => $route) {
                $params = [];
                $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
                $pattern = '#^' . $pattern . '$#';
                if (preg_match($pattern, $path, $matches)) {
                    // pull only named params
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) $params[$key] = $value;
                    }

                    [$controllerClass, $method] = $route["controller"];
                    $controller = new $controllerClass;
                    $controller->{$method}($params);
                    return;
                }
            }

            return $this->notFound();
        }

        private function notFound()
        {
            http_response_code(404);
            echo json_encode([
                "error" => "Route not found"
            ]);
            return;
        }

    }

?>