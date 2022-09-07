<?php

namespace App\Controller;

use App\Helper;
use App\Models\Social;

class SocialController
{
    public function get(): string
    {
        return Helper::Json(Social::all());
    }
}
