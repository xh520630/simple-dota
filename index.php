<?php

// 跨域设置
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: authorization');

defined('APP_PATH') or define('APP_PATH', __DIR__.'/a/');
defined('KERNEL_PATH') or define('KERNEL_PATH', __DIR__.'/kernel');
defined('PROJECT_PATH') or define('PROJECT_PATH', __DIR__);



require PROJECT_PATH . '/config/env.php';
require KERNEL_PATH . '/framework/thinkphp/start.php';
