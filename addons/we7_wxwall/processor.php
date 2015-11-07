<?php
/**
 * 图文回复处理类
 *
 * [WeEngine System] Copyright (c) 2013 qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class we7_wxwallModuleProcessor extends WeModuleProcessor {

	public $name = 'WxwallChatRobotModuleProcessor';

	public function respond() {
		if ($this->inContext) {
			return $this->respText($this->post());
		} else {
			return $this->respText($this->register());
		}
	}

	private function register() {
		global $_W;
		if (empty($_W['openid'])) {
			return '请先关注公众号再来参加活动吧！';
		}

		$sql = 'SELECT * FROM ' . tablename('wxwall_reply') . ' WHERE `rid` = :rid';
		$wall = pdo_fetch($sql, array(':rid' => $this->rule));

		if (empty($wall)) {
			return '微信墙活动不存在或已经被删除！';
		}

		$this->beginContext();
		return empty($wall['enter_tips']) ? '欢迎进入微信墙，请发表话题哦！' : $wall['enter_tips'];
	}

	private function post() {
		global $_W;
		if (!in_array($this->message['msgtype'], array('text', 'image'))) {
			return '微信墙只能发表文字和图片';
		}

		$member = $this->getMember();
		$sql = 'SELECT * FROM ' . tablename('wxwall_reply') . ' WHERE `rid` = :rid';
		$wall = pdo_fetch($sql, array(':rid' => $this->rule));

		if ( intval($wall['timeout']) > 0 &&  ($wall['timeout'] + $member['lastupdate']) < TIMESTAMP ) {
			$this->endContext();
			return '由于您长时间未操作，请重新进入微信墙！';
		}

		$this->refreshContext();

		if ( (empty($wall['quit_command']) && $this->message['content'] == '退出') ||
			(!empty($wall['quit_command']) && $this->message['content'] == $wall['quit_command'])) {
			$this->endContext();
			return empty($wall['quit_tips']) ? '您已成功退出微信墙' : $wall['quit_tips'];
		}

		$data = array(
			'rid' => $this->rule,
			'from_user' => $_W['openid'],
			'type' => $this->message['type'],
			'createtime' => TIMESTAMP,
		);

		if (empty($wall['isshow']) && empty($member['isblacklist'])) {
			$data['isshow'] = 1;
		}

		// 文字类型信息
		if ($this->message['type'] == 'text') {
			$data['content'] = $this->message['content'];
		}

		// 图片类型信息
		if ($this->message['type'] == 'image') {
			load()->func('file');
			load()->func('communication');
			$image = ihttp_request($this->message['picurl']);
			$filename = 'images/' . $_W['uniacid'] . '/' . date('Y/m/') . md5(TIMESTAMP + CLIENT_IP + random(12)) . '.jpg';
			file_write($filename, $image['content']);
			$data['content'] = $filename;
		}

		pdo_insert('wxwall_message', $data);

		if (!empty($member['isblacklist'])) {
			$content = '你已被列入黑名单，发送的消息需要管理员审核！';
		} elseif (!empty($wall['isshow'])) {
			$content = '发送消息成功，请等待管理员审核';
		} elseif(!empty($wall['send_tips'])) {
			$content = $wall['send_tips'];
		} else {
			$content = '发送消息成功。';
		}

		return $content;
	}

	private function getMember() {
		global $_W;

		$sql = 'SELECT `lastupdate`, `isblacklist`, `rid` FROM ' . tablename('wxwall_members') . ' WHERE `from_user` =
				:from_user AND `rid` = :rid';
		$params = array(':from_user' => $_W['openid'], ':rid' => $this->rule);
		$member = pdo_fetch($sql, $params);

		// 获取粉丝头像
		$account = WeAccount::create($_W['acid']);
		$fansInfo = $account->fansQueryInfo($_W['openid']);

		if (empty($member)) {
			$member = array(
				'from_user' => $_W['openid'],
				'rid' => $this->rule,
				'isjoin' => 1,
				'lastupdate' => TIMESTAMP,
				'isblacklist' => 0,
			);
			if (!is_error($fansInfo)) {
				$member['avatar'] = rtrim($fansInfo['headimgurl'], '0') . '132';
			}
			pdo_insert('wxwall_members', $member);
		} else {
			if (!is_error($fansInfo)) {
				$member['avatar'] = rtrim($fansInfo['headimgurl'], '0') . '132';
			}
			$member['lastupdate'] = TIMESTAMP;
			$params = array('from_user' => $_W['openid'],'rid' => $this->rule);
			pdo_update('wxwall_members', $member, $params);
		}

		return $member;
	}

	public function hookBefore() {
		global $_W, $engine;
	}
}
