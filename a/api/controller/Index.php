<?php
namespace app\api\controller;

class Index extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        if(!defined('SELLER_ID')) define('SELLER_ID', intval(input('seller', 0)));
    }

    //  点餐入口
    public function index()
    {
         // 有 code 从扫描进入
        if (($code = input("code", '', 'trim')))
        {
            $state = trim(input('state', ''));
            $state = json_decode($state, true);
            if (is_numeric($state))  {
                $seller_id = $state;
                $table_id  = 0;
            } else {
                $seller_id  =  $state['seller_id'];
                $table_id   =  $state['table_id'];
            }
            $field = 'app_id, app_secret';
            $app_info  =  \think\Db::name('seller_config')->where('seller_id', $seller_id)->field($field)->find();
            include KERNEL_PATH.'/class/WeChat.class.php';
            $we_chat = new \WeChat($app_info['app_id'], $app_info['app_secret']);
            $result  = $we_chat->open_id($code);
            $result  = json_decode($result, true);
            if (isset($result['openid']))
            {
                $open_id = $result['openid'];
                $where = ['platform'=>2,'uuid'=>$open_id];
                $user_id = \thinK\Db::name('user_bind')->where($where)->value('user_id');
                if(!$user_id){
                    $rData = [];
                    $rData['account'] = 'msid_' . uniqid();
                    $rData['register_time'] = date('Y-m-d H:i:s');
                    if($user_id = \think\Db::name('users')->insertGetId($rData)){
                        $rData = [];
                        $rData['user_id'] = $user_id;
                        $rData['uuid'] = $open_id;
                        $rData['nick_name'] = 'weixin';
                        $rData['platform'] = 2;
                        \think\Db::name('user_bind')->insertGetId($rData);
                    }

                }
                /*
                if (!($user = \think\Db::name('users')->where('open_id', $open_id)->find()))
                {
                    $rData['open_id']       = $open_id;
                    $rData['seller_id']     = $seller_id;
                    $rData['refresh_token'] = $result['refresh_token'];
                    $rData['create_time']   = date('Y-m-d H:i:s');
                    $user_id = \think\Db::name('users')->insertGetId($rData);
                    if ($user_id) $user['id']= $user_id;
                }
                */



               // simple_logs($user_id.'='.$open_id);

                dbSession(['uuid'=>$user_id]);
               // dbSession('account', $rData['account']);
                dbSession('user_id', $user_id);
                dbSession('open_id', $open_id);
            }
        }

        $rData = [];
        $rData['table']  = $table_id;
        $rData['token']  = \Session::session_id();
        $rData['seller'] = $seller_id;
        $this->redirect('http://diancan.jiufengtec.com/?'.http_build_query($rData).'&a=1');
        die;
    }

    // 获取菜单
    public function menu()
    {
        $condition = [];
        $condition['status']     = 0;
        $condition['seller_id']  = SELLER_ID;
        $field      = 'cat_id, cat_name';
        $categories = [];
        $category   = \think\Db::name('food_cat')->where($condition)->field($field)->order('sort_val')->select();
        foreach ($category as $item)
        {
            $item['food'] = [];
            $categories[$item['cat_id']] = $item;
        }

        $categories[0] = ['cat_id' => 0, 'cat_name' => '热卖', 'food' => []];

        $condition = [];
        $condition['seller_id']   = SELLER_ID;
        $condition['is_snapshot'] = 0;
        $condition['status']      = 0;

        $field     = 'food_id, cat_id, food_name, price, cover, description, stock, 0 as num, is_hot';
        $food      = \think\Db::name('food_menu')->where($condition)->field($field)->select();
        foreach ($food as $item) {
            $categories[$item['cat_id']]['food'][] = $item;
            if ($item['is_hot']) $categories[0]['food'][] = $item;
        }
        finish(0, '获取成功', $categories);
    }

    // 获取桌号
    public function table()
    {
        $condition = [];
        $condition['seller_id'] = SELLER_ID;
        $condition['status']    = ['not in','2'];
        $field     = 'id, name, status';
        $table_arr = \think\Db::name('food_dining_table')->field($field)->where($condition)->select();
        finish(0, '获取成功', $table_arr);
    }

    // 根据ID获取子地区
    public function district()
    {
        $condition   = [];
        $condition['parent_id'] = intval(input('pid', 0));
        $filed       = 'region_id as id, region_name as name';
        $region_rows = \think\Db::name('region')->field($filed)->where($condition)->select();
        finish(200, '获取成功', $region_rows);
    }


    // 商家列表
    public function seller()
    {
        $lng   = trim(input('lng', '0.000'));
        $lat   = trim(input('lat', '0.000'));
        $field = 's.seller_id, s.seller_name, s.cover, s.foods_avg_price as avg_price, s.trading_area as description, ';
        $field.= "(POWER(MOD(ABS(s.longitude - $lng), 360), 2) + POWER(ABS(s.latitude - $lat), 2)) AS distance";

        $condition = [];
        $condition['s.seller_id']   = ['gt', 0];
        $condition['f.is_snapshot'] = 0;
        if(($keyword = input('keyword', '', 'trim'))) $condition['f.food_name|s.seller_name']   = ['like', "%$keyword%"];

        $data = \think\Db::name('seller s')->field($field)->where($condition)
            ->join("__FOOD_MENU__ f", 'f.seller_id=s.seller_id', 'inner')
            ->group('s.seller_id')->select();

        finish(0, 'message', $data);
    }





    /*
     * name : app_index_banner APP首页banner广告
     * name : app_dishes_banner APP点餐banner广告
     * name : app_hotel_banner APP酒店banner广告
     * */
    public function ads(){
        $name = input('name','app_dishes_banner');
        action('mobile/index/ads',['pos_name'=>$name]);
    }
    // 猜你喜欢
    //public function guess(){
     //   action('mobile/index/guess');
  //  }
    // 商家详情信息
    public function seller_info(){
        $seller_id = intval(input('seller_id',0));
        action('mobile/index/seller_info',['seller_id'=>$seller_id]);
    }
    /*
     * 搜索点餐
     * seller_id 商户ID
     * keyword 搜索关键字
     * return array
     * */
    public function search(){
        $seller_id = intval(input('seller_id',0));
        $keyword = trim(input('keyword',''));
        action('mobile/index/search',['seller_id'=>$seller_id, 'keyword'=>$keyword]);
    }

    /*
     * 获取验证吗
     * mobile 手机
     * type 类型：register 注册 / reset_password 忘记密码
     * */
    public function send_mobile_code(){
        $mobile = trim(input('mobile', ''));
        $tye = trim(input('type','register'));
        action('mobile/index/send_mobile_code', ['mobile'=>$mobile,'type'=>'register']);
    }
    public function verify_mobile_code(){
        action('mobile/index/verify_mobile_code');
    }


    // 广告
    public function slide()
    {
        $condition = [];
        $condition['position_id'] = intval(input('position_id', 1));
        $condition['status']      = 1;
        $rData     = \think\Db::name('ads')->where($condition)->order('sort asc')->limit(5)->select();
        finish(0, '获取成功', $rData);
    }


    /* 首页推荐
     * page 1 分页
     * */
    public function recommend(){
        // 点餐
        $food_where = ['s.status'=>1,'f.is_snapshot'=>0,'f.status'=>0];
        $food_count =  \think\Db::name('seller s')
            ->join("__FOOD_MENU__ f",'f.seller_id=s.seller_id','inner')
            ->where($food_where)->group('s.seller_id')->count();
        // 酒店
        $hotel_where = ['s.status'=>['neq',3],'f.status'=>0];
        $hotel_count =  \think\Db::name('seller s')
            ->join("__HOTEL_LIST__ f",'f.seller_id=s.seller_id','inner')
            ->join("__ROOM_LIST__ r",'r.hotel_id=f.hotel_id','inner')
            ->where($hotel_where)->group('s.seller_id')->count();
        //echo \think\Db::name('seller s')->getLastSql();exit;
        //echo $hotel_count;exit;
        $group_where = ['s.status'=>['neq',3],'f.is_delete'=>0,'f.status'=>0];
        $group_count =  \think\Db::name('seller s')
            ->join("__GROUP_ACTIVITY__ f",'f.seller_id=s.seller_id','inner')
            ->where($group_where)->group('s.seller_id')->count();

        $food_start = $food_count > 3 ? rand(0,$food_count-3) : 0;
        $hotel_start = $hotel_count > 3 ? rand(0,$hotel_count-3) : 0;
        $group_start = $group_count > 3 ? rand(0,$group_count-3) : 0;

        $food = \think\Db::name('seller s')
            ->field("s.seller_id,1 as type, s.seller_name,s.cover,s.trading_area,s.foods_avg_price as avg_price")
            ->join("__FOOD_MENU__ f",'f.seller_id=s.seller_id','inner')
            ->where($food_where)->group('s.seller_id')->limit($food_start,3)->select();
        $hotel = \think\Db::name('seller s')
            ->field("s.seller_id,2 as type,f.hotel_id, s.seller_name,s.cover,s.trading_area,min(price) as lower_price")
            ->join("__HOTEL_LIST__ f",'f.seller_id=s.seller_id','inner')
            ->join("__ROOM_LIST__ r",'r.hotel_id=f.hotel_id','inner')
            ->where($hotel_where)->group('s.seller_id')->limit($hotel_start,3)->select();
        $group = \think\Db::name('seller s')
            ->field("s.seller_id,4 as type, s.seller_name,s.cover,s.trading_area,min(price) as lower_price")
            ->join("__GROUP_ACTIVITY__ f",'f.seller_id=s.seller_id','inner')
            ->where($group_where)->group('s.seller_id')->limit($group_start,3)->select();

        $result = array_merge($hotel,$food,$group);
        finish(0,'success',$result);
    }


    // 咨询列表
    public function info_list(){
        $page = intval(input('page',1));
        if(intval(input('type',0))){
            // 咨询 列表
            $page = $page > 0 ? $page : 1;
            $data = \think\Db::name('news i')
                ->field('i.id,i.title,i.summary,i.cover, a.nick_name, a.avatar')
                ->join('__ADMIN__ a','a.admin_id=i.admin_id','inner')
                ->where('i.status',0)
                ->page($page,10)
                ->order('i.id desc')
                ->select();
        }else{
            // 轮播
            $data = \think\Db::name('news i')
                ->field('i.id,i.title,is_hot')
                ->join('__ADMIN__ a','a.admin_id=i.admin_id','inner')
                ->where('i.status',0)
                ->page(1,5)
                ->order('i.id desc')
                ->select();
        }
        finish(0,'success',$data?$data:[]);
    }
    // 咨询详情
    public function info(){
        $id = intval(input('id',0));
        $where = ['i.id'=>$id,'i.status'=>0];
        $data = \think\Db::name('news i')
            ->field('i.id,i.title,i.summary,i.create_time,i.content,i.cover, a.nick_name, a.avatar')
            ->join('__ADMIN__ a','a.admin_id=i.admin_id','inner')
            ->where($where)
            ->find();
        if($data) finish(0,'success',$data);
        finish(201,'数据不存在');
    }


}