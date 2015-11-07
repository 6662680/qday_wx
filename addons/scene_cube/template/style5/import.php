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
        'title' => '云来三周年',
        's_id' => $appid,
        'iden' => 'style5',
        'cover1'=> $_W['siteroot'].'addons/scene_cube/demo/style5/1.png',
        'cover2' => $_W['siteroot'].'addons/scene_cube/demo/style5/1.jpg',
        'share_title' => '云来三周年',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style5/share.jpg',
        'share_content' => '云来三周年',
        'reply_title' => '云来三周年',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '云来三周年',
        'isadvanced' => 0,
        'first_type' => 2,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style5/bg.mp3',
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
[{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style5\/2.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style5\/3.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style5\/4.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"1","thumb":"addons\/scene_cube\/demo\/style5\/5.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"10","thumb":"addons\/scene_cube\/demo\/style5\/6.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"10","thumb":"addons\/scene_cube\/demo\/style5\/7.jpg","param":"","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style5\/8.jpg","param":"a:1:{s:3:\"url\";s:47:\"http:\/\/v.youku.com\/v_show\/id_XNzYzMzYwNDc2.html\";}","create_time":"0"},{"listorder":"0","m_type":"7","thumb":"addons\/scene_cube\/demo\/style5\/9.jpg","param":"a:1:{s:3:\"bg2\";s:68:\"__URL__addons\/scene_cube\/demo\/style5\/10.jpg\";}","create_time":"0"},{"listorder":"0","m_type":"8","thumb":"addons\/scene_cube\/demo\/style5\/11.jpg","param":"a:2:{s:5:\"txtbg\";s:68:\"__URL__addons\/scene_cube\/demo\/style5\/11.png\";s:6:\"btnimg\";s:68:\"__URL__addons\/scene_cube\/demo\/style5\/12.png\";}","create_time":"0"},{"listorder":"0","m_type":"5","thumb":"addons\/scene_cube\/demo\/style5\/13.jpg","param":"a:6:{s:6:\"btnimg\";s:68:\"__URL__addons\/scene_cube\/demo\/style5\/14.png\";s:5:\"txtbg\";s:68:\"__URL__addons\/scene_cube\/demo\/style5\/13.png\";s:5:\"sname\";s:9:\"\u6df1\u5733\u6e7e\";s:5:\"place\";s:6:\"\u6df1\u5733\";s:3:\"lng\";s:0:\"\";s:3:\"lat\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"9","thumb":"addons\/scene_cube\/demo\/style5\/15.jpg","param":"a:4:{s:3:\"tel\";s:11:\"13914494002\";s:5:\"email\";s:16:\"40039885@qq.com\";s:5:\"wxurl\";s:107:\"http:\/\/cc.izhice.com\/mobile.php?act=channel&name=index&weid=1&wxref=mp.weixin.qq.com#wechat_redirect\";s:6:\"weixin\";s:7:\"izhice_demo\";}","create_time":"0"}]';
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
		