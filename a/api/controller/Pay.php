<?php
namespace app\v1\controller;

use think\Db;

class Pay
{
    // 支付宝回调
    public function alipay()
    {
        $api_res  = request()->param();
        simple_logs(json_encode($api_res), 'alipay.txt');
        if (!isset($api_res['sign'])) die('fail');
        // 签名验证
        include PROJECT_PATH."/vendor/Alipay/aop/AopClient.php";
        $config    = include PROJECT_PATH."/vendor/Alipay/config.php";
        $aopClient = new \AopClient();
        $aopClient->alipayrsaPublicKey = $config['alipay_public_key'];
//        if (!$aopClient->rsaCheckV1($api_res, null, 'RSA2')) die('fail');
//        dump($api_res);exit;
        if ($api_res['trade_status'] == 'TRADE_SUCCESS')
        {
            $rData = [];
            $rData['pay_time']     = date('Y-m-d H:i:s');
            $rData['pay_status']   = 1;
            $rData['pay_type']     = 2;
            $rData['order_status'] = 3;
            $rData['out_trade_no'] = $api_res['out_trade_no'];
            if (\think\Db::name('order_food')->where('order_sn', $api_res['out_trade_no'])->update($rData)) {
                $condition = [];
                $condition['order_type'] = 1;
                $condition['order_sn']   = $api_res['out_trade_no'];
                $rData = [];
                $rData['pay_type']     = 1;
                $rData['order_status'] = 3;
                $rData['pay_status']   = 1;
                $rData['pay_time']     = date('Y-m-d H:i:s');
                $rData['out_trade_no'] = $api_res['out_trade_no'];
                \think\Db::name('order')->where($condition)->update($rData);
                die('success');

            }
        }
    }

    // 微信支付回调
    public function wxpay()
    {
        $res_xml  = file_get_contents("php://input");
        simple_logs($res_xml, 'wx_pay.txt');
        $sim_xml  = simplexml_load_string($res_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $api_res  = array_change_key_case((array)$sim_xml, CASE_LOWER);
        if (!isset($api_res['out_trade_no'])) die('数据错误');

        $order_sn   = $api_res['out_trade_no']; // 平台订单
        $order_info = \think\Db::name('order')->where('order_sn', $order_sn)->find();
        if(!$order_info) die;

        if($order_info['order_status']!=1)
        {
            simple_logs("订单[$order_sn]支付状态更改失败:".json_encode($order_info), "wx_pay.txt");
            return static::wx_xml('FAIL', '状态出错');
        }

        $rData = [];
        $rData['pay_time']     = date('Y-m-d H:i:s');
        $rData['pay_status']   = 1;
        $rData['pay_type']     = 2;
        $rData['order_status'] = 3;
//        $rData['out_trade_no'] = $api_res['transaction_id'];
        if(!(\think\Db::name('order')->where('order_sn', $order_sn)->update($rData)))
        {
            simple_logs("订单[$order_sn]支付状态更改失败", "pay_change_order.txt");
        }
        return static::wx_xml('SUCCESS', 'OK');
    }


    // 微信支付回调
    static function wx_xml($state, $msg="")
    {
        return "<xml><return_code><![CDATA[{$state}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";
    }

    // 模拟回调
    public function pay_test()
    {
        $order_sn = input('order_sn');
        $data = json_decode('{"gmt_create":"2018-02-09 11:36:48","charset":"UTF-8","seller_email":"2538765678@qq.com","subject":"1518147390233375","sign":"FAsZzNkK9\/8lbL8L2SRqLHMG0TZcANPPkbIUCGPwtbdosVv+xof7pkVnyOyKOYADw\/z6+qPYz5RsAWuPL6Uw1aQoKYrAzCbi62+kQMTOGzW92SgENGX0AWWuiS\/XwevdUVulK9OQsqPEl6ms\/s\/a8PHHa9MhszSejnBCv8LdKmawdwkOw8Kek2+kDuKJe5ocgYc5KldNkZYAVsW8KgqdwhRAB5Q+quABssw53VcSArnGr1IEr3JjC2LA4pXV8Ttc8dpnipoiNnN3Ca7X3tlm\/K1eV0qgVOmAB36pqX01T3N36TdfUztj\/tYSihQML8MJ3\/3+7ylSX21n2pd3OgJVDQ==","body":"1518147390233375","buyer_id":"2088702009409993","invoice_amount":"0.10","notify_id":"05b6ab97efe2aa1426c38d99c0bf0bfnn2","fund_bill_list":"[{\"amount\":\"0.10\",\"fundChannel\":\"ALIPAYACCOUNT\"}]","notify_type":"trade_status_sync","trade_status":"TRADE_SUCCESS","receipt_amount":"0.10","app_id":"2017122101046337","buyer_pay_amount":"0.10","sign_type":"RSA2","seller_id":"2088921060849182","gmt_payment":"2018-02-09 11:36:48","notify_time":"2018-02-09 11:36:49","version":"1.0","out_trade_no":"1518147390233375","total_amount":"0.10","trade_no":"2018020921001004990282829085","auth_app_id":"2017122101046337","buyer_logon_id":"159****2951","point_amount":"0.00"}', true);
        $data['body']         = $order_sn;
        $data['out_trade_no'] = $order_sn;
        $data['trade_no']     = date('YmdHis').substr($order_sn, 0, 14);
        echo simple_curl(API_URL . '/v1/pay/alipay', http_build_query($data));
    }

}