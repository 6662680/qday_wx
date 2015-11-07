<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'post','del');
$do = in_array($do, $dos) ? $do : 'display';
load()->model('mc');

if($do == 'display') {
	$_W['page']['title'] = '会员列表 - 会员 - 会员中心';
	global $_GPC, $_W;
	$groups = mc_groups();
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$condition = '';
	$condition .= empty($_GPC['mobile']) ? '' : " AND `mobile` LIKE '%".trim($_GPC['mobile'])."%'";
	$condition .= empty($_GPC['email']) ? '' : " AND `email` LIKE '%".trim($_GPC['email'])."%'";
	$condition .= empty($_GPC['username']) ? '' : " AND (( `realname` LIKE '%".trim($_GPC['username'])."%' ) OR ( `nickname` LIKE '%".trim($_GPC['username'])."%' )) ";
	$condition .= intval($_GPC['groupid']) > 0 ?  " AND `groupid` = '".intval($_GPC['groupid'])."'" : '';
	$sql = "SELECT uid, uniacid, groupid, realname, nickname, email, mobile, createtime  FROM ".tablename('mc_members')." WHERE uniacid = '{$_W['uniacid']}' ".$condition." ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall($sql);
	if(!empty($list)) {
		foreach($list as &$li) {
			if(empty($li['email']) || (!empty($li['email']) && substr($li['email'], -6) == 'qdaygroup.com' && strlen($li['email']) == 39)) {
				$li['email_effective'] = 0;
			} else {
				$li['email_effective'] = 1;
			}
		}
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_members')." WHERE uniacid = '{$_W['uniacid']}' ".$condition);
	$pager = pagination($total, $pindex, $psize);
}

if($do == 'post') {
	$_W['page']['title'] = '编辑会员资料 - 会员 - 会员中心';
	$uid = intval($_GPC['uid']);
	if ($_W['ispost'] && $_W['isajax']) {
		$uid = $_GPC['uid'];
		$password = $_GPC['password'];
		$sql = 'SELECT `uid`, `salt` FROM ' . tablename('mc_members') . " WHERE `uniacid`=:uniacid AND `uid` = :uid";
		$user = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
		if(empty($user) || $user['uid'] != $uid) {
			exit('error');
		}
		$password = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
		if (pdo_update('mc_members', array('password' => $password), array('uid' => $uid))) {
			exit('success');
		}
		exit('othererror');
	}
	if (checksubmit('submit')) {
		$uid = intval($_GPC['uid']);

		if (!empty($_GPC)) {
			if (!empty($_GPC['birth'])) {
				$_GPC['birthyear'] = $_GPC['birth']['year'];
				$_GPC['birthmonth'] = $_GPC['birth']['month'];
				$_GPC['birthday'] = $_GPC['birth']['day'];
			}
			if (!empty($_GPC['reside'])) {
				$_GPC['resideprovince'] = $_GPC['reside']['province'];
				$_GPC['residecity'] = $_GPC['reside']['city'];
				$_GPC['residedist'] = $_GPC['reside']['district'];
			}
			unset($_GPC['uid']);
			if(!empty($_GPC['fanid'])) {
								if(empty($_GPC['email']) && empty($_GPC['mobile'])) {
					$_GPC['email'] = md5($_GPC['openid']) . '@qdaygroup.com';
				}
				$fanid = intval($_GPC['fanid']);
								$struct = array_keys(mc_fields());
				$struct[] = 'birthyear';
				$struct[] = 'birthmonth';
				$struct[] = 'birthday';
				$struct[] = 'resideprovince';
				$struct[] = 'residecity';
				$struct[] = 'residedist';
				$struct[] = 'groupid';
				unset($_GPC['reside'], $_GPC['birth']);

				foreach ($_GPC as $field => $value) {
					if(!in_array($field, $struct)) {
						unset($_GPC[$field]);
					}
				}
				if(!empty($_GPC['avatar'])) {
					if(strexists($_GPC['avatar'], 'attachment/images/global/avatars/avatar_')) {
						$_GPC['avatar'] = str_replace($_W['attachurl'], '', $_GPC['avatar']);
					}
				}
				$condition = '';
								if(!empty($_GPC['email'])) {
					$emailexists = pdo_fetchcolumn("SELECT email FROM ".tablename('mc_members')." WHERE uniacid = :uniacid AND email = :email " . $condition, array(':uniacid' => $_W['uniacid'], ':email' => trim($_GPC['email'])));
					if($emailexists) {
						unset($_GPC['email']);
					}
				}
				if(!empty($_GPC['mobile'])) {
					$mobilexists = pdo_fetchcolumn("SELECT mobile FROM ".tablename('mc_members')." WHERE uniacid = :uniacid AND mobile = :mobile " . $condition, array(':uniacid' => $_W['uniacid'], ':mobile' => trim($_GPC['mobile'])));
					if($mobilexists) {
						unset($_GPC['mobile']);
					}
				}
				$_GPC['uniacid'] = $_W['uniacid'];
				$_GPC['createtime'] = TIMESTAMP;
				pdo_insert('mc_members', $_GPC);
				$uid = pdo_insertid();
				pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fanid, 'uniacid' => $_W['uniacid']));
				message('更新资料成功！', url('mc/member/post', array('uid' => $uid)), 'success');
			} else {
				$email_effective = intval($_GPC['email_effective']);
				if(($email_effective == 1 && empty($_GPC['email']))) {
					unset($_GPC['email']);
				}
				$uid = mc_update($uid, $_GPC);
			}
		}
		message('更新资料成功！', referer(), 'success');
	}
	
	load()->func('tpl');
	$groups = mc_groups($_W['uniacid']);
	$profile = mc_fetch($uid);
	if(!empty($profile)) {
		if(empty($profile['email']) || (!empty($profile['email']) && substr($profile['email'], -6) == 'qdaygroup.com' && strlen($profile['email']) == 39)) {
						$profile['email_effective'] = 1;
			$profile['email'] = '';
		} else {
						$profile['email_effective'] = 2;
		}
	}

	if(empty($uid)) {
		$fanid = intval($_GPC['fanid']);
		$tag = pdo_fetchcolumn('SELECT tag FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND fanid = :fanid', array(':uniacid' => $_W['uniacid'], ':fanid' => $fanid));
		if(is_base64($tag)){
			$tag = base64_decode($tag);
		}
		if(is_serialized($tag)){
			$fan = iunserializer($tag);
		}
		if(!empty($tag)) {
			if(!empty($fan['nickname'])) {
				$profile['nickname'] = $fan['nickname'];
			}
			if(!empty($fan['sex'])) {
				$profile['gender'] = $fan['sex'];
			}
			if(!empty($fan['city'])) {
				$profile['residecity'] = $fan['city'] . '市';
			}
			if(!empty($fan['province'])) {
				$profile['resideprovince'] = $fan['province'] . '省';
			}
			if(!empty($fan['country'])) {
				$profile['nationality'] = $fan['country'];
			}
			if(!empty($fan['headimgurl'])) {
				$profile['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
			}
		}
	}
}

if($do == 'del') {
	$_W['page']['title'] = '删除会员资料 - 会员 - 会员中心';
	if(checksubmit('submit')) {
		if(!empty($_GPC['uid'])) {
			$instr = implode(',',$_GPC['uid']);
			pdo_query("DELETE FROM ".tablename('mc_members')." WHERE `uniacid` = {$_W['uniacid']} AND `uid` IN ({$instr})");
			message('删除成功！', referer(), 'success');
		}
		message('请选择要删除的项目！', referer(), 'error');
	}
}

template('mc/member');