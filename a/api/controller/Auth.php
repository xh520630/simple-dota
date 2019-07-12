<?php
namespace app\api\controller;


class Auth extends Base
{
    public function _initialize()
    {

        parent::_initialize();

        $un_need_login = ['login', 'register', 'reset_password', 'logout'];

//        if (CONTROLLER == 'message') return true;
        if (in_array(ACTION, $un_need_login)) return true;

        // 检查 token
        if (!AUTH_TOKEN && !in_array(ACTION, $un_need_login)) finish(401, '请登录');


        if (AUTH_TOKEN && !dbSession('user_id')) finish(401, '登录超时');
    }
}