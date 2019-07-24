<?php
return [


    'app_debug'              => true,
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',
    'show_error_msg'         => true,

    'template' => [
        'view_depr'     => '_',
        'default_theme' => 'new',      // 模板文件名
        'view_path'     => PROJECT_PATH . '/web/diancan/dist/',
    ],

    'view_replace_str' => [
        '__ROOT__'     => '/',
        '__PUBLIC__'   => '/public/static',
    ],

    'url_html_suffix'  => false,
    'url_common_param' => true,

    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 1,
    ],
    'page_size' => 15,
    'default_page_size' => 10,
    'message_page_size' => 4,

];