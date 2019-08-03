<?php
namespace app\api\controller;

use think\Controller;

class Index extends Base
{
    public function _initialize()
    {
        parent::_initialize(); //
    }

    public function index()
    {
        echo 'test_ok';
        echo $_SERVER["HTTP_USER_AGENT"];
    }

    public function test()
    {
        echo 'ojbk';
    }

    // 添加/获取留言
    public function message()
    {
        if (request()->isPost())
        {
            $cont = trim(input('cont', ''));
            $user = trim(input('name', ''));
            if (!$cont || !$user) finish(201, '请填写完整内容');

            $condition = [];
            $condition['user_ip'] = session('user_ip');
            $condition['create_time'] = ['> time', date('Y-m-d H:i:s', time() - 10)];
            if (\think\Db::name('message')->where($condition)->find())
                finish(201, '短时间内请勿多次提交');

            $data = [];
            $data['content'] = $cont;
            $data['user_ip'] = session('user_ip');
            $data['create_user'] = $user;
            $data['create_time'] = date('Y-m-d H:i:s');
            false === \think\Db::name('message')->insert($data) ?
                finish(201, '添加失败') : finish(200, '添加成功');
        }
        $condition = [];
        $condition['is_audit'] = 1;
        $message_list = \think\Db::name('message')->where($condition)->page(page())->order('id desc')->select();
        $message_count = \think\Db::name('message')->where($condition)->count();
        finish(200, '获取成功', ['total' => $message_count, 'data' => $message_list]);
    }

    // 上传文件至七牛云
    public function uploadImg()
    {
//        if (!$hero_id = intval(input('hero_id', 0)))
//            finish(201, '操作有误');
        include VENDOR_PATH . 'php-sdk-master/autoload.php';

        $accessKey = config('qiniuKey')['aKey'];
        $secretKey = config('qiniuKey')['sKey'];
        $bucket    = config('qiniuKey')['bucket'];
        // 用于签名的公钥和私钥
        // 初始化签权对象
        $auth   = new \Qiniu\Auth($accessKey, $secretKey);
        $keyToOverwrite = 'FjPFxiAMdcV7KJNhsRVy0gKXsBaQ';
        $expires = 3600;
        $returnBody = '{"key":"http://qiniu.cs704.cn/$(key)","hash":"$(etag)","fsize":$(fsize),"bucket":"$(bucket)","code":"200"}';
        $policy = array(
            'returnBody' => $returnBody,
            'forceSaveKey'=> true,
            'saveKey' => "images/ornament/$(fname)"
        );
        $upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
        finish(200, '获取成功', ['token' => $upToken]);
    }

    // 上传文件
    public function upload()
    {
        $file = request()->file('file');
        $path = ROOT_PATH . DS . 'uploads';
        if(($info = $file->validate(['size'=>10*1024*1024,'ext'=>'jpg,png,gif,jpeg'])->move($path)))
        {
            $data = [];
            $data['url']    = domain().'/uploads/'.$info->getSaveName();
            die(json_encode(['code'=>200, 'message'=>'上传成功', 'data'=>$data]));
        }
        die(json_encode(['code'=>201, 'message'=>$file->getError()]));
    }

    // 获取英雄资料
    public function heroes_info()
    {
        // 记录登陆的IP及次数
        $ip = $_SERVER["REMOTE_ADDR"];
        $data = \think\Db::name('guest_log')->where('ip', $ip)
            ->whereTime('create_time', 'today')->find();
        if ($data) {
            $data['times'] = ++ $data['times'];
            $data['last_time'] = date('Y-m-d H:i:s', time());
            \think\Db::name('guest_log')
                ->where('id', $data['id'])
                ->update($data);
        } else {
            $data = [];
            $data['ip'] = $ip;
            $data['times'] = 1;
            $data['create_time'] = date('Y-m-d H:i:s', time());
            $data['last_time'] = date('Y-m-d H:i:s', time());
            \think\Db::name('guest_log')->insert($data);
        }


        $condition = [];
        $data = \think\Db::name('heroes')->where($condition)->select();
        $total = \think\Db::name('heroes')->where($condition)->count();
        $data_list = [];
        foreach ($data as $key => $val)
            $data_list[$val['attr']][] = $val;
        finish(200, '获取成功', ['total' => $total, 'data' => $data_list]);
    }

    // 获取英雄详情
    public function hero_detail()
    {
        if (!$id = intval(input('hero_id', 0))) finish(201, '操作有误');
        $condition = [];
        $condition['hero_id'] = $id;
//        $condition['hero_id'] = 2;
        $data = \think\Db::name('hero_ornament')->where($condition)->select();
        finish(200, '获取成功', $data);
    }

    // 获取物品详情
    public function ornament_detail()
    {
        if (!$o_id = intval(input('ornament_id', 0))) finish(201, '操作有误');
        $condition = [];
//        $condition['o_id'] = 2;
        $condition['o_id'] = $o_id;
        $data = \think\Db::name('ornament_images')->where($condition)->select();
        finish(200, '获取成功', $data);
    }

    // 添加物品
    public function addOrnament()
    {
        $data = [];
        $data['hero_id'] = intval(input('hero_id', 0));
        $data['name']    = trim(input('name', ''));
        $data['value']   = intval(input('rate', 0));
        $data['avatar']  = trim(input('avatar', ''));
        $data['origin']  = trim(input('desc', ''));
        $ornament_id = \think\Db::name('hero_ornament')->insertGetId($data);
        if (!$ornament_id) finish(201, '操作有误');

        $images_arr = input('gifArr/a', []);
        $data_arr = [];
        foreach ($images_arr as $key => $item)
        {
            $data = [];
            $data['o_id'] = $ornament_id;
            $data['url']  = $item['url'];
            $data['name']  = $item['name'];
            $data_arr[$key] = $data;
        }
        false === \think\Db::name('ornament_images')->insertAll($data_arr) ?
            finish(201, '操作失败') : finish(200, '操作成功');
    }

    // 错误提交
    public function errorSubmit()
    {
        $condition = [];
        $id = intval(input('id', 0));
        $error_type = intval(input('type', 0));

        $condition['error_id'] = $id;
        $condition['error_type'] = $error_type;
        $condition['user_ip']  = session('user_ip');
        if (\think\Db::name('error_log')->where($condition)->find())
            finish(201, '你已提交过该类型错误~');

        $data = [];
        $data['content'] = trim(input('desc', ''));
        $data['user_ip'] = session('user_ip');
        $data['error_id'] = $id;
        $data['error_type'] = $error_type;
        $data['create_time'] = date('Y-m-d H:i:s');
        \think\Db::name('error_log')->insert($data) ? finish(200, '提交成功~') : finish (201, '提交失败');
    }

    // 更新日志
    public function update_log()
    {
        $data = \think\Db::name('update_log ul')->order('ul.is_important DESC, ul.id DESC')->limit(5)->select();
        foreach ($data as &$item)
            $item['detail'] = \think\Db::name('update_detail_log')
                ->where('update_id', $item['id'])
                ->order('type ASC')->select();
        finish(200, '获取成功', $data);
    }
}