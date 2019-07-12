<?php
namespace app\api\controller;

use think\Controller;
use think\Db;

class Wx extends Controller {

    public function index()
    {
        include KERNEL_PATH.'/class/WeChat.class.php';
        $seller   = intval(input('seller_id', 0));
        $field    = 'app_id, app_secret, app_token';
        $app_info = \think\Db::name('seller_config')->where('seller_id', $seller)->field($field)->find();
        $we_chat  = new \WeChat($app_info['app_id'], $app_info['app_secret']);
        $we_chat->verification($app_info['app_token']);
    }

    // 公众号支付回调
    public function js_pay()
    {
//$res_xml  = <<<xml
//<xml><appid><![CDATA[wx39793b74fcbc9f69]]></appid>
//<bank_type><![CDATA[CFT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[1493836222]]></mch_id>
//<nonce_str><![CDATA[mskjwmvspa1nlm8c179cfh3veh0s6csj]]></nonce_str>
//<openid><![CDATA[oEAv20msO4yitObhdvV-l6hefzRo]]></openid>
//<out_trade_no><![CDATA[2018060114210030]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[D001FCEB16007BE2C91AC540B03F5F42]]></sign>
//<time_end><![CDATA[20180530171116]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4200000122201805306971849617]]></transaction_id>
//</xml>
//xml;
        $res_xml  = file_get_contents("php://input");
        simple_logs($res_xml, 'wx_callback.txt');
        include_once(VENDOR_PATH.'/WxPay/lib/WxPay.Data.php');
        $sim_xml  = simplexml_load_string($res_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $api_res  = array_change_key_case((array)$sim_xml, CASE_LOWER);
        if ($api_res['return_code'] === 'SUCCESS' && $api_res['result_code'] === 'SUCCESS')
        {

            $order_sn = $api_res['out_trade_no'];
            $rData                 =  [];
            $rData['pay_time']     = date('Y-m-d H:i:s');
            $rData['pay_status']   = 1;
            $rData['out_trade_no'] = $order_sn;
            $rData['order_status'] = 3;
            $result = \think\Db::name('order_food')->where('order_sn',$order_sn)->update($rData);

//            $print  = new Prints();
//            $result = $print->add_prints($order_sn);
            $print  =  new \app\api\logic\Prints();
            $result = $print->add_prints($order_sn);
            if ($result)  die(static::wx_xml('SUCCESS', 'OK')); // 接受到成功消息后通知微信不重复发送
        }
    }

    // 微信支付回调
    public function pay_callback()
    {


//        $res_xml  = <<<xml
//<xml><appid><![CDATA[wx06f4e1057866b933]]></appid>
//<bank_type><![CDATA[CEB_CREDIT]]></bank_type>
//<cash_fee><![CDATA[100]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[1481564272]]></mch_id>
//<nonce_str><![CDATA[neddv7v43jixhtc1eceo98pvd0odefdk]]></nonce_str>
//<openid><![CDATA[obDZiv9kYLEuCuHidUUtvlCxUsro]]></openid>
//<out_trade_no><![CDATA[G1441499952350]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[09ADC7AEB087D7EC9512D4380CF6A61C]]></sign>
//<time_end><![CDATA[20170713212910]]></time_end>
//<total_fee>100</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4002042001201707130580461762]]></transaction_id>
//</xml>
//xml;
        $res_xml  = file_get_contents("php://input");
        simple_logs($res_xml, 'wx_callback.txt');
        $sim_xml  = simplexml_load_string($res_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $api_res  = array_change_key_case((array)$sim_xml, CASE_LOWER);

        // 签名验证
        $sign_res = $api_res['sign'];
        unset($api_res['sign']);
        ksort($api_res, SORT_STRING);
        $sign_str = http_build_query($api_res);
        $sign_str.= '&key=559c91e97b1a5df48299dd669852d8be';
        $sign_md5 = strtoupper(md5($sign_str));
//        if($sign_res!=$sign_md5) die(static::wx_xml('FAIL', '签名失败'));

//        $order_sn = $api_res['out_trade_no']; // 平台订单
//
//        $order_info = \think\Db::name('order')->where('order_sn', $order_sn)->find();
//        if(!$order_info) die;
//
//        if($order_info['order_status']!=1)
//        {
//            simple_logs("订单[$order_sn]支付状态更改失败:".json_encode($order_info), "pay_change_order.txt");
//        }
//
//        $rData = [];
//        $rData['pay_time']     = date('Y-m-d H:i:s');
//        $rData['pay_status']   = 1;
//        $rData['pay_type']     = 2;
//        $rData['order_status'] = 3;
//        $rData['out_trade_no'] = $api_res['transaction_id'];
//        if(!\think\Db::name('order')->where('order_sn', $order_sn)->update($rData))
//        {
//            write_logs("订单[$order_sn]支付状态更改失败", "pay_change_order.txt");
//        }

        die(static::wx_xml('SUCCESS', 'OK'));
    }


    // 微信支付回调
    static function wx_xml($state, $msg="")
    {
        return <<<xml
<xml>
    <return_code><![CDATA[{$state}]]></return_code>
    <return_msg><![CDATA[{$msg}]]></return_msg>
</xml>
xml;
    }
}
