<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
header('Content-type: application/json');
		$qiniu = iunserializer($reply['qiniu']);
		$fmmid = random(16);
		$now = time();
		$udata = array(
			'uniacid' => $uniacid,
			'rid' => $rid,
			'from_user' => $from_user,
			'fmmid' => $fmmid,
			'mediaid'  =>$_POST['serverId'],	
			'timelength' => $_GPC['timelength'],	
			'ip' => getip(),
			'createtime' => $now,
		);
		if ($udata['mediaid']) {
			$voice = $this->downloadVoice($udata['mediaid'], $fmmid);
			if ($qiniu['isqiniu']) {											
				$nfilename = 'FMVOICE'.date('YmdHis').random(16).'.amr';
				$upurl = toimage($voice);
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
						echo json_encode($fmdata);
						exit();	
					} else {
						$insertdata = array();		
						
						if ($qiniuaudios['success'] == '-2') {
							//var_dump($err);
							$fmdata = array(
									"success" => -1,
									"msg" => $qiniuaudios['msg'],
								);
								echo json_encode($fmdata);
								exit();	
						} else {
							
							$voice = $qiniuaudios[$audiotype];
							$udata[$audiotype] = $qiniuaudios[$audiotype];
							
							pdo_insert($this->table_users_voice, $udata);
							pdo_update($this->table_users, array('fmmid' => $fmmid,'mediaid'  =>$_POST['serverId'],'lastip' => getip(),'lasttime' => $now,'voice' => $voice,'timelength' => $_GPC['timelength']), array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));
							
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
							
							/**$fmdata = array(
								"success" => 1,
								"imgurl" => $insertdata[$audiotype],
							);
							echo json_encode($fmdata);
							exit();	
						**/
						}						
					}
				
			}else {
				$udata['voice'] = $voice;
				pdo_insert($this->table_users_voice, $udata);
				pdo_update($this->table_users, array('fmmid' => $fmmid,'mediaid'  =>$_POST['serverId'],'lastip' => getip(),'lasttime' => $now,'voice' => $voice,'timelength' => $_GPC['timelength']), array('uniacid' => $uniacid, 'rid' => $rid, 'from_user' => $from_user));
			
			}
			
		}				
			
						
		
		$data=json_encode(array('ret'=>0,'serverId'=>$_POST['serverId']));
		die($data);