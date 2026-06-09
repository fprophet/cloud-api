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

        private function normalize(string $path) : string{
            
            $normalizedPath = trim($path, "/");

            $path = SITE_ROOT . "/" . $normalizedPath;
            return $path;
        }

        public function resolve()
        {
            $requestMethod = $_SERVER["REQUEST_METHOD"];

            $requestUri = $_SERVER["REQUEST_URI"];

            $parsedUri = parse_url($requestUri);

            $path = $parsedUri["path"] ;

            if ( !isset( $this->routes[$requestMethod] ) 
                || !array_key_exists($path, $this->routes[$requestMethod])){
                return $this->notFound();
            }

            [$controllerClass, $method] = $this->routes[$requestMethod][$path]["controller"];

            $controller = new $controllerClass;

            $controller->{$method}();
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