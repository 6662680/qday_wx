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
				$data = preg_replace("/data:image\/(.*);base64,/","",$data);
				if (file_put_contents($updir.$nfilename,base64_decode($data))===false) {
					$fmdata = array(
						"success" => -1,
						"msg" => '上传错误',
					);
					echo json_encode($fmdata);
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
	