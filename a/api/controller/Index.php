<?php
namespace app\api\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        echo 'test_ok';
    }

    public function test()
    {
        echo 'ojbk';
    }

    // 获取英雄资料
    public function heroes_info()
    {
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