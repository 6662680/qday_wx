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
$page=pdo_fetchall('select * from '.tablename('scene_cube_page').' where weid=3 AND list_id='.$list['id'].' ');
foreach($page as $k=>$v){
    unset($page[$k]['id']);
    unset($page[$k]['weid']);
    unset($page[$k]['list_id']);
    $page[$k]['thumb']=str_replace('http://t4.mmghome.com/','',$v['thumb']);
    $page[$k]['param']=str_replace('http://t4.mmghome.com/','__URL__',$v['param']);
}
echo json_encode($page);
print_R($page);
exit;
*/
if($list==false){
    $list_data=array(
        'weid' => $_W['weid'],
        'title' => '极致诱惑 — — 志玲说',
        's_id' => $appid,
        'iden' => 'style2',
        'cover' => $_W['siteroot'].'addons/scene_cube/style/img/default_bg.jpg',
        'cover1' => $_W['siteroot'].'addons/scene_cube/demo/style2/1.jpg',
        'cover2' => $_W['siteroot'].'addons/scene_cube/demo/style2/2.jpg',
        'share_title' => '极致诱惑 — — 志玲说',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style2/share.jpg',
        'share_content' => '极致诱惑 — — 志玲说',
        'reply_title' => '极致诱惑 — — 志玲说',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '极致诱惑 — — 志玲说',
        'isadvanced' => '0',
        'first_type' => 2,
        'bg_music_switch' => '1',
        'bg_music_icon' => 0,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style2/bg.mp3',
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
    $pagestr='[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/8.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/9.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"5","thumb":"addons\/scene_cube\/demo\/style2\/10.jpg","param":"a:2:{s:6:\"vthumb\";s:70:\"__URL__addons\/scene_cube\/demo\/style2\/10-1.jpg\";s:3:\"url\";s:84:\"__URL__addons\/scene_cube\/demo\/style2\/53f304a21bc9b57891.mp4\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style2\/11.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"3","thumb":"addons\/scene_cube\/demo\/style2\/12.jpg","param":"a:2:{s:6:\"btnimg\";s:72:\"__URL__addons\/scene_cube\/demo\/style2\/12_btn.png\";s:11:\"share_thumb\";s:74:\"__URL__addons\/scene_cube\/demo\/style2\/12_share.jpg\";}","create_time":"0"}]';
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
		