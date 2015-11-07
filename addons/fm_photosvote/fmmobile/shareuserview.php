<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
		
		$tfrom_user = $_GPC['tfrom_user'];
		//$from_user = base64_encode(authcode($from_user, 'ENCODE'));
		$fromuser = $_GPC['fromuser'];
		$serverapp = $_W['account']['level'];	//是否为高级号
		$cfg = $this->module['config'];
	    $appid = $cfg['appid'];
		$secret = $cfg['secret'];
		load()->func('communication');

		$from_user = $_COOKIE["user_oauth2_openid"];
		$from_user_putonghao = $_COOKIE["user_putonghao_openid"];
		$avatar = $_COOKIE["user_oauth2_avatar"];
		$nickname = $_COOKIE["user_oauth2_nickname"];
		$sex = $_COOKIE["user_oauth2_sex"];		
		
		if(!empty($fromuser)){
			if (!isset($_COOKIE["user_fromuser_openid"])) {
				setcookie("user_fromuser_openid", $fromuser, time()+3600*24*7*30);
			}
		}
		if(!empty($tfrom_user)){
			if (!isset($_COOKIE["user_tfrom_user_openid"])) {
				setcookie("user_tfrom_user_openid", $tfrom_user, time()+3600*24*7*30);
			}
		}
	if ($cfg['oauthtype'] == 1) {
		 if ( isset($avatar) && isset($nickname) && isset($from_user) ){
			
			 //$appid = $_W['account']['key'];
		           			
		    $photosvoteviewurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserdata', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
			header("location:$photosvoteviewurl");
			exit;
		}else{
			$this->FM_checkoauth();
			$photosvoteviewurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserdata', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
			header("location:$photosvoteviewurl");
			exit;
			//$reguser = $_W['siteroot'] .'app/'.$this->createMobileUrl('reguser', array('rid' => $rid));
			//header("location:$reguser");
			//exit;
		}
	} else {
		
		if (isset($avatar) && isset($nickname) && isset($from_user) ){
			
		    $shareuserdata = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserdata', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
			header("location:$shareuserdata");
			exit;
		}else{
			$from_user = $_W['openid'];
			if($from_user) {
				//取得openid后查询是否为高级号
				if ($serverapp==4) {//认证服务号查询是否关注
					$profile = pdo_fetch("SELECT follow FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :from_user", array(':uniacid' => $uniacid,':from_user' => $from_user));
					
					if($_W['fans']['follow'] || $profile['follow']){//已关注直接获取信息
					  
						$access_token = WeAccount::token();
						
						$oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";				
						$content = ihttp_get($oauth2_url);
						$info = @json_decode($content['content'], true);
						if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
							echo '<h1>分享获取微信公众号授权失败[无法取得info], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
							exit;
						}else{
							$avatar = $info['headimgurl'];
							$nickname = $info['nickname'];
							$sex = $info['sex'];
							//设置cookie信息
							setcookie("user_oauth2_avatar", $avatar, time()+3600*24*7);
							setcookie("user_oauth2_nickname", $nickname, time()+3600*24*7);
							setcookie("user_oauth2_sex", $sex, time()+3600*24*7);
							setcookie("user_oauth2_openid", $from_user, time()+3600*24*7);
							
							if(!empty($fromuser) && !isset($_COOKIE["user_fromuser_openid"])){
								setcookie("user_fromuser_openid", $fromuser, time()+3600*24*7*30);
							}
							$shareuserdata = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserdata', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
							header("location:$shareuserdata");
							exit;
						}		            
					}else{//非关注直接跳转授权页
						$appid = $_W['account']['key'];
						$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('oauth2', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
						$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
						header("location:$oauth2_code");
						exit;
					}	
				}else{//普通号直接跳转授权页
					if ($appid){//有借用跳转授权页没有则跳转普通注册页
						$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('oauth2', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
						$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
						header("location:$oauth2_code");
						exit;
					}else{
						$reguser = $_W['siteroot'] .'app/'.$this->createMobileUrl('reguser', array('rid' => $rid));
						header("location:$reguser");
						exit;
					}
				}			
			}else{
				//取不到openid 直接跳转授权页
				if(!empty($appid)){//有借用跳转授权页没有则跳转普通
					$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('oauth2', array('rid' => $rid,'fromuser' => $fromuser,'duli' => $_GPC['duli'],'tfrom_user' => $tfrom_user));
					$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
					header("location:$oauth2_code");
					exit;
				}else{
					$reguser = $_W['siteroot'] .'app/'.$this->createMobileUrl('reguser', array('rid' => $rid));
					header("location:$reguser");
					exit;
				}
			}
		}
	}
       
		
		
		
	