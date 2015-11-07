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
exit;*/
if($list==false){
    $list_data=array(
        'weid' => $_W['weid'],
        'title' => '我与自己久别重逢',
        's_id' => $appid,
        'iden' => 'style4',
        'cover' => $_W['siteroot'].'addons/scene_cube/demo/style4/1.png',
        'share_title' => '我与自己久别重逢',
        'share_thumb' => $_W['siteroot'].'addons/scene_cube/demo/style4/share.jpg',
        'share_content' => '2014 经济不景气 为自己打气',
        'reply_title' => '我与自己久别重逢',
        'reply_thumb' => $_W['siteroot'].'addons/scene_cube/style/img/default_cover.jpg',
        'reply_description' => '2014 经济不景气 为自己打气',
        'isadvanced' => 0,
        'first_type' => 0,
        'bg_music_switch' => 1,
        'bg_music_icon' => 1,
        'bg_music_url' => $_W['siteroot'].'addons/scene_cube/demo/style4/bg.mp3',
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
    $pagestr='[{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style4\/2.jpg","param":"a:5:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/2-1.png\";s:5:\"show1\";s:8:\"z-center\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/2-2.png\";s:4:\"pic3\";s:0:\"\";s:4:\"pic4\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style4\/3.jpg","param":"a:5:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/3-1.png\";s:5:\"show1\";s:8:\"z-center\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/3-2.png\";s:4:\"pic3\";s:0:\"\";s:4:\"pic4\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style4\/4.jpg","param":"a:5:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/4-1.png\";s:5:\"show1\";s:8:\"z-center\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/4-2.png\";s:4:\"pic3\";s:0:\"\";s:4:\"pic4\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style4\/5.jpg","param":"a:5:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/5-1.png\";s:5:\"show1\";s:8:\"z-center\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/5-2.png\";s:4:\"pic3\";s:0:\"\";s:4:\"pic4\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style4\/6.jpg","param":"a:5:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/6-1.png\";s:5:\"show1\";s:8:\"z-center\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/6-2.png\";s:4:\"pic3\";s:0:\"\";s:4:\"pic4\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"6","thumb":"addons\/scene_cube\/demo\/style4\/7.jpg","param":"a:5:{s:4:\"pic1\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/7-1.png\";s:5:\"show1\";s:8:\"z-center\";s:4:\"pic2\";s:69:\"__URL__addons\/scene_cube\/demo\/style4\/7-2.png\";s:4:\"pic3\";s:0:\"\";s:4:\"pic4\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"3","thumb":"addons\/scene_cube\/demo\/style4\/9.jpg","param":"a:2:{s:6:\"btnimg\";s:71:\"__URL__addons\/scene_cube\/demo\/style4\/9_btn.jpg\";s:11:\"share_thumb\";s:0:\"\";}","create_time":"0"},{"listorder":"0","m_type":"31","thumb":"addons\/scene_cube\/demo\/style4\/10.jpg","param":"a:6:{s:6:\"btnimg\";s:72:\"__URL__addons\/scene_cube\/demo\/style4\/10_btn.jpg\";s:3:\"tel\";s:11:\"13813874744\";s:5:\"sname\";s:18:\"\u662f\u6253\u53d1\u65af\u8482\u82ac\";s:5:\"place\";s:36:\"\u4e0a\u6d77\u5e02\u6768\u6d66\u533a\u56fd\u548c\u8def36\u53f7-a12\";s:3:\"lng\";s:10:\"121.525772\";s:3:\"lat\";s:9:\"31.308563\";}","create_time":"0"}]';
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
		