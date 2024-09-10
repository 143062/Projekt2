<?php

class Router
{
    private static $routes = [];

    public static function get($url, $controller)
    {
        self::$routes['GET'][$url] = $controller;
    }

    public static function post($url, $controller)
    {
        self::$routes['POST'][$url] = $controller;
    }

    public static function run($url)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method][$url])) {
            echo "Page not found.";
            return;
        }

        $controllerAction = self::$routes[$method][$url];

        if (is_array($controllerAction)) {
            $controllerName = $controllerAction[0];
            $action = $controllerAction[1];

            $controller = new $controllerName;
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                echo "Method not found.";
            }
        } elseif (is_callable($controllerAction)) {

            $controllerAction();
        } else {
            echo "Invalid route configuration.";
        }
    }
}
