<?php

namespace App;

class Helper
{

    public static $obj = [];
    public static $delete = ['fields', 'table', 'find'];

    public static function serialize($obj, &$new)
    {
        if (is_array($obj) || is_object($obj)) {
            foreach ($obj as $key => $value) {
                if (is_object($value) && method_exists($value, 'getObject')) {
                    $new[$key] = $value->getObject();
                }
                if ((in_array($key, self::$delete))) {
                } elseif (is_object($value)) {
                    self::serialize($value, $new[$key]);
                } else if (is_array($value)) {
                    self::serialize($value, $new[$key]);
                } else {
                    $new[$key] = $value;
                }
            }
        }
    }

    public static function Json($obj)
    {
        $new = [];
        self::serialize($obj, $new);
        return json_encode($new);
    }
}
