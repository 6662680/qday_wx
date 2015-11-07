<?php
/**
 * 女神来了模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class fm_photosvoteModuleProcessor extends WeModuleProcessor {
	public $name = 'fm_photosvoteModuleProcessor';
	public $title = '女神来了';
	public $table_reply  = 'fm_photosvote_reply';//规则 相关设置
	public $table_users   = 'fm_photosvote_provevote';	//投稿参加活动的人
	public $table_log   = 'fm_photosvote_votelog';//投票记录
	public $table_bbsreply   = 'fm_photosvote_bbsreply';//投票记录
	public $table_banners   = 'fm_photosvote_banners';//幻灯片
	public $table_advs   = 'fm_photosvote_advs';//广告
	public $table_users_voice  	 = 'fm_photosvote_provevote_voice';	//
	public $table_users_name  	 = 'fm_photosvote_provevote_name';	//
	public $table_gift   = 'fm_photosvote_gift';
	public $table_data   = 'fm_photosvote_data';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$from_user= $this->message['from'];
		$tag = $this->message['content'];
		$uniacid = $_W['uniacid'];//当前公众号ID	
		load()->func('communication');
			
			$sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
			$row = pdo_fetch($sql, array(':rid' => $rid));
				if (empty($row['id'])) {
					
					if (!$this->inContext && ($this->message['type'] == 'image' || $this->message['type'] == 'voice' || $this->message['type'] == 'video')) {
						$message = "请按活动规则参与活动，谢谢您的支持！";
					}else {
						$message = "亲，您还没有设置完成关键字或者未添加活动！";
					}
					
					return $this->respText($message);		
				}
			$qiniu = iunserializer($row['qiniu']);
			if ($row['status']==0){
				$message = "亲，".$row['title']."活动暂停了！您可以\n";
				$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&rid=".$rid."&m=fm_photosvote&do=paihang&uniacid=".$uniacid."'>看看排行榜</a>\n";
				if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
					$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
				}			
				return $this->respText($message);
			}
				$now = time();
				
				if($now >= $row['start_time'] && $now <= $row['end_time']){
					if ($row['status']==0){
						$message = "亲，".$row['title']."活动暂停了！";
						return $this->respText($message);
					}else{
						
						
						$command = $row['command'];
						$ckcommand = $row['ckcommand'];
							
						switch ($this->message['type']) {
							case 'text':
								if ($this->message['content'] == $command) {//报名判断
									if (empty($_SESSION['bmstart'])) {
										$isuser = pdo_fetch("SELECT * FROM ".tablename($this->table_users) . " WHERE uniacid = '{$uniacid}' AND `from_user` = '{$from_user}'");
										if (!empty($isuser)) {
											if ($row['isindex'] == 1) {
												$advs = pdo_fetch("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY id ASC LIMIT 1");
											}else {
												$advs = array();
												$advs['advname'] = $isuser['description'];
												$advs['thumb'] = $isuser['photo'];
											}
											return $this->respNews(array(
												'Title' => '您已经报过名了，点击以完善信息',
												'Description' => $advs['advname'],
												'PicUrl' => toimage($advs['thumb']),
												'Url' => $this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $from_user))
											));
										}
										$this->beginContext(1800);

										$_SESSION['bmstart']= $this->message['content'];
										//$_SESSION['mediaid']= $this->message['mediaid'];	
										//$_SESSION['ok']= 1;
										$msg = "欢迎参加".$row['title']."的活动，现在开始报名\n\n"."请按下面的顺序报名：\n";
										$msg.= "▶️ 上传封面\n";
										if ($row['mediatype']) {
											$msg.= "▶️ 上传相册照片\n";
										}
										if ($row['mediatypem']) {
											$msg.= "▶️ 录制好声音\n";
										}
										if ($row['mediatypev']) {
											$msg.= "▶️ 录制视频\n";
										}
										$msg.= "▶️ 根据提示，填写报名资料\n\n";
										$msg.= $_W['account']['name']."感谢您的参与!\n";
										
										return $this->respText($msg);
									
									}else {
										$_SESSION['bmstart']= $this->message['content'];
										$msg = "帮助信息：\n--------------\n\n"."请按下面的顺序报名：\n";
										$msg.= "▶️ 上传封面\n";
										if ($row['mediatype']) {
											$msg.= "▶️ 上传相册照片\n";
										}
										if ($row['mediatypem']) {
											$msg.= "▶️ 录制好声音\n";
										}
										if ($row['mediatypev']) {
											$msg.= "▶️ 录制视频\n";
										}
										$msg.= "▶️ 根据提示，填写报名资料\n\n";
										$msg.= $_W['account']['name']."感谢您的参与!\n";
										
										return $this->respText($msg);
									}
								}else {
									if ($this->inContext && !empty($_SESSION['bmstart'])) {
										if ($this->message['content'] == 't') {

											$this->endContext();
											return $this->respText('退出成功！');
										}

										if ($_SESSION['imageok'] <> 1) {

											$msg = $_W['account']['name']." 提请您：\n▶️ 请先上传封面\n";
											return $this->respText($msg);
										}
										if ($row['mediatype']) {
											if ($_SESSION['imagesok'] <> 1) {
												$msg = $_W['account']['name']." 提请您：\n▶️ 请上传相册照片\n";
												return $this->respText($msg);
											}
										}

										if ($row['mediatypem']) {
											if ($_SESSION['voiceok'] <> 1) {
												$msg = $_W['account']['name']." 提请您：\n▶️ 请录制好声音\n";
												return $this->respText($msg);
											}
										}
									
										if ($row['mediatypev']) {
											if ($_SESSION['videook'] <> 1) {
												$msg = $_W['account']['name']." 提请您：\n▶️ 请录制视频\n";
												return $this->respText($msg);
											}
										}
									}
									if (!$this->inContext) {

										$zjrets = preg_match('/^[0-9]{1,5}$/i', $tag);

										if ($_SESSION['ok'] <> 1) {
											if (!$zjrets && !is_numeric($this->message['content'])) {
												$picture = $row['picture'];
												if (substr($picture,0,6)=='images'){
													$picture = $_W['attachurl'] . $picture;
												}else{
													$picture = $_W['siteroot'] . $picture;
												}
												return $this->respNews(array(
													'Title' => $row['title'],
													'Description' => htmlspecialchars_decode($row['description']),
													'PicUrl' => $picture,
													'Url' => $this->createMobileUrl('photosvoteview', array('rid' => $rid)),
												));
											}							
										
										
											$this->beginContext(60);//锁定60秒
											$_SESSION['ok']= 1;	
											$_SESSION['content']= $this->message['content'];						
											$_SESSION['code']=random(4,true);
											return $this->respText("为防止恶意刷票，请回复验证码：".$_SESSION["code"]);	
										
										}else {
											if($this->message['content']!=$_SESSION['code']){
												$_SESSION['code']=random(4,true);
												return $this->respText("验证码错误，请重新回复验证码：".$_SESSION['code']);	
											}else{
												$tag = $_SESSION['content'];
												//$rets = preg_match('/'.$command.'/i', $tag);
												//$ckrets = preg_match('/'.$ckcommand.'/i', $tag);
												$this->endContext();
												
												

													if ($zjrets)  {
														
														if ($now <= $row['tstart_time']) {							
															return $this->respText($row['ttipstart']);								
														}
														if ($now >= $row['tend_time']) {								
															return $this->respText($row['ttipend']);				
														}

														$starttime=mktime(0,0,0);//当天：00：00：00
														$endtime = mktime(23,59,59);//当天：23：59：59
														$times = '';
														$times .= ' AND createtime >=' .$starttime;
														$times .= ' AND createtime <=' .$endtime;
														
														
														

														$where .= " AND id = '".$tag."'";
														
														
														$t = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and rid = :rid ".$where." LIMIT 1", array(':uniacid' => $uniacid,':rid' => $rid));					
														if (empty($t)) {
															$message = '未找到参赛者编号为 '.$tag.' 的用户，请重新输入！';
															$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&rid=".$rid."&m=fm_photosvote&do=photosvoteview'>活动首页</a>\n";
															if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
																$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
															}
															return $this->respText($message);
														}

														if($t['status']!='1'){
															
															$message = '您投票的用户编号为 '.$tag.'还未通过审核，请稍后再试,您可以：';
															$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&rid=".$rid."&m=fm_photosvote&do=photosvoteview&uniacid=".$uniacid."'>活动首页</a>\n";
															if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
																$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
															}
															
															return $this->respText($message);
															
															
														}
														if ($row['isdaojishi']) {
															$votetime = $reply['votetime']*3600*24;
															$isvtime = $now - $t['createtime'];
															if($isvtime >= $votetime) {
																
																	$message = empty($row['ttipvote']) ? $t['nickname'].' 的投票时间已经结束' : $row['ttipvote'];
																
																return $this->respText($message);
															}
														}
														$tfrom_user = $t['from_user'];

														$daytpxz = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user,':rid' => $rid));

														$fansmostvote = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':rid' => $rid));	//总共可以投几次
														if ($fansmostvote < $row['fansmostvote']) {

															if ($daytpxz >= $row['daytpxz']) {
																$message = '您当前最多可以投'.$row['daytpxz'].'个参赛选手，您当天的次数已经投完，请明天再来';
																return $this->respText($message);
															}	else {
																if ($tfrom_user == $from_user) {
																	//message('您不能为自己投票',referer(),'error');
																	$message = '您不能为自己投票';
																	return $this->respText($message);
																}else {
																	
																	
																	$dayonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
																	
																	$allonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
																	if ($allonetp >= $row['allonetp']) {
																		$message = "您总共可以给她投票".$row['allonetp']."次，您已经投完！您还可以：\n";
																		$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&tfrom_user=".$tfrom_user."&rid=".$rid."&m=fm_photosvote&do=tuserphotos&uniacid=".$uniacid."'>查看她的投票</a>\n";
																			if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
																				$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
																			}
																		
																		return $this->respText($message);
																	} else {
																		if ($dayonetp >= $row['dayonetp']) {
																			$message = "您当天最多可以给她投票".$row['dayonetp']."次，您已经投完，请明天再来。您还可以：\n";
																			$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&tfrom_user=".$tfrom_user."&rid=".$rid."&m=fm_photosvote&do=tuserphotos&uniacid=".$uniacid."'>查看她的投票</a>\n";
																			if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
																				$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
																			}
																			return $this->respText($message);
																			//exit;
																		}else {												
																															
																			$atype = 'weixin';
																			$token = $_W['account']['access_token']['token'];
																			$urls = sprintf("https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN", $token,$from_user);
																			$contents = ihttp_get($urls);
																			$dats = $contents['content'];
																			$re = @json_decode($dats, true);
																			
																			$nickname = $re['nickname'];
																			$avatar = $re['headimgurl'];
																			
																			
																			$votedate = array(
																				'uniacid' => $uniacid,
																				'rid' => $rid,
																				'tptype' => '2',
																				'avatar' => $avatar,
																				'nickname' => $nickname,
																				'from_user' => $from_user,
																				'afrom_user' => $_COOKIE["user_fromuser_openid"],
																				'tfrom_user' => $tfrom_user,
																				'ip' => getip(),
																				'createtime' => time(),
																				
																			);
																			$votedate['iparr'] = $this->getiparr($votedate['ip']);	
																			pdo_insert($this->table_log, $votedate);
																			pdo_update($this->table_users, array('hits' => $t['hits']+1,'photosnum'=> $t['photosnum']+1), array('rid' => $rid, 'from_user' => $tfrom_user,'uniacid' => $uniacid));
																			//message('您成功的为Ta投了一票！',referer(),'success');
																			if (!empty($t['realname'])) {
																				$tname = '姓名为： ' . $t['realname'];
																			}else {
																				$tname = '昵称为： ' . $t['nickname'];
																			}
																			$message = "恭喜您成功的为编号为： ".$t['id']." , ".$tname." 的参赛者投了一票！";
																			//$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&rid=".$rid."&m=fm_photosvote&do=tuserphotos&tfrom_user=".$tfrom_user."'>查看她的投票</a>\n";
																											
																			//return $this->respText($message);
																			$rowtp = array();
																					$rowtp['title'] = $message;
																					$rowtp['description'] =htmlspecialchars_decode($message);
																					$rowtp['picurl'] = !empty($t['photo']) ? toimage($t['photo']) : toimage($t['avatar']);
																					$rowtp['url'] =  $this->createMobileUrl('tuserphotos', array('rid' => $rid, 'tfrom_user' => $tfrom_user));
																			
																			$news[] = $rowtp;
																			if ($row['isindex'] == 1) {
																				$advs = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC LIMIT 6");
																				
																				foreach($advs as $c) {
																					$rowadv = array();
																					$rowadv['title'] = $c['advname'];
																					$rowadv['description'] = $c['description'];
																					$rowadv['picurl'] = toimage($c['thumb']);
																					$rowadv['url'] = empty($c['link']) ? $this->createMobileUrl('photosvoteview', array('rid' => $rid)) : $c['link'];
																					$news[] = $rowadv;
																				}
																				
																			}
																			$huodong = iunserializer($row['huodong']);
																			if ($huodong['ishuodong'] == 1 && !empty($huodong['huodongurl'])) {
																				$rowhd = array();
																				$rowhd['title'] = $huodong['huodongname'];
																				$rowhd['description'] = $huodong['huodongdes'];
																				$rowhd['picurl'] = toimage($huodong['hhhdpicture']);
																				$rowhd['url'] = $huodong['huodongurl']."&from=fm_photosvote&oid=".$from_user;
																				$news[] = $rowhd;
																				
																			}
																			return $this->respNews($news);												
																		}
																	}
																}
																
															}//end daytpxz
														}else{
															$message = '在此活动期间，你总共可以投 '.$row['fansmostvote'].' 票，目前你已经投完！';
															return $this->respText($message);
														}

													}//end zjrets
											}
										}
									}
									
								}
								if($this->message['content']=="n" || $this->message['content']=="N" ){
									$_SESSION['imageid']  = '';
									$_SESSION['imageurl'] = '';
									load()->func('file');
									file_delete($_SESSION['imageurl']);
									$msg = $_W['account']['name']." 提请您：\n▶️ 请重新重新上传的封面\n";
									return $this->respText($msg);	
								}
								if (empty($_SESSION['photoname'])) {
									$_SESSION['photoname']= $this->message['content'];
									$_SESSION['photonameok'] = 1;
									$msg = $_W['account']['name']." 提请您：\n▶️ 请回复主题介绍：";
									//$_SESSION['imageok']= 1;	
									return $this->respText($msg);
								}

								if (empty($_SESSION['description'])) {
									$_SESSION['description']= $this->message['content'];
									$_SESSION['descriptionok'] = 1;
									$msg = $_W['account']['name']." 提请您：\n▶️ 请回复真实姓名：";
									//$_SESSION['imageok']= 1;	
									return $this->respText($msg);
								}
								if (empty($_SESSION['realname'])) {
									$_SESSION['realname']= $this->message['content'];
									$_SESSION['realnameok'] = 1;
									$msg = $_W['account']['name']." 提请您：\n▶️ 请回复手机号码：";
									//$_SESSION['imageok']= 1;	
									return $this->respText($msg);
								}
							
								if (empty($_SESSION['mobile'])) {
									$_SESSION['mobile']= $this->message['content'];
									$_SESSION['mobileok'] = 1;
								}

								$sql = 'SELECT uid FROM ' . tablename('mc_mapping_fans') . ' WHERE `uniacid`=:uniacid AND `openid`=:openid';
								$pars = array();
								$pars[':uniacid'] = $_W['uniacid'];
								$pars[':openid'] = $from_user;
								$uid = pdo_fetchcolumn($sql, $pars);
								$fan = pdo_fetch("SELECT avatar,nickname FROM ".tablename('mc_members') . " WHERE uniacid = '{$uniacid}' AND `uid` = '{$uid}'");
								if (!empty($fan)) {
									$avatar = $fan['avatar'];
									$nickname = $fan['nickname'];
								}

								
								$data = array(
									'rid'       => $rid,
									'uniacid'      => $uniacid,
									'from_user' => $from_user,
									'avatar'    => $avatar,
									'nickname'  => $nickname,
									'photo'  => $_SESSION["imageurl"],
									'music'  => $_SESSION["voiceurl"],
									'voice'  => $_SESSION["voiceurl"],
									'vedio'  => $_SESSION["videourl"],
									'picarr_1'  => $_SESSION["imagesurl1"],			    
									'picarr_2'  => $_SESSION["imagesurl2"],			    
									'picarr_3'  => $_SESSION["imagesurl3"],			    
									'picarr_4'  => $_SESSION["imagesurl4"],			    
									'picarr_5'  => $_SESSION["imagesurl5"],			    
									'picarr_6'  => $_SESSION["imagesurl6"],			    
									'picarr_7'  => $_SESSION["imagesurl7"],			    
									'picarr_8'  => $_SESSION["imagesurl8"],			    
									'description'  => $_SESSION["description"],
									'photoname'  => $_SESSION["photoname"],
									'realname'  => $_SESSION["realname"],
									'mobile'  => $_SESSION["mobile"],
									'photosnum'  => '0',
									'xnphotosnum'  => '0',
									'hits'  => '1',
									'xnhits'  => '1',
									'yaoqingnum'  => '0',
									'status'  => $row['tpsh'] == 1 ? '0' : '1',
									'createip' => getip(),
									'lastip' => getip(),
									'lasttime' => time(),		    
									'sharetime' => time(),
									'createtime'  => time()
								);

								$data['iparr'] = $this->getiparr($data['lastip']);
								pdo_insert($this->table_users, $data);
								$this->endContext();
								//$msg = $_W['account']['name']." 提请您：\n恭喜您报名成功！";
								$_SESSION['bmsuccess']= 1;	
								return $this->respNews(array(
									'Title' => '恭喜'.$nickname.'报名成功！',
									'Description' => '点击以完善信息',
									'PicUrl' => toimage($avatar),
									'Url' => $this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $from_user)),	
								));
								//return $this->respText($msg);
								
								break;
							case 'image':
								$fmmid = random(16);
								if (empty($_SESSION['imageid'])) {
									$_SESSION['imageid']= $this->message['mediaid'];
									if ($row['mediatypev']) {
										$info = "▶️ 请开始录制您的视频";
									}
									if ($row['mediatypem']) {
										$info = "▶️ 请开始录制您的好声音";
									}
									if ($row['mediatype']) {
										$info = "▶️ 请开始上传相册照片";
									}
									if (!$row['mediatypev'] && !$row['mediatypem'] && !$row['mediatype']) {
										$info = "▶️ 请回复您的照片主题宣言：";
									}
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您的封面\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请回复：n\n";
									$imageurl = $this->downloadMedia($_SESSION['imageid'], $fmmid, 'images');
									//$imageurl = str_replace("../attachment/", '', $imageurl);
									$_SESSION['imageurl'] = $imageurl;

									if ($qiniu['isqiniu']) {
										$nfilename = 'FMFetchiHH'.date('YmdHis').random(16).'.jpg';							
										$qiniu['upurl'] = $_SESSION['imageurl'];
										$mid = 0;
										$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
										return $this->respText($nfilename);
										$qiniuimages = $this->fmqnimages($nfilename, $qiniu, $mid, $username);
										if ($qiniuimages['success'] == '-1') {
											$fmdata = array(
												"success" => -1,
												"msg" => $qiniuimages['msg'],
											);
											return $this->respText($fmdata['msg']);
										}else {
											
											if ($username) {
												$insertdataname = array();
												$insertdataname['photoname'] = $nfilename;
												pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
											}else {
												$insertdataname = array(
													'rid'       => $rid,
													'uniacid'   => $uniacid,
													'from_user' => $from_user,
													'photoname' => $nfilename,
												);
												pdo_insert($this->table_users_name, $insertdataname);
											}
											
										}
										$_SESSION['imageurl'] = $qiniuimages['imgurl'];
									}
									//$msg .= "封面地址" . "\n" . toimage($_SESSION['imageurl']);

									$_SESSION['imageok']= 1;
									return $this->respText($msg);
								}else {

										if ($_SESSION['realnameok'] == 1) {
											$msg = $_W['account']['name']." 提请您：\n▶️ 请回复您的手机号码\n".$_SESSION['voiceid'];
											return $this->respText($msg);
										}
										if ($_SESSION['descriptionok'] == 1) {
											$msg = $_W['account']['name']." 提请您：\n▶️ 请回复您的真实姓名\n".$_SESSION['voiceid'];
											return $this->respText($msg);
										}
										if ($_SESSION['photonameok'] == 1) {
											$msg = $_W['account']['name']." 提请您：\n▶️ 请回复您的主题介绍\n".$_SESSION['voiceid'];
											return $this->respText($msg);
										}

										if ($row['mediatypev']) {
											if ($_SESSION['videook'] == 1) {
												$msg = $_W['account']['name']." 提请您：\n▶️ 请回复您的照片主题宣言\n".$_SESSION['voiceid'];
												return $this->respText($msg);
											}
										}

										if ($row['mediatypem']) {
											if ($_SESSION['voiceok'] == 1) {
												$msg = $_W['account']['name']." 提请您：\n▶️ 请录制您的视频\n".$_SESSION['voiceid'];
												return $this->respText($msg);
											}
										}
										
								}

								for ($i = 1; $i <= $row['tpxz']; $i++) {
									if (empty($_SESSION['imagesid'.$i])) {
										if ($row['mediatype']) {
											$_SESSION['imagesid'.$i]= $this->message['mediaid'];
											if ($row['mediatypev']) {
												$info = "▶️ 请开始录制您的视频";
											}
											if ($row['mediatypem']) {
												$info = "▶️ 请开始录制您的好声音";
											}
											if (!$row['mediatypev'] && !$row['mediatypem']) {
												$info = "▶️ 请回复您的照片主题宣言：";
											}
											$msg = $_W['account']['name']." 提请您：\n我们已经收到您的相册照片，您总共可以上传".$row['tpxz']."张相册照片\n"."您已经上了".$i."张相册照片\n"."如果您只想上传到当前的照片数，".$info;
											$imagesurl = $this->downloadMedia($_SESSION['imagesid'.$i], $fmmid, 'images');	
											
											//$imagesurl = str_replace("../attachment/", '', $imagesurl);
											$_SESSION['imagesurl'.$i] = $imagesurl;
											if ($qiniu['isqiniu']) {	
												$nfilename = 'FMFetchiHH'.date('YmdHis').random(16).'.jpg';							
												$qiniu['upurl'] = $_SESSION['imagesurl'.$i];
												$mid =$i;
												$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
												$qiniuimages = $this->fmqnimages($nfilename, $qiniu, $mid, $username);
												if ($qiniuimages['success'] == '-1') {
													$fmdata = array(
														"success" => -1,
														"msg" => $qiniuimages['msg'],
													);
													return $this->respText($fmdata['msg']);
												}else {
													
													if ($username) {
														$insertdataname = array();
														$insertdataname['picarr_'.$mid.'_name'] = $nfilename;
														pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
													}else {									
														$insertdataname = array(
															'rid'       => $rid,
															'uniacid'      => $uniacid,
															'from_user' => $from_user,
														);
														$insertdataname['picarr_'.$mid.'_name'] = $nfilename;
														pdo_insert($this->table_users_name, $insertdataname);
													}
													
												}
												$_SESSION['imageurl'] = $qiniuimages['imgurl'];
											}
											//$msg .= "\n相册图片地址" . $i . "\n" . toimage($_SESSION['imagesurl'.$i]);
											$_SESSION['imagesok']= 1;
											return $this->respText($msg);
										}else{
											$_SESSION['imagesok']= 0;
											$msg = $_W['account']['name']." 提请您：\n本次活动未开启相册功能，请回复”报名“，按其顺序（要求）上传资料报名\n".$_W['account']['name']."感谢您的支持！";
											return $this->respText($msg);
										}
									}
								}
								//for ($i = 1; $i <= $row['tpxz']; $i++) {
									//if (!empty($_SESSION['imagesid'.$i])) {
										if (!$row['mediatype']) {
											$_SESSION['imagesok']= 0;
											$msg = $_W['account']['name']." 提请您：\n本次活动未开启相册功能，请回复”报名“，按其顺序（要求）上传资料报名\n".$_W['account']['name']."感谢您的支持！";
											return $this->respText($msg);
										}
										if ($_SESSION['realnameok'] == 1) {
											$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的手机号码\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['descriptionok'] == 1) {
											$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的真实姓名\n";
											//return $this->respText($msg);
										}
										if ($_SESSION['photonameok'] == 1) {
											$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的主题介绍\n";
											//return $this->respText($msg);
										}

										if ($row['mediatypev'] == 1) {
											if ($_SESSION['videook']) {
												$info = $_W['account']['name']." 提请您：\n▶️ 请回复您的照片主题宣言\n";
												//return $this->respText($msg);
											}
										}

										if ($row['mediatypem'] == 1) {
											if ($_SESSION['voiceok']) {
												$info = $_W['account']['name']." 提请您：\n▶️ 请录制您的视频\n";
												//return $this->respText($msg);
											}
										}
										if ($row['mediatype'] == 1) {
											if ($_SESSION['imagesok']) {
												$info = $_W['account']['name']." 提请您：\n▶️ 请录制您的好声音\n";
												//return $this->respText($msg);
											}
										}
										$msg = $_W['account']['name']." 提请您：\n我们已经收到您的相册照片，您总共可以上传".$row['tpxz']."张相册照片\n"."您已经上了".$row['tpxz']."张相册照片\n"."".$info;
										//$_SESSION['imagesok']= 1;
										return $this->respText($msg);
									//}
								
								break;
							
							case 'voice':
								$fmmid = random(16);
								if (empty($_SESSION['voiceid'])) {
									$_SESSION['voiceid']= $this->message['mediaid'];
									if ($row['mediatypev']) {
										$info = "▶️ 请开始录制您的视频";
									}else{
										$info = "▶️ 请回复您的照片主题宣言：";
									}


									$voiceurl = $this->downloadMedia($_SESSION['voiceid'], $fmmid, 'voice');
									$_SESSION['voiceurl'] = $voiceurl;
									if ($qiniu['isqiniu']) {
										$nfilename = 'FMVOICEHH'.date('YmdHis').random(16).'.amr';
										$upurl = tomedia($_SESSION['voiceurl']);
										$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
										$audiotype = 'voice';
											$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upurl, $audiotype, $username);
											$nfilenamefop = $qiniuaudios['nfilenamefop'];
											if ($qiniuaudios['success'] == '-1') {
											//	var_dump($err);
												$fmdata = array(
													"success" => -1,
													"msg" => $qiniuaudios['msg'],
												);
												return $this->respText($fmdata['msg']);
											} else {
												$insertdata = array();		
												
												if ($qiniuaudios['success'] == '-2') {
													//var_dump($err);
													$fmdata = array(
															"success" => -1,
															"msg" => $qiniuaudios['msg'],
														);
														return $this->respText($fmdata['msg']);
												} else {
													
													$voice = $qiniuaudios[$audiotype];
													//$udata[$audiotype] = $qiniuaudios[$audiotype];
													
													//pdo_insert($this->table_users_voice, $udata);
													//pdo_update($this->table_users, array('fmmid' => $fmmid,'mediaid'  =>$_POST['serverId'],'lastip' => getip(),'lasttime' => $now,'voice' => $voice,'timelength' => $_GPC['timelength']), array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));
													
													if ($username) {
														$insertdataname = array();
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
													}else {
														$insertdataname = array(
															'rid'       => $rid,
															'uniacid'      => $uniacid,
															'from_user' => $from_user,
														);
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_insert($this->table_users_name, $insertdataname);
													}
												}						
											}
										$_SESSION['voiceurl'] = $voice;
									}
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您录制的好声音\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请重新录制好声音\n";
									//$msg .= "\n好声音地址" . "\n" . tomedia($_SESSION['voiceurl']);

									
									$_SESSION['voiceok']= 1;
									return $this->respText($msg);
								}else{
									
									

									$_SESSION['voiceid']= $this->message['mediaid'];	
									
									if ($_SESSION['realnameok']) {
										$info = "▶️ 请回复您的手机号码\n";
										//return $this->respText($msg);
									}
									if ($_SESSION['descriptionok']) {
										$info = "▶️ 请回复您的真实姓名\n";
										//return $this->respText($msg);
									}
									if ($_SESSION['photonameok']) {
										$info = "▶️ 请回复您的主题介绍\n";
										//return $this->respText($msg);
									}

									if ($row['mediatypev']) {
										if ($_SESSION['videook']) {
											$info = "▶️ 请回复您的照片主题宣言\n";
											//return $this->respText($msg);
										}
									}

									if ($row['mediatypem']) {
										if ($_SESSION['voiceok']) {
											$info = "▶️ 请录制您的视频\n";
											//return $this->respText($msg);
										}
									}
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您重新录制的好声音\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请再次重新录制好声音\n";
									$voiceurl = $this->downloadMedia($_SESSION['voiceid'], $fmmid, 'voice');
									load()->func('file');
									file_delete($_SESSION['voiceurl']);
									$_SESSION['voiceurl'] = $voiceurl;

									//return $this->respText($qiniu);
									if ($qiniu['isqiniu']) {
										$nfilename = 'FMVOICEHH'.date('YmdHis').random(16).'.amr';
										$upurl = tomedia($_SESSION['voiceurl']);
										$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
										$audiotype = 'voice';
											$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upurl, $audiotype, $username);
											$nfilenamefop = $qiniuaudios['nfilenamefop'];
											if ($qiniuaudios['success'] == '-1') {
											//	var_dump($err);
												$fmdata = array(
													"success" => -1,
													"msg" => $qiniuaudios['msg'],
												);
												return $this->respText($fmdata['msg']);
											} else {
												$insertdata = array();		
												
												if ($qiniuaudios['success'] == '-2') {
													//var_dump($err);
													$fmdata = array(
															"success" => -1,
															"msg" => $qiniuaudios['msg'],
														);
														return $this->respText($fmdata['msg']);
												} else {
													
													$voice = $qiniuaudios[$audiotype];
													//$udata[$audiotype] = $qiniuaudios[$audiotype];
													
													//pdo_insert($this->table_users_voice, $udata);
													//pdo_update($this->table_users, array('fmmid' => $fmmid,'mediaid'  =>$_POST['serverId'],'lastip' => getip(),'lasttime' => $now,'voice' => $voice,'timelength' => $_GPC['timelength']), array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));
													
													if ($username) {
														$insertdataname = array();
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
													}else {
														$insertdataname = array(
															'rid'       => $rid,
															'uniacid'      => $uniacid,
															'from_user' => $from_user,
														);
														$insertdataname[$audiotype.'name'] = $nfilename;
														$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
														pdo_insert($this->table_users_name, $insertdataname);
													}
												}						
											}
										$_SESSION['voiceurl'] = $voice;
									}
									//$msg .= "\n好声音地址" . "\n" . tomedia($_SESSION['voiceurl']);
									$_SESSION['voiceok']= 1;
									return $this->respText($msg);
								}

								break;
							case 'video':

								$fmmid = random(16);
								if (empty($_SESSION['videoid'])) {
									$_SESSION['videoid']= $this->message['mediaid'];
									$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
									$_SESSION['videourl'] = $videourl;

									if ($qiniu['isqiniu']) {	//开启七牛存储
										$audiotype = 'vedio';
										$nfilename = 'FMHH'.date('YmdHis').random(8).'hhvideo.mp4';
										$upmediatmp = toimage($_SESSION['videourl']);

										$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username);
										$nfilenamefop = $qiniuaudios['nfilenamefop'];
										if ($qiniuaudios['success'] == '-1') {
										//	var_dump($err);
											$fmdata = array(
												"success" => -1,
												"msg" => $qiniuaudios['msg'],
											);
											return $this->respText($fmdata['msg']);
										} else {
											$insertdata = array();		
											
											if ($qiniuaudios['success'] == '-2') {
												//var_dump($err);
												$fmdata = array(
														"success" => -1,
														"msg" => $err,
													);
													return $this->respText($fmdata['msg']);
											} else {
												//var_dump($ret);
												$insertdata[$audiotype] = $qiniuaudios[$audiotype];			
												//pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
												if ($username) {
													$insertdataname = array();
													$insertdataname[$audiotype.'name'] = $nfilename;
													$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
													pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
												}else {
													$insertdataname = array(
														'rid'       => $rid,
														'uniacid'      => $uniacid,
														'from_user' => $from_user,
													);
													$insertdataname[$audiotype.'name'] = $nfilename;
													$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
													pdo_insert($this->table_users_name, $insertdataname);
												}
												
											}						
										}
										$_SESSION['videourl'] = $qiniuaudios[$audiotype];
									}

									$_SESSION['videook']= 1;
									
									$info = "▶️ 请回复您的照片主题宣言：";
									
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请重新录制视频\n";
									//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
									return $this->respText($msg);
								}else{
									//$this->istip('video');
									$_SESSION['videoid']= $this->message['mediaid'];
									load()->func('file');
									file_delete($_SESSION['videourl']);
									$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
									$_SESSION['videourl'] = $videourl;

									if ($qiniu['isqiniu']) {	//开启七牛存储
										$audiotype = 'vedio';
										$nfilename = 'FMHH'.date('YmdHis').random(8).'hhvideo.mp4';

										$upmediatmp = toimage($_SESSION['videourl']);
										$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username);
										$nfilenamefop = $qiniuaudios['nfilenamefop'];

										if ($qiniuaudios['success'] == '-1') {
										//	var_dump($err);
											$fmdata = array(
												"success" => -1,
												"msg" => $qiniuaudios['msg'],
											);
											return $this->respText($fmdata['msg']);
										} else {
											$insertdata = array();		
											
											if ($qiniuaudios['success'] == '-2') {
												//var_dump($err);
												$fmdata = array(
														"success" => -1,
														"msg" => $err,
													);
													return $this->respText($fmdata['msg']);
											} else {
												//var_dump($ret);
												$insertdata[$audiotype] = $qiniuaudios[$audiotype];	
												
												
												if ($username) {
													$insertdataname = array();
													$insertdataname[$audiotype.'name'] = $nfilename;
													$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
													pdo_update($this->table_users_name, $insertdataname, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
												}else {
													$insertdataname = array(
														'rid'       => $rid,
														'uniacid'      => $uniacid,
														'from_user' => $from_user,
													);
													$insertdataname[$audiotype.'name'] = $nfilename;
													$insertdataname[$audiotype.'namefop'] = $nfilenamefop;
													pdo_insert($this->table_users_name, $insertdataname);
												}
											}						
										}
										$_SESSION['videourl'] = $qiniuaudios[$audiotype];
									}
									
									if ($_SESSION['realnameok']) {
										$info = "▶️ 请回复您的手机号码\n";
										//return $this->respText($msg);
									}
									if ($_SESSION['descriptionok']) {
										$info = "▶️ 请回复您的真实姓名\n";
										//return $this->respText($msg);
									}
									if ($_SESSION['photonameok']) {
										$info = "▶️ 请回复您的主题介绍\n";
										//return $this->respText($msg);
									}

									if ($row['mediatypev']) {
										if ($_SESSION['videook']) {
											$info = "▶️ 请回复您的照片主题宣言\n";
											//return $this->respText($msg);
										}
									}
									
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您重新录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请再次重新录制视频\n";
									//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
									$_SESSION['videook']= 1;
									return $this->respText($msg);
								}
								break;
							case 'shortvideo':

								$fmmid = random(16);
								if (empty($_SESSION['videoid'])) {
									$_SESSION['videoid']= $this->message['mediaid'];
									$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
									$_SESSION['videourl'] = $videourl;
									$_SESSION['videook']= 1;
									
									$info = "▶️ 请回复您的照片主题宣言：";
									
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请重新录制视频\n";
									//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
									return $this->respText($msg);
								}else{
									//$this->istip('video');
									$_SESSION['videoid']= $this->message['mediaid'];
									load()->func('file');
									file_delete($_SESSION['videourl']);	
									$videourl = $this->downloadMedia($_SESSION['videoid'], $fmmid, 'video');
									$_SESSION['videourl'] = $videourl;
									
									if ($_SESSION['realnameok']) {
										$info = "▶️ 请回复您的手机号码\n";
										//return $this->respText($msg);
									}
									if ($_SESSION['descriptionok']) {
										$info = "▶️ 请回复您的真实姓名\n";
										//return $this->respText($msg);
									}
									if ($_SESSION['photonameok']) {
										$info = "▶️ 请回复您的主题介绍\n";
										//return $this->respText($msg);
									}

									if ($row['mediatypev']) {
										if ($_SESSION['videook']) {
											$info = "▶️ 请回复您的照片主题宣言\n";
											//return $this->respText($msg);
										}
									}
									
									$msg = $_W['account']['name']." 提请您：\n我们已经收到您重新录制的视频\n"."▶️ 如果满意，".$info."\n"."▶️ 如果不满意，请再次重新录制视频\n";
									//$msg .= "\n视频地址" . "\n" . tomedia($_SESSION['videourl']);
									$_SESSION['videook']= 1;
									return $this->respText($msg);
								}
								break;

							default:

							break;
						}

						
					}//总的结束
								
				}else{
					
					if($now <= $row['start_time']){
						$message = "亲，".$row['title']."活动将在".date("Y-m-d H:i:s", $row['start_time'])."时准时开放投票,您可以：\n";
						$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&rid=".$rid."&m=fm_photosvote&do=photosvoteview&uniacid=".$uniacid."'>先睹为快</a>\n";
						if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
							$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
						}
						
					}elseif($now >= $row['end_time']){
						$message = "亲，".$row['title']."活动已经于".date("Y-m-d H:i:s", $row['end_time'])."时结束,您可以：\n";
						$message .= "1、<a href='".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&j=".$_W['acid']."&c=entry&rid=".$rid."&m=fm_photosvote&do=paihang&uniacid=".$uniacid."'>看看排行榜</a>\n";
						if ($row['ishuodong'] == 1 && !empty($row['huodongurl'])) {
							$message .= "2、<a href='".$row['huodongurl']."'>".$row['huodongname']."</a>";
						}			
						
					}
					return $this->respText($message);				
				}
		

	}
	
	public function isNeedSaveContext() {
		return false;
	}


	public function istip($type = '') {
		if ($_SESSION['realnameok']) {
			$msg = $_W['account']['name']." 提请您：\n请回复您的手机号码\n".$_SESSION['voiceid'];
			return $this->respText($msg);
		}
		if ($_SESSION['descriptionok']) {
			$msg = $_W['account']['name']." 提请您：\n请回复您的真实姓名\n".$_SESSION['voiceid'];
			return $this->respText($msg);
		}
		if ($_SESSION['photonameok']) {
			$msg = $_W['account']['name']." 提请您：\n请回复您的主题介绍\n".$_SESSION['voiceid'];
			return $this->respText($msg);
		}
		if ($type != 'video') {
			if ($row['mediatypev']) {
				if ($_SESSION['videook']) {
					$msg = $_W['account']['name']." 提请您：\n请回复您的照片主题宣言\n".$_SESSION['voiceid'];
					return $this->respText($msg);
				}
			}
			if ($type != 'voice') {
				if ($row['mediatypem']) {
					if ($_SESSION['voiceok']) {
						$msg = $_W['account']['name']." 提请您：\n请录制您的视频\n".$_SESSION['voiceid'];
						return $this->respText($msg);
					}
				}
				if ($type != 'image') {

					if ($row['mediatype']) {
						if ($_SESSION['imagesok']) {
							$msg = $_W['account']['name']." 提请您：\n请录制您的好声音\n".$_SESSION['voiceid'];
							return $this->respText($msg);
						}
					}
				}
			}
		}
		
	}
	public function GetIpLookup($ip = ''){  
		if(empty($ip)){  
			$ip = getip();  
		}  
		$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);  
		if(empty($res)){ return false; }  
		$jsonMatches = array();  
		preg_match('#\{.+?\}#', $res, $jsonMatches);  
		if(!isset($jsonMatches[0])){ return false; }  
		$json = json_decode($jsonMatches[0], true);  
		if(isset($json['ret']) && $json['ret'] == 1){  
			$json['ip'] = $ip;  
			unset($json['ret']);  
		}else{  
			return false;  
		}  
		return $json;  
	}  
	public function getiparr($ip) {
		$ip = $this->GetIpLookup($row['ip']);
		$iparr = array();
		$iparr['country'] .= $ip['country'];
		$iparr['province'] .= $ip['province'];
		$iparr['city'] .= $ip['city'];
		$iparr['district'] .= $ip['district'];
		$iparr['ist'] .= $ip['ist'];
		$iparr = iserializer($iparr);
		return $iparr;
	}


	function fmqnimages($nfilename, $qiniu, $mid, $username) {
		$fmurl = 'http://demo.qdaygroup.com/api/qiniu/api.php?';
		$hosts = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$host = base64_encode($hosts);
		
		$visitorsip = base64_encode(getip());	
		
		$fmimages = array(
			'nfilename' => $nfilename,
			'qiniu' => $qiniu,
			'mid' => $mid,
			'username' => $username,
		);
		
		$fmimages =  base64_encode(base64_encode(iserializer($fmimages)));
		
		$fmpost = $fmurl.'host='.$host."&visitorsip=" . $visitorsip."&webname=" . $webname."&fmimages=".$fmimages;		
		
		load()->func('communication');
		$content = ihttp_get($fmpost);		
		$fmmv = @json_decode($content['content'], true);
		
		if ($mid == 0) {
			
			$fmdata = array(
				"success" => $fmmv['success'],
				"msg" =>$fmmv['msg'],
			);
			$fmdata['mid'] == 0;
			$fmdata['imgurl'] = $fmmv['imgurl'];
				
			return $fmdata;
			exit;
			
		}else{
			$fmdata = array(
				"success" => $fmmv['success'],
				"msg" => $fmmv['msg'],
			);
			$fmdata['picarr_'.$mid] = $fmmv['picarr_'.$mid];
			return $fmdata;
			exit;
		}
		//return $fmmv;
	}

	function fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username) {
		global $_W;	
		$fmurl = 'http://demo.qdaygroup.com/api/qiniu/api.php?';
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$host = base64_encode($host);
		$clientip = base64_encode($_W['clientip']);

		$fmaudios = array(
			'nfilename' => $nfilename,
			'qiniu' => $qiniu,
			'upmediatmp' => $upmediatmp,
			'audiotype' => $audiotype,
			'username' => $username,
		);
		$fmaudios =  base64_encode(base64_encode(iserializer($fmaudios)));
				
		$fmpost = $fmurl.'host='.$host."&visitorsip=" . $clientip."&fmaudios=".$fmaudios;	

		load()->func('communication');		
		$content = ihttp_get($fmpost);
		$fmmv = @json_decode($content['content'], true);
			
		$fmdata = array(		
			"msg" => $fmmv['msg'],
			"success" => $fmmv['success'],
			"nfilenamefop" => $fmmv['nfilenamefop'],	
		);
		$fmdata[$audiotype] = $fmmv[$audiotype];
			
		return $fmdata;
		exit();	
		
	}
	
	function downloadMedia($mediaid, $filename, $type) {
		//下载语音		
		global $_W;
		load()->func('file');
		$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_W['acid'];		
		$access_token = $_W['account']['access_token']['token'];
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = $this->downloadWeixinFile($url);	
		if ($type == 'images') {
			$typepath = "images";
		}else {
			$typepath = "audios";
		}
		$path = "{$typepath}/{$uniacid}/" . date('Y/m/');
		mkdirs(ATTACHMENT_ROOT . '/' . $path);
		if ($type == 'images') {
			$filenames = $path ."HHimages_" . $filename . ".jpg";
		}
		if ($type == 'voice') {
			$filenames = $path ."HHvoice_" . $filename . ".amr";
		}
		if ($type == 'video') {
			$filenames = $path ."HHvideo_" . $filename . ".mp4";
		}
		
		
		$this->saveWeixinFile(ATTACHMENT_ROOT . '/' . $filenames, $fileInfo["body"]);
		
		return $filenames;
		
		
	}
	
	function downloadWeixinFile($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);    
		curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		curl_close($ch);
		$imageAll = array_merge(array('header' => $httpinfo), array('body' => $package)); 
		return $imageAll;
	}
	 	
	function saveWeixinFile($filename, $filecontent) {		
		$local_file = fopen($filename, 'w');
		if (false !== $local_file){
			if (false !== fwrite($local_file, $filecontent)) {
				fclose($local_file);
			}
		}
	}
	
}


