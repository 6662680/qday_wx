<?php


defined('IN_IA') or exit('Access Denied');
define('CURRENT_VERSION', 0.1);

class Meepo_qmbttzModuleSite extends WeModuleSite {

	public function doWebSet(){
		global $_W,$_GPC;
		$id = $_GPC['id'];
		$weid = $_W['weid'];
		
		
		$reply = pdo_fetch("SELECT * FROM ".tablename('meepo_qmbttz_set')." WHERE weid=:weid limit 1",array(':weid'=>$weid));
		
		
		
		if(checksubmit()){
		
			$data = array(
				'weid'=>$weid,
				'title'=>$_GPC['title'],
				'share_title' => $_GPC['share_title'],
				'share_desc' => $_GPC['share_desc'],
				'share_url'=>$_GPC['share_url'],
				'share_txt'=>$_GPC['share_txt'],
				'copyright'=>$_GPC['copyright'],
			);
			
			if(empty($reply)){
				pdo_insert('meepo_qmbttz_set',$data);
			}else{
				pdo_update('meepo_qmbttz_set',$data,array('id'=>$settings['id']));
			}
			message('操作成功',$this->createWebUrl('set'));
			
		}
		
		if(empty($reply)){
			$reply = array(
			
				'title'=>'冰桶挑战',
				'share_title'=>'冰桶挑战，关怀渐冻人，支持弱势公益。',
				'share_desc'=>'“冰桶挑战”，由美国棒球运动员Peter　Frates发起，旨在帮助那些患有“渐冻人症”(ALS)的群体。当前已经在全球流行，国内外部分娱乐巨星、运动员以及IT行业部分大佬均积极参与的同时，并献出爱心捐款。',
				'share_txt'=>'朋友们，当你们品尝美食的时候，你是否知道有一群人只能看着津液流淌不止，等待身边的亲人一口一口地喂下；朋友，当你和别人沟通且开怀大笑的时候，是否知道有一群人有口难开，用眼睛一眨一眨交流，眼睛里浸满泪花；朋友，当你们游走古迹美景的时候，是否知道有一群人全身僵硬躺在病床上不能翻身，不能表达苦楚，不能抓挠痛痒，有时就快要不能呼吸……正是这样一群人，他们拥有炙热的灵魂，他们拥有不屈的脊梁，他们拥有聪明的头脑，但他们需要你，需要你的爱和关怀。朋友，你听见他们的呼唤了吗？他们拥有同一个名字叫“渐冻人”（ALS）。我相信在爱的光芒下，他们会慢慢融化，我相信只要坚持就会重生。',
				'share_url'=>$_W['siteroot'].$this->createMobileUrl('list'),
			);
		}
		
		include $this->template('set');
	
	}
	
	public function doMobilelist(){
		
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$setting = pdo_fetch("SELECT * FROM ".tablename('meepo_qmbttz_set')." WHERE weid=:weid limit 1",array(':weid'=>$weid));
		
		$_share_img=$_W['siteroot'].'./addons/meepo_qmbttz/share-logo.png';
		
		$_share['link']=$setting['share_url'];
		$_share['title'] = $setting['share_title'];
		//print_r($_share['title']);
		$_share_content= $setting['share_desc'];
		
		include $this->template('list');
	}

}