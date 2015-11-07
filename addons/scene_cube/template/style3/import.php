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
        'title' => '变形金刚4•绝迹重生',
        's_id' => $appid,
        'iden' => 'style3',
        'cover1' => $_W['siteroot'].'addons/scene_cube/demo/style3/0.jpg',
        'cover2' => $_W['siteroot'].'addons/scene_cube/demo/style3/0-1.jpg',
        'share_title' => '变形金刚4•绝迹重生',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style3/share.jpg',
        'share_content' => '变形金刚4•绝迹重生',
        'reply_title' => '变形金刚4•绝迹重生',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '变形金刚4•绝迹重生',
        'isadvanced' => 0,
        'first_type' => 2,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style3/bg.mp3',
        'start_time' => time(),
        'end_time' => strtotime("+1 year"),
        'hits' => 0,
        'shares' => 0,
        'tongji' => 0,
        'isyuyue' => 0,
        'iscomment' => 0,
        'isdemo' => 1,
    );
    pdo_insert('scene_cube_list',$list_data);
    $list_id=pdo_insertid();
    $pagestr='[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/1.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/8.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"5","thumb":"addons\/scene_cube\/demo\/style3\/9.jpg","param":"a:2:{s:6:\"vthumb\";s:69:\"__URL__addons\/scene_cube\/demo\/style3\/9-1.jpg\";s:3:\"url\";s:69:\"__URL__addons\/scene_cube\/demo\/style3\/9-2.mp4\";}","create_time":"0"},{"listorder":"0","m_type":"5","thumb":"addons\/scene_cube\/demo\/style3\/10.jpg","param":"a:2:{s:6:\"vthumb\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/10-1.jpg\";s:3:\"url\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/10-2.mp4\";}","create_time":"0"},{"listorder":"0","m_type":"5","thumb":"addons\/scene_cube\/demo\/style3\/11.jpg","param":"a:2:{s:6:\"vthumb\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/11-1.jpg\";s:3:\"url\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/10-2.mp4\";}","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style3\/12.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"8","thumb":"addons\/scene_cube\/demo\/style3\/13.jpg","param":"a:9:{s:3:\"tel\";s:13:\"13800138000\";s:5:\"email\";s:18:\"12345@qq.com\";s:5:\"wxurl\";s:0:\"\";s:6:\"weixin\";s:0:\"\";s:6:\"mthumb\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/13-1.jpg\";s:5:\"sname\";s:19:\"\u676d\u5dde\u8f7b\u4e91-\u6d4b\u8bd5\";s:5:\"place\";s:39:\"\u6d59\u6c5f\u7701\u676d\u5dde\u5e02\u4e0b\u57ce\u533a\u4e2d\u5c71\u5317\u8def\";s:3:\"lng\";s:10:\"120.169756\";s:3:\"lat\";s:8:\"30.28386\";}","create_time":"0"},{"listorder":"0","m_type":"8","thumb":"addons\/scene_cube\/demo\/style3\/14.jpg","param":"a:9:{s:3:\"tel\";s:13:\"13800138000\";s:5:\"email\";s:15:\"12345@qq.com\";s:5:\"wxurl\";s:0:\"\";s:6:\"weixin\";s:0:\"\";s:6:\"mthumb\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/14-1.jpg\";s:5:\"sname\";s:12:\"\u5357\u4eac\u667a\u7b56\";s:5:\"place\";s:35:\"\u4e0a\u6d77\u5e02\u6768\u6d66\u533a\u653f\u901a\u8def260-8\u53f7\";s:3:\"lng\";s:10:\"121.515854\";s:3:\"lat\";s:9:\"31.307636\";}","create_time":"0"},{"listorder":"0","m_type":"8","thumb":"addons\/scene_cube\/demo\/style3\/15.jpg","param":"a:9:{s:3:\"tel\";s:13:\"13800138000\";s:5:\"email\";s:18:\"12345@qq.com\";s:5:\"wxurl\";s:0:\"\";s:6:\"weixin\";s:0:\"\";s:6:\"mthumb\";s:70:\"__URL__addons\/scene_cube\/demo\/style3\/15-1.jpg\";s:5:\"sname\";s:12:\"\u5357\u4eac\u667a\u7b56\";s:5:\"place\";s:39:\"\u6d59\u6c5f\u7701\u676d\u5dde\u5e02\u4e0b\u57ce\u533a\u4e2d\u5c71\u5317\u8def\";s:3:\"lng\";s:10:\"120.169756\";s:3:\"lat\";s:8:\"30.28386\";}","create_time":"0"}]';
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
		