<?php

namespace App;

class Regex
{

    public static function date(): string
    {
        return '[0-9]{4}-[0-9]{2}-[0-9]{2}';
    }

    public static function int(): string
    {
        return '\d+';
    }

    public static function flot(): string
    {
        return '\-?\d+(\.\d{0,})?';
    }
}
