<?php
namespace app\api\controller;

use think\Db;

class User extends Auth
{

    public function _initialize()
    {
        parent::_initialize();
//        $un_need_login = ['register','login','change_mobile_step_1','change_mobile_step_2'];
//        if (!AUTH_TOKEN && !in_array(ACTION, $un_need_login)) finish(401, '请登录');
//        if (in_array(ACTION, $un_need_login)) return true;
//        if (AUTH_TOKEN && !dbSession('user_id')) finish(401, '登录超时');
    }

    // 用户注册
    public function register()
    {
        if (!($mobile = input('mobile', '', 'trim')))           finish(1, '请输入手机号');
        if (!($password = input('password', '')))               finish(1, '请输入密码');
        if (!is_mobile($mobile))                                finish(1, '手机号格式不正确');
//        if (!($code = input('code', '')))                       finish(1, '请输入手机码');

        $condition = [];
        $condition['mobile'] = $mobile;
        if (\think\Db::name('users')->where($condition)->find()) finish(1, '手机号已存在');

        $domain = $_SERVER['HTTP_REFERER'];
        if ($domain != 'http://mall_mobile.jcy.jiufengtec.com')  {
            if (!verify_mobile_code('register'))
                finish(1, '验证码不正确');
        }
        $rData = [];
        $rData['account']       = 'msid_' . uniqid();
        $rData['mobile']        = input('mobile', '', 'trim');
        $rData['password']      = encrypt_pass(input('password', ''));
        $rData['nick_name']     = input('nick_name', '');
        $rData['register_time'] = date('Y-m-d H:i:s');

        if($parent_id = intval(input('pid',0))){
            $where = ['user_id'=>$parent_id,'status'=>0];
            $rData['parent_id'] = \think\Db::name('users')->where($where)->value('user_id');
            $rData['parent_id'] = $rData['parent_id'] ? $rData['parent_id'] : 0;
        }

        if (!($user_id = \think\Db::name('users')->insertGetId($rData))) finish(1, '注册失败，请重试');

        // 获取抵用卷
        \app\mobile\logic\Coupon::give_coupon($user_id,1);
        // 推荐者记录
        $sn = trim(input('sn',''));
        if($rData['parent_id'] && $sn){
            \app\mobile\logic\Coupon::user_recommend($rData['parent_id'],$user_id,$sn);
        }
        $user_info = \think\Db::name('users')->where($condition)->find();

        dbSession(['uuid' => $user_info['user_id']]);
        dbSession('account', $user_info['account']);
        dbSession('user_id', $user_info['user_id']);
        finish(0, '注册成功', ['token'=>\Session::session_id(),'user_id'=>$user_info['user_id'],'account'=>$user_info['account']]);
    }


    // 用户登录
    public function login()
    {
        if (!($mobile   = input('username', ''))) finish(1, '请输入账号');
        if (!($password = input('password', ''))) finish(1, '请输入密码');

        $condition = [];
        $condition['mobile'] = $mobile;
        if (!($user_info = \think\Db::name('users')->where($condition)->find())) finish(1, '手机号不存在');
        if (!($user_info['password'] == encrypt_pass($password))) finish(1, '密码不正确');

        // 登录成功，生成 token
        dbSession(['uuid' => $user_info['user_id']]);
        dbSession('account', $user_info['account']);
        dbSession('user_id', $user_info['user_id']);
        finish(0, '登录成功', ['token'=>\Session::session_id(),'user_id'=>$user_info['user_id'],'account'=>$user_info['account']]);
    }

    public function sms_login(){
        if (!($mobile   = input('mobile', ''))) finish(1, '请输入账号');
        if (!($code = input('code', ''))) finish(1, '请输入短信验证码');

        $condition['mobile'] = $mobile;
        if (!($user_info = \think\Db::name('users')->where($condition)->find())) finish(1, '手机号不存在');
        if(!$res = verify_mobile_code('sms_login')) finish(201,'登录失败');
        // 登录成功，生成 token
        dbSession(['uuid' => $user_info['user_id']]);
        dbSession('account', $user_info['account']);
        dbSession('user_id', $user_info['user_id']);
        finish(0, '登录成功', ['token'=>\Session::session_id(),'user_id'=>$user_info['user_id'],'account'=>$user_info['account']]);
    }

    // 修改密码
    public function change_password()
    {
        if (!($old_pass = input('old_password', '')))   finish(1, '请输入旧密码');
        if (!($password = input('password', '')))       finish(1, '请输入密码');
        $condition = [];
        $condition['user_id'] = dbSession('user_id');
        $info = \think\Db::name('users')->where($condition)->find();
        if  ($info['password'] != encrypt_pass($old_pass))  finish(1, '原密码有误');
        false === \think\Db::name('users')->where($condition)->update(['password'=>encrypt_pass($password)]) ?
            finish(1, '修改失败') : finish(0, '修改成功');
    }

    // 重置密码
    public function reset_password()
    {
        if (!($mobile = input('mobile', '', 'trim')))                        finish(1, '请输入手机号');
        if (!($password = input('password', '')))                            finish(1, '请输入密码');
        if (!is_mobile($mobile))                                             finish(1, '手机号格式不正确');
        if (input('password', '') == '')                                     finish(1, '新密码不能为空');
        if (input('password', '') != input('confirm', ''))                   finish(1, '密码不一致');

        $domain = $_SERVER['HTTP_REFERER'];
        if ($domain != 'http://mall_mobile.jcy.jiufengtec.com')  {
            if (!verify_mobile_code('reset_password'))
                finish(1, '验证码不正确');
        }

        $condition = [];
        $condition['mobile'] = $mobile;
        if (!($info = \think\Db::name('users')->where($condition)->find()))  finish(1, '手机号不存在');

        $rData = [];
        $rData['password'] = encrypt_pass(input('password', ''));
        if (false === \think\Db::name('users')->where($condition)->update($rData))        finish(1, '重置失败');

        finish(0, '重置成功');
    }

    // 查看/修改用户信息
    public function profile()
    {
        $condition = [];
        $condition['user_id'] = dbSession('user_id');
        if (request()->isPost()) {
            $rData = [];
            if ($avatar      = trim(input('avatar', '')))               $rData['avatar']              = $avatar;
            //if ($email       = trim(input('email', '')))                $rData['email']               = $email;
            if ($gender      = intval(input('gender', 0)))              $rData['gender']              = $gender;
            if ($nick_name   = trim(input('nick_name', '')))            $rData['nick_name']           = $nick_name;
            if ($birthday    = trim(input('birthday', '')))   $rData['birthday']            = $birthday;
            //if ($occupation  = trim(input('occupation', '')))           $rData['occupation']          = $occupation;
            if ($signature   = trim(input('signature', '')))            $rData['signature']           = $signature;
            //if ($province    = intval(trim(input('province', 0))))      $rData['province']            = $province;
            //if ($city        = intval(trim(input('city', 0))))          $rData['city']                = $city;
            //if ($district    = intval(trim(input('district', 0))))      $rData['district']            = $district;
            //if ($address     = trim(input('address', '')))              $rData['address']             = $address;
            //if ($real_name   = trim(input('real_name', '')))            $rData['real_name']           = $real_name;
            //if ($hobby       = trim(input('hobby', '')))                $rData['hobby']               = $hobby;
            //if ($id_card     = trim(input('id_card', '')))              $rData['id_card']             = $id_card;
            //if ($expiry_date = trim(input('expiry_date', '')))          $rData['expiry_date']         = $expiry_date;
            //if ($id_card_f   = trim(input('id_card_photo_f', '')))      $rData['id_card_photo_f']     = $id_card_f;
            //if ($id_card_b   = trim(input('id_card_photo_b', '')))      $rData['id_card_photo_b']     = $id_card_b;
            //if ($id_card_h   = trim(input('id_card_photo_hand', '')))   $rData['id_card_photo_hand']  = $id_card_h;
            if(!$rData) finish(201,'非法操作');
            if (false === \think\Db::name('users')->where($condition)->update($rData)) finish(201, '保存失败');
            finish(0, '保存成功');
        } else {
            $field  = 'mobile, user_id, avatar, real_name, nick_name, gender,';
            $field .= 'province, city, district, signature, id_card_photo_f, id_card_photo_b, id_card_photo_hand, ';
            $field .= 'id_card, hobby, birthday, occupation, integral, experience';
            $user_info = \think\Db::name('users')->where($condition)->field($field)->find();
            if(!$user_info['avatar']) $user_info['avatar'] = 'http://'.$_SERVER['HTTP_HOST'].'/public/static/tou.png';
            finish(0, '获取成功', $user_info);
        }
    }

    // 修改手机号码第一步
    public function change_mobile_step_1()
    {
        if (!($mobile = input('mobile', ''))) finish(1, '请输入手机号');
        $info = \think\Db::name('users')->where('user_id', dbSession('user_id'))->find();
        $domain = $_SERVER['HTTP_REFERER'];
        if ($domain != 'http://mall_mobile.jcy.jiufengtec.com')  {
            if (!verify_mobile_code('change_phone_step_1'))
                finish(1, '验证码不正确');
        }
        if ($info['mobile'] != $mobile) finish(1, '原手机号不正确');
        finish(0, '原手机号码正确');
    }

    // 修改手机号码第二步
    public function change_mobile_step_2()
    {
        if (!($mobile = input('mobile', ''))) finish(1, '请输入手机号');
        $info = \think\Db::name('users')->where('mobile', $mobile)->find();
        $domain = $_SERVER['HTTP_REFERER'];
        if ($domain != 'http://mall_mobile.jcy.jiufengtec.com')  {
            if (!verify_mobile_code('change_phone_step_2'))
                finish(1, '验证码不正确');
        }
        if ($info && $info['user_id'] != dbSession('user_id')) finish(1, '已绑定其他账号');
        false === \think\Db::name('users')->where('user_id', dbSession('user_id'))->update(['mobile' => $mobile]) ?
        finish(1, '修改失败') : finish(0, '修改成功');
    }

    // 退出
    public function logout()
    {
        \Session::clear();
        finish(0, '注销成功');
    }


    // 以下都是原来的

    // 提交订单
    public function order_submit()
    {
        $result = \app\api\logic\Order::done();
        $result ? finish(0, '下单成功', ['order_sn' => $result]) : finish(201, '下单失败');
    }

    // 查询订单付款状态
    public function order_pay_status()
    {
        if (!($order_sn = intval(input('order_sn', ''))))  finish(201, '订单号错误');
        $result         = \think\Db::name('order_food')->where('order_sn', $order_sn)->value('pay_status');
        finish(0, '获取成功', ['pay_status' => $result]);
    }


    // 订单列表
    public function order_list()
    {
        $condition = [];
        $condition['o.user_id']   = dbSession('user_id');
        $condition['o.is_delete'] = 0;
        $condition['o.seller_id'] = intval(input('seller', 0));

        if ($order_sn = trim(input('order_sn', '')))  $condition['o.order_sn']  = $order_sn;
        if ($keyword  = trim(input('keyword', '')))   $condition['m.food_name'] = ['like', '%'.$keyword.'%'];
        $field   = ' o.order_id, o.order_sn, o.diners_num, o.order_amount, o.order_time, o.pay_status,';
        $field  .= ' o.pay_time, o.note, o.table_id, o.order_status';
        $order_list = \think\Db::name('order_food o')->field($field)
            ->where($condition)->order('order_id', 'desc')->page(page())->select();

        $field   = ' m.food_name, m.price, m.cover, i.food_num';
        foreach ($order_list as $key => $val) {
            $order_list[$key]['order_detail_food'] = \think\Db::name('order_food_info i')
                ->where('i.order_id', $order_list[$key]['order_id'])
                ->join('__FOOD_MENU__ m', 'm.food_id = i.food_id')
                ->field($field)->select();
        }
        finish(0, '获取成功', $order_list);
    }

    //订单详情
    public function order_detail()
    {
        $condition = [];
        if (!($order_sn = intval(input('order_sn')))) finish(201, '数据有误');
        $condition['o.order_sn']  = $order_sn;

        $field   = 'o.order_id, o.order_sn, o.diners_num, o.order_amount, o.order_time, o.pay_status, o.order_status,';
        $field  .= 'o.pay_time, o.note, o.order_status, o.table_id, t.name as table_name';
        $order_list = \think\Db::name('order_food o')->field($field)
            ->join('__ORDER_FOOD_INFO__ i','i.order_id = o.order_id')
            ->join('__FOOD_DINING_TABLE__ t', 't.id=o.table_id', 'LEFT')
            ->order('order_id','desc')->where($condition)->find();

        $field   = ' m.food_name, m.price, m.cover, i.food_num';
        $order_list['order_detail_food'] = \think\Db::name('order_food_info i')
            ->where('i.order_id',$order_list['order_id'])
            ->join('__FOOD_MENU__ m', 'm.food_id = i.food_id')
            ->field($field)->select();

        finish(0, '获取成功', $order_list);
    }

    //订单支付
    public function order_pay()
    {
        if (!($order_sn = intval(input('order_sn', 0)))) finish(1, '订单号错误');

        $field   = 'o.order_id, o.order_sn, o.diners_num, o.order_amount, o.seller_id, ';
        $field  .= 'o.order_time, o.pay_status, o.pay_time, o.note, o.table_id';
        $order_info = \think\Db::name('order_food o')->field($field)->distinct(true)
            ->join('__ORDER_FOOD_INFO__ i' ,'i.order_id = o.order_id')
            ->join('__FOOD_MENU__ m', 'm.food_id = i.food_id')
            ->order('o.order_id', 'desc')->where('o.order_sn',$order_sn)->find();

        $pay_type = input('pay_type', 0);
        if ($pay_type == 1)
        {
            // 支付宝支付
            define('ALIPAY_WAPPAY', PROJECT_PATH.'/vendor/Alipay/wappay');
            require_once ALIPAY_WAPPAY.'/service/AlipayTradeService.php';
            require_once ALIPAY_WAPPAY.'/buildermodel/AlipayTradeWapPayContentBuilder.php';

            $alipay_config = require PROJECT_PATH.'/vendor/Alipay/config.php';
            $alipay_config['return_url'] = '';

            $aop = new \AopClient;
            $aop->gatewayUrl = $alipay_config['gatewayUrl'];
            $aop->appId      = $alipay_config['app_id'];
            $aop->rsaPrivateKey = $alipay_config['merchant_private_key'];
            $aop->format    = "json";
            $aop->charset   = "UTF-8";
            $aop->signType  = "RSA2";
            $aop->alipayrsaPublicKey = $alipay_config['alipay_public_key'];

            $subject         = '聚春园';
            $out_trade_no    = $order_info['order_sn'];
            $timeout_express = "10m";
            $total_amount    = '0.01';//$order_info['order_amount']/100;

            $request = new \AlipayTradeAppPayRequest();

            $bizcontent = json_encode([
                'body'     => $subject,
                'subject'  => $subject,
                'out_trade_no'    => $out_trade_no,
                'timeout_express' => $timeout_express,
                'total_amount'    => $total_amount,
                'product_code'    => 'QUICK_MSECURITY_PAY'
            ]);

            $request->setNotifyUrl('http://life.jiufengtec.com/api/pay/alipay');
            $request->setBizContent($bizcontent);
            $response = $aop->sdkExecute($request);
            finish(0, '提交订单成功', $response);
        } else {
            // 微信支付
            $effective = 86400;
            include_once PROJECT_PATH.'/vendor/WxPay/lib/WxPay.Api.php';
            include_once PROJECT_PATH.'/vendor/WxPay/lib/WxPay.JsApiPay.php';
            include_once PROJECT_PATH.'/vendor/WxPay/lib/WxPay.Exception.php';
//        $fee   = intval(floor($order_info['order_amount'])) * 100;
            $fee   = 1;
            $app_id = \think\Db::name('seller_config')->where('seller_id', $order_info['seller_id'])->value('app_id');
            $input  = new \WxPayUnifiedOrder();
//        $input->SetAppid($app_id);
            $input->SetBody('订单：'.$order_info['order_sn']);
            $input->SetAttach("");
            $input->SetOut_trade_no($order_info['order_sn']);
            $input->SetTotal_fee($fee);
            $input->SetTime_start(date('YmdHis'));
            $input->SetTime_expire(date("YmdHis", time() + $effective));
            $input->SetGoods_tag('订单：'.$order_info['order_sn']);
            $input->SetNotify_url('http://life.jiufengtec.com/api/wx/js_pay');
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid(dbSession('open_id'));
            $order = \WxPayApi::unifiedOrder($input);
//simple_logs(dbSession('open_id'));
            $tools = new \JsApiPay();
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $editAddress     = $tools->GetEditAddressParameters();
            finish(0, '获取成功', ['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress]);
        }
    }

    // 反馈
    public function add_feedback()
    {
        if (!input('content', '', 'trim'))     finish(1, '请输入内容');
        if (!input('type', '', 'trim'))        finish(1, '请输入类型');
        $rData = [];
        $rData['user_id']     = dbSession('user_id');
        $rData['type']        = input('type', '', 'trim');
        $rData['content']     = input('content', '', 'trim');
        $rData['mobile']      = input('mobile', '', 'trim');
        $rData['image']       = json_encode(input('image/a', []));
        $rData['create_time'] = date('Y-m-d H:i:s');
        \think\Db::name('feedback')->insert($rData) ? finish(0, '提交成功'):finish(1, '提交失败');
    }
    // 订单列表
    public function orders(){
        $page = intval(input('page',1));
        $size = 10;
        $user_id = dbSession('user_id');
        $where = ['is_delete'=>0,'user_id'=>$user_id];
        if($order_type = intval(input('order_type',0))) $where['order_type'] = $order_type;
        $result = \think\Db::name('order')->where($where)->page($page,$size)->order('order_time desc')->select();
        if($result){
           // $logic_order = new  \app\api\logic\Order();
            foreach($result as &$value){
                // 商家
                if($value['seller_id']){
                    $value['seller_info'] = \think\Db::name('seller')
                        ->field('seller_id,cover,seller_name,mobile')
                        ->where('seller_id',$value['seller_id'])
                        ->find();
                    $value['cover'] = $value['seller_info']['cover'];
                }else{
                    $value['seller_info'] = [];
                    $value['order']['cover'] = '';
                }
                $value['order_amount'] = 0.01*$value['order_amount'];
                //$cover = '';
                if(1 == $value['order_type']){
                    // 点餐
                   // $field = "order_id,pay_status,pay_type,order_status,is_accept,is_refund,is_comment";
                    //$value['order'] = \think\Db::name('order_food')->field($field)->where('order_sn',$value['order_sn'])->find();

                }else if(2 == $value['order_type']){
                    // 酒店
                    $hotel_id  = \think\Db::name('order_hotel o')
                        ->join('__ORDER_HOTEL_INFO__ oh','oh.order_id=o.order_id','inner')
                        ->where('o.order_sn',$value['order_sn'])
                        ->value('oh.hotel_id');
                    if($hotel_id){
                        $cover = \think\Db::name('hotel_list')->where('hotel_id',$hotel_id)->value('cover');
                        if($cover) $value['cover'] = $cover;
                    }
                }else if(4 == $value['order_type']){
                    // 团购
                   $group = \think\Db::name('order_group o')
                        ->join('order_group_info og','og.order_id=o.order_id','inner')
                       ->field('og.type,og.group_id')
                        ->where('o.order_sn',$value['order_sn'])
                        ->find();
                   if($group){
                       if(in_array($group['type'],[1,3])){
                           $cover = \think\Db::name('group_activity')->where('group_id',$group['group_id'])->value('cover');
                           if($cover) $value['cover'] = $cover;
                       }
                   }
                }
            }
        }
        finish(0,'success',$result);
    }

    public function order_info(){
        $order_sn = trim(input('order_sn',''));
        $order_id = intval(input('order_id',0));
        $where = ['is_delete'=>0];
        if($order_id){
            $where['order_id'] = $order_id;
        }else{
            $where['order_sn'] = $order_sn;
        }
        $row = \think\Db::name('order')->field('order_id,order_sn,api_order_sn,order_type')->where($where)->find();
        if(!$row) finish(201,'订单不存在或已删除');
        if(!$data = \app\api\logic\Order::order_detail($row)){
            finish(201,'订单不存在或已删除');
        }
        $data['seller_info'] = \think\Db::name('seller')
            ->field('seller_id,cover,seller_name,mobile,address')
            ->where('seller_id',$data['seller_id'])
            ->find();
        $data['seller_info'] = $data['seller_info'] ? $data['seller_info'] : [];
        $data['order_type'] = $row['order_type'];
        finish(0,'success',$data);
    }

    // 添加删除收藏
    public function collect(){
        if(request()->isPost()){
            \app\api\logic\Collect::add_collect();
        }
    }
    // 抵用卷列表
    public function coupon_list(){
        $type = intval(input('type',0));
        if(!in_array($type,[0,1])) finish(201,'非法操作');
        $result = \app\mobile\logic\Coupon::get_list($type ? 0 : '1,2');
        finish(0,'success',$result);
    }
    // 消息列表
    public function message_list(){

        $user_id = dbSession('user_id');
        $notice_id = \think\Db::name('notice')->where('status',1)->order('id desc')->value('id');
        $notice_id = $notice_id ? $notice_id : 0;
        \think\Db::name('users')->where('user_id',$user_id)->setField('notice_id',$notice_id);

        $page = intval(input('page',1));
        $page = $page > 1 ? $page : 1;
        $size = 10;
        $data = \think\Db::name('notice')->field('id,title,summary,publish_time')->where('status',1)->page($page,$size)->order('publish_time desc')->select();
        if($data){
            $today = date("Y-m-d")." 00:00:00";
            $today = strtotime($today);
            $yesterday = $today - 24 * 60 * 60;
            $beforeday = $today - 2 * 24 * 60 * 60;
            foreach($data as $key => $val){
                $publish_time = strtotime($val['publish_time']);
                if($today <= $publish_time){
                    $data[$key]['publish_time'] = '今天 '.date('H:i');
                }else if($today > $publish_time && $yesterday <= $publish_time){
                    $data[$key]['publish_time'] = '昨天 '.date('H:i');
                }else if($beforeday <= $publish_time && $yesterday > $publish_time){
                    $data[$key]['publish_time'] = '前天 '.date('H:i');
                }else{
                    $data[$key]['publish_time'] = date('Y-m-d H:i');
                }
                //$data[$key]['uri'] = url('message_detail?id='.$val['id']);
            }
        }else{
            $data = [];
        }
        finish(0,'success', $data ? $data : []);
    }
    // 消息详情
    public function message(){
        $id = intval(input('id',0));
        $where = ['id'=>$id,'status'=>1];
        $today = date("Y-m-d")." 00:00:00";
        $today = strtotime($today);
        $yesterday = $today - 24 * 60 * 60;
        $beforeday = $today - 2 * 24 * 60 * 60;
        if($data = \think\Db::name('notice')->where($where)->find()){
            $publish_time = strtotime($data['publish_time']);
            if($today <= $publish_time){
                $data['publish_time'] = '今天 '.date('H:i');
            }else if($today > $publish_time && $yesterday <= $publish_time){
                $data['publish_time'] = '昨天 '.date('H:i');
            }else if($beforeday <= $publish_time && $yesterday > $publish_time){
                $data['publish_time'] = '前天 '.date('H:i');
            }else{
                $data['publish_time'] = date('Y-m-d H:i');
            }
            finish(0,'success',$data);
        }
        finish(201,'数据不存在');
    }

    public function user_recommend(){
        $type = intval(input('type',0));
        $where = [];
        if(0 == $type){
            // 本月推荐
            $where = ' AND year(u.register_time) = year(now()) AND month(u.register_time) = month(now())';
            $result = \app\mobile\logic\Coupon::user_recommend_list($where);
        }else if(1 == $type){
            // 全部推荐
            $result = \app\mobile\logic\Coupon::user_recommend_list();
        }else if(2 == $type){
            // 未下单的用户
            $where = ' and r.order_id = 0';
            $result = \app\mobile\logic\Coupon::user_recommend_list($where);
        }else{
            finish(201,'非法操作');
        }
        finish(0,'success',$result);
    }

    public function refund_detail(){
        $id = intval(input('id',0));

    }

    // 申请退款
    public function submit_refund(){
        if(request()->isPost()){

            $rData = [];
            $rData['user_id'] = dbSession('user_id');
            $rData['order_id'] = intval(input('order_id',0));
            $where = ['is_delete'=>0,'order_id'=>$rData['order_id']];
            $order_row = \think\Db::name('order')->field('order_id,order_sn,order_status,is_appeal,is_refund')->where($where)->find();
            if(4 != $order_row['order_type']) finish(201,'订单不能申请退款');
            if(6 == $order_row['order_status'] ) finish(201,'订单已完成,不能申请退款');
            if(2 == $order_row['order_status'] ) finish(201,'订单已取消,不能申请退款');
            if(0 == $order_row['pay_status']) finish(201,'订单未付款,不能申请退款');
            if(1 == $order_row['is_refund']) finish(201,'订单已退款');

            $rData['refund_reason_id'] = trim(input('reason_id/a',[]));

            $code_ids = intval(input('order_group_code_id/a',[]));
            if(!$code_ids) finish(201,'请选择团购订单兑换码');
            foreach($code_ids as $val){
                if(!is_numeric($val) || $val <= 0) finish(201, '非法操作');
            }

            $rData['refund_reason_id'] = trim(input('reason_id/a',[]));
            if(!$rData['refund_reason_id']) finish(201,'请选择退款理由');
            $reason_where = ['is_delete'=>0,'id'=>['in',join(',',$rData['refund_reason_id'])]];
            $count = \think\Db::name('refund_reason')
                ->where($reason_where)
                ->count();
            if(!$count || count($rData['refund_reason_id']) != $count) finish(201,'退款理由错误');
            $rData['refund_reason_id'] = join(',',$rData['refund_reason_id']);
            \think\Db::startTrans();
            if(!$order_refund_id = \think\Db::name('order_refund')->insertGetId($rData)){
                \think\Db::rollback();
                finish(201,'更新失败');
            }

            $data = [];
            $data['order_refund_id'] = $order_refund_id;
            if($code_ids){
                foreach($code_ids as $value){
                    $data['order_group_code_id'] =$value;
                    if(!\think\Db::name('order_refund_code')->insertGetId()){
                        \think\Db::rollback();
                        finish(201,'更新失败');
                    }
                }
            }
            \think\Db::commit();
            finish(0,'更新成功');
        }
    }

}