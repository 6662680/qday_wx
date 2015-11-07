<?php
	$storeid = $_GPC['storeid'];
	
	if($op=='display'){
		//$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $_W['weid']));
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$list = pdo_fetchall("select * from ".tablename('weilive_comment')." where weid = ".$_W['weid']." and storeid = ".$storeid. " order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_comment')." where weid = ".$_W['weid']." and storeid = ".$storeid);
		$pager = pagination1($total, $pindex, $psize);
	}
	if($op=='sort'){
		$op = 'display';
		$sort = array(
			'mobile'=>$_GPC['mobile']
		);
		$list = pdo_fetchall("select * from ".tablename('weilive_comment')." where weid = ".$_W['weid']." and storeid = ".$storeid. " and mobile like '%".$sort['mobile']."%' order by createtime desc");
	}
	if($op=='post'){
		$id = $_GPC['id'];
		if(intval($id)){
			$item = pdo_fetch("select * from ".tablename('weilive_comment')." where id = ".$id);
		}
		if(checksubmit('submit')){
			$comment = array(
				'weid'=>$_W['weid'],
				'storeid'=>$storeid,
				'mobile'=>$_GPC['mobile'],
				'comment'=>trim($_GPC['comment']),
				'createtime'=>strtotime($_GPC['createtime']),
				'isopen'=>$_GPC['isopen']
			);
			
			if(intval($id)){
				$temp = pdo_update('weilive_comment', $comment, array('id'=>$id));
				if($temp){
					message('提交成功！', $this->createWebUrl('comment', array('op'=>'display', 'storeid'=>$storeid)), 'success');
				} else {
					message('提交失败！', $this->createWebUrl('comment', array('op'=>'post', 'storeid'=>$storeid, 'id'=>$id)), 'error');
				}
			} else {
				$temp = pdo_insert('weilive_comment', $comment);
				if($temp){
					message('提交成功！', $this->createWebUrl('comment', array('op'=>'display', 'storeid'=>$storeid)), 'success');
				} else {
					message('提交失败！', $this->createWebUrl('comment', array('op'=>'post', 'storeid'=>$storeid, 'id'=>$id)), 'error');
				}
			}
		}
	}
	
	if($op=='delete'){
		$temp = pdo_delete('weilive_comment', array('id'=>$_GPC['id']));
		if($temp){
			message('删除成功！', $this->createWebUrl('comment', array('op'=>'display', 'storeid'=>$storeid)), 'success');
		} else {
			message('删除失败！', $this->createWebUrl('comment', array('op'=>'post', 'storeid'=>$storeid, 'id'=>$id)), 'error');
		}
	}
	
	include $this->template('web/comment_list');
?>