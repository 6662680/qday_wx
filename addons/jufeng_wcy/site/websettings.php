<?php
global $_GPC, $_W;
			$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_sms')." WHERE weid = '{$_W['uniacid']}'");
				if($_GPC['op'] == "emailsend"){
					if(empty($category['email']) || empty($category['emailpsw']) || empty($category['smtp'])){
						message('请先填写飞信号、密码、SMTP服务器并提交。', $this->createWebUrl('settings'), 'error');}
						else{
							$this->sendmail('聚风微餐饮测试邮件','欢迎使用【聚风网络】的聚风微餐饮，邮件接口已经可以使用。',$category['email'],$category['smtp'],$category['email'],$category['emailpsw']);
						message('若'.$category['email'].'能收到邮件，说明接口设置成功。', $this->createWebUrl('settings'), 'success');
						}
					}
					else if($_GPC['op'] == "smssend"){
					if(empty($category['smsnum']) || empty($category['smspsw']) || empty($category['smstest'])){
						message('请先填写短信接口账号、短信接口密码、测试手机号并提交。', $this->createWebUrl('settings'), 'error');}
						else{
							$this->sendSMS($category['smsnum'],$category['smspsw'],$category['smstest'],'欢迎使用聚风微餐饮，接口设置成功。');
						message('若'.$category['smstest'].'能收到短信，说明接口设置成功。', $this->createWebUrl('settings'), 'success');
						}
					}
					else if($_GPC['op'] == "transfer" && checksubmit('token')){
						$sql = "
                        ";
pdo_query($sql);
						message('数据迁移完成。', $this->createWebUrl('settings', array('op' => 'transfer')), 'success');
						}
			else if (checksubmit('email') || checksubmit('smtp')){
				$data = array(
					'weid' => $_W['uniacid'],
					'email' => $_GPC['email'],	
					'emailpsw' => $_GPC['emailpsw'],
					'smtp' => $_GPC['smtp'],
					'smsnum' => $_GPC['smsnum'],
					'smspsw' => $_GPC['smspsw'],
					'smstest' => $_GPC['smstest'],				
				);
				if(!empty($category)){
				pdo_update('jufeng_wcy_sms',$data, array('weid' => $_W['uniacid']));
				}
				else{
					pdo_insert('jufeng_wcy_sms', $data);
					}
			message('更新设置成功', $this->createWebUrl('settings'), 'success');
				}
			include $this->template('settings');
					?>