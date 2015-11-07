<?php
	$award_id = intval($_GPC['award_id']);
	if (!empty($_GPC['award_id']))
	{
		$follow = pdo_fetch("select uid from ".tablename('mc_mapping_fans')." uniacid = ".$weid." and openid = '".$_W['openid']."'");
		$fans = mc_search($follow['uid'], array('credit1'));
		$award_info = pdo_fetch("SELECT * FROM ".tablename('weilive_list')." WHERE award_id = $award_id AND weid = '{$weid}'");
		if ($fans['credit1'] >= $award_info['credit_cost'] && $award_info['amount'] > 0)
		{
			$data = array(
				'amount' => $award_info['amount'] - 1
			);
			pdo_update('weilive_list', $data, array('weid' => $weid, 'award_id' => $award_id));

			$data = array(
				'weid' => $weid,
				'from_user' => $_W['fans']['from_user'],
				'award_id' => $award_id,
				'createtime' => TIMESTAMP
			);
			pdo_insert('weilive_request', $data);

			$data = array(
				'realname' => $_GPC['realname'],
				'mobile' => $_GPC['mobile'],
				'credit1' => $fans['credit1'] - $award_info['credit_cost'],
				'residedist' => $_GPC['residedist'],
			);
			mc_update($follow['uid'], $data);
			
			// navigate to user profile page
			message('积分兑换成功！', create_url('mobile/module/mycredit', array('weid' => $weid, 'name' => 'hcweilive', 'do' => 'mycredit','op' => 'display')), 'success');
		}
		else
		{
			message('积分不足或商品已经兑空，请重新选择商品！<br>当前商品所需积分:'.$award_info['credit_cost'].'<br>您的积分:'.$fans['credit1']
				. '. 商品剩余数量:' . $award_info['amount']
				. '<br><br>小提示：<br>每日签到，在线订票，宾馆预订可以赚取积分',

				create_url('mobile/module/award', array('weid' => $weid, 'name' => 'hcweilive')), 'error');
		}
	}
	else
	{
		message('请选择要兑换的商品！', create_url('mobile/module/award', array('weid' => $weid, 'name' => 'hcweilive')), 'error');
	}
?>