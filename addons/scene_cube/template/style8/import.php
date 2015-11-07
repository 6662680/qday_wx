<?php
/**
 * 一张独立图片存放在page thumb中
 * 场景独立参数，存在param中
 * 排序id越大
 *
 *
 */

defined('IN_IA') or exit('Access Denied');
/*
$list=pdo_fetch('select * from '.tablename('scene_cube_list').' where weid=3 AND s_id='.$appid.' ');
//print_R($list);
//exit;
$page=pdo_fetchall('select * from '.tablename('scene_cube_page').' where weid=3 AND list_id='.$list['id'].' ');
foreach($page as $k=>$v){
    unset($page[$k]['id']);
    unset($page[$k]['weid']);
    unset($page[$k]['list_id']);
    $page[$k]['thumb']=str_replace('http://t4.mmghome.com/','',$v['thumb']);
    $page[$k]['param']=str_replace('http://t4.mmghome.com/','__URL__',$v['param']);
}
echo json_encode($page);
//print_R($page);
exit;*/
if($list==false){
    $list_data=array(
        'weid' => $_W['weid'],
        'title' => '2014 NEW FUN 泳池趴',
        's_id' => $appid,
        'iden' => 'style8',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/style8/0.jpg',
        'share_title' => '2014 NEW FUN 泳池趴',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style8/share.jpg',
        'share_content' => '2014 NEW FUN 泳池趴',
        'reply_title' => '2014 NEW FUN 泳池趴',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '2014 NEW FUN 泳池趴',
        'isadvanced' => 0,
        'first_type' => 1,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style8/bg.mp3',
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
    $pagestr='[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/1.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style8\/5.jpg","param":"a:2:{s:5:\"nails\";a:7:{i:0;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-1.png\";i:1;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-2.png\";i:2;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-3.png\";i:3;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-4.png\";i:4;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-5.png\";i:5;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-6.png\";i:6;s:69:\"__URL__addons\/scene_cube\/demo\/style8\/5-7.png\";}s:6:\"thumbs\";a:7:{i:0;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-1-1.jpg\";i:1;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-2-1.jpg\";i:2;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-3-1.jpg\";i:3;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-4-1.jpg\";i:4;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-5-1.jpg\";i:5;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-6-1.jpg\";i:6;s:71:\"__URL__addons\/scene_cube\/demo\/style8\/5-7-1.jpg\";}}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/8.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/9.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/10.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/11.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style8\/12.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"7","thumb":"addons\/scene_cube\/demo\/style8\/13.jpg","param":"a:2:{s:3:\"str\";s:11:\"13800138000\";s:3:\"url\";s:11:\"13800138000\";}","create_time":"0"},{"listorder":"0","m_type":"31","thumb":"addons\/scene_cube\/demo\/style8\/14.jpg","param":"a:6:{s:3:\"str\";s:12:\"\u516c\u53f8\u5730\u5740\";s:3:\"tel\";s:11:\"13813813813\";s:5:\"sname\";s:12:\"\u5357\u4eac\u667a\u7b56\";s:5:\"place\";s:36:\"\u4e0a\u6d77\u5e02\u6768\u6d66\u533a\u56fd\u548c\u8def36\u53f7-a12\";s:3:\"lng\";s:10:\"121.525772\";s:3:\"lat\";s:9:\"31.308563\";}","create_time":"0"}]';
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
		