<?php
/**
 * 获取客户端IP
 *
 *
 * @return  string
 */
if (!function_exists('IPClient'))
{
    function IPClient()
    {
        if(getenv("HTTP_CLIENT_IP")) return getenv("HTTP_CLIENT_IP");
        if(getenv("HTTP_X_FORWARDED_FOR")) return getenv("HTTP_X_FORWARDED_FOR");
        if(getenv("REMOTE_ADDR")) return getenv("REMOTE_ADDR");
        return '0.0.0.0';
    }
}



// 自定义 session 存储方案
if (!function_exists('_session'))
{
    include KERNEL_PATH . '/class/Session.class.php';

    function dbSession($name, $value = '', $prefix = null)
    {
        if (is_array($name)) {
            // 初始化
            \Session::init($name);

        } elseif (is_null($name)) {
            // 清除
            \Session::clear('' === $value ? null : $value);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ?
                \Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);

        } elseif (is_null($value)) {
            // 删除
            return \Session::delete($name, $prefix);
        } else {
            // 设置
            return \Session::set($name, $value, $prefix);
        }
    }
}


/**
 * 验证用户名是否为字母和数字且开头为字母的格式
 *
 * @access  public
 * @param   string      $account      需要验证的账号
 *
 * @return  boolean
 */
if (!function_exists('is_account'))
{
    function is_account($account)
    {
        return preg_match("/^[a-zA-Z][a-zA-Z0-9_]*$/", $account);
    }
}


/**
 * 验证输入的手机号是否合法
 *
 * @access  public
 * @param   string      $mobile      需要验证的手机号
 *
 * @return  boolean
 */
if (!function_exists('is_mobile'))
{
    function is_mobile($mobile)
    {
        return preg_match("/^(13|17|15|18)[0-9]{9}$/", $mobile);
    }
}


/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
if (!function_exists('is_email'))
{
    function is_email($email)
    {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($email, '@') !== false && strpos($email, '.') !== false) {
            return (preg_match($chars, $email)) ? true : false;
        } else {
            return false;
        }
    }
}


/**
 * 生成全球唯一标识符
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return string
 */
if (!function_exists('GUID'))
{
    function GUID()
    {
        if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');

        $hyphen = chr(45);// "-"
        $char   = strtoupper(md5(uniqid(rand(), true)));
        $uuid   = substr($char, 0, 8).$hyphen.substr($char, 8, 4).$hyphen.substr($char,12, 4).$hyphen
            .substr($char, 16, 4).$hyphen.substr($char, 20, 12);
        return $uuid;
    }
}


/**
 * 简单封装的 curl 请求函数
 *
 * @access  public
 * @param   string      $url      请求地址
 * @param   string      $data     请求数据
 * @param   string      $method   请求方式
 *
 * @return  string
 */
if (!function_exists('simple_curl'))
{
    function simple_curl($url, $data = '', $method = 'post')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_HEADER,0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_POST, ($method == 'post' ? 1:0));
        $result = curl_exec($curl); curl_close($curl);
        return $result;
    }
}

/**
 * 简单封装的 日志记录 函数
 *
 * @access  public
 * @param   string      $content   日志内容
 * @param   string      $file_name 日记文件
 *
 * @return  void
 */
if (!function_exists('simple_logs'))
{
    function simple_logs($content, $file_name='normal.txt')
    {
        $text      = date("Y-m-d H:i:s") . '|' . $content;
        $file_path = PROJECT_PATH . '/storage/logs/' . $file_name;
        $maxsize   = 1024 * 1024 * 10;

        $fp = fopen($file_path, "a");
        flock($fp, LOCK_EX);
        fwrite($fp, "\n" . $text);
        flock($fp, LOCK_UN);
        fclose($fp);

        if (filesize($file_path) >= $maxsize)
        {
            $t = explode('.', $file_path);
            $file = substr($file_path, 0, strrpos($file_path, '.'));
            rename($file_path, $file . '_' . date('YmdHis') . '.' . end($t));
        }
    }
}