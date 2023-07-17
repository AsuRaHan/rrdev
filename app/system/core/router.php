<?php

namespace rrdev\core;

class Router {
    private $routes=[];
    private $url='';
    public function __construct() {
        // инициализация правил маршрутизации
        $this->routes = include CONFIG_DIR . 'routes.php';
        $this->GetUrl();
    }

    public function addRoute($url, $controller, $action, $params = []) {
        // добавление правила маршрутизации
        $this->routes[] = [
            'path' => "$url",
            'controller' => $controller,
            'action' => $action,
            'params' => $params,
        ];
    }
    /**
     * Функция получения запроса который пришел от пользователя приложением
     */
    private function GetUrl() {
        $pathInfo = filter_input(INPUT_SERVER, 'PATH_INFO');
        if ($pathInfo) {
            $path = $pathInfo;
        } else {
            $requestURI = filter_input(INPUT_SERVER, 'REQUEST_URI');
            dd($requestURI);
            if (strpos($requestURI, '?')) {
                $requestURI = substr($requestURI, 0, strpos($requestURI, '?'));
            } elseif (strpos($requestURI, '&')) {
                $requestURI = substr($requestURI, 0, strpos($requestURI, '&'));
            }
            $path = trim($requestURI);
        }
        if (!$path) {
            $path = '/';
        }
        $path = parse_url($path);
        $this->url = $path['path'];
    }
    public function match() {

        foreach ($this->routes as $route) {
            if (preg_match("#^{$route['path']}$#", $this->url, $matches)) {
                $params = array_slice($matches, 1);
                $routeParams = isset($route['params']) ? $route['params'] : [];
                return [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                    'params' => array_merge($routeParams, $params),
                ];
            }
        }
        // автороутинг
        $urlParts = explode('/', trim($this->url, '/'));
        $controllerName = '';
        $actionName = '';
        $params = [];
        if (!empty($urlParts[0])) {
            $controllerName = ucfirst($urlParts[0]) . 'Controller';
        }
        if (!empty($urlParts[1])) {
            $actionName = $urlParts[1];
        }
        if (!empty($urlParts[2])) {
            $params = array_slice($urlParts, 2);
        }

        return [
            'controller' => $controllerName,
            'action' => $actionName,
            'params' => $params,
            ];
    }
}