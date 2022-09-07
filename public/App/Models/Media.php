<?php

namespace App\Models;

use App\Database\Model;

class Media extends Model
{
    public $table = "media";
    public $fields = ['id', 'model_type', 'model_id', 'collection_name', 'name', 'file_name', 'mime_type'];
    public $find = "model_id";
}
