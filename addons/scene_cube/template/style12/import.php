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
        'title' => '九章别墅',
        's_id' => $appid,
        'iden' => 'style12',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/style12/1.png',
        'share_title' => '九章别墅',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style12/share.jpg',
        'share_content' => '九章别墅',
        'reply_title' => '九章别墅',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '九章别墅',
        'isadvanced' => 0,
        'first_type' => 0,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => '',
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
[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/1.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"21","thumb":"addons\/scene_cube\/style\/img\/default_bg.jpg","param":"a:6:{s:3:\"tel\";s:0:\"\";s:5:\"sname\";s:12:\"\u4e5d\u7ae0\u522b\u5885\";s:5:\"place\";s:27:\"\u5317\u4eac\u5e02\u671d\u9633\u533a\u91d1\u76cf\u8def\";s:3:\"lng\";s:10:\"116.576191\";s:3:\"lat\";s:9:\"40.009917\";s:5:\"thumb\";a:2:{i:0;s:68:\"__URL__addons\/scene_cube\/demo\/style12\/3.jpg\";i:1;s:68:\"__URL__addons\/scene_cube\/demo\/style12\/4.jpg\";}}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/12.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/8.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/9.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/10.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style12\/11.jpg","param":"","create_time":"0"}]';
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
		