<?php
//乞讨活动后台管理
global $_W,$_GPC;

$this->__init();

if($_W['ispost']){
	if(!empty($_GPC['delete'])){
		$select = $_GPC['select'];
		foreach ($select as $key) {
			pdo_delete('meepo_begging',array('id'=>$key));
		}
		message('删除数据成功',referer(),success);
	}

	if(!empty($_GPC['upload'])){
		$select = $_GPC['select'];
		$in = db_create_in($select,'b.id');
		$sql = "SELECT b.*,m.avatar,m.nickname FROM ".tablename('meepo_begging')." as b LEFT JOIN ".tablename('mc_members')
				." AS m ON b.uid = m.uid WHERE ".$in;
		$params = array(':uniacid'=>$_W['uniacid']);
		$list = pdo_fetchall($sql,$params);

		
		//导出
		include_once ('../framework/library/phpexcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		$objDrawing = new PHPExcel_Worksheet_Drawing();

		$objPHPExcel->getProperties()->setCreator("Meepo");
		$objPHPExcel->getProperties()->setLastModifiedBy("Meepo");
		$objPHPExcel->getProperties()->setTitle("Meepo");

		$objPHPExcel->getActiveSheet()->setCellValue('A1', '昵称');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '头像');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '已讨金钱');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '排名');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '参与时间');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

		foreach ($list as $key => $value) {
			$sql = "SELECT COUNT(*) FROM ".tablename('meepo_begging')." WHERE uniacid = :uniacid AND money > :money ";
			$params = array(':uniacid'=>$_W['uniacid'],':money'=>floatval($value['money']));
			$value['num'] = pdo_fetchcolumn($sql,$params);
			$value['num'] = $value['num']+1;
			$value['createtime'] = date('Y-m-d',$value['createtime']);
			$value['avatar'] = tomedia($value['avatar']);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.($key+2), $value['nickname']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($key+2), $value['avatar']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($key+2), $value['money'].'元');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($key+2), '第'.$value['num'].'名');
			$objPHPExcel->getActiveSheet()->getStyle('D'.($key+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($key+2), $value['createtime']);
		}

		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="resume.xls"');
		header("Content-Transfer-Encoding:binary");
		$objWriter->save('php://output');

		exit();
	}

	if(!empty($_GPC['uploadall'])){
		$in = db_create_in($select,'b.id');
		$sql = "SELECT b.*,m.avatar,m.nickname FROM ".tablename('meepo_begging')." as b LEFT JOIN ".tablename('mc_members')
				." AS m ON b.uid = m.uid ";
		$params = array(':uniacid'=>$_W['uniacid']);
		$list = pdo_fetchall($sql,$params);

		
		//导出
		include_once ('../framework/library/phpexcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		$objDrawing = new PHPExcel_Worksheet_Drawing();

		$objPHPExcel->getProperties()->setCreator("Meepo");
		$objPHPExcel->getProperties()->setLastModifiedBy("Meepo");
		$objPHPExcel->getProperties()->setTitle("Meepo");

		$objPHPExcel->getActiveSheet()->setCellValue('A1', '昵称');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '头像');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '已讨金钱');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '排名');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '参与时间');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

		foreach ($list as $key => $value) {
			$sql = "SELECT COUNT(*) FROM ".tablename('meepo_begging')." WHERE uniacid = :uniacid AND money > :money ";
			$params = array(':uniacid'=>$_W['uniacid'],':money'=>floatval($value['money']));
			$value['num'] = pdo_fetchcolumn($sql,$params);
			$value['num'] = $value['num']+1;
			$value['createtime'] = date('Y-m-d',$value['createtime']);
			$value['avatar'] = tomedia($value['avatar']);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.($key+2), $value['nickname']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($key+2), $value['avatar']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($key+2), $value['money'].'元');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($key+2), '第'.$value['num'].'名');
			$objPHPExcel->getActiveSheet()->getStyle('D'.($key+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($key+2), $value['createtime']);
		}

		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="resume.xls"');
		header("Content-Transfer-Encoding:binary");
		$objWriter->save('php://output');

		exit();
	}
}

$pindex = max(1, intval($_GPC['page']));
$psize = 20;
if (!empty($_GPC['keyword'])) {
	$condition .= " AND m.nickname LIKE '%{$_GPC['keyword']}%'";
}
$sql = "SELECT b.*,m.avatar,m.nickname FROM ".tablename('meepo_begging')." as b LEFT JOIN ".tablename('mc_members')." as m ON b.uid = m.uid "
." WHERE b.uniacid = :uniacid {$condition} ORDER BY money + cash DESC ". "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
$params = array(':uniacid'=>$_W['uniacid']);
$lists = pdo_fetchall($sql,$params);

foreach ($lists as $li) {
	$li['money'] = $li['money'] + $li['cash'];
	$li['createtime'] = date('Y-m-d',$li['createtime']);
	$li['avatar'] = tomedia($li['avatar']);
	$li['look'] = $this->createWebUrl('look',array('uid'=>$li['uid']));
	$li['add'] = $this->createWebUrl('add',array('uid'=>$li['uid']));
	$list[] = $li;
}


$params = array(':uniacid'=>$_W['uniacid']);
$total = pdo_fetchcolumn(
	'SELECT COUNT(*) FROM ' . tablename('meepo_begging') . " as b "
	." left join ".tablename('mc_members')." as m on b.uid = m.uid "
	." WHERE b.uniacid = :uniacid {$condition} ", $params);
$pager = pagination($total, $pindex, $psize);

include $this->template('manage');

function db_create_in($item_list, $field_name = '') {
    if (empty($item_list)) {
        return $field_name . " IN ('') ";
    } else {
        if (!is_array($item_list)) {
            $item_list = explode(',', $item_list);
        }
        $item_list = array_unique($item_list);
        $item_list_tmp = '';
        foreach ($item_list AS $item) {
            if ($item !== '') {
                $item_list_tmp.= $item_list_tmp ? ",'$item'" : "'$item'";
            }
        }
        if (empty($item_list_tmp)) {
            return $field_name . " IN ('') ";
        } else {
            return $field_name . ' IN (' . $item_list_tmp . ') ';
        }
    }
}