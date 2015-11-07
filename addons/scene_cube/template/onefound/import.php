<?php
/**
 * 一张独立图片存放在page thumb中
 * 场景独立参数，存在param中
 * 排序id越大
 *
 *
 */

defined('IN_IA') or exit('Access Denied');


if($list==false){
    $list_data=array(
        'weid' => $_W['weid'],
        'title' => '壹基金邀您壹生益起走',
        's_id' => $appid,
        'iden' => 'onefound',
        'cover' => $_W['siteroot'].'addons/scene_cube/style/img/default_bg.jpg',
        'cover1' => $_W['siteroot'].'addons/scene_cube/demo/onefound/2.png',
        'cover2' => $_W['siteroot'].'addons/scene_cube/demo/onefound/1.jpg',
        'share_title' => '壹基金邀您壹生益起走',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/onefound/share.jpg',
        'share_content' => '壹基金邀您壹生益起走',
        'reply_title' => '壹基金邀您壹生益起走',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '壹基金邀您壹生益起走',
        'isadvanced' => '0',
        'first_type' => 2,
        'bg_music_switch' => '1',
        'bg_music_icon' => 0,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/onefound/bg.mp3',
        'start_time' => time(),
        'end_time' => strtotime("+1 year"),
        'hits' => 0,
        'shares' => 0,
        'isyuyue' => 0,
        'iscomment' => 0,
        'isdemo'=>1,
    );
    pdo_insert('scene_cube_list',$list_data);
    $list_id=pdo_insertid();
    $pagestr='
[{"listorder":"0","m_type":"34","thumb":"addons\/scene_cube\/demo\/onefound\/3.jpg","param":"a:1:{s:4:\"pic1\";s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/3-1.png\";}","create_time":"0"},{"listorder":"0","m_type":"35","thumb":"addons\/scene_cube\/demo\/onefound\/4.jpg","param":"a:1:{s:6:\"thumbs\";a:7:{i:0;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-1.png\";i:1;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-2.png\";i:2;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-3.png\";i:3;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-4.png\";i:4;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-5.png\";i:5;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-6.png\";i:6;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/4-7.png\";}}","create_time":"0"},{"listorder":"0","m_type":"34","thumb":"addons\/scene_cube\/demo\/onefound\/5.jpg","param":"a:1:{s:4:\"pic1\";s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/5-1.png\";}","create_time":"0"},{"listorder":"0","m_type":"35","thumb":"addons\/scene_cube\/demo\/onefound\/6.jpg","param":"a:1:{s:6:\"thumbs\";a:6:{i:0;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/6-1.png\";i:1;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/6-2.png\";i:2;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/6-3.png\";i:3;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/6-4.png\";i:4;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/6-5.png\";i:5;s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/6-6.png\";}}","create_time":"0"},{"listorder":"0","m_type":"34","thumb":"addons\/scene_cube\/demo\/onefound\/7.jpg","param":"a:1:{s:4:\"pic1\";s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/7-1.png\";}","create_time":"0"},{"listorder":"0","m_type":"3","thumb":"addons\/scene_cube\/demo\/onefound\/8.jpg","param":"a:2:{s:6:\"btnimg\";s:73:\"__URL__addons\/scene_cube\/demo\/onefound\/8-btn.png\";s:11:\"share_thumb\";s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/8-3.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"34","thumb":"addons\/scene_cube\/demo\/onefound\/9.jpg","param":"a:1:{s:4:\"pic1\";s:71:\"__URL__addons\/scene_cube\/demo\/onefound\/9-1.png\";}","create_time":"0"},{"listorder":"0","m_type":"35","thumb":"addons\/scene_cube\/demo\/onefound\/10.jpg","param":"a:1:{s:6:\"thumbs\";a:5:{i:0;s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/10-1.png\";i:1;s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/10-2.png\";i:2;s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/10-3.png\";i:3;s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/10-4.png\";i:4;s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/10-5.png\";}}","create_time":"0"},{"listorder":"0","m_type":"34","thumb":"addons\/scene_cube\/demo\/onefound\/11.jpg","param":"a:1:{s:4:\"pic1\";s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/11-1.png\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/onefound\/12.jpg","param":"a:2:{s:6:\"btnimg\";s:72:\"__URL__addons\/scene_cube\/demo\/onefound\/12-1.png\";s:3:\"url\";s:83:\"https:\/\/ssl.gongyi.qq.com\/m\/weixin\/detail.htm?showwxpaytitle=1#p%3Ddetail%26id%3D27\";}","create_time":"0"}]';
    $pageArr=json_decode($pagestr,true);
    foreach($pageArr as $v){
        $page_data=array(
            'weid'=>$_W['weid'],
            'list_id'=>$list_id,
            'listorder'=>$v['listorder'],
            'm_type'=>$v['m_type'],
            'thumb'=>$_W['siteroot'].$v['thumb'],
            'param'=>empty($v['param'])?'':str_replace('__URL__',$_W['siteroot'],$v['param']),
            'create_time'=>time(),
        );
        pdo_insert('scene_cube_page',$page_data);
    }
    message($app['title'].'数据导入成功',$this->createWeburl('manager'));
}
		