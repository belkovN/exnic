<?php

namespace App;

class Request
{
    private  $rules;
    private $route;

    public function __construct($rules)
    {
        $this->rules = $rules['class']['2'];
        $this->route = $rules['route'];
    }

    public static function req($rules)
    {
        return new Request($rules);
    }

    public function get($param)
    {
        if ($this->rules == null) return null;
        $rules = str_replace('/', '\/', $this->route);
        preg_match('/^' . $rules . '$/u', $_SERVER['REQUEST_URI'], $m);


        if (isset($this->rules)) {
            $regex = explode(',', $this->rules);
            foreach ($regex as $key => $value) {
                list(,, $p) = explode(':', $value);
                if ($p == $param) {
                    if (isset($m[$key + 1]))
                        return $m[$key + 1];
                }
            }
        }

        $e = explode(',', $this->rules);
        foreach ($e as $v) {
            $ex = explode(":", $v);
            if ($param == $ex['2']) {
                if ($ex['0'] == "regex") {
                    if (isset($_POST[$ex['2']]) && preg_match('/^' . $ex['1'] . '$/u', $_POST[$ex['2']], $m)) {
                        return $m['0'];
                    } else {
                        return null;
                    }
                }
            }
        }
        return null;
    }
}
