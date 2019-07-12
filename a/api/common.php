<?php

function get_menu($node, $parent = 0)
{
    $menu = array();
    foreach($node as $item)
    {
        if($item['parent']!=$parent) continue;
        $menu[$item['node_id']]          = $item;
        $menu[$item['node_id']]['child'] = get_menu($node, $item['node_id']);
    }
    return $menu;
}

function pagination($total, $page_size = 10, $page_key='page')
{
    $pages    = ceil($total/$page_size);
    $page     = input($page_key, 1, 'intval');
    $prev     = ($page-1); if($prev<1) $prev = 1;
    $next     = ($page+1); if($next>$pages) $next = $pages;
    $page_min = ($page-5); if($page_min<1) $page_min = 1;
    $page_max = ($page+5); if($page_max>=$pages) $page_max = $pages+1;

    // 获取搜索字符串
    $nQuery = function($page) use ($page_key)
    {

        $req = request()->param();

        unset($req['s']);
        $req[$page_key] = $page;

        return url(request()->controller().'/'.request()->action(), $req);
    };


    $pHtml = '<div id="pagination" class="clearfix">';
    $pHtml.= '<div class="data-rows">共 '.$total.' 条数据，每页 '.$page_size.' 条，共 '.$pages.' 页</div>';
    $pHtml.= '<div class="data-page"><ul>';


    $pHtml.= '<li><a href="'.$nQuery($prev).'">上一页</a></li>';

    if($page_min>=2)
    {
        $pHtml.= '<li><a href="'.$nQuery(1).'">1</a></li>';
        if($page_min>2)
            $pHtml.= '<li><a href="javascript:;">...</a></li>';
    }

    for($i=$page_min; $i<$page; $i++)
    {
        $pHtml.= '<li><a href="'.$nQuery($i).'">'.$i.'</a></li>';
    }

    $pHtml.= '<li><a href="javascript:;" class="page-active">'.$page.'</a></li>';

    for($i=($page+1); $i<$page_max; $i++)
    {
        $pHtml.= '<li><a href="'.$nQuery($i).'">'.$i.'</a></li>';
    }

    if($i<=$pages)
    {
        if($i<$pages)
            $pHtml.= '<li><a href="javascript:;">...</a></li>';
        $pHtml.= '<li><a href="'.$nQuery($pages).'">'.$pages.'</a></li>';
    }

    $pHtml.= '<li><a href="'.$nQuery($next).'">下一页</a></li>';
    $pHtml.= '</ul></div></div>';

    return $pHtml;
};