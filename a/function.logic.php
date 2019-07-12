<?php
// +----------------------------------------------------------------------
// | 公共逻辑函数
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://www.jiufengtec.com All rights reserved.
// +----------------------------------------------------------------------


/**
 * 验证手机码是否正确
 * @access  public
 * @param   string       $type      手机码类型
 * @return  int
 */

function verify_mobile_code($type = '')
{
    if (!($mobile = input('mobile', '', 'trim')))   finish(1, '手机号为空');
    if (!is_mobile($mobile))                        finish(1, '手机号格式不正确');

    $condition = [];
    $condition['mobile'] = $mobile;
    $condition['type']   = $type ? $type:input('type', '');
    $condition['send_time'] = ['gt', time() - 600];
    if (!($code = \think\Db::name('mobile_code')->where($condition)->order('id desc')->value('code'))) return false;
    return ($code != input('code', '')) ? 0:1;
}


// 七牛云存储
if (!function_exists('qiniu_token'))
{
    function qiniu_token($ak = '', $sk = '', $bucket = '')
    {
        $cache_file = PROJECT_PATH.'/storage/app/cache/QiNiu_Token_'.$bucket.'.cache';
        $cache_cont = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true):[];
        if (!isset($cache_cont['token']) || (time() - $cache_cont['time'])>3600)
        {
            include VENDOR_PATH."Qiniu/Qiniu.php";

            $policy = [
                'scope'    => $bucket,
                'deadline' => time() + 3600,
                'returnBody' => json_encode([
                    "name" => "$(key)",
                    "size" => "$(fsize)",
                    "w"    => "$(imageInfo.width)",
                    "h"    => "$(imageInfo.height)",
                    "hash" => "$(etag)",
                ]),
            ];

            $auth  = new \Qiniu\Auth($ak, $sk);
            $token = $auth->uploadToken($bucket, null, 3600, $policy);
            file_put_contents($cache_file, json_encode(['time' => time(), 'token' => $token]));
        }
        else
        {
            $token = $cache_cont['token'];
        }

        return $token;
    }
}

// 缩小用户头像
function avatar_image_slim($avatar = ''){
    return $avatar . '?imageView2/1/w/120/h/120/q/75|imageslim';
}

// 恢复正常用户头像
function avatar_image_normal($avatar = '')
{
    $avatar_arr = explode('?imageView2/1/w/120/h/120/q/75|imageslim', $avatar);
    return $avatar_arr[0];
}