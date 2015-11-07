<?php
/*
 * 
 *
 *
 * 
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_nsignModule extends WeModule {

	public function fieldsFormDisplay($rid = 0) {

		global $_W;
		load()->func('tpl');
		if (!empty($rid)) {
		
			$reply = pdo_fetch("SELECT * FROM ".tablename('nsign_reply')." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));				
 		} 
		
		include $this->template('form');
		
	}

	public function fieldsFormValidate($rid = 0) {

		return '';
		
	}

	public function fieldsFormSubmit($rid) {
		global $_GPC, $_W;
		load()->func('file');
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
            'title' => $_GPC['title'],
			'picture' => $_GPC['picture'],
			'description' => $_GPC['description'],
			'content' => htmlspecialchars_decode($_GPC['content']),
		);
		
		if (empty($id)) {
			pdo_insert('nsign_reply', $insert);
		} 
		else {
			if (!empty($_GPC['picture'])) {
				file_delete($_GPC['picture-old']);
			} 
			else {
				unset($insert['picture']);
			}
			pdo_update('nsign_reply', $insert, array('id' => $id));
		}
	}

	public function ruleDeleted($rid) {
		global $_W;
		$replies = pdo_fetchall("SELECT id, picture FROM ".tablename('nsign_reply')." WHERE rid = '$rid'");
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['picture']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete('nsign_reply', "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
	
	
	public function settingsDisplay($settings) {
		global $_GPC, $_W;
		load()->func('tpl');
		if(checksubmit()) {
			$cfg = array(
				'times' => intval($_GPC['times']),
				'credit' => intval($_GPC['credit']),
				'showrank' => intval($_GPC['showrank']),
				'tsign' => intval($_GPC['tsign']),
				'csign' => intval($_GPC['csign']),
				
				'osign' => intval($_GPC['osign']),
				
				'tsignprize' => $_GPC['tsignprize'],
				
				'csignprize' => $_GPC['csignprize'],
				
				'osignprize' => $_GPC['osignprize'],
				
				'start_day' => $_GPC['start_day']['start'],

				'end_day' => $_GPC['start_day']['end'],

				'start_time' => $_GPC['start_time'],

				'end_time' => $_GPC['end_time'],

			);
			
			$start_day = $cfg['start_day'];

			$start_day = strtotime($start_day);

			$end_day = $cfg['end_day'];
			
			$date_day = array('start'=>time(),'end'=>$end_day);

			$end_day = strtotime($end_day);

			$start_time = $cfg['start_time'];

			$start_time = strtotime($start_time);

			$end_time = $cfg['end_time'];

			$end_time = strtotime($end_time);
			
			if($start_day >= $end_day){

				message('开始日期不得晚于结束日期', 'refresh', 'error');

			}

			if($start_time >= $end_time){

				message('开始时间不得晚于结束时间', 'refresh', 'error');

			}

			elseif($this->saveSettings($cfg)) {

				message('保存成功', 'refresh');

			}
			
		}
		
		if(!isset($settings['times'])) {

			$settings['times'] = '1';

		}
		
		if(!isset($settings['credit'])) {

			$settings['credit'] = '2';

		}
		
		if(!isset($settings['showrank'])) {

			$settings['showrank'] = '10';

		}
		
		if(!isset($settings['tsign'])) {

			$settings['tsign'] = '0';

		}
		
		if(!isset($settings['csign'])) {

			$settings['csign'] = '0';

		}
		
		if(!isset($settings['osign'])) {

			$settings['osign'] = '0';

		}

		if(!isset($settings['start_day'])) {

			$settings['start_day'] = date('Y-m-d H:i', time());

		}
		
		if(!isset($settings['end_day'])) {

			$settings['end_day'] = date('Y-m-d H:i', time()+2592000);

		}
		
		if(!isset($settings['start_time'])) {

			$settings['start_time'] = '06:00';

		}
		
		if(!isset($settings['end_time'])) {

			$settings['end_time'] = '22:00';

		}

		include $this->template('setting');

	}

}