<?php

namespace App\Database;

abstract class DBCommonMethods
{
    public function __construct(
        protected $host,
        protected $db,
        protected $uid,
        protected $password,
        protected $charset    = 'utf8mb4',
    ) {
    }
}
