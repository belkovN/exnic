<?php

namespace App\Route;

final class Route
{

    private $route = [];
    private static $object = null;
    public $type;

    public function __toString()
    {
        return $this;
    }

    private function addRoute($route, $class, $type)
    {

        $class['2'] = (!isset($class['2'])) ? null : $class['2'];
        self::$object->route[$type][] = ['route' => $route, 'class' => $class];
        self::$object->type = $type;
    }

    public function where($param, $regex)
    {
        self::$object->route[$this->type][count(self::$object->route[$this->type]) - 1]['route'] = str_replace('{' . $param . '}', '(' . $regex . ')', self::$object->route[$this->type][count(self::$object->route[$this->type]) - 1]['route']);
        self::$object->route[$this->type][count(self::$object->route[$this->type]) - 1]['class'][2] .= (!isset(self::$object->route[$this->type][count(self::$object->route[$this->type]) - 1]['class'][2])) ? 'regex:(' . $regex . '):' . $param : ',regex:(' . $regex . '):' . $param;
        return $this;
    }

    public static function get($route, $class)
    {
        if (!(self::$object instanceof self)) {
            self::$object = new self();
        }
        self::$object->addRoute($route, $class, 'GET');
        return self::$object;
    }

    public static function post($route, $class)
    {
        if (!(self::$object instanceof self)) {
            self::$object = new self();
        }
        self::$object->addRoute($route, $class, 'POST');
        return self::$object;
    }


    public static function route($route, $type)
    {
        $routes = array_column(self::$object->route[$type], 'route');
        $key = array_search($route, $routes);
        if (is_int($key))
            return self::$object->route[$type][$key];
        else {
            foreach (self::$object->route[$type] as $key => $value) {
                $value['route'] = str_replace('/', '\/', $value['route']);
                if (preg_match('/^' . $value['route'] . '$/u', $route, $m)) {
                    return self::$object->route[$type][$key];
                }
            }
            return false;
        }
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
