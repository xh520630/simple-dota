<?php
namespace app\api\logic;


use think\Controller;

class Prints extends Controller
{
    public function add_prints($order_sn = 0)
    {
        if (!$order_sn) return false;

        $print_id   = \think\Db::name('order_food')->where('order_sn',$order_sn)->value('print_id');
        $condition  = [];
        $condition['o.order_sn']   =  $order_sn;
        $condition['o.pay_status'] =  1;
//        $field      = 'i.price, m.food_name, count(i.food_id) as num, i.food_id';

        $field      = 'm.food_name, o.order_id, m.food_id, m.print_id, i.price';
        $food_info  = \think\Db::name('order_food o')->field($field)->where($condition)
            ->join('__ORDER_FOOD_INFO__ i', 'i.order_id = o.order_id')
            ->join('__FOOD_MENU__ m', 'm.food_id = i.food_id')->select();

        foreach ($food_info as $key => $val) {
            $rData  = [];
            $rData['print_status']  = 0;
            $rData['print_time']    = date('Y-m-d H:i:s');
            $rData['machine_id']    = $food_info[$key]['print_id'];
            $rData['order_id']      = $food_info[$key]['order_id'];
            $rData['food_id']       = $food_info[$key]['food_id'];
            $str  = "\n--------------------------------\n";
            $str .= "\n{$food_info[$key]['food_name']} \n";
            $str .= "\n--------------------------------\n";
            $rData['print_content'] = $str;
            \think\Db::name('order_food_print')->insert($rData);
        }

//        return 1;
        $food_info    = [];
        $field        = 'm.food_name, o.order_id, m.food_id, m.print_id, i.price, count(m.food_id) as num';
        $food_info[]  = \think\Db::name('order_food o')->field($field)->where($condition)
            ->join('__ORDER_FOOD_INFO__ i', 'i.order_id = o.order_id')
            ->join('__FOOD_MENU__ m', 'm.food_id = i.food_id')->group('m.food_id')->select();

        $field2     = 'o.pay_time, o.out_trade_no, o.print_id, o.order_amount, o.order_id, o.order_sn, t.name, s.seller_name';
        $order_info = \think\Db::name('order_food o')->where($condition)
            ->join('__FOOD_DINING_TABLE__ t', 'o.table_id = t.id')
            ->join('__SELLER__ s', 's.seller_id = o.seller_id')
            ->field($field2)->find();
        foreach ($order_info as $key => $item) $food_info[$key] = $item;
        //清单

        $printStream = "\n商家名称:  {$food_info['seller_name']}\n";
        $printStream.= "\n桌号    :  {$food_info['name']}\n";
        $printStream.= "\n订单号  :  {$food_info['order_sn']}\n";
        $printStream.= "\n付款时间:  {$food_info['pay_time']}\n";
        $printStream.= "\n--------------------------------\n";
        foreach ($food_info[0] as $key => $item)
        {
            $price = sprintf('%7s', $item['price']);
            $num   = sprintf('%2s', $item['num']);
            $name  = $item['food_name'];
            $len   = strlen($name);
            if ($len > 24) {
                $last = mb_substr($name, 7, 1);
                $str  = '';
                if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $last))
                {
                    //末尾中文
                    $name_fir   = mb_substr($name, 0, 8, 'utf-8');
                    if (strlen($name_fir) < 24) {
                        for ($i = 0; $i < (24-strlen($name_fir))/3; $i++) $str.=' ';
                    }
                    $name_fir  .= $str;
                    $name_sec   = mb_substr($name, 8, 8, 'utf-8');

                } else {
                    //末尾非中文
                    $name_fir   = mb_substr($name, 0, 7, 'utf-8');
                    if (strlen($name_fir) < 24) {
                        for ($i = 0; $i < (24-strlen($name_fir))/3; $i++) $str.='  ';
                    }
                    $name_fir  .= $str;
                    $name_sec   = mb_substr($name, 7, 8, 'utf-8');

                }
                $printStream .= "\n$name_fir  x$num  $price\n";
                $printStream .= "\n$name_sec\n";
            } elseif ($len == 24) {
                $printStream .= "\n$name  x$num  $price\n";
            } else {
                $str  = '';
                for ($i=0; $i < (24-$len)/3; $i++) $str.='  ';
                $name = $name.$str;
                $printStream .= "\n$name  x$num  $price\n";
            }

//            $printStream .= "\n$name    x{$item['num']} ￥{$item['price']}\n\n";

        }
        $amount = sprintf('%7s',$food_info['order_amount']);
        $printStream.= "\n--------------------------------\n";
        $printStream.= "\n                 总计：$amount\n";
        $printStream.= "\n\n";
        $printStream.= "\n\n";


        $rData = [] ;
        $rData['print_status']  = 0;
        $rData['machine_id']    = $food_info['print_id'];
        $rData['order_id']      = $food_info['order_id'];
        $rData['food_id']       = 0;  //食物ID为0时打印的是用户清单
        $rData['print_time']    = date('Y-m-d H:i:s');
        $rData['print_content'] = $printStream;
        $result = \think\Db::name('order_food_print')->insert($rData);
        return 1;
    }
}