<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

		$cover_reply = pdo_fetch("SELECT * FROM ".tablename("cover_reply")." WHERE uniacid = :uniacid and module = 'fm_photosvote'", array(':uniacid' => $uniacid));
		$reply = pdo_fetchall("SELECT * FROM ".tablename($this->table_reply)." WHERE uniacid = :uniacid and status = 1 and start_time<".$time."  and end_time>".$time." ORDER BY `end_time` DESC", array(':uniacid' => $uniacid));

		foreach ($reply as $mid => $replys) {
			$reply[$mid]['num'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and rid = :rid", array(':uniacid' => $uniacid, ':rid' => $replys['rid']));
			$reply[$mid]['is'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and rid = :rid and from_user = :from_user", array(':uniacid' => $uniacid, ':rid' => $replys['rid'], ':from_user' => $from_user));
			$picture = $replys['picture'];
			if (substr($picture,0,6)=='images'){
			    $reply[$mid]['picture'] = $_W['attachurl'] . $picture;
			}else{
			    $reply[$mid]['picture'] = $_W['siteroot'] . $picture;
			}
		}

		//查询参与情况
		$usernum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user", array(':uniacid' => $uniacid, ':from_user' => $from_user));

	    $user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			//include $this->template('listentry');
		} else { 
			include $this->template('listentry');
		}		