<?php
namespace app\api\controller;

use think\Controller;

class Index extends Controller
{
    public function _initialize()
    {
        parent::_initialize(); //
    }

    public function index()
    {
        echo 'test_ok';
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
            $cont = trim(input('content', ''));
            $user = trim(input('user', ''));
            if (!$cont || !$user) finish(201, '请求有误');
            $data = [];
            $data['content'] = $cont;
            $data['create_user'] = $user;
            $data['create_time'] = date('Y-m-d H:i:s');
            false === \think\Db::name('message')->insert($data) ?
                finish(201, '添加失败') : finish(200, '添加成功');
        }
        $condition = [];
        $message_list = \think\Db::name('message')->where($condition)->page(page())->order('id desc')->select();
        $message_count = \think\Db::name('message')->where($condition)->count();
        finish(200, '获取成功', ['total' => $message_count, 'list' => $message_list]);
    }

    // 上传文件
    public function upload()
    {
        echo 2333;exit;
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
//        $condition['hero_id'] = $id;
        $condition['hero_id'] = 2;
        $data = \think\Db::name('hero_ornament')->where($condition)->select();
        finish(200, '获取成功', $data);
    }

    // 获取物品详情
    public function ornament_detail()
    {
        if (!$o_id = intval(input('ornament_id', 0))) finish(201, '操作有误');
        $condition = [];
        $condition['o_id'] = 2;
//        $condition['o_id'] = $o_id;
        $data = \think\Db::name('ornament_images')->where($condition)->select();
        finish(200, '获取成功', $data);
    }
}