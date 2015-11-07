<?php
/**
 * 会员余额导入模块微站定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class Fwei_moneyimportModuleSite extends WeModuleSite {

	public function doWebImport() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_GPC, $_W;
		$uniacid = $_W['uniacid'];
		if (checksubmit('submit')) {
			$force = $_GPC['force'];
			$file = $_FILES['file'];
			if( $file['name'] && $file['error'] == 0){
				$type = @end( explode('.', $file['name']));
				$type = strtolower($type);
				if( !in_array($type, array('xls','xlsx')) ){
					message('文件类型错误！',  '', 'error');
				}
				//开始导入
				set_time_limit(0);
				include_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
				/** PHPExcel_IOFactory */
				include_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/IOFactory.php';
				if( $type == 'xls' ){
					$inputFileType = 'Excel5';    //这个是读 xls的
				}else{
					$inputFileType = 'Excel2007';//这个是计xlsx的
				}
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($file['tmp_name']);

				$objWorksheet = $objPHPExcel->getActiveSheet();//取得总行数
				$highestRow = $objWorksheet->getHighestRow();//取得总列数
				for ($row = 2;$row <= $highestRow;$row++){
					$mobile = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$money = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
					$mobile = trim($mobile);
					$money = floatval( $money );
					if( empty($mobile) ){
						continue;
					}
					$condition = array('uniacid'=>$uniacid, 'mobile'=>$mobile);
					if( !$force ){
						$condition['credit2'] = 0;
					}
					pdo_update('mc_members', array('credit2'=>$money), $condition);
				}
				message('数据更新成功',  $this->createWebUrl('import'));
			}
			message('文件上传失败！',  '', 'error');
		}
		include $this->template('import');
	}

}