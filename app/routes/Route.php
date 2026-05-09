<?php
namespace app\routes;

class Route
{
    static public $routes = [];

    
    static public function Get($path, $controller, $method, $middleware = null)
    {
       
        $path = trim($path, '/');  
        
        if (strpos($path, ":number") !== false) {
            $path = str_replace("/:number", "/([0-9]+)", $path);
        }
        if (strpos($path, ":all") !== false) {
            $path = str_replace("/:all", '/([^"<>\\\\{}|^~$&/]+)', $path);
        }
        
        $controllerClass = "app\\controllers\\" . $controller;
        
        self::$routes['GET'][$path] = [
            'controller' => $controllerClass,
            'method' => $method,
            'middleware' => $middleware 
        ];
        
        return self::$routes;
    }

    static public function Post($path, $controller, $method, $middleware = null)
    {
        if (strpos($path, ":number") !== false) {
            $path = str_replace("/:number", "/([0-9]+)", $path);
        }
        if (strpos($path, ":all") !== false) {
            $path = str_replace("/:all", '/([^"<>\\\\{}|^~$&/]+)', $path);
        }
        
        $controllerClass = "app\\controllers\\" . $controller;
        
        self::$routes['POST'][$path] = [
            'controller' => $controllerClass,
            'method' => $method,
            'middleware' => $middleware 
        ];
        
        return self::$routes;
    }
}