<?php

namespace app\core;

use app\configs\Config;
use app\errors\Errors;
use app\routes\Web;

class Core extends Web
{
    public function __construct()
    {
        $routes = $this->route();

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $normalizedUri = preg_replace('#/+#', '/', $requestUri);
        $normalizedUri = str_replace('../', '', $normalizedUri);

        if ($_ENV["WEB"] === "on") {
            $path = trim(parse_url($normalizedUri, PHP_URL_PATH) ?: '', "/");
        } elseif ($_ENV["WEB"] === "off") {
            $parsedPath = parse_url($normalizedUri, PHP_URL_PATH);
            if ($parsedPath !== null) {
                $path = trim(str_replace(Config::PROJECTNAME() . "/", "", $parsedPath), "/");
            } else {
                $path = '';
            }
        } else {
            Errors::_500_();
        }

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($routes[$method] as $route => $info) {
            if (preg_match("#^$route$#", $path, $matches)) {
                $data = $matches[1] ?? null;

                if (isset($info['middleware']) && !empty($info['middleware'])) {
                    $middlewareClass = "app\\middlewares\\" . $info['middleware'];
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if ($middlewareInstance->handle() === false) {
                            return;
                        }
                    }
                }

                if (class_exists($info['controller'])) {
                    $controller = new $info['controller'];

                    if (method_exists($controller, $info['method'])) {
                        if ($method === 'POST') {
                            $cleanPost = array_map(function ($item) {
                                return is_string($item) ? htmlspecialchars($item) : $item;
                            }, $_POST);

                            $controller->{$info['method']}($cleanPost, $data === null ? null : htmlspecialchars(urldecode($data)));
                        } else {
                            $controller->{$info['method']}($data === null ? null : htmlspecialchars(urldecode($data)));
                        }
                    } else {
                        Errors::_404_();
                    }
                } else {
                    Errors::_404_();
                }
                break;
            }
        }

        if (!isset($controller)) {
            Errors::_404_();
        }
    }

    private function route()
    {
        $routes = new Web();
        return $routes->routes();
    }
}
