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

        // 定义常量
        defined('MODULE') or define('MODULE', strtolower(request()->module()));
        defined('CONTROLLER') or define('CONTROLLER', strtolower(request()->controller()));
        defined('ACTION') or define('ACTION', strtolower(request()->action()));
        defined('AUTH_TOKEN') or define('AUTH_TOKEN', input('server.HTTP_AUTHORIZATION', ''));
        simple_logs(json_encode(['c' => CONTROLLER, 'a' => ACTION, 'data' => request()->param(),'token' => AUTH_TOKEN]));

        if (AUTH_TOKEN && ACTION !='login') \Session::session_id(AUTH_TOKEN);
    }
}