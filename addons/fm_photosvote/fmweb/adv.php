<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE uniacid = '{$uniacid}' AND rid = '{$rid}' ORDER BY displayorder DESC");
			//include $this->template('adv');
        } elseif ($operation == 'post') {
			
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
				//exit;
                $data = array(
                    'uniacid' => $uniacid,
                    'rid' => $rid,
                    'advname' => $_GPC['advname'],
                    'description' => htmlspecialchars_decode($_GPC['description']), 
                    'link' => $_GPC['link'],
                    'thumb' => $_GPC['thumb'],
                    'enabled' => intval($_GPC['enabled']),
                    'ismiaoxian' => intval($_GPC['ismiaoxian']),
                    'issuiji' => intval($_GPC['issuiji']),
                    'nexttime' => intval($_GPC['nexttime']),
                    'times' => intval($_GPC['times']),
                    'displayorder' => intval($_GPC['displayorder'])
                );
                if (!empty($id)) {
                    pdo_update($this->table_advs, $data, array('id' => $id));
					load()->func('file');
					file_delete($_GPC['thumb_old']);
                } else {
                    pdo_insert($this->table_advs, $data);
                    $id = pdo_insertid();
                }
                message('更新广告成功！', $this->createWebUrl('adv', array('op' => 'display', 'rid' => $rid)), 'success');
            }
            $adv = pdo_fetch("select * from " . tablename($this->table_advs) . " where id=:id and uniacid=:uniacid and rid=:rid limit 1", array(":id" => $id, ":uniacid" => $uniacid, ':rid' => $rid));
			//include $this->template('adv_post');
	   } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $adv = pdo_fetch("SELECT id  FROM " . tablename($this->table_advs) . " WHERE id = '$id' AND uniacid=" . $uniacid . " AND rid=" . $rid . "");
            if (empty($adv)) {
                message('抱歉，广告不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display', 'rid' => $rid)), 'error');
            }
            pdo_delete($this->table_advs, array('id' => $id));
            message('广告删除成功！', $this->createWebUrl('adv', array('op' => 'display', 'rid' => $rid)), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('adv');
   