<?php

namespace App\Models;

use App\Database\Model;

class Social extends Model
{
    public $table = "social_accounts";
    public $fields = ['id', 'provider_name', 'provider_id', 'user_id'];
    public $find = "user_id";
}
