<?php

/**
 * 微餐饮回复处理类
 */
defined('IN_IA') or exit('Access Denied');
class Jufeng_wcyModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
	$content = $this->message['content'];
	$foods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND title LIKE '%{$content}%' ");
	$pcate = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND parentid = '0' AND name LIKE '%{$content}%' ");
	$news[] = array(
				'title' =>'在线订餐',
				'description' =>'方便快捷，价格优惠',
				'picurl' =>'../addons/jufeng_wcy/images/dc.jpg',
				'url' =>$this->createMobileUrl('dianjia'),
);
$foodsnum = count($foods);
$pcatenum = count($pcate);
if($foodsnum == 0 && $pcatenum == 0){
	$news[] = array(
				'title' =>'直接回复你想吃的，可搜索菜品和餐馆哦',
);
$news[] = array(
				'title' =>'没有关于【'.$content.'】的菜品或餐馆',
);
	}
	if($pcatenum < 9){$printnum = $pcatenum;}else{$printnum = 9;}
	for($i=0;$i<$printnum;$i++){
	$news[] = array(
				'title' =>"【".$pcate[$i]['name']."】——"."热度：".$pcate[$i]['total'],
				'description' =>"",
				'picurl' =>$_W['attachurl'].$pcate[$i]['thumb'],
				'url' =>$this->createMobileUrl('list',array('pcate'=>$pcate[$i]['id'])),
);
	}
	$printnum = 9 - $printnum;
if($foodsnum < $printnum){$printnum = $foodsnum;}
	for($i=0;$i<$printnum;$i++){
		$dianjia = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND id = '{$foods[$i]['pcate']}'");
	$news[] = array(
				'title' =>"【".$foods[$i]['title']."】——".$dianjia['name'],
				'description' =>"菜品热度：".$foods[$i]['hits'],
				'picurl' =>$_W['attachurl'].$foods[$i]['thumb'],
				'url' =>$this->createMobileUrl('list',array('pcate'=>$foods[$i]['pcate'])),
);
	}
return $this->respNews($news);
	}

}

