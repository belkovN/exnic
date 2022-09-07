<?php

namespace App;

class Regex
{

    public static function date(): string
    {
        return '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])';
    }

    public static function int(): string
    {
        return '(\d+)';
    }

    public static function flot(): string
    {
        return '\-?\d+(\.\d{0,})?';
    }
}
