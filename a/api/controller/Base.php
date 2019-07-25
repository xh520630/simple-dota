<?php
namespace app\api\controller;

use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {

        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: authorization');

        defined('CONTROLLER') or define('CONTROLLER', strtolower(request()->controller()));
        defined('ACTION') or define('ACTION', strtolower(request()->action()));
        defined('AUTH_TOKEN') or define('AUTH_TOKEN', input('server.HTTP_AUTHORIZATION'));

        $ip = $_SERVER["REMOTE_ADDR"];
        session('user_ip', $ip);
    }
}