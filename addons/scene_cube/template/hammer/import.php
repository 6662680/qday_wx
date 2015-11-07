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
        'title' => '锤子 · 手机',
        's_id' => $appid,
        'iden' => 'hammer',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/hammer/1.jpg',
        'share_title' => '锤子 · 手机',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/hammer/share.jpg',
        'share_content' => '锤子 · 手机',
        'reply_title' => '锤子 · 手机',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '我不是为了输赢  我就是认真 by 伊索科技',
        'isadvanced' => 0,
        'first_type' => 2,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/hammer/bg.mp3',
        'start_time' => time(),
        'end_time' => strtotime("+1 year"),
        'hits' => 0,
        'shares' => 0,
        'isyuyue' => 0,
        'iscomment' => 0,
        'isdemo' => 1,
    );
    pdo_insert('scene_cube_list',$list_data);
    $list_id=pdo_insertid();
    $pagestr='
[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"7","thumb":"addons\/scene_cube\/demo\/hammer\/8.jpg","param":"a:2:{s:6:\"vthumb\";s:0:\"\";s:3:\"url\";s:47:\"http:\/\/v.youku.com\/v_show\/id_XNzE0OTY2Njg4.html\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/9.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/10.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/11.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"7","thumb":"addons\/scene_cube\/demo\/hammer\/12.jpg","param":"a:2:{s:6:\"vthumb\";s:0:\"\";s:3:\"url\";s:47:\"http:\/\/v.youku.com\/v_show\/id_XNzE0ODc0MDMy.html\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/13.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/hammer\/14.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/hammer\/15.jpg","param":"a:2:{s:6:\"btnimg\";s:67:\"__URL__addons\/scene_cube\/resource\/15-1.png\";s:3:\"url\";s:42:\"http:\/\/m.suning.com\/product\/121160448.html\";}","create_time":"0"}]';
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
		