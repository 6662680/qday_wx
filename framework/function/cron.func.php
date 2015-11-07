<?php
/**
 * 计划任务
 * [Weizan System] Copyright (c) 2013 qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

function cron_run($cronid = 0) {
	if(empty($cronid)) {
		$cron = pdo_fetch('SELECT * FROM ' . tablename('cron') . ' WHERE available > 0 AND nextrun <= ' . TIMESTAMP . ' ORDER BY nextrun ASC');
	} else {
		$cron = pdo_fetch('SELECT * FROM ' . tablename('cron') . ' WHERE cronid = :id', array(':id' => intval($cronid)));
	}
	if(empty($cron)) {
		return false;
	}
	if($cron['type'] == 'system') {
		//定义系统内置的计划任务文件放在哪个路径
		$cronfile = IA_ROOT . '/cron/' . $cron['filename'];
	} elseif($cron['type'] == 'module') {
		//定义模块的计划任务文件放在哪个路径
		$cronfile = IA_ROOT . '/cron/' . $cron['filename'];
	}

	if(is_file($cronfile)) {
		$cron['minute'] = explode("\t", $cron['minute']);
		//设置下次执行时间
		cron_setnexttime($cron);
		@set_time_limit(1000);
		//@ignore_user_abort(TRUE);
		if(!@include $cronfile) {
			return false;
		}
	}
}

function cron_setnexttime($cron) {
	if(empty($cron)) return FALSE;
	list($yearnow, $monthnow, $daynow, $weekdaynow, $hournow, $minutenow) = explode('-', date('Y-m-d-w-H-i', TIMESTAMP));

	if($cron['weekday'] == -1) {
		if($cron['day'] == -1) {
			$firstday = $daynow;
			$secondday = $daynow + 1;
		} else {
			$firstday = $cron['day'];
			$secondday = $cron['day'] + date('t', TIMESTAMP);
		}
	} else {
		$firstday = $daynow + ($cron['weekday'] - $weekdaynow);
		$secondday = $firstday + 7;
	}

	if($firstday < $daynow) {
		$firstday = $secondday;
	}

	if($firstday == $daynow) {
		$todaytime = cron_todaynextrun($cron);
		if($todaytime['hour'] == -1 && $todaytime['minute'] == -1) {
			$cron['day'] = $secondday;
			$nexttime = cron_todaynextrun($cron, 0, -1);
			$cron['hour'] = $nexttime['hour'];
			$cron['minute'] = $nexttime['minute'];
		} else {
			$cron['day'] = $firstday;
			$cron['hour'] = $todaytime['hour'];
			$cron['minute'] = $todaytime['minute'];
		}
	} else {
		$cron['day'] = $firstday;
		$nexttime = cron_todaynextrun($cron, 0, -1);
		$cron['hour'] = $nexttime['hour'];
		$cron['minute'] = $nexttime['minute'];
	}
	$nextrun = mktime($cron['hour'], $cron['minute'] > 0 ? $cron['minute'] : 0, 0, $monthnow, $cron['day'], $yearnow);
	$data = array('lastrun' => TIMESTAMP, 'nextrun' => $nextrun);
	if(!($nextrun > TIMESTAMP)) {
		$data['available'] = '0';
	}
	pdo_update('cron', $data, array('cronid' => $cron['cronid']));
	return true;
}

function cron_todaynextrun($cron, $hour = -2, $minute = -2) {
	$hour = $hour == -2 ? date('H', TIMESTAMP) : $hour;
	$minute = $minute == -2 ? date('i', TIMESTAMP)  : $minute;

	$nexttime = array();
	if($cron['hour'] == -1 && !$cron['minute']) {
		$nexttime['hour'] = $hour;
		$nexttime['minute'] = $minute + 1;
	} elseif($cron['hour'] == -1 && $cron['minute'] != '') {
		$nexttime['hour'] = $hour;
		if(($nextminute = cron_nextminute($cron['minute'], $minute)) === false) {
			++$nexttime['hour'];
			$nextminute = $cron['minute'][0];
		}
		$nexttime['minute'] = $nextminute;
	} elseif($cron['hour'] != -1 && $cron['minute'] == '') {
		if($cron['hour'] < $hour) {
			$nexttime['hour'] = $nexttime['minute'] = -1;
		} elseif($cron['hour'] == $hour) {
			$nexttime['hour'] = $cron['hour'];
			$nexttime['minute'] = $minute + 1;
		} else {
			$nexttime['hour'] = $cron['hour'];
			$nexttime['minute'] = 0;
		}
	} elseif($cron['hour'] != -1 && $cron['minute'] != '') {
		$nextminute = cron_nextminute($cron['minute'], $minute);
		if($cron['hour'] < $hour || ($cron['hour'] == $hour && $nextminute === false)) {
			$nexttime['hour'] = -1;
			$nexttime['minute'] = -1;
		} else {
			$nexttime['hour'] = $cron['hour'];
			$nexttime['minute'] = $nextminute;
		}
	}
	return $nexttime;
}


function cron_nextminute($nextminutes, $minutenow) {
	foreach($nextminutes as $nextminute) {
		if($nextminute > $minutenow) {
			return $nextminute;
		}
	}
	return false;
}
