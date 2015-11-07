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
exit; */
if($list==false){
    $list_data=array(
        'weid' => $_W['weid'],
        'title' => 'iPhone6，再一次创造',
        's_id' => $appid,
        'iden' => 'style7',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/style7/1.png',
        'share_title' => 'iPhone6，再一次创造',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style7/share.jpg',
        'share_content' => 'iPhone6，再一次创造',
        'reply_title' => 'iPhone6，再一次创造',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => 'iPhone6，再一次创造',
        'isadvanced' => 0,
        'first_type' => 1,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style7/bg.mp3',
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
    $pagestr='[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style7\/7.jpg","param":"a:1:{s:6:\"thumbs\";a:7:{i:0;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-1.jpg\";i:1;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-2.jpg\";i:2;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-3.jpg\";i:3;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-4.jpg\";i:4;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-5.jpg\";i:5;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-6.jpg\";i:6;s:69:\"__URL__addons\/scene_cube\/demo\/style7\/7-7.jpg\";}}","create_time":"0"},{"listorder":"0","m_type":"4","thumb":"addons\/scene_cube\/demo\/style7\/8.jpg","param":"a:1:{s:3:\"url\";s:47:\"http:\/\/v.youku.com\/v_show\/id_XNzAyNDcyMzAw.html\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/9.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style7\/10.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"3","thumb":"addons\/scene_cube\/demo\/style7\/11.jpg","param":"a:2:{s:6:\"btnimg\";s:72:\"__URL__addons\/scene_cube\/demo\/style7\/11_btn.png\";s:11:\"share_thumb\";s:0:\"\";}","create_time":"0"}]';
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
		