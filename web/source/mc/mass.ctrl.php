<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('default', 'groupdata', 'post', 'send', 'ajax');
$do = in_array($do, $dos) ? $do : 'default';
if($do == 'default') {
	$accounts = uni_accounts($_W['uniacid']);
	if(!empty($accounts)) {
		$accdata = array();
		foreach($accounts as $account) {
			if($account['type'] == 1 && $account['type'] > 0) {
				$accdata[] = array('acid' => $account['acid'], 'name' => $account['name']);
			}
		}
	}


	$groups_data = pdo_fetchall('SELECT * FROM ' . tablename('mc_fans_groups') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if(!empty($groups_data)) {
		$groups = array();
		foreach($groups_data as $gr) {
			$groups[$gr['acid']] = iunserializer($gr['groups']);
		}
	}

	load()->func('tpl');
	template('mc/mass');
}

if($do == 'groupdata') {
	if($_W['isajax']) {
		$acid = intval($_GPC['acid']);
		$groups = pdo_fetch('SELECT * FROM ' . tablename('mc_fans_groups') . ' WHERE uniacid = :uniacid AND acid = :acid', array(':uniacid' => $_W['uniacid'], ':acid' => $acid));
		$groups = unserialize($groups['groups']) ? unserialize($groups['groups']) : array();
		if(empty($groups)) {
			exit(json_encode(array('status' => 'empty', 'message' => '该公众号还没有从公众平台获取粉丝分组')));
		} else {
			$html = '<option name="groupid" value="0">请选择粉丝分组</option><option value="-2" name="groupid">全部用户</option>';
			foreach($groups as $group) {
				if( $group['id'] == 0) {
					$group['id'] = -1;
				}
				$html .= '<option name="groupid" data-num = "'. $group['count'] .'" value="' . $group['id'] . '">' .  $group['name'] . '</option>';
			}
			exit(json_encode(array('status' => 'success', 'message' => $html)));
		}
	}
}

if($do == 'post') {
	if($_W['isajax']) {
		$acid = intval($_GPC['acid']);
		$groupid = intval($_GPC['groupid']);
		$msgtype = intval($_GPC['msgtype']);
		if($groupid == -1) {
			$groupid = 0;
		}

		if($msgtype != 6) {
			$formdata_tmp = explode('&', urldecode($_GPC['formdata']));
			if(!empty($formdata_tmp)) {
				$formdata = array();
				foreach($formdata_tmp as $formda) {
					$li = explode('=', $formda);
					$formdata[$li[0]] = $li[1];
				}
			}
		} else {
			if(!empty($_GPC['formdata'])) {
				foreach($_GPC['formdata'] as $key => $li_tmp) {
					$content = $_GPC['content'][$key];
					if(empty($content)) {
						continue;
					} else {
						$str_find = array('../attachment/images', 'resource/components/tinymce/plugins/emoticons/img');
						$str_replace = array($_W['siteroot'] . 'attachment/images', $_W['siteroot'] . 'web/resource/components/tinymce/plugins/emoticons/img');
						$content =  str_replace($str_find, $str_replace, $content);
					}
					$formdata_tmp = explode('&', urldecode($li_tmp));
					if(!empty($formdata_tmp)) {
						foreach($formdata_tmp as $formda) {
							$li = explode('=', $formda);
							if(($li[0] == 'title' && $li[1] == '') || ($li[0] == 'thumb_media_id' && $li[1] == '')) {
								break;
							}
							if($li[0] == 'content_source_url' && !empty($li[1])) {
								$li[1] = str_replace(array('*', '$'), array('&', '='), $li[1]);
								if(!strexists($li[1], 'http://') && !strexists($li[1], 'https://')) {
									$li[1] = $_W['siteroot'] . 'app' . ltrim($li[1], '.');
								}
							}
							$formdata_tmp1[$li[0]] = urlencode($li[1]);
						}
						if(!empty($formdata_tmp1)) {
							$formdata_tmp1['content'] = urlencode(addslashes(htmlspecialchars_decode($content)));
							if(!isset($formdata_tmp1['show_cover_pic'])) {
								$formdata_tmp1['show_cover_pic'] = 0;
							}
							$formdata[] = $formdata_tmp1;
							unset($_GPC['formdata'][$key], $formdata_tmp, $formda, $li, $formdata_tmp1);
						}
					}
				}
			}
		}

		$send['filter']['is_to_all'] = false;
		$send['filter']['group_id'] = $groupid;
		if($groupid == -2) {
			$send['filter']['is_to_all'] = true;
			$send['filter']['group_id'] = 0;
		}
		if($msgtype == '7') {
			$send['msgtype'] = 'text';
			$send['text'] = array('content' => urlencode($formdata['content']));
			$insert['content'] = $formdata['content'];
			$insert['msgtype'] = 'text';
		} elseif($msgtype == '2') {
			$send['msgtype'] = 'image';
			$send['image'] = array('media_id' => $formdata['media_id']);
			$insert['content'] = $formdata['media_id'];
			$insert['msgtype'] = 'image';
		} elseif($msgtype == '3') {
			$send['msgtype'] = 'voice';
			$send['voice'] = array('media_id' => $formdata['media_id']);
			$insert['content'] = $formdata['media_id'];
			$insert['msgtype'] = 'voice';
		} elseif($msgtype == '4'){
						$data['media_id'] =  $formdata['media_id'];
			$data['title'] =  urlencode($formdata['title']);
			$data['description'] =  urlencode($formdata['description']);
			$acc = WeAccount::create($acid);
			$status = $acc->uploadVideo($data);
			if(is_error($status)) {
				exit(json_encode($status));
			}

			$send['msgtype'] = 'mpvideo';
			$send['mpvideo'] = array('media_id' => $status['media_id']);
			$insert['msgtype'] = 'video';
			$insert['content'] = $formdata['media_id'];
		} elseif($msgtype == '6') {
						if(!empty($formdata)) {
				$data['articles'] = $formdata;
				$acc = WeAccount::create($acid);
				$status = $acc->uploadNews($data);
				if(is_error($status)) {
					exit(json_encode($status));
				}
			} else {
				exit(json_encode(error(-1, '没有有效的消息内容')));
			}
			$send['msgtype'] = 'mpnews';
			$send['mpnews'] = array('media_id' => $status['media_id']);
			$insert['msgtype'] = 'news';
			$insert['content'] = iserializer($formdata);
		}
		$acc = WeAccount::create($acid);
		$status = $acc->fansSendAll($send);
		if(is_error($status)) {
			exit(json_encode($status));
		} else {
						$insert['createtime'] = TIMESTAMP;
			$insert['fansnum'] = intval($_GPC['fansnum']);
			$insert['groupname'] = trim($_GPC['groupname']);
			$insert['uniacid'] = $_W['uniacid'];
			$insert['acid'] = $acid;
			pdo_insert('mc_mass_record', $insert);
			exit('success');
		}
	}
}

if($do == 'send') {
	$accounts = uni_accounts();
	if(!isset($_GPC['acid'])) {
		$account = current($accounts);
		if($account !== false){
			$_GPC['acid'] = intval($account['acid']);
		}
	}
	$acid = intval($_GPC['acid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' WHERE `uniacid`=:uniacid';
	$pars = array();
	$pars[':uniacid'] = $_W['uniacid'];
	if(!empty($acid)) {
		$condition .= ' AND `acid`=:acid';
		$pars[':acid'] = $acid;
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_mass_record').$condition, $pars);
	$list = pdo_fetchall("SELECT * FROM ".tablename('mc_mass_record') . $condition ." ORDER BY `id` DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $pars);
	$types = array('text' => '文本消息', 'images' => '图片消息', 'voice' => '语音消息', 'video' => '视频消息', 'news' => '图文消息');
	if(!empty($list)) {
		foreach($list as &$li) {
			if($li['msgtype'] == 'image') {
				$images = media2local($li['content']);
				$li['content'] = '<a href="javascript:;" class="ajax-show" id="'.$li['id'].'"><i class="fa fa-file-image-o"></i> 图片消息</a>';
			} elseif($li['msgtype'] == 'voice') {
				$li['content'] = '<a href="'.media2local($li['content']).'" target="_blank"><i class="fa fa-bullhorn"></i> 语音消息</a>';
			} elseif($li['msgtype'] == 'video') {
				$li['content'] = '<a href="'.media2local($li['content']).'" target="_blank"><i class="fa fa-video-camera"></i> 视频消息</a>';
			} elseif($li['msgtype'] == 'news') {
				$li['content'] = '<a href="javascript:;" class="ajax-show" id="'.$li['id'].'"><i class="fa fa-file-image-o"></i> 图文消息</a>';
			} elseif($li['msgtype'] == 'text') {
				$li['content'] = '<a href="javascript:;" class="ajax-show" id="'.$li['id'].'""><i class="fa fa-file-text-o"></i> 文本消息</a>';
			}
			$li['msgtype'] = $types[$li['msgtype']];
		}
	}
	$pager = pagination($total, $pindex, $psize);
	template('mc/send');
}
if($do == 'ajax') {
	if($_W['ispost']) {
		$id = intval($_GPC['id']);
		$data = pdo_fetch('SELECT * FROM ' . tablename('mc_mass_record') . ' WHERE id = :id AND uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if(empty($data)) {
			exit('err');
		} else {
			if($data['msgtype'] == 'news') {
				$data['content'] = iunserializer($data['content']);
				$content = iurldecode($data['content']);
				$i = 1;
				$flag = count($content);
				foreach($content as &$con) {
					$con['thumb_media_id'] = media2local($con['thumb_media_id']);
					if($i == 1) {
						$html = '<div style="width:400px">
								<div class="panel panel-default" style="margin-bottom:0">
									<div class="panel-heading">
										<h4 class="form-control-static">'.$con['title'].'</h4>
										<img class="img-rounded" ng-show="entry.src" src="'.$con['thumb_media_id'].'" style="width:100%;height:200px;" />';

					}
					if($flag == 1) {
						$html .= '      <span class="help-block">'.cutstr($con['digest'], 20).'</span>
									</div>
								</div>
							</div>';
					} else {
						if($i != 1) {
							$html .= '</div><div class="panel-footer" style="border-top:0;padding:10px 20px;">';
							$html .= '<div class="li-block" style="padding:0px;height:50px;margin:0;line-height:50px;">
											<a href="" target="_blank;">
												<div class="left" style="width:250px;float:left;">'.$con['title'].'</div>
												<div class="right" style="width:60px;float:right;">
													<img src="'.$con['thumb_media_id'].'" style="width:60px;height:50px"alt=""/>
												</div>
											</a>
									  </div>
								  </div>
								</div>
							</div>';
						}
					}
					$i++;
				}
			} elseif($data['msgtype'] == 'text') {
				$html = '<div class="panel panel-default" style="margin-bottom:0">
							<div class="panel panel-body">'.emotion($data['content']).'</div>
						</div>';
			} elseif($data['msgtype'] == 'image') {
				$data['content'] = media2local($data['content']);
				$html = '<div class="panel panel-default" style="margin-bottom:0">
							<div class="panel panel-body"><img src="'.$data['content'].'" style="width:365px;"></div>
						</div>';
			}
			exit($html);
		}
	}
}
function iurldecode($str) {
	if(!is_array($str)) {
		return urldecode($str);
	}
	foreach($str as $key => $val) {
		$str[$key] = iurldecode($val);
	}
	return $str;
}
