<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
			
			$starttime=mktime(0,0,0);//当天：00：00：00
			$endtime = mktime(23,59,59);//当天：23：59：59
			$times = '';
			$times .= ' AND createtime >=' .$starttime;
			$times .= ' AND createtime <=' .$endtime;		
		if ($reply['isipv'] == 1) {
			$mineip = getip();	
			
			$iplist = pdo_fetchall('SELECT * FROM '.tablename($this->table_iplist).' WHERE uniacid= :uniacid  AND  rid= :rid order by `createtime` desc ', array(':uniacid' => $uniacid, ':rid' => $rid));
			$totalip = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid  AND rid= :rid AND ip = :ip  '.$times.' order by `ip` desc ', array(':uniacid' => $uniacid, ':rid' => $rid, ':ip' => $mineip));
			
			$limitip = empty($reply['limitip']) ? '2' : $reply['limitip'] ;
			if ($totalip > $limitip && $reply['ipstopvote'] == 1) {
				$ipurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('stopip', array('from_user' => $from_user, 'rid' => $rid));
				$fmdata = array(
					"success" => 3,
					"linkurl" => $ipurl,
					"msg" => '你存在刷票的嫌疑或者您的网络不稳定，请重新进入！',
				);
				echo json_encode($fmdata);
				exit();	
			}
			
			$mineipz = sprintf("%u",ip2long($mineip));
			foreach ($iplist as $i) {
				$iparrs = iunserializer($i['iparr']);
				$ipstart = sprintf("%u",ip2long($iparrs['ipstart']));
				$ipend = sprintf("%u",ip2long($iparrs['ipend']));					
				if ($mineipz >= $ipstart && $mineipz <= $ipend) {						
					$ipdate = array(
						'rid' => $rid,
						'uniacid' => $uniacid,
						'avatar' => $avatar,
						'nickname' => $nickname,
						'from_user' => $from_user,
						'ip' => $mineip,
						'hitym' => 'tvote',
						'createtime' => time(),
					);
					$ipdate['iparr'] = getiparr($ipdate['ip']);
					pdo_insert($this->table_iplistlog, $ipdate);
					if ($reply['ipstopvote'] == 1) {
						$ipurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('stopip', array('from_user' => $from_user, 'rid' => $rid));
						
						$fmdata = array(
							"success" => 3,
							"linkurl" => $ipurl,
							"msg" => '你存在刷票的嫌疑或者您的网络不稳定，请重新进入！',
						);
						echo json_encode($fmdata);
						exit();	
					}
					break;
				}
			}
		}

		
						
			//查询自己是否参与活动
		if(!empty($from_user)) {
			$mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
		}
			
		//查询是否参与活动
		if(!empty($tfrom_user)) {
		    $user = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $tfrom_user,':rid' => $rid));
		    if (!empty($user)) {
			   // pdo_update($this->table_users, array('hits' => $user['hits']+1), array('rid' => $rid, 'from_user' => $tfrom_user));
		    }else{
				$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvoteview', array('rid' => $rid));
				$fmdata = array(
							"success" => 3,
							"linkurl" => $url,
							"msg" => '！',
						);
						echo json_encode($fmdata);
						exit();	
				
				//header("location:$url");
				//exit;
			}
			
		}
		
		$bbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE uniacid = :uniacid AND tfrom_user = :tfrom_user AND rid = :rid order by `id` desc ",  array(':uniacid' => $uniacid,':tfrom_user' => $tfrom_user,':rid' => $rid));
		
		if ($reply['tmreply'] == 1) {//开启评论				
			if ($_GPC['tmreply'] == 1) {//开启评论	
				$tid = $user['id'];
				$content = $_GPC['msgstr'];
				//$reply_id = $user['id'];
				
				$rdata = array(
					'uniacid' => $uniacid,
					'rid' => $rid,
					'avatar' => $avatar,
					'nickname' => $nickname,
					'tfrom_user' => $tfrom_user,//帖子作者的openid
					'tid' => $tid,//帖子的ID
					'from_user' => $from_user,//回复评论帖子的openid
					//'reply_id' => $reply_id,//回复评论帖子的ID
					//'rfrom_user' => $rfrom_user,//被回复的评论的作者的openid
					//'to_reply_id' => $to_reply_id,//回复评论的id
					'content' => $content,//评论回复内容
					//'storey' => $storey,//绝对楼层
					'ip' => getip(),
					'createtime' => time(),
					
				);			
				$rdata['iparr'] = getiparr($rdata['ip']);
				pdo_insert($this->table_bbsreply, $rdata);
				$reply_id = pdo_insertid();
				pdo_update($this->table_bbsreply, array('storey' => $reply_id), array('uniacid' => $uniacid, 'rid' => $rid, 'id' => $reply_id ));
			}
		}
	
	
	
		if ($reply['isbbsreply'] == 1) {//开启评论	
			if ($_GPC['isbbsreply'] == 1) {
				if (empty($tfrom_user)) {
					$msg = '被投票人不存在！';
					//message($msg,$turl,'error');
					$fmdata = array(
						"success" => -1,
						"msg" => $msg,
					);
					echo json_encode($fmdata);
					exit();	
				}
				if (empty($_GPC['content'])) {
					$msg = '你还没有评论哦';
					//message($msg,$turl,'error');
					$fmdata = array(
						"success" => -1,
						"msg" => $msg,
					);
					echo json_encode($fmdata);
					exit();	
				}
				
				/**if ($reply['iscode'] == 1) {					
					$code = $_GPC['code'];
					if (empty($code)) {
						$fmdata = array(
							"success" => -1,
							"msg" => '请输入验证码！',
						);
						echo json_encode($fmdata);
						exit();	
					}
					$hash = md5($code . $_W['config']['setting']['authkey']);
					if($_GPC['__code'] != $hash) {					
						$fmdata = array(
							"success" => -1,
							"msg" => '你输入的验证码不正确, 请重新输入.',
						);
						echo json_encode($fmdata);
						exit();	
						//message('你输入的验证码不正确, 请重新输入.');
					}
				}**/
				$tid = $user['id'];
				$content = $_GPC['content'];
				//$reply_id = $user['id'];
				
				$rdata = array(
					'uniacid' => $uniacid,
					'rid' => $rid,
					'avatar' => $avatar,
					'nickname' => $nickname,
					'tfrom_user' => $tfrom_user,//帖子作者的openid
					'tid' => $tid,//帖子的ID
					'from_user' => $from_user,//回复评论帖子的openid
					//'reply_id' => $reply_id,//回复评论帖子的ID
					//'rfrom_user' => $rfrom_user,//被回复的评论的作者的openid
					//'to_reply_id' => $to_reply_id,//回复评论的id
					'content' => $content,//评论回复内容
					//'storey' => $storey,//绝对楼层
					'ip' => getip(),
					'createtime' => time(),
					
				);
				$rdata['iparr'] = getiparr($rdata['ip']);
				pdo_insert($this->table_bbsreply, $rdata);
				$reply_id = pdo_insertid();
				pdo_update($this->table_bbsreply, array('storey' => $reply_id), array('uniacid' => $uniacid, 'rid' => $rid, 'id' => $reply_id ));
				
				$msg = '评论成功！';
				//message($msg,$turl,'error');
				$fmdata = array(
					"success" => 1,
					"msg" => $msg,
				);
				echo json_encode($fmdata);
				exit();	
				//message('评论成功！', referer(), 'success');
			
			}
		}
	