<?php
/**
 * [Fmoons System] Copyright (c) 2014 qdaygroup.com
 * Fmoons is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
define('IN_API', true);
require_once '../../framework/bootstrap.inc.php';
load()->model('reply');
load()->app('common');
load()->classs('wesession');
$api = $_GPC['api'];
		if ($_GPC['fmimages']) {
			$fmimages = iunserializer(base64_decode(base64_decode($_GPC['fmimages'])));
			$nfilename = $fmimages['nfilename'];
			$qiniu = $fmimages['qiniu'];
			$mid = $fmimages['mid'];
			$username = $fmimages['username'];
				
			require_once("qiniu/io.php");
			require_once("qiniu/rs.php");	
			
			$key1 = $nfilename;
			$accessKey = $qiniu['accesskey'];
			$secretKey = $qiniu['secretkey'];
			$bucket = $qiniu['bucket'];
			$qiniuurl = $qiniu['qnlink'];	
			$upurl = $qiniu['upurl'];	
			
			Qiniu_setKeys($accessKey, $secretKey);
			$client = new Qiniu_MacHttpClient(null);
			
			
			list($ret, $err) = Qiniu_RS_Fetch($client, $upurl, $bucket, $key1);

			
			if ($err !== null) {
			//	var_dump($err);
				$fmdata = array(
					"success" => -1,
					"msg" => $err,
				);
				echo json_encode($fmdata);
				exit();	
			} else {
				$fmdata = array();								
				if ($mid == 0) {
					Qiniu_RS_Delete($client, $bucket, $username['photoname']);
					$fmdata = array(
						"success" => 1,
						"msg" => '上传成功',
					);
					$fmdata['mid'] == 0;
					$fmdata['imgurl'] = "http://".$qiniuurl."/".$nfilename;
					
					echo json_encode($fmdata);
					exit();
				}else {
					Qiniu_RS_Delete($client, $bucket,$username['picarr_'.$mid.'_name']);
					$fmdata = array(
						"success" => 1,
						"msg" => '上传成功',
					);
					$fmdata['picarr_'.$mid] = "http://".$qiniuurl."/".$nfilename;
					
					echo json_encode($fmdata);
					exit();
				}
			}
		}elseif ($_GPC['fmaudios']) {
			//$upmediatmp = base64_decode($_GPC['upmediatmp']);
			$fmaudios = iunserializer(base64_decode(base64_decode($_GPC['fmaudios'])));
			//$audiotype = base64_decode($_GPC['audiotype']);
			//$nfilename = iunserializer(base64_decode($_GPC['nfilename']));
			//$qiniu = iunserializer(base64_decode($_GPC['qiniu']));
			//$username = iunserializer(base64_decode($_GPC['username']));
			$nfilename = $fmaudios['nfilename'];
			$qiniu = $fmaudios['qiniu'];
			$upmediatmp = $fmaudios['upmediatmp'];
			$upurl = $fmaudios['upurl'];
			$audiotype = $fmaudios['audiotype'];
			$username = $fmaudios['username'];
			
			require_once("./qiniu/io.php");
			require_once("./qiniu/rs.php");					
			require_once('./qiniu/pfop.php');
			require_once('./qiniu/http.php');					
			$key1 = $nfilename;	
			$accessKey = $qiniu['accesskey'];
			$secretKey = $qiniu['secretkey'];
			$bucket = $qiniu['bucket'];
			$qiniuurl = $qiniu['qnlink'];
			$pipeline = $qiniu['pipeline'];
			if ($oauth['qiniushuiying']) {
				$aq =  !empty($qiniu['aq']) ?  $qiniu['aq'] : '1';
				$videologo = !empty($qiniu['videologo']) ?  $qiniu['videologo'] : '../../web/resource/images/gw-logo.png';
				$videofbl =  !empty($qiniu['videofbl']) ?  $qiniu['videofbl'] : '640x480';
				$wmgravity =  !empty($qiniu['wmgravity']) ?  $qiniu['wmgravity'] : 'NorthEast';
			}else {
				$aq =  '1';
				$videologo = '../../web/resource/images/gw-logo.png';
				$videofbl =  '640x480';
				$wmgravity =  'NorthEast';
			}
			
			
			
			Qiniu_SetKeys($accessKey, $secretKey);
			$client = new Qiniu_MacHttpClient(null);
			/**if($upmediatmp){
				$putPolicy = new Qiniu_RS_PutPolicy($bucket);
				$upToken = $putPolicy->Token(null);
				$putExtra = new Qiniu_PutExtra();
				$putExtra->Crc32 = 1;
				list($ret, $err) = Qiniu_PutFile($upToken, $key1, $upmediatmp, $putExtra);
			}else {**/
				list($ret, $err) = Qiniu_RS_Fetch($client, $upmediatmp, $bucket, $key1);
			//}
			
			if ($err !== null) {
			//	var_dump($err);
				$fmdata = array(
					"success" => -1,
					"msg" => $err,
				);
				//echo json_encode($fmdata);
				//exit();	
				echo json_encode($fmdata);
				exit();
			} else {
				
				
				$pfop = new Qiniu_Pfop();

				$pfop->Bucket = $bucket;
				$pfop->Key = $nfilename;
				if ($audiotype == 'music') {
					$nfilenamefop = 'FMFOPM'.date('YmdHis').random(16).'.mp3';								
					$savedKey = $nfilenamefop;						
					$entry = Qiniu_Encode("$pfop->Bucket:$savedKey");
					$pfop->Fops = "avthumb/mp3/aq/$aq/ar/44100|saveas/$entry";
					
				}elseif ($audiotype == 'voice') {
					$nfilenamefop = 'FMVOICEFOPM'.date('YmdHis').random(16).'.mp3';								
					$savedKey = $nfilenamefop;						
					$entry = Qiniu_Encode("$pfop->Bucket:$savedKey");
					$pfop->Fops = "avthumb/mp3/aq/$aq/ar/44100|saveas/$entry";
				}elseif ($audiotype == 'vedio') {
					$nfilenamefop = 'FMFOPV'.date('YmdHis').random(16).'.mp4';
					$savedKey = $nfilenamefop;
					$entry = Qiniu_Encode("$pfop->Bucket:$savedKey");
					$image = Qiniu_Encode("$videologo");
					$pfop->Fops = "avthumb/mp4/wmImage/$image/wmGravity/$wmgravity/vcodec/libx264/s/$videofbl/rotate/auto|saveas/$entry";
				}
				if ($pipeline) {
					$pfop->Pipeline = $pipeline;
				}
				
				
				list($ret, $err) = $pfop->MakeRequest($client);
				
				//$pfop->Force = 1;

				//echo "\n\n====> pfop result: \n";
				if ($err !== null) {
					//var_dump($err);
					$fmdata = array(
							"success" => -1,
							"msg" => $err,
						);
						//echo json_encode($fmdata);
					echo json_encode($fmdata);
					exit();
						
				} else {
					//var_dump($ret);
					Qiniu_RS_Delete($client, $bucket, $username[$audiotype.'name']);
					Qiniu_RS_Delete($client, $bucket, $username[$audiotype.'namefop']);
					Qiniu_RS_Delete($client, $bucket, $key1);		
					$fmdata = array(
						"success" => 1,
						"nfilenamefop" => $nfilenamefop,
					);
					$fmdata[$audiotype] = "http://".$qiniuurl."/".$nfilenamefop;
						
					echo json_encode($fmdata);
					exit();
				
				}						
			}
		}
	
