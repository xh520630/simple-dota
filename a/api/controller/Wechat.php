<?php
namespace app\api\controller;


use think\Controller;

class Wechat extends Controller
{
    //只能继承controller,不然和微信官方交互时报错
    public function _initialize()
    {
        parent::_initialize();

        if (!($table_id = intval(input('state',0)))) finish(201,'餐桌数据有误');
//        $table_id = 2;

        $field     =  's.appid, s.appsecret, s.app_token';
        $app_info  =  \think\Db::name('food_dining_table t')->where('t.id',$table_id)
            ->join('__SELLER__ s', 's.seller_id=t.seller_id')->field($field)->find();
        //通过当前餐桌获取商户ID,并且取得商户微信公众号信息
        defined("TOKEN")  or define("TOKEN" ,$app_info['app_token']);
        defined('APP_ID')  or define('APP_ID' ,$app_info['appid']);
        defined('APP_SEC') or define('APP_SEC',$app_info['appsecret']);
        defined('APP_URL') or define('APP_URL','https://api.weixin.qq
        .com/cgi-bin/token?grant_type=client_credential&appid=');
    }

    //接受微信消息
    public function wechat()
    {
        $echoStr = isset($_GET['echostr']) ? $_GET['echostr'] : '';
        if(empty($echoStr)){
            $this->response();
        } else{
            $this->valid();
        }
    }

    //验证
    public function valid()
    {
        /*
            获取微信发送过来的 echostr，随机字符串
        */
        $echoStr = $_GET['echostr'];

        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }else{
            file_put_contents('debug-1.txt', '验证码没通过');
        }
    }

    //验证详情
    public function checkSignature(){
        // 获取微信发送过来的校验结果，也就是微信官方已经做好的饭团
        $signature = $_GET['signature'];
        // 获取微信发送过来的材料之一，时间戳
        $timestamp = $_GET['timestamp'];
        // 获取微信发送过来的材料之一，随机数
        $nonce = $_GET['nonce'];
        // 双方都定义好的暗号
        $token = TOKEN;
        // 字典排序
        $tmpArr = array($token,$timestamp,$nonce);
        sort($tmpArr,SORT_STRING);

        // 将新排序后的数组再分割成字符串
        $tmpStr = implode($tmpArr);

        // sha1 加密
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            return true;
        }
        else{
            return false;
        }
    }


    //当需要获取用户详细信息时使用
    public function get_user_info($access_token = '',$open_id = '')
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$open_id&lang=zh_CN";
        $result  = $this->curlHttp($url,'GET','');
        $result = json_decode($result,true);
        return $result;
    }


    //CURL
    public function curlHttp($url,$method,$data){
        $curl=curl_init();
        //        var_dump($curl);
        //请求的URL
        curl_setopt($curl,CURLOPT_URL,$url);
        //请求的类型
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        //请求的数据结构
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        //请求的结果原样返回,而不是true/false
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        //http和https之间的关系你懂的
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);

        $result=curl_exec($curl);

        curl_close($curl);
        return $result;
    }

}