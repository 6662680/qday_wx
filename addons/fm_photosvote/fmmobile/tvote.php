<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
if($_W['isajax']) {
	
			//$uniacid = $_W['acid'];//当前公众号ID	
			//$oauthuser = $this->FM_checkoauth();
			//$from_user = $oauthuser['from_user'];
			//$avatar = $oauthuser['avatar'];
			//$nickname = $oauthuser['nickname'];
			//$follow = $oauthuser['follow'];
			//$tfrom_user = $_GPC['tfrom_user'];
			//$from_user = $oauthuser['from_user'];	
			
			$rid = $_GPC['rid'];
			$tfrom = $_GPC['tfrom'];
			$vote = $_GPC['vote'];			
			$tid = $_GPC['tid'];
			
			if (!empty($tid)) {
				$tuser = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and id = :id and rid = :rid", array(':uniacid' => $uniacid,':id' => $tid,':rid' => $rid));
				$tfrom_user = $tuser['from_user'];
			}else {
				$tfrom_user = $_GPC['tfrom_user'];
			}
			
			// $user = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and id = :id and rid = :rid", array(':uniacid' => $uniacid,':id' => $tid,':rid' => $rid));
			//$tfrom_user = $user['from_user'];
			
			//$from_user = $_GPC['from_user'];
			//$from_user = $_COOKIE["user_oauth2_openid"];
			//$from_user_putonghao = $_COOKIE["user_putonghao_openid"];
			
			$fromuser = $_GPC["fromuser"];//分享人
			
			//$from_user = base64_encode(authcode($_COOKIE["user_oauth2_openid"], 'ENCODE'));
			//if (empty($from_user)){
			//	$from_user = $from_user;
			//	$from_user = $from_user;	
			//}
			if (empty($fromuser)){
				$fromuser = $_COOKIE["user_fromuser_openid"];
			}		
			//echo $fromuser;
			//exit;
			////$this->checkoauth2($rid,$from_user);//查询是否有cookie信息
			//活动规则
			if (!empty($rid)) {
				$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));$bgarr = iunserializer($reply['bgarr']);
			}
			
			
			include $this->template('tvote');
			exit();
		}