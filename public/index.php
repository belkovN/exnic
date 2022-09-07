<?php

declare(strict_types=1);

ini_set('display_errors', 1);
require '../vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

use App\Request;
use App\Route\Route;
use App\Helper;
use App\Regex;




Route::get('/user/list', ['App\Controller\UserController', 'all']);

Route::get('/', ['App\Controller\SocialController', 'get']);

Route::post('/user/{user_id}', ['App\Controller\UserController', 'get'])
    ->where('user_id', '\d+')
    ->where('date', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])')
    ->where('fname', '[А-Яа-яA-Za-z0-9]+');

Route::post('/user/save', ['App\Controller\UserController', 'save', 'regex:(\d+):user_id,regex:([a-zA-ZА-Яа-я]+):fname']);
Route::post('/user/store', ['App\Controller\UserController', 'save', 'regex:(\d+):user_id,regex:([a-zA-ZА-Яа-я]+):fname']);

Route::get('/user/{id}/{date}', ['App\Controller\UserController', 'get'])
    ->where('id', Regex::int())
    ->where('date', Regex::date());



$uri = parse_url($_SERVER['REQUEST_URI']);
$route = Route::route($uri['path'], $_SERVER['REQUEST_METHOD']);

if (!$route) die(Helper::Json(['return' => 404]));

$controller = new $route['class']['0'];
echo call_user_func([$controller, $route['class']['1']], Request::req($route));
