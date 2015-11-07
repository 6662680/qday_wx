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
        'title' => '办公家具 就选优的！',
        's_id' => $appid,
        'iden' => 'style9',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/style9/1.jpg',
        'share_title' => '朗度L&#039;art des',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style9/share.jpg',
        'share_content' => '办公家具 就选优的！',
        'reply_title' => '办公家具 就选优的！',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '办公家具 就选优的！',
        'isadvanced' => 0,
        'first_type' => 1,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style9/bg.mp3',
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
    $pagestr='[{"listorder":"0","m_type":"11","thumb":"addons\/scene_cube\/demo\/style9\/2.jpg","param":"a:2:{s:5:\"nails\";a:4:{i:0;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/2-1.jpg\";i:1;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/2-2.jpg\";i:2;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/2-3.jpg\";i:3;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/2-4.jpg\";}s:6:\"thumbs\";a:4:{i:0;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/2-1-1.jpg\";i:1;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/2-2-1.jpg\";i:2;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/2-3-1.jpg\";i:3;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/2-4-1.jpg\";}}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"14","thumb":"addons\/scene_cube\/demo\/style9\/4.jpg","param":"a:3:{s:5:\"title\";a:5:{i:0;s:12:\"\u9884\u7ea6\u670d\u52a1\";i:1;s:12:\"\u4e0a\u95e8\u91cf\u5c3a\";i:2;s:12:\"\u8bbe\u8ba1\u65b9\u6848\";i:3;s:12:\"\u4e0a\u95e8\u5b89\u88c5\";i:4;s:6:\"\u4fdd\u4fee\";}s:6:\"thumbs\";a:5:{i:0;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/4-1.jpg\";i:1;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/4-2.jpg\";i:2;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/4-3.jpg\";i:3;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/4-4.jpg\";i:4;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/4-5.jpg\";}s:7:\"content\";a:5:{i:0;s:78:\"\u514d\u8d39\u9884\u7ea6\u670d\u52a1\uff1a\u7559\u4e0b\u60a8\u7684\u8054\u7cfb\u65b9\u5f0f\uff0c\u6211\u4eec\u7ed9\u60a8\u5b89\u6392\u4e0a\u95e8\u670d\u52a1\";i:1;s:101:\"\u514d\u8d39\u4e0a\u95e8\u91cf\u5c3a\uff1a\u5728\u7ebf\u9884\u7ea6\u540e\uff0c24\u5c0f\u65f6\u5185\u6211\u4eec\u7ed9\u60a8\u5b89\u6392\u4e13\u4e1a\u4eba\u5458\uff0c\u514d\u8d39\u4e0a\u95e8\u91cf\u5c3a\";i:2;s:117:\"\u514d\u8d39\u8bbe\u8ba1\u65b9\u6848\uff1a\u6839\u636e\u60a8\u7684\u7a7a\u95f4\u60c5\u51b5\u53ca\u9700\u6c42\u4e0e\u559c\u597d\uff0c\u514d\u8d39\u63d0\u4f9b\u6700\u9002\u5408\u60a8\u7684\u529e\u516c\u7a7a\u95f4\u89e3\u51b3\u65b9\u6848\";i:3;s:105:\"\u514d\u8d39\u4e0a\u95e8\u5b89\u88c5\uff1a\u5408\u540c\u7b7e\u8ba2\u5e76\u652f\u4ed8\u8ba2\u91d1\u540e\uff0c\u6211\u4eec\u4e3a\u60a8\u51c6\u5907\u5b9a\u5236\u8d27\u54c1\uff0c\u514d\u8d39\u4e0a\u95e8\u5b89\u88c5\";i:4;s:225:\"\u6709\u6548\u671f\u5185\u4fdd\u4fee\uff1a\u201c\u4f18\u7684\u8054\u76df\u201d\u4e25\u683c\u6267\u884c\u56fd\u5bb6\u6709\u5173\u4ea7\u54c1\u201c\u4e09\u5305\u201d\u7684\u89c4\u5b9a\uff0c\u5c5e\u4ea7\u54c1\u8d28\u91cf\u95ee\u9898\u6d88\u8d39\u8005\u53ef\u4eab\u53d7\u677f\u5f0f\u3001\u6cb9\u6f06\u7c7b\u00a05\u00a0\u5e74\uff0c\u8f6f\u4f53\u7c7b3\u5e74\u4ee5\u4e0a\uff08\u89c6\u7ec6\u5206\u54c1\u7c7b\u5728\u5408\u540c\u4e2d\u5355\u72ec\u6807\u6ce8\uff09\";}}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/13.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"11","thumb":"addons\/scene_cube\/demo\/style9\/5.jpg","param":"a:2:{s:5:\"nails\";a:6:{i:0;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/5-1.jpg\";i:1;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/5-2.jpg\";i:2;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/5-3.jpg\";i:3;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/5-4.jpg\";i:4;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/5-5.jpg\";i:5;s:69:\"__URL__addons\/scene_cube\/demo\/style9\/5-6.jpg\";}s:6:\"thumbs\";a:6:{i:0;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/5-1-1.jpg\";i:1;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/5-2-1.jpg\";i:2;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/5-3-1.jpg\";i:3;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/5-4-1.jpg\";i:4;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/5-5-1.jpg\";i:5;s:71:\"__URL__addons\/scene_cube\/demo\/style9\/5-6-1.jpg\";}}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/8.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/9.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style9\/10.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"16","thumb":"addons\/scene_cube\/demo\/style9\/11.jpg","param":"a:2:{s:3:\"str\";s:10:\"点我立即分享\";s:11:\"share_thumb\";s:0:\"\";}","create_time":"0"}]';
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
		