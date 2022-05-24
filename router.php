<?php

namespace Router;

use Request\Request;

class Router
{
    public string $route;
    public string  $root;
    public string $queryString;
    public string  $requestMethod;
    public string  $requestURI;
    public string  $phpSelf;
    protected array $_get;
    protected array $_post;
    public function __construct()
    {
        $this->queryString = $_SERVER['QUERY_STRING'];
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestURI = $_SERVER['REQUEST_URI'];
        $this->phpSelf = $_SERVER['PHP_SELF'];
        $this->root = str_replace('/public/index.php', '', $this->phpSelf);
        $this->route = str_replace($this->root, '', $this->requestURI);
        $this->route = str_replace('?'.$this->queryString, '', $this->route);
        $this->_get = [];
        $this->_post = [];
    }
    public function get($route='/', $callback)
    {
        $this->_get[$route] = $callback;
    }
    public function post($route='/', $callback)
    {
        $this->_post[$route] = $callback;
    }
    public function resolveRoute()
    {
        // в зависимости от метода просматриваем массив
        $routes = ($this->requestMethod === 'GET') ? $this->_get : $this->_post;
        // разбиваем текущий роут на массив
        $current = explode('/', $this->route);
        $current_length = count($current);
        foreach($routes as $route => $callback)
        {
            $arr = explode('/', $route);
            if (count($arr) !== $current_length)
            {
                continue;
            }
            // проверяем в цикле
            // массив аргументов
            $argument = [];
            // $argument = null;
            $match = true;
            for ($i = 0; $i < $current_length; $i++){
                // аргумент
                if ($arr[$i][0] === ':')
                {
                    $argument[] = $current[$i];
                    // $argument = $current[$i];
                    continue;
                }
                if ($current[$i] !== $arr[$i])
                {
                    $match = false;
                }
            }
            if ($match)
            {
                $req = new Request();
                // создаем класс из массива вызова
                // [SomeClass::class, 'functionName']
                $conf = new $callback[0]($req);
                // функция строкой
                $func = $callback[1];
                if (count($argument))
                // if ($argument)
                {
                    // вызов с аргументом
                    $conf->$func(...$argument);
                    // $conf->$func($argument);
                } else
                {
                    $conf->$func();
                }
                return;
            }
        }
        // default
        $req = new Request();
        $callback = $routes['/'];
        // создаем класс из массива вызова
        // [SomeClass::class, 'functionName']
        $conf = new $callback[0]($req);
        // функция строкой
        $func = $callback[1];
        $conf->$func();
    }
}
?>