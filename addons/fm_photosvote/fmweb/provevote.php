<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');
		$reply = pdo_fetch('SELECT * FROM '.tablename($this->table_reply).' WHERE uniacid= :uniacid AND rid =:rid ', array(':uniacid' => $uniacid, ':rid' => $rid) );
		
		$foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';		
		if ($foo == 'display') {
		
			if ($_GPC['sh'] == 1) {
				$status = intval($_GPC['status']);
				$from_user = $_GPC['from_user'];
				$now = time();
				pdo_update($this->table_users, array('status' => $status, 'lasttime' => $now), array('from_user' => $from_user, 'rid' => $rid));
				$this->sendMobileHsMsg($from_user, $rid, $uniacid);
				message('审核通过成功！',referer(),'success');
			}
			if (checksubmit('delete')) {
				pdo_delete($this->table_users, " id IN ('".implode("','", $_GPC['select'])."')");
				message('删除成功！', create_url('site/module', array('do' => 'Provevote', 'name' => 'fm_photosvote', 'rid' => $rid, 'page' => $_GPC['page'], 'foo' => 'display')));
			}
			$where = '';
			//!empty($_GPC['keywordnickname']) && $where .= " AND nickname LIKE '%{$_GPC['keywordnickname']}%'";
			if (!empty($_GPC['keyword'])) {
				$keyword = $_GPC['keyword'];
				if (is_numeric($keyword)) 
					$where .= " AND id = '".$keyword."'";
				else 				
					$where .= " AND nickname LIKE '%{$keyword}%'";
				
			}
			
			!empty($_GPC['keywordid']) && $where .= " AND rid = '{$_GPC['keywordid']}'";
			!empty($rid) && $where .= " AND rid = '{$rid}'";

			$where .= " AND status = '0'";
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;

			//取得用户列表
			$list_praise = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid) );
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' ', array(':uniacid' => $uniacid));
			$pager = pagination($total, $pindex, $psize);
			
			
			
			//include $this->template('provevote');
		} elseif ($foo == 'post') {
					
			$from_user = $_GPC['from_user'];
			
			if (!empty($rid) && !empty($from_user)) {
				$item = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE rid = :rid AND from_user = :from_user" , array(':rid' => $rid, ':from_user' => $from_user));
				if (empty($item)) {
					message('抱歉，报名人不存在或是已经删除！', '', 'error');
				}
			}
			$picarr = $this->getpicarr($uniacid,$reply['tpxz'],$from_user,$rid);
			
			if (checksubmit('fileupload-delete')) {
				file_delete($_GPC['fileupload-delete']);
				pdo_update($this->table_users, array('photo' => ''), array('rid' => $rid, 'from_user' => $from_user));
				message('删除成功！', referer(), 'success');
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['photoname'])) {
					message('投票主题不能为空，请输入投票主题！');
				}
				
				preg_match('/[a-zA-z]+:\/\/[^\s]*/', $_GPC["youkuurl"], $matchs);
				$tyurl = str_replace("&quot;", '', $matchs[0]);
				$data = array(
					'uniacid' => $uniacid,
					'photoname' => $_GPC['photoname'],
					'avatar' => $_GPC['avatar'],
					'youkuurl'  => $tyurl,
					'realname'  => $_GPC["realname"],
					'mobile'  => $_GPC["mobile"],
					'weixin'  => $_GPC["weixin"],
					'qqhao'  => $_GPC["qqhao"],
					'email'  => $_GPC["email"],
					'job'  => $_GPC["job"],
					'xingqu'  => $_GPC["xingqu"],
					'address'  => $_GPC["address"],
					'photosnum' => intval($_GPC['photosnum']),
					'xnphotosnum' => intval($_GPC['xnphotosnum']),
					'hits' => intval($_GPC['hits']),
					'xnhits' => intval($_GPC['xnhits']),
					'sharenum' => intval($_GPC['sharenum']),
					//'photo' => $_GPC['photo'],
					//'music' => $_GPC['music'],
					//'voice' => $_GPC['voice'],
					//'vedio' => $_GPC['vedio'],
					'status' => intval($_GPC['status']),
					'description' => htmlspecialchars_decode($_GPC['description']),
					
				);
				
				//多图上传
				
				/**
				$picarrTmp = array();
				for ($i = 1; $i <= $reply['tpxz']; $i++) {
					$picarrTmp[] .= $_GPC['picarr_'.$i];	
				}
				$data['picarr'] = iserializer($picarrTmp);
				**/
				
				pdo_update($this->table_users, $data, array('rid' => $rid, 'from_user' => $from_user));
				
				if ($_GPC['member'] == '1') {
					message('报名用户更新成功！', $this->createWebUrl('members', array('rid' => $rid, 'foo' => 'display')), 'success');
				}else {
					message('报名用户更新成功！', $this->createWebUrl('provevote', array('rid' => $rid, 'foo' => 'display')), 'success');
				}
				
				
			}
			//include $this->template('provevote_post');
		}
		
		include $this->template('provevote');
	