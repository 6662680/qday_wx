<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
		$qiniu = iunserializer($reply['qiniu']);
		$now= time();
			
		load()->func('file');
		
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
			
			$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
		}
		/**if ($reply['subscribe']) {
			if ($follow <> 1) {
				$linkurl = $this->createMobileUrl('subscribeshare', array('rid' => $rid));
				$fmdata = array(
					"success" => 3,
					"rid" => $rid,
					"msg" => '请关注后报名！',
					"linkurl" => $reply['shareurl'],
				);
				echo json_encode($fmdata);
				exit();	
			}
		}
		**/
		
		
		
		$now = time();
		if($now <= $reply['bstart_time'] || $now >= $reply['bend_time']) {
					
			if ($now <= $reply['bstart_time']) {
				$fmdata = array(
					"success" => -1,
					"msg" => $reply['btipstart'],
				);
				echo json_encode($fmdata);
				exit();	
			}
			if ($now >= $reply['bend_time']) {
				$fmdata = array(
					"success" => -1,
					"msg" => $reply['btipend'],
				);
				echo json_encode($fmdata);
				exit();	
			}
		}
		if (!$mygift) {
			$insertdata = array(
				'rid'       => $rid,
				'uniacid'      => $uniacid,
				'from_user' => $from_user,
				'avatar'    => $avatar,
				'nickname'  => $nickname,			    
				'photo'  => '',			    
				'description'  => '',
				'photoname'  => '',
				'realname'  => '',
				'mobile'  => '',
				'weixin'  => '',
				'qqhao'  => '',
				'email'  => '',
				'job'  => '',
				'xingqu'  => '',
				'address'  => '',
				'photosnum'  => '0',
				'xnphotosnum'  => '0',
				'hits'  => '1',
				'xnhits'  => '1',
				'yaoqingnum'  => '0',
				'createip' => getip(),
				'lastip' => getip(),
				'status'  => '0',
				'sharetime' => $now,
				'createtime'  => $now,
			);
			$insertdata['iparr'] = getiparr($insertdata['lastip']);
			pdo_insert($this->table_users, $insertdata);
			
			   if($reply['isfans']){
					if($myavatar){
				        fans_update($from_user, array(
					        'avatar' => $myavatar,					
		                ));
				    } 
					if($mynickname){
				        fans_update($from_user, array(
					        'nickname' => $mynickname,					
		                ));
				    }
					
			        if($reply['isrealname']){
				        fans_update($from_user, array(
					        'realname' => $realname,					
		                ));
				    }
				    if($reply['ismobile']){
				        fans_update($from_user, array(
					        'mobile' => $mobile,					
		                ));
				    }				
				    if($reply['isqqhao']){
				        fans_update($from_user, array(
					        'qq' => $qqhao,					
		                ));
				    }
				    if($reply['isemail']){
				        fans_update($from_user, array(
					        'email' => $email,					
		                ));
				    }
				    if($reply['isaddress']){
				        fans_update($from_user, array(
					        'address' => $address,					
		                ));
				    }				
			    }
				 //查询是否被邀请人员
				$yaoqing = pdo_fetch("SELECT id,uid FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid ORDER BY `visitorstime` asc", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
				if (!empty($yaoqing)){//更新被邀请人员状态 是以时间为标准为邀请人加资格
					pdo_update($this->table_data,array('isin' => 4),array('id' => $yaoqing['id']));
					$yaoqingren = pdo_fetch("SELECT yaoqingnum FROM ".tablename($this->table_users)." WHERE id = :id", array(':id' => $yaoqing['uid']));
					pdo_update($this->table_users,array('yaoqingnum' => $yaoqingren['yaoqingnum']+1),array('id' => $yaoqing['uid']));
					//查询所有其他邀请人并相互增加人气
					$yaoqingall = pdo_fetchall("SELECT id,uid FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid and id!=".$yaoqing['id']." ORDER BY `visitorstime` asc", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
					foreach ($yaoqingall as $row) {
						pdo_update($this->table_data,array('isin' => 2),array('id' => $row['id']));
						if($reply['opensubscribe']==2){
							$yaoqingren = pdo_fetch("SELECT yaoqingnum FROM ".tablename($this->table_users)." WHERE id = :id", array(':id' => $row['uid']));
							pdo_update($this->table_users,array('yaoqingnum' => $yaoqingren['yaoqingnum']+1),array('id' => $row['uid']));					
						}
					}
				}
				
		}
					
		if ($_GPC['upphotosone'] == 'start') {
			$base64=file_get_contents("php://input"); //获取输入流			
			
			
			$base64=json_decode($base64,1);
			$data = $base64['base64'];
			
			if($data){
				$harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
				
				preg_match("/data:image\/(.*?);base64/",$data,$res);
				$ext = $res[1];
				$setting = $_W['setting']['upload']['image'];
				if (!in_array(strtolower($ext), $setting['extentions']) || in_array(strtolower($ext), $harmtype)) {
					$fmdata = array(
						"success" => -1,
						"msg" => '系统不支持您上传的文件（扩展名为：'.$ext.'）,请上传正确的图片文件',
					);
					echo json_encode($fmdata);
					die;
				}
				
				$nfilename = 'FMFetchi'.date('YmdHis').random(16).'.'.$ext;
				$updir = '../attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/';
				mkdirs($updir);	
				
				$data = preg_replace("/^data:image\/(.*);base64,/","",$data);
				
				if (!$data) {
					$fmdata = array(
						"success" => -1,
						"msg" => $data.'当前图片宽度大于3264px,系统无法识别为其生成！',
					);
					echo json_encode($fmdata);
					exit;
				}
				
				if (file_put_contents($updir.$nfilename,base64_decode($data))===false) {
					$fmdata = array(
						"success" => -1,
						"msg" => '上传错误',
					);
					echo json_encode($fmdata);
					exit;
				}else{
					$mid = $_GPC['mid'];
					
					if (!$qiniu['isqiniu']) {
						$picurl = $updir.$nfilename;
						if ($mid == 0) {
							pdo_update($this->table_users, array('photo' => $picurl), array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
						}else {
							$insertdata = array();								
							$insertdata['picarr_'.$mid] = $updir.$nfilename;
							pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
						}
						$fmdata = array(
							"success" => 1,
							"msg" => '上传成功！',
							"imgurl" => $picurl,
						);
						echo json_encode($fmdata);
						exit();	
					}else {										
						$qiniu['upurl'] = $_W['siteroot'].'attachment/images/'.$uniacid.'/'.date("Y").'/'.date("m").'/'.$nfilename;						
						$qiniuimages = $this->fmqnimages($nfilename, $qiniu, $mid, $username);
						if ($qiniuimages['success'] == '-1') {
							$fmdata = array(
								"success" => -1,
								"msg" => $qiniuimages['msg'],
							);
							echo json_encode($fmdata);
							exit();
						}else {
							$insertdata = array();
							if ($mid == 0 && $qiniuimages['mid'] == 0) {
								
								$insertdata['photo'] = $qiniuimages['imgurl'];
								pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
					
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
								file_delete($updir.$nfilename);
								$fmdata = array(
									"success" => 1,
									"msg" => $qiniuimages['msg'],
									"imgurl" => $insertdata['photo'],
								);
								echo json_encode($fmdata);
								exit();	
							}else {
								$insertdata['picarr_'.$mid] = $qiniuimages['picarr_'.$mid];
								pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
								
								
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
								file_delete($updir.$nfilename);
								$fmdata = array(
									"success" => 1,
									"msg" => $qiniuimages['msg'],
									"imgurl" => $insertdata['picarr_'.$mid],
								);
								echo json_encode($fmdata);
								exit();	
							}
							
						}
					}
				}
				
			}else{
				$fmdata = array(
					"success" => -1,
					"msg" =>'没有发现上传图片',
				);
				echo json_encode($fmdata);
				exit();	
			}
		}
		if ($_GPC['upaudios'] == 'start') {
			//var_dump($_FILES);
			$audiotype = $_GPC['audiotype'];
			$upmediatmp = $_FILES[$audiotype]["tmp_name"];
			if ($qiniu['videologo']) {
				$qiniu['videologo'] = toimage($qiniu['videologo']);
			}
			
			if($upmediatmp){
				
				$ext = $_FILES[$audiotype]["type"];				
				$nfilename = 'FM'.date('YmdHis').random(8).$_FILES[$audiotype]["name"];						
				
				$updir = '../attachment/audios/'.$uniacid.'/'.date("Y").'/'.date("m").'/';
				mkdirs($updir);	
				if ($mygift[$audiotype]) {
					file_delete($mygift[$audiotype]);	
				}		
				$music = file_upload($_FILES[$audiotype], 'audio'); 
			
				
				$videopath = $music['path']; 
				
				if ($qiniu['isqiniu']) {	//开启七牛存储
					
					$upmediatmp = toimage($videopath);
					$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upmediatmp, $audiotype, $username);
					$nfilenamefop = $qiniuaudios['nfilenamefop'];
					if ($qiniuaudios['success'] == '-1') {
					//	var_dump($err);
						$fmdata = array(
							"success" => -1,
							"msg" => $qiniuaudios['msg'],
						);
						echo json_encode($fmdata);
						exit();	
					} else {
						$insertdata = array();		
						
						if ($qiniuaudios['success'] == '-2') {
							//var_dump($err);
							$fmdata = array(
									"success" => -1,
									"msg" => $err,
								);
								echo json_encode($fmdata);
								exit();	
						} else {
							//var_dump($ret);
							$insertdata[$audiotype] = $qiniuaudios[$audiotype];			
							pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
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
							
							$fmdata = array(
								"success" => 1,
								"imgurl" => $insertdata[$audiotype],
							);
							echo json_encode($fmdata);
							exit();	
						
						}						
					}
				}else {
					$insertdata = array();
					//$updir = '../attachment/audios/'.$uniacid.'/'.date("Y").'/'.date("m").'/';
					//mkdirs($updir);	
					//if ($mygift[$audiotype]) {
					//	file_delete($mygift[$audiotype]);	
					//}		
					//$music = file_upload($_FILES[$audiotype], 'audio'); 
					$insertdata[$audiotype] = $music['path']; 
											
					pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
					$fmdata = array(
						"success" => 1,
						"imgurl" => $insertdata[$audiotype],
					);
					echo json_encode($fmdata);
					exit();	
				}
			}else{
				
								
				if ($_GPC[$audiotype] && stristr($username[$audiotype.'namefop'],$_GPC[$audiotype])) {
					if ($qiniu['isqiniu']) {	//开启七牛存储	
							
						$upurl = $_GPC[$audiotype];
						$qiniuaudios = $this->fmqnaudios($nfilename, $qiniu, $upurl,$audiotype, $username);
						$nfilenamefop = $qiniuaudios['nfilenamefop'];
						if ($qiniuaudios['success'] == '-1') {
							//	var_dump($err);
								$fmdata = array(
									"success" => -1,
									"msg" => $qiniuaudios['msg'],
								);
								echo json_encode($fmdata);
								exit();	
							} else {
								if ($qiniuaudios['success'] == '-2') {
									//var_dump($err);
									$fmdata = array(
										"success" => -1,
										"msg" => $err,
									);
									echo json_encode($fmdata);
									exit();	
								} else {
									//var_dump($ret);
									$insertdata[$audiotype] = $qiniuaudios[$audiotype];			
									pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
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
									
									$fmdata = array(
										"success" => 1,
										"imgurl" => $insertdata[$audiotype],
									);
									echo json_encode($fmdata);
									exit();	
								
								}	
							}	
					}else {
						$insertdata = array();							
						$insertdata[$audiotype] = $_GPC[$audiotype];
						pdo_update($this->table_users, $insertdata, array('from_user'=>$from_user, 'rid' => $rid, 'uniacid' => $uniacid));
						$fmdata = array(
							"success" => 1,
							"imgurl" => $_GPC[$audiotype],
						);
						echo json_encode($fmdata);
						exit();	
					}
					
					
					
					
				}else {
					if ($audiotype == 'music') {
						$msg = '请上传音频或者填写远程音频地址';
					}elseif ($audiotype == 'vedio') {
						$msg = '请上传视频或者填写远程视频地址';
					}
					
					$fmdata = array(
						"success" => -1,
						"msg" => $msg,
					);
					echo json_encode($fmdata);
					die;
				}
			}
		}
		
		if ($_GPC['treg'] == 1) {
		    if (empty($mygift)) {
				$msg = '请先上传封面照片！';
				$fmdata = array(
					"success" => -1,
					"msg" => $msg,
				);
				echo json_encode($fmdata);
				exit();						
			}
			
			if (empty($_GPC['photoname'])) {
				//message('照片主题名没有填写！');
				$msg = '照片主题名没有填写！';
				$fmdata = array(
					"success" => -1,
					"msg" => $msg,
				);
				echo json_encode($fmdata);
				exit();	
			}
			if (empty($_GPC['description'])) {
				//message('介绍没有填写');
				$msg = '介绍没有填写';
				$fmdata = array(
					"success" => -1,
					"msg" => $msg,
				);
				echo json_encode($fmdata);
				exit();	
			}
			
			if($reply['isrealname']){
				if (empty($_GPC['realname'])) {
					//message('您的真实姓名没有填写，请填写！');
					$msg = '您的真实姓名没有填写，请填写！';
					$fmdata = array(
						"success" => -1,
						"msg" => $msg,
					);
					echo json_encode($fmdata);
					exit();	
				}
			}
			if($reply['ismobile']){
				if(!preg_match(REGULAR_MOBILE, $_GPC['mobile'])) {
					//message('必须输入手机号，格式为 11 位数字。');
					$msg = '必须输入手机号，格式为 11 位数字。';
					$fmdata = array(
						"success" => -1,
						"msg" => $msg,
					);
					echo json_encode($fmdata);
					exit();	
				}
			}
			
			if($reply['isrealname']){
				if ($mygift['realname']) {
					if ($mygift['realname'] == $_GPC['realname']) {
					
					}else {
						$realname = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and realname = :realname and rid = :rid", array(':uniacid' => $uniacid,':realname' => $_GPC['realname'],':rid' => $rid));
						if (!empty($realname)) {
							//message('您的真实姓名已经参赛，请重新填写！');
							$msg = '您的真实姓名已经参赛，请重新填写！';
							$fmdata = array(
								"success" => -1,
								"msg" => $msg,
							);
							echo json_encode($fmdata);
							exit();	
						}
					}
				
				}
				
			}
			
			if($reply['ismobile']){
				if ($mygift['mobile']) {
					if ($mygift['mobile'] == $_GPC['mobile']) {
					
					}else {
						$ymobile = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and mobile = :mobile and rid = :rid", array(':uniacid' => $uniacid,':mobile' => $_GPC['mobile'],':rid' => $rid));
						if(!empty($ymobile)) {
							//message('非常抱歉，此手机号码已经被注册，你需要更换注册手机号！');
							$msg = '非常抱歉，此手机号码已经被注册，你需要更换注册手机号！';
							$fmdata = array(
								"success" => -1,
								"msg" => $msg,
							);
							echo json_encode($fmdata);
							exit();	
						}
					}
				}
			}
			
		    $now = time();
				preg_match('/[a-zA-z]+:\/\/[^\s]*/', $_GPC["youkuurl"], $matchs);
				$tyurl = str_replace("&quot;", '', $matchs[0]);
				$udata = array(
					'avatar'    => $avatar,
					'nickname'  => $nickname,
					'description'  => $_GPC["description"],
					'photoname'  => $_GPC["photoname"],
					'youkuurl'  => $tyurl,
					'realname'  => $_GPC["realname"],
					'mobile'  => $_GPC["mobile"],
					'weixin'  => $_GPC["weixin"],
					'qqhao'  => $_GPC["qqhao"],
					'email'  => $_GPC["email"],
					'job'  => $_GPC["job"],
					'xingqu'  => $_GPC["xingqu"],
					'address'  => $_GPC["address"],
					'status'  => $reply['tpsh'] == 1 ? '0' : '1',
					'lastip' => getip(),
					'lasttime' => $now,
				);
				$udata['iparr'] = getiparr($udata['lastip']);
				pdo_update($this->table_users, $udata , array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));
				
			    if($reply['isfans']){
			        if($avatar){
				        fans_update($from_user, array(
					        'avatar' => $avatar,					
		                ));
				    } 
					if($mynickname){
				        fans_update($from_user, array(
					        'nickname' => $mynickname,					
		                ));
				    }
					if($reply['isrealname']){
				        fans_update($from_user, array(
					        'realname' => $realname,					
		                ));
				    }
				    if($reply['ismobile']){
				        fans_update($from_user, array(
					        'mobile' => $mobile,					
		                ));
				    }				
				    if($reply['isqqhao']){
				        fans_update($from_user, array(
					        'qq' => $qqhao,					
		                ));
				    }
				    if($reply['isemail']){
				        fans_update($from_user, array(
					        'email' => $email,					
		                ));
				    }
				    if($reply['isaddress']){
				        fans_update($from_user, array(
					        'address' => $address,					
		                ));
				    }				
			    }

			    if ($_W['account']['level'] == 4){
					$this->sendMobileRegMsg($from_user, $rid, $uniacid);
				}

				if ($reply['tpsh'] == 1) {
					$msg = '恭喜你报名成功，现在进入审核';
				}else {
					$msg = '恭喜你报名成功！';
				}
				
				
				$linkurl = $_W['siteroot'].'app/'.$this->createMobileUrl('tuser', array('rid' => $rid,'tfrom_user' => $from_user));
				$fmdata = array(
					"success" => 1,
					"msg" => $msg,
					"linkurl" => $linkurl,
				);
				echo json_encode($fmdata);
				exit();	
		}
	