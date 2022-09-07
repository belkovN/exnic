<?php

namespace App\Database;

use config\dbconfig;

class DB
{

    private static $oDB = null;

    public static function table($table)
    {
        if (self::$oDB != null) {
            self::$oDB->init();
            self::$oDB->table($table);
            return self::$oDB;
        }
        $driver = "\\App\Database\\" . dbconfig::$driver;
        self::$oDB = new $driver(
            dbconfig::$host,
            dbconfig::$db,
            dbconfig::$uid,
            dbconfig::$password,
        );
        self::$oDB->db_connect();
        self::$oDB->table($table);
        return self::$oDB;
    }
}
