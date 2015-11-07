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
//print_R($page);
exit;  */
if($list==false){
    $list_data=array(
        'weid' => $_W['weid'],
        'title' => '改变世界非你莫属',
        's_id' => $appid,
        'iden' => 'employ',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/employ/1.jpg',
        'share_title' => '改变世界非你莫属',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/employ/share.jpg',
        'share_content' => '改变世界非你莫属',
        'reply_title' => '改变世界非你莫属',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '改变世界非你莫属',
        'isadvanced' => 0,
        'first_type' => 1,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/employ/bg.mp3',
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
[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/employ\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/employ\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/employ\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/employ\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/employ\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/7.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:18:\"\u9ad8\u7ea7\u4ea7\u54c1\u7ecf\u7406\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/8.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:15:\"\u65b0\u5a92\u4f53\u4f20\u64ad\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/9.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:21:\"\u524d\u7aef\u5f00\u53d1\u5de5\u7a0b\u5e08\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/10.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:18:\"PHP\u5f00\u53d1\u5de5\u7a0b\u5e08\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/11.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:11:\"UI\u8bbe\u8ba1\u5e08\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/12.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:15:\"\u6d4b\u8bd5\u5de5\u7a0b\u5e08\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/13.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:15:\"\u5927\u5ba2\u6237\u7ecf\u7406\";}","create_time":"0"},{"listorder":"0","m_type":"2","thumb":"addons\/scene_cube\/demo\/employ\/14.jpg","param":"a:3:{s:4:\"str1\";s:0:\"\";s:4:\"str2\";s:0:\"\";s:4:\"str3\";s:8:\"BD\u7ecf\u7406\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/employ\/15.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/employ\/15.png\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/employ\/18.png\";}","create_time":"0"}]';
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
		