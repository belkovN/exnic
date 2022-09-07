<?php

namespace App\Interface;

interface IAuth
{
    public static function verifyCredentials(): bool;
}
