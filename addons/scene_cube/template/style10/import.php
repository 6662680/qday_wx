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
        'title' => '山海和声·飞越彩虹',
        's_id' => $appid,
        'iden' => 'style10',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/style10/1.png',
        'share_title' => '山海和声·飞越彩虹',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style10/share.jpg',
        'share_content' => '山海和声·飞越彩虹',
        'reply_title' => '山海和声·飞越彩虹',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '山海和声·飞越彩虹',
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
    $pagestr='
[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style10\/bj1.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/2.png\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/2.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style10\/bj1.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/3.png\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/3.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style10\/bj1.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/4.png\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/4.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"3","thumb":"addons\/scene_cube\/demo\/style10\/7.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/5.jpg\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/6.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style10\/bj1.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/8.png\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/8.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"4","thumb":"addons\/scene_cube\/demo\/style10\/bj2.jpg","param":"a:2:{s:4:\"pic1\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/9.png\";s:4:\"pic2\";s:68:\"__URL__addons\/scene_cube\/demo\/style10\/9.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"4","thumb":"addons\/scene_cube\/demo\/style10\/bj2.jpg","param":"a:2:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/10.png\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/10.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"4","thumb":"addons\/scene_cube\/demo\/style10\/bj2.jpg","param":"a:2:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/11.png\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/11.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"4","thumb":"addons\/scene_cube\/demo\/style10\/bj2.jpg","param":"a:2:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/12.png\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/12.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"4","thumb":"addons\/scene_cube\/demo\/style10\/bj2.jpg","param":"a:2:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/13.png\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/13.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"5","thumb":"addons\/scene_cube\/demo\/style10\/15.jpg","param":"a:1:{s:3:\"url\";s:47:\"http:\/\/v.youku.com\/v_show\/id_XNzg0Njk0MDUy.html\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style10\/16.jpg","param":"a:2:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/16.png\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style10\/17.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"9","thumb":"addons\/scene_cube\/demo\/style10\/bj1.jpg","param":"a:4:{s:3:\"tel\";s:13:\"0755-83290513\";s:5:\"email\";s:17:\"songhe0755@qq.com\";s:5:\"wxurl\";s:107:\"http:\/\/mp.weixin.qq.com\/s?__biz=MjM5NjE1ODk4MQ==&mid=200501841&idx=2&sn=14f6c5f818119a4e861bc9816fe12f2c#rd\";s:6:\"weixin\";s:30:\"\u5173\u6ce8\u677e\u79be\u6210\u957f\u5173\u7231\u57fa\u91d1\";}","create_time":"0"}]';
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
		