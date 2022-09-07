<?php

namespace App\Controller;

use App\Helper;
use App\Models\User;
use App\Request;

class UserController
{
    public function get(Request $req)
    {
        return Helper::Json(['result' => 'ok', 'fname' => $req->get('fname'), 'date' => $req->get('date'), 'user' => User::find($req->get('user_id') != null ? $req->get('user_id') : $req->get('id'))]);
    }

    public function all()
    {
        return Helper::Json(['result' => 'ok', 'user' => User::all()]);
    }

    public function store(Request $req)
    {
    }

    public function save(Request $req)
    {
        $user = User::find($req->get("user_id"));
        $user->fname = $req->get("fname");
        $user->save();
        return $user;
    }
}
