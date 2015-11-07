<?php
/**
 * [WeiZan System] Copyright (c) 2014 WeiZan.Com
 * WeiZan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->func('communication');

$code = $_GPC['code'];
$scope = $_GPC['scope'];

if(!empty($_W['oauth_account'])) {
	if(!empty($code)) {
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$_W['oauth_account']['key']}&secret={$_W['oauth_account']['secret']}&code={$code}&grant_type=authorization_code";
		$response = ihttp_get($url);
		if(!is_error($response)) {
			$oauth = @json_decode($response['content'], true);
			if(is_array($oauth) && !empty($oauth['openid'])) {
				
				$_SESSION['oauth_openid'] = $oauth['openid'];
				$_SESSION['oauth_acid'] = $_W['oauth_account']['acid'];
				
				if (intval($_W['account']['level']) == 4) {
										$fan = mc_fansinfo($oauth['openid']);
					if (!empty($fan)) {
						
						$_SESSION['openid'] = $oauth['openid'];
						
						if (!empty($fan['uid'])) {
							$member = mc_fetch($fan['uid'], array('uid'));
							if (!empty($member) && $member['uniacid'] == $_W['uniacid']) {
								$_SESSION['uid'] = $member['uid'];
							}
						}
						
					} else {
												$accObj = WeiXinAccount::create($_W['account']);
						$userinfo = $accObj->fansQueryInfo($oauth['openid']);
						
						if(!is_error($userinfo) && !empty($userinfo) && is_array($userinfo) && !empty($userinfo['subscribe'])) {
							
							$userinfo['nickname'] = stripcslashes($userinfo['nickname']);
							$userinfo['avatar'] = $userinfo['headimgurl'];
							unset($userinfo['headimgurl']);
							
							$_SESSION['userinfo'] = base64_encode(iserializer($userinfo));
							
							$record = array(
								'openid' 		=> $userinfo['openid'],
								'uid' 			=> 0,
								'acid' 			=> $_W['acid'],
								'uniacid' 		=> $_W['uniacid'],
								'salt' 			=> random(8),
								'updatetime' 	=> TIMESTAMP,
								'nickname' 		=> stripslashes($userinfo['nickname']),
								'follow' 		=> $userinfo['subscribe'],
								'followtime' 	=> $userinfo['subscribe_time'],
								'unfollowtime' 	=> 0,
								'tag' 			=> base64_encode(iserializer($userinfo))
							);
														if (!isset($setting['passport']) || empty($setting['passport']['focusreg'])) {
																$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
								$data = array(
									'uniacid' 		=> $_W['uniacid'],
									'email' 		=> md5($oauth['openid']).'@qdaygroup.com',
									'salt' 			=> random(8),
									'groupid' 		=> $default_groupid,
									'createtime' 	=> TIMESTAMP,
									'password' 		=> md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']),
									'nickname' 		=> stripslashes($userinfo['nickname']),
									'avatar' 		=> rtrim($userinfo['avatar'], '0') . 132,
									'gender' 		=> $userinfo['sex'],
									'nationality' 	=> $userinfo['country'],
									'resideprovince'=> $userinfo['province'] . '省',
									'residecity' 	=> $userinfo['city'] . '市',
								);
								pdo_insert('mc_members', $data);
								$uid = pdo_insertid();
								$record['uid'] = $uid;
								$_SESSION['uid'] = $uid;
							}
							pdo_insert('mc_mapping_fans', $record);
							
							$_SESSION['openid'] = $record['openid'];
							$_W['fans'] = $record;
							$_W['fans']['from_user'] = $record['openid'];
						}
					}
				} else {
										$mc_oauth_fan = _mc_oauth_fans($oauth['openid'], $_W['acid']);
					if (empty($mc_oauth_fan) && (!empty($_SESSION['openid']) || !empty($_SESSION['uid']))) {
						$data = array(
							'acid' 			=> $_W['acid'],
							'oauth_openid'	=> $oauth['openid'],
							'uid'			=> intval($_SESSION['uid']),
							'openid'		=> $_SESSION['openid']
						);
						pdo_insert('mc_oauth_fans', $data);
					}
					if (!empty($mc_oauth_fan)) {
						if (empty($_SESSION['uid']) && !empty($mc_oauth_fan['uid'])) {
							$_SESSION['uid'] = intval($mc_oauth_fan['uid']);
						}
						if (empty($_SESSION['openid']) && !empty($mc_oauth_fan['openid'])) {
							$_SESSION['openid'] = strval($mc_oauth_fan['openid']);
						}
					}
				}
				
				if ($scope == 'userinfo') {
					$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$oauth['access_token']}&openid={$oauth['openid']}&lang=zh_CN";
					$response = ihttp_get($url);
					if (!is_error($response)) {
						$userinfo = array();
						$userinfo = @json_decode($response['content'], true);
						$userinfo['nickname'] = stripcslashes($userinfo['nickname']);
						$userinfo['avatar'] = $userinfo['headimgurl'];
						unset($userinfo['headimgurl']);
						
						$_SESSION['userinfo'] = base64_encode(iserializer($userinfo));
						
						$fan = mc_fansinfo($_SESSION['openid']);
						if (!empty($fan)) {
							$record = array();
							$record['updatetime'] = TIMESTAMP;
							$record['nickname'] = stripslashes($userinfo['nickname']);
							$record['tag'] = base64_encode(iserializer($userinfo));
							pdo_update('mc_mapping_fans', $record, array('openid' => $fan['openid'], 'acid' => $_W['acid'], 'uniacid' => $_W['uniacid']));
						}
						if(!empty($fan['uid']) || !empty($_SESSION['uid'])) {
							$uid = $fan['uid'];
							if(empty($uid)){
								$uid = $_SESSION['uid'];
							}
							$user = mc_fetch($uid, array('nickname', 'gender', 'residecity', 'resideprovince', 'nationality', 'avatar'));
							$record = array();
							if(empty($user['nickname']) && !empty($userinfo['nickname'])) {
								$record['nickname'] = stripslashes($userinfo['nickname']);
							}
							if(empty($user['gender']) && !empty($userinfo['sex'])) {
								$record['gender'] = $userinfo['sex'];
							}
							if(empty($user['residecity']) && !empty($userinfo['city'])) {
								$record['residecity'] = $userinfo['city'] . '市';
							}
							if(empty($user['resideprovince']) && !empty($userinfo['province'])) {
								$record['resideprovince'] = $userinfo['province'] . '省';
							}
							if(empty($user['nationality']) && !empty($userinfo['country'])) {
								$record['nationality'] = $userinfo['country'];
							}
							if(empty($user['avatar']) && !empty($userinfo['avatar'])) {
								$record['avatar'] = rtrim($userinfo['avatar'], '0') . 132;
							}
							if(!empty($record)) {
								pdo_update('mc_members', $record, array('uid' => intval($user['uid'])));
							}
						}
					} else {
						message('微信授权获取用户信息失败,错误信息为: ' . $response['message']);
					}
				}
				$forward = base64_decode($_SESSION['dest_url']);
				$forward = strexists($forward, 'i=') ? $forward : "{$forward}&i={$_W['uniacid']}&j={$_W['acid']}";
				if (!empty($_W['openid'])) {
					$_SESSION['dest_url'] = '';
					unset($_SESSION['dest_url']);
				}
				header('Location: ' . $_W['siteroot'] . 'app/index.php?' . $forward . '&wxref=mp.weixin.qq.com#wechat_redirect');
				exit;
			}
		}
		$state = 'we7sid-'.$_W['session_id'];
		$url = "{$_W['siteroot']}app/index.php?i={$_W['uniacid']}&j={$_W['acid']}&c=auth&a=oauth&scope=snsapi_base";
		$callback = urlencode($url);
		$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$_W['oauth_account']['key']}&redirect_uri={$callback}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
		header('Location: ' . $forward);
		exit;
	}
}
exit('访问错误');
