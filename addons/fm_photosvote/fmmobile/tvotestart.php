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
			
			//查询自己是否参与活动
			if(!empty($from_user)) {
				$mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
			}
			
			
			//查询是否参与活动
			if(!empty($tfrom_user)) {
				$user = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $tfrom_user,':rid' => $rid));
				if (!empty($user)) {
					//pdo_update($this->table_users, array('hits' => $user['hits']+1), array('rid' => $rid, 'from_user' => $tfrom_user));
				}else{
					$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvoteview', array('rid' => $rid));
					//header("location:$url");
					$fmdata = array(
						"success" => 3,
						"linkurl" => $url,
						"msg" => '没有找到该用户！',
					);
					echo json_encode($fmdata);
					exit();	
				}
				
			}
			
			
			$uservote = pdo_fetch("SELECT * FROM ".tablename($this->table_log)." WHERE uniacid = :uniacid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':rid' => $rid));
			$uallonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
			$udayonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
			
			
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
			
			
			if($_GPC['vote'] == '1') {
				
				$starttime=mktime(0,0,0);//当天：00：00：00
				$endtime = mktime(23,59,59);//当天：23：59：59
				$times = '';
				$times .= ' AND createtime >=' .$starttime;
				$times .= ' AND createtime <=' .$endtime;
				$now = time();
				
				if ($reply['isdaojishi']) {
					$votetime = $reply['votetime']*3600*24;
					$isvtime = $now - $user['createtime'];
					if($isvtime >= $votetime) {
						$fmdata = array(
							"success" => -1,
							"msg" => empty($reply['ttipvote']) ? '你的投票时间已经结束' : $reply['ttipvote'],
						);
						echo json_encode($fmdata);
						exit();	
					}
				}
				
				
				if($now <= $reply['tstart_time'] || $now >= $reply['tend_time']) {
					
					if ($now <= $reply['tstart_time']) {
						$fmdata = array(
							"success" => -1,
							"msg" => $reply['ttipstart'],
						);
						echo json_encode($fmdata);
						exit();	
					}
					if ($now >= $reply['tend_time']) {
						$fmdata = array(
							"success" => -1,
							"msg" => $reply['ttipend'],
						);
						echo json_encode($fmdata);
						exit();	
					}
				}
				
				
				if ($_GPC['vfrom'] == 'photosvoteview') {
					$turl = $this->createMobileUrl('photosvoteview', array('rid' => $rid));
				} elseif ($_GPC['vfrom'] == 'tuser') {
					$turl = $this->createMobileUrl('tuser', array('rid' => $rid));
				} elseif ($_GPC['vfrom'] == 'tuserphotos') {
					$turl = $this->createMobileUrl('tuserphotos', array('rid' => $rid));
				} else {
					$turl = referer();
				}

				$daytpxz = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user,':rid' => $rid));//当天共投多少参赛者
				$fansmostvote = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':rid' => $rid));	//总共可以投几次
				if ($reply['subscribe'] == 1) {
					
					if ($follow == 1) {
						if ($fansmostvote < $reply['fansmostvote']) {

							if ($daytpxz >= $reply['daytpxz']) {
								$msg = '您当前最多可以投票'.$reply['daytpxz'].'个参赛选手，您当天的次数已经投完，请明天再来';
								//message($msg,$turl,'error');
								$fmdata = array(
									"success" => -1,
									"msg" => $msg,
								);
								echo json_encode($fmdata);
								exit();	
								
								
							}	else {
								if ($tfrom_user == $from_user) {
									//message(,$turl,'error');
									$msg = '您不能为自己投票';
									$fmdata = array(
										"success" => -1,
										"msg" => $msg,
									);
									echo json_encode($fmdata);
									exit();	
								}else {
									$dayonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
									
									$allonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
									

									
									
									if ($allonetp >= $reply['allonetp']) {
										$msg = '您总共可以给他投票'.$reply['allonetp'].'次，您已经投完！';
										//message($msg,$turl,'error');
										$fmdata = array(
											"success" => -1,
											"msg" => $msg,
										);
										echo json_encode($fmdata);
										exit();	
										
									} else {					
										if ($dayonetp >= $reply['dayonetp']) {
											$msg = '您当天最多可以给他投票'.$reply['dayonetp'].'次，您已经投完，请明天再来';
											//message($msg,$turl,'error');
											$fmdata = array(
												"success" => -1,
												"msg" => $msg,
											);
											echo json_encode($fmdata);
											exit();	
											
											
											//exit;
										}else {
																				
											$votedate = array(
												'uniacid' => $uniacid,
												'rid' => $rid,
												'tptype' => '1',
												'avatar' => $avatar,
												'nickname' => $nickname,
												'from_user' => $from_user,
												'afrom_user' => $fromuser,
												'tfrom_user' => $tfrom_user,
												'ip' => getip(),
												'createtime' => time(),
												
											);
											$votedate['iparr'] = getiparr($votedate['ip']);
											pdo_insert($this->table_log, $votedate);
											pdo_update($this->table_users, array('photosnum'=> $user['photosnum']+1), array('rid' => $rid, 'from_user' => $tfrom_user,'uniacid' => $uniacid));
											
											
											$tuservote = pdo_fetch("SELECT * FROM ".tablename($this->table_log)." WHERE uniacid = :uniacid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':rid' => $rid));
											
											if ($_W['account']['level'] == 4){
												$this->sendMobileVoteMsg($tuservote,$from_user, $rid, $uniacid);
											}
											
											
											
											if (!empty($user['realname'])) {
												$user['realname'] = $user['realname'];
											} else {
												$user['realname'] = $user['nickname'];
											}
											
											
											$str = array('#编号#'=>$user['id'],'#参赛人名#'=>$user['realname']);
											$res = strtr($reply['votesuccess'],$str);										
											$msg = '恭喜您成功的为编号为： '.$user['id'].' ,姓名为： '.$user['realname'].' 的参赛者投了一票！';
											$msg = empty($res) ? $msg : $res ;
											
											$fmdata = array(
												"success" => 1,
												"msg" => $msg,
											);
											echo json_encode($fmdata);
											exit();	
										}
									}


								}
							}
						}else{
							$msg = '在此活动期间，你总共可以投 '.$reply['fansmostvote'].' 票，目前你已经投完！';
							
							$fmdata = array(
								"success" => -1,
								"msg" => $msg,
							);
							echo json_encode($fmdata);
							exit();	
						}

					}else {
						
						$fmdata = array(
							"success" => 10,
							"msg" => $reply['shareurl'],
						);
						echo json_encode($fmdata);
						exit();	
						//$surl = $reply['shareurl'];
						//header("location:$surl");
						//exit;
					}
				} else {
					if ($fansmostvote < $reply['fansmostvote']) {
					
						if ($daytpxz >= $reply['daytpxz']) {
							$msg = '您当前最多可以投票'.$reply['daytpxz'].'个参赛选手，您当天的次数已经投完，请明天再来';
							//message($msg,$turl,'error');
							$fmdata = array(
								"success" => -1,
								"msg" => $msg,
							);
							echo json_encode($fmdata);
							exit();	
						}	else {
							
							if ($tfrom_user == $from_user) {
								$msg = '您不能为自己投票';
								$fmdata = array(
									"success" => -1,
									"msg" => $msg,
								);
								echo json_encode($fmdata);
								exit();	
							}else {
								
								
								$dayonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
								
								$allonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
								if ($allonetp >= $reply['allonetp']) {	
									$msg = '您总共可以给他投票'.$reply['allonetp'].'次，您已经投完！';
									//message($msg,$turl,'error');
									$fmdata = array(
										"success" => -1,
										"msg" => $msg,
									);
									echo json_encode($fmdata);
									exit();	
								} else {
									if ($dayonetp >= $reply['dayonetp']) {
											$msg = '您当前最多可以给他投票'.$reply['dayonetp'].'次，您已经投完，请明天再来';
											//message($msg,$turl,'error');
											$fmdata = array(
												"success" => -1,
												"msg" => $msg,
											);
											echo json_encode($fmdata);
											exit();	
										//exit;
									}else {
										
										
										$votedate = array(
											'uniacid' => $uniacid,
											'rid' => $rid,
											'avatar' => $avatar,
											'nickname' => $nickname,
											'from_user' => $from_user,
											'afrom_user' => $fromuser,
											'tfrom_user' => $tfrom_user,
											'ip' => getip(),
											'createtime' => time(),
										);
										$votedate['iparr'] = getiparr($votedate['ip']);
										pdo_insert($this->table_log, $votedate);
										pdo_update($this->table_users, array('photosnum'=> $user['photosnum']+1), array('rid' => $rid, 'from_user' => $tfrom_user,'uniacid' => $uniacid));
										
										$tuservote = pdo_fetch("SELECT * FROM ".tablename($this->table_log)." WHERE uniacid = :uniacid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':rid' => $rid));
										if ($_W['account']['level'] == 4){
											$this->sendMobileVoteMsg($tuservote,$from_user, $rid, $uniacid);
										}
										
										if (!empty($user['realname'])) {
											$user['realname'] = $user['realname'];
										} else {
											$user['realname'] = $user['nickname'];
										}
										$str = array('#编号#'=>$user['id'],'#参赛人名#'=>$user['realname']);
											$res = strtr($reply['votesuccess'],$str);										
											$msg = '恭喜您成功的为编号为： '.$user['id'].' ,姓名为： '.$user['realname'].' 的参赛者投了一票！';
											$msg = empty($res) ? $msg : $res ;
										$fmdata = array(
											"success" => 1,
											"msg" => $msg,
										);
										echo json_encode($fmdata);
										exit();	
										//message('您成功的为Ta投了一票！',$turl,'success');
									}
								}
							}
						}
					}else{
						$msg = '在此活动期间，你总共可以投 '.$reply['fansmostvote'].' 票，目前你已经投完！';
						
						$fmdata = array(
							"success" => -1,
							"msg" => $msg,
						);
						echo json_encode($fmdata);
						exit();	
					}

				}
			
			}
		echo json_encode($fmdata);
		exit();	
	