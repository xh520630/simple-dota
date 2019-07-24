<?php

function finish($code = 200, $message = '', $data = [], $other = [])
{
    header('Content-Type: text/json');
    $data = ['code' => $code, 'message' => $message, 'data' => $data];
    $data = array_merge($data, $other);
    die(json_encode($data, JSON_UNESCAPED_UNICODE));
}


function encrypt_pass($password = '')
{
    return sha1(md5(sha1($password)));
}


if (!function_exists('page')) {
    function page()
    {
        return intval(input('page', 1)) . ',' . config('message_page_size');
    }
}


function is_ajax(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ('XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'])){
        return true;
    }
    return false;
}
