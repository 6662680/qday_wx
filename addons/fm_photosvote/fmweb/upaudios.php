<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$from_user = $_GPC['from_user'];//

		$reply = pdo_fetch('SELECT * FROM '.tablename($this->table_reply).' WHERE uniacid= :uniacid AND rid =:rid ', array(':uniacid' => $uniacid, ':rid' => $rid) );
		$qiniu = iunserializer($reply['qiniu']);
		load()->func('file');
		$username = pdo_fetch("SELECT * FROM ".tablename($this->table_users_name)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));			
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
		
		
		