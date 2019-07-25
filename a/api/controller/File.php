<?php
namespace app\api\controller;


class File extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        if (!$auth = input('token', 'ritian250'))
            finish(201, '访问有误');
        if ($auth !== 'ritian250')
            finish(202, '访问有误');

    }

    public function index()
    {
        echo  233;
    }

    public function upload()
    {
        include VENDOR_PATH . 'php-sdk-master/autoload.php';

        $accessKey = 'ZqD2OsF49FzDhLDpXLNwBV4bdl5bdlKrPAAn4X7Q';
        $secretKey = 'Gfy46eaEsXBexRAmrASd8hRT6FXdNZYgYu3LUsP-';
        $bucket    = 'ritian';
        // 用于签名的公钥和私钥
        // 初始化签权对象
        $auth   = new \Qiniu\Auth($accessKey, $secretKey);
        $keyToOverwrite = 'FjPFxiAMdcV7KJNhsRVy0gKXsBaQ';
        $expires = 3600;
        $returnBody = '{"key":"$(key)", "hash":"$(etag)", "code":"200"}';
        $policy = array(
            'returnBody' => $returnBody
        );
        $upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
        $this->assign('token', $upToken);
    }

    public function test()
    {

        include VENDOR_PATH . 'php-sdk-master/autoload.php';

        $accessKey = 'ZqD2OsF49FzDhLDpXLNwBV4bdl5bdlKrPAAn4X7Q';
        $secretKey = 'Gfy46eaEsXBexRAmrASd8hRT6FXdNZYgYu3LUsP-';
        $bucket    = 'ritian';
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
//        $this->assign('key', $keyToOverwrite);
        $this->assign('token', $upToken);

        return $this->fetch();
    }
}