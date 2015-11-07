<?php
/**
 * [WeiZan System] Copyright (c) 2014 WeiZan.Com
 * WeiZan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('mc');
$dos = array('display', 'view', 'initsync', 'updategroup', 'sms');
$do = in_array($do, $dos) ? $do : 'display';
if($do == 'display') {
	$_W['page']['title'] = '粉丝列表 - 粉丝 - 会员中心';
	if(checksubmit('submit')) {
		if (!empty($_GPC['delete'])) {
			$fanids = array();
			foreach($_GPC['delete'] as $v) {
				$fanids[] = intval($v);
			}
			pdo_query("DELETE FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = :uniacid AND fanid IN ('" . implode("','", $fanids) . "')",array(':uniacid' => $_W['uniacid']));
			message('粉丝删除成功！', url('mc/fans/', array('type' => $_GPC['type'], 'acid' => $_GPC['acid'])), 'success');
		}
	}
	$accounts = uni_accounts();
	if(empty($accounts) || !is_array($accounts) || count($accounts) == 0){
		message('请指定公众号');
	}
	if(!isset($_GPC['acid'])){
		$account = current($accounts);
		if($account !== false){
			$acid = intval($account['acid']);
		}
	} else {
		$acid = intval($_GPC['acid']);
		if(!empty($acid) && !empty($accounts[$acid])) {
			$account = $accounts[$acid];
		}
	}
	reset($accounts);
	
	if($_W['isajax']) {
		$post = $_GPC['__input'];
		if($post['method'] == 'sync') {
			if(is_array($post['fanids'])) {
				$fanids = array();
				foreach($post['fanids'] as $fanid) {
					$fanid = intval($fanid);
					$fanids[] = $fanid;
				}
				$fanids = implode(',', $fanids);
				$sql = 'SELECT `fanid`,`uid`,`openid` FROM ' . tablename('mc_mapping_fans') . " WHERE `acid`='{$acid}' AND `fanid` IN ({$fanids})";
				$ds = pdo_fetchall($sql);
				$acc = WeAccount::create($acid);
				foreach($ds as $row) {
					$fan = $acc->fansQueryInfo($row['openid'], true);
					if(!is_error($fan) && $fan['subscribe'] == 1) {
						$group = $acc->fetchFansGroupid($row['openid']);
						$record = array();
						if(!is_error($group)) {
							$record['groupid'] = $group['groupid'];
						}
						$record['updatetime'] = TIMESTAMP;
						$record['followtime'] = $fan['subscribe_time'];
						$fan['nickname'] = stripcslashes($fan['nickname']);
						$record['nickname'] = stripslashes($fan['nickname']);
						$record['tag'] = iserializer($fan);
						$record['tag'] = base64_encode($record['tag']);
						pdo_update('mc_mapping_fans', $record, array('fanid' => $row['fanid']));
						
						if(!empty($row['uid'])) {
							$user = mc_fetch($row['uid'], array('nickname', 'gender', 'residecity', 'resideprovince', 'nationality', 'avatar'));
							$rec = array();
							if(empty($user['nickname']) && !empty($fan['nickname'])) {
																$rec['nickname'] = stripslashes($fan['nickname']);
							}
							if(empty($user['gender']) && !empty($fan['sex'])) {
								$rec['gender'] = $fan['sex'];
							}
							if(empty($user['residecity']) && !empty($fan['city'])) {
								$rec['residecity'] = $fan['city'] . '市';
							}
							if(empty($user['resideprovince']) && !empty($fan['province'])) {
								$rec['resideprovince'] = $fan['province'] . '省';
							}
							if(empty($user['nationality']) && !empty($fan['country'])) {
								$rec['nationality'] = $fan['country'];
							}
							if(empty($user['avatar']) && !empty($fan['headimgurl'])) {
								$rec['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
							}
							if(!empty($rec)) {
								pdo_update('mc_members', $rec, array('uid' => $row['uid']));
							}
						}
					}
				}
			}
			exit('success');
		}
		if($post['method'] == 'download') {
			$acc = WeAccount::create($acid);
			if(!empty($post['next'])) {
				$_GPC['next_openid'] = $post['next'];
			}
			$fans = $acc->fansAll();
			if(!is_error($fans) && is_array($fans['fans'])) {
				$count = count($fans['fans']);
				$buffSize = ceil($count / 500);
				for($i = 0; $i < $buffSize; $i++) {
					$buffer = array_slice($fans['fans'], $i * 500, 500);
					$openids = implode("','", $buffer);
					$openids = "'{$openids}'";
					$sql = 'SELECT `openid` FROM ' . tablename('mc_mapping_fans') . " WHERE `acid`={$acid} AND `openid` IN ({$openids})";
					$ds = pdo_fetchall($sql);
					$exists = array();
					foreach($ds as $row) {
						$exists[] = $row['openid'];
					}
					$sql = '';
					foreach($buffer as $openid) {
						if(!empty($exists) && in_array($openid, $exists)) {
							continue;
						}
						$salt = random(8);
						$sql .= "('{$acid}', '{$_W['uniacid']}', 0, '{$openid}', '{$salt}', 1, 0, ''),";
					}
					if(!empty($sql)) {
						$sql = rtrim($sql, ',');
						$sql = 'INSERT INTO ' . tablename('mc_mapping_fans') . ' (`acid`, `uniacid`, `uid`, `openid`, `salt`, `follow`, `followtime`, `tag`) VALUES ' . $sql;
						pdo_query($sql);
					}
				}

				$ret = array();
				$ret['total'] = $fans['total'];
				if(!empty($fans['fans'])) {
					$ret['count'] = count($fans['fans']);
				} else {
					$ret['count'] = 0;
				}
				if(!empty($fans['next'])) {
					$ret['next'] = $fans['next'];
				}
				exit(json_encode($ret));
			} else {
				exit(json_encode($fans));
			}
		}
	}
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' WHERE `uniacid`=:uniacid';
	$pars = array();
	$pars[':uniacid'] = $_W['uniacid'];
	if(!empty($acid)) {
		$condition .= ' AND `acid`=:acid';
		$pars[':acid'] = $acid;
	}
	if($_GPC['type'] == 'bind') {
		$condition .= ' AND `uid`>0';
		$type = 'bind';
	}
	if($_GPC['type'] == 'unbind') {
		$condition .= ' AND `uid`=0';
		$type = 'unbind';
	}
	$nickname = trim($_GPC['nickname']);
	if(!empty($nickname)) {
		$condition .= " AND nickname LIKE '%{$nickname}%'";
	}
	$starttime = empty($_GPC['time']['start']) ? strtotime('-30 days') : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
	$follow = intval($_GPC['follow']);
	if(!$follow) {
		$orderby = ' ORDER BY fanid DESC';
		$condition .= ' AND ((followtime >= :starttime AND followtime <= :endtime) OR (unfollowtime >= :starttime AND unfollowtime <= :endtime))';
	} elseif($follow == 1) {
		$orderby = ' ORDER BY followtime DESC';
		$condition .= ' AND follow = 1 AND followtime >= :starttime AND followtime <= :endtime';
	} elseif($follow == 2) {
		$orderby = ' ORDER BY unfollowtime DESC';
		$condition .= ' AND follow = 0 AND unfollowtime >= :starttime AND unfollowtime <= :endtime';
	}
	$pars[':starttime'] = $starttime;
	$pars[':endtime'] = $endtime;
	
	$groups_data = pdo_fetchall('SELECT * FROM ' . tablename('mc_fans_groups') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if(!empty($groups_data)) {
		$groups = array();
		foreach($groups_data as $gr) {
			$groups[$gr['acid']] = iunserializer($gr['groups']);
		}
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_mapping_fans').$condition, $pars);
	$list = pdo_fetchall("SELECT * FROM ".tablename('mc_mapping_fans') . $condition . $orderby . ' LIMIT ' .($pindex - 1) * $psize.','.$psize, $pars);
	if(!empty($list)) {
		foreach($list as &$v) {
			if(!empty($v['uid'])) {
				$user = mc_fetch($v['uid'], array('realname', 'nickname', 'mobile', 'email', 'avatar'));
				if(!empty($user['avatar'])){
					$user['avatar'] = tomedia($user['avatar']);
				}
			}
			if (!empty($v['tag']) && is_string($v['tag'])) {
				if (is_base64($v['tag'])){
					$v['tag'] = base64_decode($v['tag']);
				}
								if (is_serialized($v['tag'])) {
					$v['tag'] = @iunserializer($v['tag']);
				}
				if(!empty($v['tag']['headimgurl'])) {
					$v['tag']['avatar'] = tomedia($v['tag']['headimgurl']);
					unset($v['tag']['headimgurl']);
				}
			}
			if(empty($v['tag'])) {
				$v['tag'] = array();
			}
			if(!empty($user)) {
				$niemmo = $user['realname'];
				if(empty($niemmo)) {
					$niemmo = $user['nickname'];
				}
				if(empty($niemmo)) {
					$niemmo = $user['mobile'];
				}
				if(empty($niemmo)) {
					$niemmo = $user['email'];
				}
				if(empty($niemmo) || (!empty($niemmo) && substr($niemmo, -6) == 'qdaygroup.com' && strlen($niemmo) == 39)) {
					$niemmo_effective = 0;
				} else {
					$niemmo_effective = 1;
				}
				$v['user'] = array('niemmo_effective' => $niemmo_effective, 'niemmo' => $niemmo, 'nickname' => $user['nickname']);
			}
			if(empty($v['user']['nickname']) && !empty($v['tag']['nickname'])){
				$v['user']['nickname'] = $v['tag']['nickname'];
			}
			if(empty($v['user']['avatar']) && !empty($v['tag']['avatar'])){
				$v['user']['avatar'] = $v['tag']['avatar'];
			}
			$v['account'] = $accounts[$v['acid']]['name'];
			
			unset($user,$niemmo,$niemmo_effective);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($do == 'view') {
	$_W['page']['title'] = '粉丝详情 - 粉丝 - 会员中心';
	$fanid = intval($_GPC['id']);
	if(empty($fanid)) {
		message('访问错误.');
	}
	$row = pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans')." WHERE fanid = :fanid AND uniacid = :uniacid LIMIT 1", array(':fanid' => $fanid,':uniacid' => $_W['uniacid']));	
	$account = WeAccount::create($row['acid']);
	$accountInfo = $account->fetchAccountInfo();
	$row['account'] = $accountInfo['name'];
	if(!empty($row['uid'])) {
		$user = mc_fetch($row['uid'], array('nickname', 'mobile', 'email'));
		$row['user'] = $user['nickname'];
		if(empty($row['user'])) {
			$row['user'] = $user['mobile'];
		}
		if(empty($row['user'])) {
			$row['user'] = $user['email'];
		}
		if(!empty($row['user']) && substr($row['user'], -6) == 'qdaygroup.com' && strlen($row['user']) == 39) {
			$row['user'] = "用户uid：{$row['uid']}。昵称,手机号,邮箱尚未完善";
		}
	} else {
		$row['user'] = '还未登记为会员';
	}
}

if($do == 'initsync') {
	$acid = intval($_GPC['acid']);

	if(intval($_GPC['page']) == 0) {
		message('正在更新粉丝数据,请不要关闭浏览器', url('mc/fans/initsync', array('page' => 1, 'acid' => $acid)), 'success');
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . " WHERE uniacid = :uniacid AND acid = :acid", array(':uniacid' => $_W['uniacid'], ':acid' => $acid));
	$total_page = ceil($total / $psize);
	$ds = pdo_fetchall("SELECT * FROM ".tablename('mc_mapping_fans') ." WHERE uniacid = :uniacid AND acid = :acid ORDER BY `fanid` DESC LIMIT ".($pindex - 1) * $psize.','.$psize, array(':uniacid' => $_W['uniacid'], ':acid' => $acid));
	$acc = WeAccount::create($acid);
	if(!empty($ds)) {
		foreach($ds as $row) {
			if(!empty($row['tag'])) {
				continue;
			}
			$fan = $acc->fansQueryInfo($row['openid'], true);
			if(!is_error($fan) && $fan['subscribe'] == 1) {
				$group = $acc->fetchFansGroupid($row['openid']);
				$record = array();
				if(!is_error($group)) {
					$record['groupid'] = $group['groupid'];
				}
				$record['updatetime'] = TIMESTAMP;
				$record['followtime'] = $fan['subscribe_time'];
				$fan['nickname'] = stripcslashes($fan['nickname']);
				$record['nickname'] = stripslashes($fan['nickname']);
				$record['tag'] = iserializer($fan);
				$record['tag'] = base64_encode($record['tag']);
				pdo_update('mc_mapping_fans', $record, array('fanid' => $row['fanid']));
				
				if(!empty($row['uid'])) {
					$user = mc_fetch($row['uid'], array('nickname', 'gender', 'residecity', 'resideprovince', 'nationality', 'avatar'));
					$rec = array();
					if(empty($user['nickname']) && !empty($fan['nickname'])) {
						$rec['nickname'] = stripslashes($fan['nickname']);
					}
					if(empty($user['gender']) && !empty($fan['sex'])) {
						$rec['gender'] = $fan['sex'];
					}
					if(empty($user['residecity']) && !empty($fan['city'])) {
						$rec['residecity'] = $fan['city'] . '市';
					}
					if(empty($user['resideprovince']) && !empty($fan['province'])) {
						$rec['resideprovince'] = $fan['province'] . '省';
					}
					if(empty($user['nationality']) && !empty($fan['country'])) {
						$rec['nationality'] = $fan['country'];
					}
					if(empty($user['avatar']) && !empty($fan['headimgurl'])) {
						$rec['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
					}
					if(!empty($rec)) {
						pdo_update('mc_members', $rec, array('uid' => $row['uid']));
					}
				}
			}
		}
	}
	$pindex++;
	$log = ($pindex - 1) * $psize;
	if($pindex > $total_page) {
		message('粉丝数据更新完成', url('mc/fans'), 'success');
	} else {
		message('正在更新粉丝数据,请不要关闭浏览器,已完成更新 ' . $log . ' 条数据。', url('mc/fans/initsync', array('page' => $pindex, 'acid' => $acid)));
	}
}

if($do == 'updategroup') {
	if($_W['isajax']) {
		$acid = intval($_GPC['acid']);
		$groupid = intval($_GPC['groupid']);
		$openid = trim($_GPC['openid']);
		if($acid > 0 && !empty($openid)) {
			$acc = WeAccount::create($acid);
			$data = $acc->updateFansGroupid($openid, $groupid);
			if(is_error($data)) {
				exit(json_encode(array('status' => 'error', 'mess' => $data['message'])));
			} else {
				pdo_update('mc_mapping_fans', array('groupid' => $groupid), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
				exit(json_encode(array('status' => 'success')));
			}
		} else {
			exit(json_encode(array('status' => 'error', 'mess' => '公众号信息和粉丝openid错误')));
		}
	}
}

template('mc/fans');