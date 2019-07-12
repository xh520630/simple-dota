<?php
namespace app\api\logic;




class Order
{

    // 下单
    public static function done()
    {
        if (!($diners_num = intval(trim(input('diners_num', 0)))))  finish(201, '请选择用餐人数');
        if (!($table_id   = intval(input('table_id', 0))))          finish(201, '请选择桌号');
        if (!($cart_arr  =  input('cart/a', [])))                   finish(201, '购物车中没有商品');


        $mobile = \think\Db::name('users')->where("user_id",dbSession('user_id'))->value('mobile');
        $seller_id = \think\Db::name('food_dining_table')->where('id', $table_id)->value('seller_id');
        $print_id  = \think\Db::name('seller_config')->where('seller_id',$seller_id)->value('print_machine');
        $rData     = [];
        $rData['note']          = trim(input('note',''));
        $rData['user_id']       = dbSession('user_id');
        $rData['diners_num']    = $diners_num;
        $rData['order_sn']      = substr(date('Y'),-2).date('mdHis').mt_rand(1000,9999);
        $rData['table_id']      = $table_id;
        $rData['order_amount']  = self::get_amount();
        $rData['order_time']    = date("Y-m-d H:i:s");
        $rData['seller_id']     = $seller_id;
        $rData['mobile']        = $mobile ? $mobile : '';
        $rData['order_status']  = 1;
        $rData['print_id']      = $print_id ? $print_id : 0;        //清单打单机机号

        \think\Db::startTrans();
        if(!$order_id = \think\Db::name('order_food')->insertGetId($rData)){
            \think\Db::rollback();
            finish(201,'下单失败1');
        }

        $data = [];
        $data['order_sn'] = $rData['order_sn'];
        $data['order_type'] = 1;
        $data['user_id'] = $rData['user_id'];
        $data['seller_id']     = $seller_id;
        $data['order_amount'] = 100 * $rData['order_amount'];
        $data['order_status'] = 0;
        $data['order_time'] = $rData['order_time'];
        $data['pay_type'] = intval(input('pay_type',1));
        if(!\think\Db::name('order')->insertGetId($data)){
            \think\Db::rollback();
            finish(201,'下单失败2');
        }

        $order_sn = $rData['order_sn'];

        foreach ($cart_arr as $key => $value) {
            $rData = [];
            $rData['food_id']        =   $cart_arr[$key]['food_id'];
            $price = \think\Db::name('food_menu')->where('food_id', $rData['food_id'])->value('price');
            $rData['food_num']       =   1;     //固定为1,有多个单子的话插入多条记录
            $rData['order_id']       =   $order_id;
            $rData['order_sn']       =   $order_sn;
            $rData['price']          =   $price;
            if ($cart_arr[$key]['num']>1){
                for ($num = 0; $num < $cart_arr[$key]['num']; $num ++){
                    if(!\think\Db::name('order_food_info')->insert($rData)){
                        \think\Db::rollback();
                        finish(201,'下单失败3');
                    }
                }
            } else {
                if(!\think\Db::name('order_food_info')->insert($rData)){
                    \think\Db::rollback();
                    finish(201,'下单失败4');
                }
            }
        }
        \think\Db::commit();
        return $order_sn;
    }

    // 获取总金额
    public static function get_amount()
    {
        if (! ($cart_arr  =  input('cart/a',[],'trim')))        finish(201,'信息有误');
        $amount = 0;
        foreach ($cart_arr as $key => $value) {
            $price = \think\Db::name('food_menu')->where('food_id',$cart_arr[$key]['food_id'])->value('price');
            $amount += $price * $cart_arr[$key]['num'];
        }
        return $amount;
    }

    // 判断红包是否能使用
    public static function bonus_permission($bonus_id = '')
    {
        
        $bonus_info = \think\Db::name('user_bonus b')->where('b.bonus_id', $bonus_id)
            ->join('__BONUS_TYPE__ bt', 'bt.type_id = b.bonus_type_id')->find();
        if (!$bonus_info)  finish(1, '红包信息有误');
//        dump($bonus_info);exit;
        if ($bonus_info['bonus_sn'] && !($bonus_sn = trim(input('bonus_sn', ''))))  finish(1, '请输入红包编号');
        if ($bonus_info['bonus_sn'] && ($bonus_info['bonus_sn'] != $bonus_sn))      finish(1, '红包编号有误');
        dump($bonus_info);
    }

    public static function order_list($row){
        $result= [];
        switch($row['order_type']){
            case '1':
                // 点餐
                $field = "order_id,pay_status,pay_type,order_status,is_accept,is_refund,is_comment";
                $result = \think\Db::name('order_food')->field($field)->where('order_sn',$row['order_sn'])->find();
                break;
            case '2':
                // 酒店
                $field = "order_id,status as order_status,pay_status,pay_method as pay_type";
                $result = \think\Db::name('order_hotel')->field($field)->where('order_sn',$row['order_sn'])->find();
                break;
            case '3':
                // 商城

                break;
            case '4':
                // 团购订单
                $field = "order_id,pay_status,pay_type,order_status,is_accept,is_refund,is_comment";
                $result = \think\Db::name('order_group')->where('order_sn',$row['order_sn'])->find();
                break;
            case '5':
                // 外卖订单

                break;
        }
        return $result;
    }

    public static function order_detail($row){
        $result= [];
        switch($row['order_type']){
            case '1':
                // 点餐
                $result = \think\Db::name('order_food')->where('order_sn',$row['order_sn'])->find();
                if($result){
                    $result['table_name'] = \think\Db::name('food_dining_table')->where('id',$result['table_id'])->value('name');
                    $result['table_name'] = $result['table_name'] ? $result['table_name'] : '';
                    // 点餐
                    $result['goods'] = \think\Db::name('order_food_info o')
                        ->field('o.food_id,o.food_num,o.price,o.served,f.food_name,f.cover')
                        ->join('__FOOD_MENU__ f','f.food_id=o.food_id','left')
                        ->where('order_sn',$row['order_sn'])
                        ->select();
                    $result['goods'] = $result['goods'] ? $result['goods'] : [];
                }
                break;
            case '2':
                // 酒店
                /*$result = \think\Db::name('order_hotel')->where('order_sn',$row['order_sn'])->find();
                if($result){
                    $result['goods'] = \think\Db::name('order_hotel_info o')
                        ->field('o.*,h.hotel_name,h.address,h.book_notice')
                        ->join('__HOTEL_LIST__ h','h.hotel_id=o.hotel_id','left')
                        //->join('__ROOM__LIST__ r','o.room_id=r.room_id','left')
                        ->where('order_id',$result['order_id'])->find();
                    if($result['goods']){
                        $result['goods']['room'] = \think\Db::name('room_list')->where('room_id',$result['goods']['room_id'])->find();
                        if($result['goods']['room']['support']){
                            $where_support = ['status'=>1,'id'=>['in',$result['goods']['room']['support']]];
                            $result['goods']['room']['support'] = \think\Db::name('room_support')->field('name')->where($where_support)->select();
                            if($result['goods']['room']['support']) $result['goods']['room']['support'] = get_array_column($result['goods']['room']['support'],'name');
                        }
                        if($result['goods']['room']['service']){
                            $where_support = ['status'=>0,'id'=>['in',$result['goods']['room']['service']]];
                            $result['goods']['room']['support'] = \think\Db::name('room_support')->field('name')->where($where_support)->select();
                            if($result['goods']['room']['service']) $result['goods']['room']['support'] = get_array_column($result['goods']['room']['support'],'name');
                        }
                        $where = ['order_id'=>$result['order_id'],'room_id'=>$result['goods']['room_id']];
                        $result['goods']['goods'] = \think\Db::name('hotel_book')->field('')->where($where)->select();
                    }
                }*/        $order_info = \think\Db::name('order_hotel o')->field("o.*,oh.*,h.hotel_name")
                ->where('o.order_sn',$row['order_sn'])
                ->join('__ORDER_HOTEL_INFO__ oh', 'oh.order_id=o.order_id')
                ->join('__HOTEL_LIST__ h', 'oh.hotel_id=h.hotel_id')->find();

//         字符串处理
                $order_info['days']       = (strtotime($order_info['leave_date']) - strtotime($order_info['live_date']) ) / 86400;
                $order_info['live_date']  = str_replace('-', '月', substr($order_info['live_date'], 5)).'号';
                $order_info['leave_date'] = str_replace('-', '月', substr($order_info['leave_date'], 5)).'号';
                $result = $order_info;
                break;
            case '3':
                // 商城

                break;
            case '4':
                // 团购订单
                $result = \think\Db::name('order_group')->where('order_sn',$row['order_sn'])->find();
                if($result){
                    if($result['coupon_id']){
                        $result['coupon_name'] = \think\Db::name('user_coupon')->where('id',$result['coupon_id'])->value('coupon_name');
                    }else{
                        $result['coupon_name'] = '';
                    }
                    $result['coupon_name'] = $result['coupon_name'] ? $result['coupon_name'] : '';
                    $order_detail = \think\Db::name('order_group_info')
                        ->field('id,group_id,group_name,type,cover,image,number,price,guarantee,group_remark,served,group_notice')
                        ->where('order_sn',$row['order_sn'])
                        ->find();
                    $order_detail['guarantee'] = $order_detail['guarantee'] ? json_decode($order_detail['guarantee'],JSON_OBJECT_AS_ARRAY) : [];
                    $order_detail['image'] = $order_detail['image'] ? json_decode($order_detail['image'], JSON_OBJECT_AS_ARRAY):[];
                    if($order_detail){
                       if(1 == $order_detail['type']){
                           // 套餐
                           $order_detail['goods'] = \think\Db::name('order_group_goods')
                               ->field('id,goods_name,number,unit,price,is_delete,served')
                               ->where('order_group_id',$order_detail['id'])
                               ->select();
                        }else if(2 == $order_detail['type']){
                           // 代金卷
                           $order_detail['goods'] = \think\Db::name('order_group_voucher')
                               ->field('id,number,amount,voucher_description as description')
                               ->where('order_group_id',$order_detail['id'])
                               ->select();
                       }else if(3 == $order_detail['type']){
                           // 足浴
                           $order_detail['goods'] = \think\Db::name('order_group_bath')
                               ->field('id,bath_name,price,number,unit,served')
                               ->where('order_group_id',$order_detail['id'])
                               ->select();
                       }
                        $order_detail['goods'] = $order_detail['goods'] ? $order_detail['goods'] : [];
                    }
                    $result['goods'] = $order_detail;
                    // 卷码
                    $code_result = \think\Db::name('order_group_code')
                        ->field('id,code,status')
                        ->where('order_id',$result['order_id'])
                        ->select();
                    $result['code'] = $code_result ? $code_result : [];
                }
                break;
            case '5':
                // 外卖订单

                break;
        }
        return $result;
    }



}