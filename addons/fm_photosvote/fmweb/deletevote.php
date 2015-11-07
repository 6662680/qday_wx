<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$reply = pdo_fetch("select * from ".tablename($this->table_reply)." where rid = :rid and uniacid=:uniacid", array(':rid' => $rid, ':uniacid' => $uniacid));
        if (empty($reply)) {
            $this->webmessage('抱歉，要修改的活动不存在或是已经被删除！');
        }
		
        foreach ($_GPC['idArr'] as $k => $id) {
			
			
            $id = intval($id);
			
			
            if ($id == 0)
                continue;
			 
			$fans = pdo_fetch("select * from ".tablename($this->table_log)." where id = :id", array(':id' => $id));
            $tfans = pdo_fetch('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid AND rid =:rid AND from_user =:from_user ', array(':uniacid' => $uniacid, ':rid' => $rid, ':from_user' => $fans['tfrom_user']) );
			
			if (empty($fans)) {
                $this->webmessage('抱歉，选中的投票数据不存在！');
            }
			
			//删除粉丝参与记录
			pdo_delete($this->table_log, array('id' => $id));
			//更新粉丝数据
			pdo_update($this->table_users, array(
						'photosnum' => $tfans['photosnum'] - 1,
						'hits' => $tfans['hits'] - 1,
						),
						array('from_user' => $fans['tfrom_user'], 'rid' => $rid)
					);
			
        }
        $this->webmessage('投票记录删除成功！', '', 0);
    