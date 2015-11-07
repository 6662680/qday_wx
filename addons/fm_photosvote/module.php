<?php
/**
 * 女神来了模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class Fm_photosvoteModule extends WeModule
{
	public $title = '女神来了';
	public $table_reply = 'fm_photosvote_reply';
	public $table_users = 'fm_photosvote_provevote';
	public $table_log = 'fm_photosvote_votelog';
	public $table_bbsreply = 'fm_photosvote_bbsreply';
	public $table_banners = 'fm_photosvote_banners';
	public $table_advs = 'fm_photosvote_advs';
	public $table_gift = 'fm_photosvote_gift';
	public $table_data = 'fm_photosvote_data';

	public function fieldsFormDisplay($rid = 0)
	{
		global $_GPC, $_W;
		load()->func('tpl');
		load()->func('communication');
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$bgarr = iunserializer($reply['bgarr']);
			$qiniu = iunserializer($reply['qiniu']);
			$messagetemplate = iunserializer($reply['mtemplates']);
			$huodong = iunserializer($reply['huodong']);
			$istop = iunserializer($reply['istop']);
			//$reply['a'] = 'aHR0cDovL24uZm1vb25zLmNvbS9hcGkvYXBpLnBocD8mYXBpPWFwaQ==';
			$award = pdo_fetchall("SELECT * FROM " . tablename($this->table_gift) . " WHERE rid = :rid ORDER BY `id` ASC", array(':rid' => $rid));
			if (!empty($award)) {
				foreach ($award as &$pointer) {
					if (!empty($pointer['activation_code'])) {
						$pointer['activation_code'] = implode("\n", (array)iunserializer($pointer['activation_code']));
					}
				}
			}
		} else {
			$reply = array('periodlottery' => 1, 'maxlottery' => 1, 'a' => 'aHR0cDovL24uZm1vb25zLmNvbS9hcGkvYXBpLnBocD8mYXBpPWFwaQ==',);
		}
		$now = time();
		$reply['start_time'] = empty($reply['start_time']) ? $now : $reply['start_time'];
		$reply['end_time'] = empty($reply['end_time']) ? strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)) : $reply['end_time'];
		$reply['tstart_time'] = empty($reply['tstart_time']) ? strtotime(date("Y-m-d H:i", $now + 3 * 24 * 3600)) : $reply['tstart_time'];
		$reply['tend_time'] = empty($reply['tend_time']) ? strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)) : $reply['tend_time'];
		$reply['bstart_time'] = empty($reply['bstart_time']) ? $now : $reply['bstart_time'];
		$reply['bend_time'] = empty($reply['bend_time']) ? strtotime(date("Y-m-d H:i", $now + 3 * 24 * 3600)) : $reply['bend_time'];
		$reply['ttipstart'] = empty($reply['ttipstart']) ? "投票时间还没有开始!" : $reply['ttipstart'];
		$reply['ttipend'] = empty($reply['ttipend']) ? "投票时间已经结束开始!" : $reply['ttipend'];
		$reply['btipstart'] = empty($reply['btipstart']) ? "报名时间还没有开始!" : $reply['btipstart'];
		$reply['btipend'] = empty($reply['btipend']) ? "报名时间已经结束!" : $reply['btipend'];
		$reply['isbbsreply'] = !isset($reply['isbbsreply']) ? "1" : $reply['isbbsreply'];
		$reply['opensubscribe'] = !isset($reply['opensubscribe']) ? "4" : $reply['opensubscribe'];
		$reply['share_shownum'] = !isset($reply['share_shownum']) ? "50" : $reply['share_shownum'];
		$reply['picture'] = empty($reply['picture']) ? "../addons/fm_photosvote/template/images/pimages.jpg" : $reply['picture'];
		$reply['sharephoto'] = empty($reply['sharephoto']) ? "../addons/fm_photosvote/template/images/pimages.jpg" : $reply['sharephoto'];
		$reply['stopping'] = empty($reply['stopping']) ? "../addons/fm_photosvote/template/images/stopping.jpg" : $reply['stopping'];
		$reply['nostart'] = empty($reply['nostart']) ? "../addons/fm_photosvote/template/images/nostart.jpg" : $reply['nostart'];
		$reply['end'] = empty($reply['end']) ? "../addons/fm_photosvote/template/images/end.jpg" : $reply['end'];
		$reply['cqtp'] = !isset($reply['cqtp']) ? "0" : $reply['cqtp'];
		$reply['moshi'] = !isset($reply['moshi']) ? "1" : $reply['moshi'];;
		$reply['tpsh'] = !isset($reply['tpsh']) ? "0" : $reply['tpsh'];
		$reply['indexorder'] = !isset($reply['indexorder']) ? "1" : $reply['indexorder'];
		$reply['indexpx'] = !isset($reply['indexpx']) ? "0" : $reply['indexpx'];
		$reply['tpxz'] = empty($reply['tpxz']) ? "5" : $reply['tpxz'];
		$reply['autolitpic'] = empty($reply['autolitpic']) ? "500" : $reply['autolitpic'];
		$reply['autozl'] = empty($reply['autozl']) ? "50" : $reply['autozl'];
		$reply['daytpxz'] = empty($reply['daytpxz']) ? "8" : $reply['daytpxz'];
		$reply['dayonetp'] = empty($reply['dayonetp']) ? "1" : $reply['dayonetp'];
		$reply['allonetp'] = empty($reply['allonetp']) ? "1" : $reply['allonetp'];
		$reply['fansmostvote'] = empty($reply['fansmostvote']) ? "1" : $reply['fansmostvote'];
		$reply['addpv'] = empty($reply['addpv']) ? "0" : $reply['addpv'];
		$reply['command'] = empty($reply['command']) ? "报名" : $reply['command'];
		$reply['indextpxz'] = empty($reply['indextpxz']) ? "10" : $reply['indextpxz'];
		$reply['phbtpxz'] = empty($reply['phbtpxz']) ? "10" : $reply['phbtpxz'];
		$reply['userinfo'] = empty($reply['userinfo']) ? "请留下您的个人信息，谢谢!" : $reply['userinfo'];
		$reply['isindex'] = !isset($reply['isindex']) ? "1" : $reply['isindex'];
		$reply['isrealname'] = !isset($reply['isrealname']) ? "1" : $reply['isrealname'];
		$reply['ismobile'] = !isset($reply['ismobile']) ? "1" : $reply['ismobile'];
		$reply['isjob'] = !isset($reply['isjob']) ? "1" : $reply['isjob'];
		$reply['isxingqu'] = !isset($reply['isxingqu']) ? "1" : $reply['isxingqu'];
		$reply['isfans'] = !isset($reply['isfans']) ? "1" : $reply['isfans'];
		$reply['copyrighturl'] = empty($reply['copyrighturl']) ? "http://" . $_SERVER ['HTTP_HOST'] : $reply['copyrighturl'];
		$reply['copyright'] = empty($reply['copyright']) ? $_W['account']['name'] : $reply['copyright'];
		$reply['xuninum'] = !isset($reply['xuninum']) ? "500" : $reply['xuninum'];
		$reply['xuninumtime'] = !isset($reply['xuninumtime']) ? "86400" : $reply['xuninumtime'];
		$reply['xuninuminitial'] = !isset($reply['xuninuminitial']) ? "10" : $reply['xuninuminitial'];
		$reply['xuninumending'] = !isset($reply['xuninumending']) ? "50" : $reply['xuninumending'];
		$reply['zbgcolor'] = empty($reply['zbgcolor']) ? "#3a0255" : $reply['zbgcolor'];
		$reply['zbg'] = empty($reply['zbg']) ? "../addons/fm_photosvote/template/mobile/photos/bg.jpg" : $reply['zbg'];
		$reply['zbgtj'] = empty($reply['zbgtj']) ? "../addons/fm_photosvote/template/mobile/photos/bg_x.png" : $reply['zbgtj'];
		$reply['lapiao'] = empty($reply['lapiao']) ? "拉票" : $reply['lapiao'];
		$reply['sharename'] = empty($reply['sharename']) ? "分享" : $reply['sharename'];
		$reply['tpname'] = empty($reply['tpname']) ? "投Ta一票" : $reply['tpname'];
		$reply['rqname'] = empty($reply['rqname']) ? "人气" : $reply['rqname'];
		$reply['tpsname'] = empty($reply['tpsname']) ? "票数" : $reply['tpsname'];
		//$reply['d'] = base64_decode("aHR0cDovL2FwaS5mbW9vbnMuY29tL2luZGV4LnBocD8md2VidXJsPQ==") . $_SERVER ['HTTP_HOST'] . "&visitorsip=" . $_W['clientip'] . "&modules=" . $_GPC['m'];
		$reply['addpvapp'] = !isset($reply['addpvapp']) ? "1" : $reply['addpvapp'];
		$reply['iscode'] = !isset($reply['iscode']) ? "0" : $reply['iscode'];
		$reply['isedes'] = !isset($reply['isedes']) ? "1" : $reply['isedes'];
		$reply['tmreply'] = !isset($reply['tmreply']) ? "1" : $reply['tmreply'];
		$reply['tmyushe'] = !isset($reply['tmyushe']) ? "1" : $reply['tmyushe'];
		$reply['isipv'] = !isset($reply['isipv']) ? "1" : $reply['isipv'];
		$reply['ipturl'] = !isset($reply['ipturl']) ? "1" : $reply['ipturl'];
		//$reply['dc'] = ihttp_get($reply['d']);
		$reply['ipstopvote'] = !isset($reply['ipstopvote']) ? "1" : $reply['ipstopvote'];
		$reply['tmoshi'] = !isset($reply['tmoshi']) ? "2" : $reply['tmoshi'];
		$reply['mediatype'] = !isset($reply['mediatype']) ? "1" : $reply['mediatype'];
		$reply['mediatypem'] = !isset($reply['mediatypem']) ? "0" : $reply['mediatypem'];
		$reply['mediatypev'] = !isset($reply['mediatypev']) ? "0" : $reply['mediatypev'];
		$reply['votesuccess'] = empty($reply['votesuccess']) ? "恭喜您成功的为编号为：#编号# ,姓名为： #参赛人名# 的参赛者投了一票！" : $reply['votesuccess'];
		$reply['subscribedes'] = empty($reply['subscribedes']) ? "请长按二维码关注或点击“关注投票”，前往" . $_W['account']['name'] . "为您的好友投票。如已关注，请关闭此对话框，进入视频为Ta点赞或拉票。" : $reply['subscribedes'];
		$reply['csrs'] = empty($reply['csrs']) ? "参赛人数" : $reply['csrs'];
		$reply['ljtp'] = empty($reply['ljtp']) ? "累计投票" : $reply['ljtp'];
		$reply['cyrs'] = empty($reply['cyrs']) ? "参与人数" : $reply['cyrs'];
		$reply['voicebg'] = empty($reply['voicebg']) ? "../addons/fm_photosvote/template/mobile/audio/t1/images/voicebg.jpg" : $reply['voicebg'];
		$reply['voicemoshi'] = !isset($reply['voicemoshi']) ? "1" : $reply['voicemoshi'];
		$reply['isdaojishi'] = !isset($reply['isdaojishi']) ? "0" : $reply['isdaojishi'];
		$reply['ttipvote'] = empty($reply['ttipvote']) ? "你的投票时间已经结束" : $reply['ttipvote'];
		$reply['cyrs'] = empty($reply['cyrs']) ? "参与人数" : $reply['cyrs'];
		$reply['limitip'] = empty($reply['limitip']) ? "10" : $reply['limitip'];
		$reply['votetime'] = empty($reply['votetime']) ? "10" : $reply['votetime'];
		$reply['iplocaldes'] = empty($reply['iplocaldes']) ? "你所在的地区不在本次投票地区。本次投票地区： #限制地区# 内" : $reply['iplocaldes'];
		$reply['zanzhums'] = !isset($reply['zanzhums']) ? "1" : $reply['zanzhums'];
		$istop['istopheader'] = !isset($istop['istopheader']) ? "1" : $istop['istopheader'];
		$istop['ipannounce'] = !isset($istop['ipannounce']) ? "0" : $istop['ipannounce'];
		$istop['isbgaudio'] = !isset($istop['isbgaudio']) ? "0" : $istop['isbgaudio'];
		$huodong['ishuodong'] = !isset($huodong['ishuodong']) ? "0" : $huodong['ishuodong'];
		$bgarr['topbgcolor'] = empty($bgarr['topbgcolor']) ? "" : $bgarr['topbgcolor'];
		$bgarr['topbg'] = empty($bgarr['topbg']) ? "" : $bgarr['topbg'];
		$bgarr['topbgtext'] = empty($bgarr['topbgtext']) ? "" : $bgarr['topbgtext'];
		$bgarr['topbgrightcolor'] = empty($bgarr['topbgrightcolor']) ? "" : $bgarr['topbgrightcolor'];
		$bgarr['topbgright'] = empty($bgarr['topbgright']) ? "" : $bgarr['topbgright'];
		$bgarr['foobg1'] = empty($bgarr['foobg1']) ? "" : $bgarr['foobg1'];
		$bgarr['foobg2'] = empty($bgarr['foobg2']) ? "" : $bgarr['foobg2'];
		$bgarr['foobgtextn'] = empty($bgarr['foobgtextn']) ? "" : $bgarr['foobgtextn'];
		$bgarr['foobgtexty'] = empty($bgarr['foobgtexty']) ? "" : $bgarr['foobgtexty'];
		$bgarr['foobgtextmore'] = empty($bgarr['foobgtextmore']) ? "" : $bgarr['foobgtextmore'];
		$bgarr['foobgmorecolor'] = empty($bgarr['foobgmorecolor']) ? "" : $bgarr['foobgmorecolor'];
		$bgarr['foobgmore'] = empty($bgarr['foobgmore']) ? "" : $bgarr['foobgmore'];
		//$bgarr['t'] = @json_decode($reply['dc']['content'], true);
		$bgarr['bodytextcolor'] = empty($bgarr['bodytextcolor']) ? "" : $bgarr['bodytextcolor'];
		$bgarr['bodynumcolor'] = empty($bgarr['bodynumcolor']) ? "" : $bgarr['bodynumcolor'];
		$bgarr['bodytscolor'] = empty($bgarr['bodytscolor']) ? "" : $bgarr['bodytscolor'];
		$bgarr['bodytsbg'] = empty($bgarr['bodytsbg']) ? "" : $bgarr['bodytsbg'];
		$bgarr['copyrightcolor'] = empty($bgarr['copyrightcolor']) ? "" : $bgarr['copyrightcolor'];
		$bgarr['inputcolor'] = empty($bgarr['inputcolor']) ? "" : $bgarr['inputcolor'];
		//if ($bgarr['t']['s']==0) {
		//	$settingurl = url('profile/module/setting',array('m'=>'fm_photosvote'));
		//	message($bgarr['t']['m'],$settingurl,'error');			
		//}
		$qiniu['isqiniu'] = !isset($qiniu['isqiniu']) ? "0" : $qiniu['isqiniu'];
		$picture = $reply['picture'];
		if (substr($picture, 0, 6) == 'images') {
			$picture = $_W['attachurl'] . $picture;
		}
		if (substr($picture, 0, 6) == 'images') {
			$picture = $_W['attachurl'] . $picture;
		}
		$sharephoto = $reply['sharephoto'];
		if (substr($sharephoto, 0, 6) == 'images') {
			$sharephoto = $_W['attachurl'] . $sharephoto;
		}
		if (substr($sharephoto, 0, 6) == 'images') {
			$sharephoto = $_W['attachurl'] . $sharephoto;
		}
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0)
	{
		return '';
	}

	public function fieldsFormSubmit($rid)
	{
		global $_GPC, $_W;
		load()->func('communication');
		$uniacid = empty($_W['acid']) ? $_W['uniacid'] : $_W['acid'];
		$id = intval($_GPC['reply_id']);
		//$replyt = array('a' => 'aHR0cDovL24uZm1vb25zLmNvbS9hcGkvYXBpLnBocD8mYXBpPWFwaQ==',);
		$insert = array('rid' => $rid, 'uniacid' => $uniacid, 'title' => $_GPC['title'], 'picture' => $_GPC['picture'], 'sharephoto' => $_GPC['sharephoto'], 'subscribe' => intval($_GPC['subscribe']), 'opensubscribe' => intval($_GPC['opensubscribe']), 'tj' => $_GPC['tj'], 'moshi' => intval($_GPC['moshi']), 'cqtp' => intval($_GPC['cqtp']), 'tpsh' => intval($_GPC['tpsh']), 'indexpx' => intval($_GPC['indexpx']), 'tpxz' => $_GPC['tpxz'] > 8 ? '8' : intval($_GPC['tpxz']), 'lapiao' => $_GPC['lapiao'], 'sharename' => $_GPC['sharename'], 'autolitpic' => intval($_GPC['autolitpic']), 'autozl' => $_GPC['autozl'] > 100 ? '100' : intval($_GPC['autozl']), 'daytpxz' => intval($_GPC['daytpxz']), 'dayonetp' => intval($_GPC['dayonetp']), 'allonetp' => intval($_GPC['allonetp']), 'fansmostvote' => intval($_GPC['fansmostvote']), 'indextpxz' => intval($_GPC['indextpxz']), 'addpv' => intval($_GPC['addpv']), 'phbtpxz' => intval($_GPC['phbtpxz']), 'description' => $_GPC['description'], 'ttipstart' => $_GPC['ttipstart'], 'ttipend' => $_GPC['ttipend'], 'btipstart' => $_GPC['btipstart'], 'btipend' => $_GPC['btipend'], 'content' => htmlspecialchars_decode($_GPC['content']), 'start_time' => strtotime($_GPC['datelimit']['start']), 'end_time' => strtotime($_GPC['datelimit']['end']), 'tstart_time' => strtotime($_GPC['tdatelimit']['start']), 'tend_time' => strtotime($_GPC['tdatelimit']['end']), 'bstart_time' => strtotime($_GPC['bdatelimit']['start']), 'bend_time' => strtotime($_GPC['bdatelimit']['end']), 'isvisits' => intval($_GPC['isvisits']), 'status' => intval($_GPC['status']), 'isbbsreply' => intval($_GPC['isbbsreply']), 'share_shownum' => intval($_GPC['share_shownum']), 'shareurl' => $_GPC['shareurl'], 'sharetitle' => $_GPC['sharetitle'], 'sharecontent' => $_GPC['sharecontent'], 'userinfo' => $_GPC['userinfo'], 'isindex' => intval($_GPC['isindex']), 'isvotexq' => intval($_GPC['isvotexq']), 'ispaihang' => intval($_GPC['ispaihang']), 'isreg' => intval($_GPC['isreg']), 'isdes' => intval($_GPC['isdes']), 'isrealname' => intval($_GPC['isrealname']), 'ismobile' => intval($_GPC['ismobile']), 'isweixin' => intval($_GPC['isweixin']), 'isqqhao' => intval($_GPC['isqqhao']), 'isemail' => intval($_GPC['isemail']), 'isaddress' => intval($_GPC['isaddress']), 'isjob' => intval($_GPC['isjob']), 'isxingqu' => intval($_GPC['isxingqu']), 'iscopyright' => intval($_GPC['iscopyright']), 'isfans' => intval($_GPC['isfans']), 'copyright' => $_GPC['copyright'], 'copyrighturl' => $_GPC['copyrighturl'], 'xuninum' => $_GPC['xuninum'], 'hits' => $_GPC['hits'], 'xuninumtime' => $_GPC['xuninumtime'], 'xuninuminitial' => $_GPC['xuninuminitial'], 'xuninumending' => $_GPC['xuninumending'], 'stopping' => $_GPC['stopping'], 'nostart' => $_GPC['nostart'], 'end' => $_GPC['end'], 'zbgcolor' => $_GPC['zbgcolor'], 'zbg' => $_GPC['zbg'], 'zbgtj' => $_GPC['zbgtj'], 'addpvapp' => intval($_GPC['addpvapp']), 'iscode' => intval($_GPC['iscode']), 'codekey' => $_GPC['codekey'], 'isedes' => intval($_GPC['isedes']), 'tmreply' => intval($_GPC['tmreply']), 'tmyushe' => intval($_GPC['tmyushe']), 'isipv' => intval($_GPC['isipv']), 'ipturl' => intval($_GPC['ipturl']), 'ipstopvote' => intval($_GPC['ipstopvote']), 'tmoshi' => intval($_GPC['tmoshi']), 'mediatype' => intval($_GPC['mediatype']), 'mediatypem' => intval($_GPC['mediatypem']), 'mediatypev' => intval($_GPC['mediatypev']), 'tpname' => $_GPC['tpname'], 'rqname' => $_GPC['rqname'], 'tpsname' => $_GPC['tpsname'], 'votesuccess' => $_GPC['votesuccess'], 'subscribedes' => $_GPC['subscribedes'], 'csrs' => $_GPC['csrs'], 'ljtp' => $_GPC['ljtp'], 'cyrs' => $_GPC['cyrs'], 'voicemoshi' => $_GPC['voicemoshi'], 'votetime' => $_GPC['votetime'], 'ttipvote' => $_GPC['ttipvote'], 'isdaojishi' => $_GPC['isdaojishi'], 'limitip' => $_GPC['limitip'], 'iplocallimit' => $_GPC['iplocallimit'], 'iplocaldes' => $_GPC['iplocaldes'], 'indexorder' => $_GPC['indexorder'], 'zanzhums' => $_GPC['zanzhums'], 'command' => $_GPC['command'], 'webinfo' => htmlspecialchars_decode($_GPC['webinfo']),);
		$mtemplates = array('messagetemplate' => $_GPC['messagetemplate'], 'regmessagetemplate' => $_GPC['regmessagetemplate'], 'shmessagetemplate' => $_GPC['shmessagetemplate'], 'fmqftemplate' => $_GPC['fmqftemplate']);
		$insert['mtemplates'] = iserializer($mtemplates);
		$istop = array('istopheader' => intval($_GPC['istopheader']), 'ipannounce' => intval($_GPC['ipannounce']), 'isbgaudio' => intval($_GPC['isbgaudio']), 'bgmusic' => $_GPC['bgmusic'],);
		$insert['istop'] = iserializer($istop);
		$huodong = array('ishuodong' => $_GPC['ishuodong'], 'huodongname' => $_GPC['huodongname'], 'huodongdes' => $_GPC['huodongdes'], 'huodongurl' => $_GPC['huodongurl'], 'hhhdpicture' => $_GPC['hhhdpicture']);
		$insert['huodong'] = iserializer($huodong);
		$bgarr = array('topbgcolor' => $_GPC['topbgcolor'], 'topbg' => $_GPC['topbg'], 'topbgtext' => $_GPC['topbgtext'], 'topbgrightcolor' => $_GPC['topbgrightcolor'], 'topbgright' => $_GPC['topbgright'], 'foobg1' => $_GPC['foobg1'], 'foobgtextn' => $_GPC['foobgtextn'], 'foobg2' => $_GPC['foobg2'], 'foobgtexty' => $_GPC['foobgtexty'], 'foobgtextmore' => $_GPC['foobgtextmore'], 'foobgmorecolor' => $_GPC['foobgmorecolor'], 'foobgmore' => $_GPC['foobgmore'], 'bodytextcolor' => $_GPC['bodytextcolor'], 'bodynumcolor' => $_GPC['bodynumcolor'], 'bodytscolor' => $_GPC['bodytscolor'], 'bodytsbg' => $_GPC['bodytsbg'], 'copyrightcolor' => $_GPC['copyrightcolor'], 'inputcolor' => $_GPC['inputcolor'],);
		$insert['bgarr'] = iserializer($bgarr);
		$qiniu = array('isqiniu' => intval($_GPC['isqiniu']), 'accesskey' => $_GPC['accesskey'], 'secretkey' => $_GPC['secretkey'], 'qnlink' => $_GPC['qnlink'], 'bucket' => $_GPC['bucket'], 'pipeline' => $_GPC['pipeline'], 'aq' => $_GPC['aq'], 'videofbl' => $_GPC['videofbl'], 'videologo' => $_GPC['videologo'], 'wmgravity' => $_GPC['wmgravity'],);
		$insert['qiniu'] = iserializer($qiniu);
		//$replyt['d'] = base64_decode($replyt['c']['apiurl']).$_SERVER ['HTTP_HOST']."&visitorsip=" . $_W['clientip'];				
		//$replyt['dc'] = ihttp_get($replyt['d']);
		//$replyt['t'] = @json_decode($replyt['dc']['content'], true);	
		
		
		//if ($replyt['t']['config']){
			if (empty($id)) {
				pdo_insert($this->table_reply, $insert);
			} else {			
				pdo_update($this->table_reply, $insert, array('id' => $id));
			}
		//}
	}

	public function ruleDeleted($rid)
	{
		pdo_delete($this->table_reply, array('rid' => $rid));
		pdo_delete($this->table_users, array('rid' => $rid));
		pdo_delete($this->table_log, array('rid' => $rid));
		pdo_delete($this->table_gift, array('rid' => $rid));
		pdo_delete($this->table_bbsreply, array('rid' => $rid));
		pdo_delete($this->table_banners, array('rid' => $rid));
		pdo_delete($this->table_advs, array('rid' => $rid));
		pdo_delete($this->table_data, array('rid' => $rid));
	}

	public function settingsDisplay($settings)
	{
		global $_GPC, $_W;
		load()->func('communication');
		//$a ='aHR0cDovL24uZm1vb25zLmNvbS9hcGkvYXBpLnBocD8mYXBpPWFwaQ==';		
		//$ca = ihttp_get(base64_decode($a));
		//$c = @json_decode($ca['content'], true);		
		//$d = base64_decode($c['apiurl']).$_SERVER ['HTTP_HOST']."&visitorsip=" . $_W['clientip'];				
		//$dc = ihttp_get($d);
		//$t = @json_decode($dc['content'], true);		
		$wechats = pdo_fetch("SELECT level FROM ".tablename('account_wechats')." WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
		if(checksubmit()) {
			$cfg = array();
			$cfg['oauthtype'] = $_GPC['oauthtype'];
			$cfg['appid'] = $_GPC['appid'];
			$cfg['secret'] = $_GPC['secret'];
			$cfg['isopenjsps'] = $_GPC['isopenjsps'];
			$cfg['ismiaoxian'] = $_GPC['ismiaoxian'];
			$cfg['mxnexttime'] = $_GPC['mxnexttime'];
			$cfg['mxtimes'] = $_GPC['mxtimes'];
			//if ($t['config']) {
				if($this->saveSettings($cfg)) {
					message('保存成功', 'refresh');
				}
			//}
		}
		
		include $this->template('setting');
	}

	

}
