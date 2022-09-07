<?php

namespace App\Models;

use App\Database\Model;

class User extends Model
{
    public $table = "users";
    public $fields = ['id', 'url', 'email', 'password', 'date_of_birth', 'ircc_no', 'proxy', 'user_proxy', 'password_proxy'];
    public $find = "id";

    public function connection()
    {
        return [
            'social' => '\App\Models\Social',
            'media' => '\App\Models\Media',
        ];
    }
}
