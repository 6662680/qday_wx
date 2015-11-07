<?php

defined('IN_IA') or exit('Access Denied');

session_start();

class Eso_SaleModuleSite extends WeModuleSite {

	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$uniacid=$_W['uniacid'];
		$op= $operation = $_GPC['op']?$_GPC['op']:'display';

		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}

	public function __mobile($f_name){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';

		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';
	}


	public function doWebCategory() {
		global $_W,$_GPC;

		checklogin();
		load()->func('tpl');
		$uniacid=$_W['uniacid'];
		$op= $operation = $_GPC['op']?$_GPC['op']:'display';

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('eso_sale_category', array('displayorder' => $displayorder), array('id' => $id));
				}
				message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
			foreach ($category as $index => $row) {
				if (!empty($row['parentid'])) {
					$children[$row['parentid']][] = $row;
					unset($category[$index]);
				}
			}
			include $this->template('category');
		} elseif ($operation == 'post') {
			$parentid = intval($_GPC['parentid']);
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$category = pdo_fetch("SELECT * FROM " . tablename('eso_sale_category') . " WHERE id = '$id'");
			} else {
				$category = array(
					'displayorder' => 0,
				);
			}
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT id, name FROM " . tablename('eso_sale_category') . " WHERE id = '$parentid'");
				if (empty($parent)) {
					message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['catename'])) {
					message('抱歉，请输入分类名称！');
				}
				$data = array(
					'uniacid' => $_W['uniacid'],
					'name' => $_GPC['catename'],
					'enabled' => intval($_GPC['enabled']),
					'displayorder' => intval($_GPC['displayorder']),
					'isrecommand' => intval($_GPC['isrecommand']),
					//    'commission' => intval($_GPC['commission']),
					'description' => $_GPC['description'],
					'parentid' => intval($parentid),
				);
				if (!empty($_FILES['thumb']['tmp_name'])) {
					file_delete($_GPC['thumb_old']);
					$upload = file_upload($_FILES['thumb']);
					if (is_error($upload)) {
						message($upload['message'], '', 'error');
					}
					$data['thumb'] = $upload['path'];
				}

				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('eso_sale_category', $data, array('id' => $id));
				} else {
					pdo_insert('eso_sale_category', $data);
					$id = pdo_insertid();
				}
				message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
			include $this->template('category');
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT id, parentid FROM " . tablename('eso_sale_category') . " WHERE id = '$id'");
			if (empty($category)) {
				message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
			}
			pdo_delete('eso_sale_category', array('id' => $id, 'parentid' => $id), 'OR');
			message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
		}


	}

	public function doWebSetGoodsProperty() {

		global $_GPC, $_W;

		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		$data = intval($_GPC['data']);
		empty($data) ? ($data = 1) : $data = 0;
		if (!in_array($type, array('new', 'hot', 'recommand', 'discount', 'status'))) {
			die(json_encode(array("result" => 0)));
		}
		if($_GPC['type']=='status'){
			pdo_update("eso_sale_goods", array($type => $data), array("id" => $id, "uniacid" => $_W['uniacid']));
		} else {
			pdo_update("eso_sale_goods", array("is" . $type => $data), array("id" => $id, "uniacid" => $_W['uniacid']));
		}
		die(json_encode(array("result" => 1, "data" => $data)));
	}

	public function doWebGoods() {
		global $_GPC, $_W;
		load()->func('tpl');

		$category = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		if (!empty($category)) {
			$children = '';
			foreach ($category as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
				}
			}
		}

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'post') {


			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，商品不存在或是已经删除！', '', 'error');
				}
				$allspecs = pdo_fetchall("select * from " . tablename('eso_sale_spec')." where goodsid=:id order by displayorder asc",array(":id"=>$id));
				foreach ($allspecs as &$s) {
					$s['items'] = pdo_fetchall("select * from " . tablename('eso_sale_spec_item') . " where specid=:specid order by displayorder asc", array(":specid" => $s['id']));
				}
				unset($s);

				$params = pdo_fetchall("select * from " . tablename('eso_sale_goods_param') . " where goodsid=:id order by displayorder asc", array(':id' => $id));
				$piclist = unserialize($item['thumb_url']);
				//处理规格项
				$html = "";
				$options = pdo_fetchall("select * from " . tablename('eso_sale_goods_option') . " where goodsid=:id order by id asc", array(':id' => $id));
				$piclist1 = unserialize($item['thumb_url']);
				$piclist = array();
				if(is_array($piclist1)){
					foreach($piclist1 as $p){
						$piclist[]  = is_array($p)?$p['attachment']:$p;
					}
				}

				//排序好的specs
				$specs = array();
				//找出数据库存储的排列顺序
				if (count($options) > 0) {
					$specitemids = explode("_", $options[0]['specs'] );
					foreach($specitemids as $itemid){
						foreach($allspecs as $ss){
							$items=  $ss['items'];
							foreach($items as $it){
								if($it['id']==$itemid){
									$specs[] = $ss;
									break;
								}
							}
						}
					}

					$html = '<table  class="tb spectable" style="border:1px solid #ccc;"><thead><tr>';

					$len = count($specs);
					$newlen = 1; //多少种组合
					$h = array(); //显示表格二维数组
					$rowspans = array(); //每个列的rowspan


					for ($i = 0; $i < $len; $i++) {
						//表头
						$html.="<th>" . $specs[$i]['title'] . "</th>";

						//计算多种组合
						$itemlen = count($specs[$i]['items']);
						if ($itemlen <= 0) {
							$itemlen = 1;
						}
						$newlen*=$itemlen;

						//初始化 二维数组
						$h = array();
						for ($j = 0; $j < $newlen; $j++) {
							$h[$i][$j] = array();
						}
						//计算rowspan
						$l = count($specs[$i]['items']);
						$rowspans[$i] = 1;
						for ($j = $i + 1; $j < $len; $j++) {
							$rowspans[$i]*= count($specs[$j]['items']);
						}
					}
					//   print_r($rowspans);exit();

					$html .= '<th><div class="input-append input-prepend"><span class="add-on">库存</span><input type="text" class="span1 option_stock_all"  VALUE=""/><span class="add-on"><a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></th>';
					$html.= '<th><div class="input-append input-prepend"><span class="add-on">销售价格</span><input type="text" class="span1 option_marketprice_all"  VALUE=""/><span class="add-on"><a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div><br/></th>';
					$html.='<th><div class="input-append input-prepend"><span class="add-on">市场价格</span><input type="text" class="span1 option_productprice_all"  VALUE=""/><span class="add-on"><a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></th>';
					$html.='<th><div class="input-append input-prepend"><span class="add-on">成本价格</span><input type="text" class="span1 option_costprice_all"  VALUE=""/><span class="add-on"><a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></th>';
					$html.='<th><div class="input-append input-prepend"><span class="add-on">重量(克)</span><input type="text" class="span1 option_weight_all"  VALUE=""/><span class="add-on"><a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></th>';
					$html.='</tr>';
					for($m=0;$m<$len;$m++){
						$k = 0;$kid = 0;$n=0;
						for($j=0;$j<$newlen;$j++){
							$rowspan = $rowspans[$m]; //9
							if( $j % $rowspan==0){
								$h[$m][$j]=array("html"=> "<td rowspan='".$rowspan."'>".$specs[$m]['items'][$kid]['title']."</td>","id"=>$specs[$m]['items'][$kid]['id']);
								// $k++; if($k>count($specs[$m]['items'])-1) { $k=0; }
							}
							else{
								$h[$m][$j]=array("html"=> "","id"=>$specs[$m]['items'][$kid]['id']);
							}
							$n++;
							if($n==$rowspan){
								$kid++; if($kid>count($specs[$m]['items'])-1) { $kid=0; }
								$n=0;
							}
						}
					}

					$hh = "";
					for ($i = 0; $i < $newlen; $i++) {
						$hh.="<tr>";
						$ids = array();
						for ($j = 0; $j < $len; $j++) {
							$hh.=$h[$j][$i]['html'];
							$ids[] = $h[$j][$i]['id'];
						}
						$ids = implode("_", $ids);

						$val = array("id" => "","title"=>"", "stock" => "", "costprice" => "", "productprice" => "", "marketprice" => "", "weight" => "");
						foreach ($options as $o) {
							if ($ids === $o['specs']) {
								$val = array("id" => $o['id'],
									"title"=>$o['title'],
									"stock" => $o['stock'],
									"costprice" => $o['costprice'],
									"productprice" => $o['productprice'],
									"marketprice" => $o['marketprice'],
									"weight" => $o['weight']);
								break;
							}
						}

						$hh .= '<td>';
						$hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="span1 option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/></td>';
						$hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="span1 option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
						$hh .= '<input name="option_ids[]"  type="hidden" class="span1 option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
						$hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="span1 option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
						$hh .= '</td>';
						$hh .= '<td><input name="option_marketprice_' . $ids . '[]" type="text" class="span1 option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
						$hh .= '<td><input name="option_productprice_' . $ids . '[]" type="text" class="span1 option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
						$hh .= '<td><input name="option_costprice_' . $ids . '[]" type="text" class="span1 option_costprice option_costprice_' . $ids . '" " value="' . $val['costprice'] . '"/></td>';
						$hh .= '<td><input name="option_weight_' . $ids . '[]" type="text" class="span1 option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
						$hh .="</tr>";
					}
					$html.=$hh;
					$html.="</table>";
				}
			}
			if (empty($category)) {
				message('抱歉，请您先添加商品分类！', $this->createWebUrl('category', array('op' => 'post')), 'error');
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['goodsname'])) {
					message('请输入商品名称！');
				}
				if (empty($_GPC['pcate'])) {
					message('请选择商品分类！');
				}
				if(empty($_GPC['thumbs'])){
					$_GPC['thumbs'] = array();
				}
				$data = array(
					'uniacid' => intval($_W['uniacid']),
					'displayorder' => intval($_GPC['displayorder']),
					'title' => $_GPC['goodsname'],
					'pcate' => intval($_GPC['pcate']),
					'ccate' => intval($_GPC['ccate']),
					'type' => intval($_GPC['type']),
					'isrecommand' => intval($_GPC['isrecommand']),
					'ishot' => intval($_GPC['ishot']),
					'isnew' => intval($_GPC['isnew']),
					'isdiscount' => intval($_GPC['isdiscount']),
					'istime' => intval($_GPC['istime']),
					'timestart' => strtotime($_GPC['timestart']),
					'timeend' => strtotime($_GPC['timeend']),
					'description' => $_GPC['description'],
					'content' => htmlspecialchars_decode($_GPC['content']),
					'goodssn' => $_GPC['goodssn'],
					'unit' => $_GPC['unit'],
					'createtime' => TIMESTAMP,
					'total' => intval($_GPC['total']),
					'totalcnf' => intval($_GPC['totalcnf']),
					'marketprice' => $_GPC['marketprice'],
					'weight' => $_GPC['weight'],
					'costprice' => $_GPC['costprice'],
					'productprice' => $_GPC['productprice'],
					'productsn' => $_GPC['productsn'],
					'credit' => intval($_GPC['credit']),
					'maxbuy' => intval($_GPC['maxbuy']),
					'commission' => intval($_GPC['commission']),
					'commission2' => intval($_GPC['commission2']),
					'commission3' => intval($_GPC['commission3']),
					'hasoption' => intval($_GPC['hasoption']),
					'sales' => intval($_GPC['sales']),
					'status' => intval($_GPC['status']),
					'thumb' => $_GPC['thumb'],
					'xsthumb' => $_GPC['xsthumb'],
				);
				if(is_array($_GPC['thumbs'])){
					$data['thumb_url'] = serialize($_GPC['thumbs']);
				}

				if (empty($id)) {
					pdo_insert('eso_sale_goods', $data);
					$id = pdo_insertid();
				} else {
					unset($data['createtime']);
					pdo_update('eso_sale_goods', $data, array('id' => $id));
				}


				$totalstocks = 0;

				//处理自定义参数    

				$param_ids = $_POST['param_id'];
				$param_titles = $_POST['param_title'];
				$param_values = $_POST['param_value'];
				$param_displayorders = $_POST['param_displayorder'];
				$len = count($param_ids);
				$paramids = array();
				for ($k = 0; $k < $len; $k++) {
					$param_id = "";
					$get_param_id = $param_ids[$k];
					$a = array(
						"title" => $param_titles[$k],
						"value" => $param_values[$k],
						"displayorder" => $k,
						"goodsid" => $id,
					);
					if (!is_numeric($get_param_id)) {
						pdo_insert("eso_sale_goods_param", $a);
						$param_id = pdo_insertid();
					} else {
						pdo_update("eso_sale_goods_param", $a, array('id' => $get_param_id));
						$param_id = $get_param_id;
					}
					$paramids[] = $param_id;
				}
				if (count($paramids) > 0) {
					pdo_query("delete from " . tablename('eso_sale_goods_param') . " where goodsid=$id and id not in ( " . implode(',', $paramids) . ")");
				}
				else{
					pdo_query("delete from " . tablename('eso_sale_goods_param') . " where goodsid=$id");
				}
//                if ($totalstocks > 0) {
//                    pdo_update("eso_sale_goods", array("total" => $totalstocks), array("id" => $id));
//                }
				//处理商品规格
				$files = $_FILES;
				$spec_ids = $_POST['spec_id'];
				$spec_titles = $_POST['spec_title'];

				$specids = array();
				$len = count($spec_ids);
				$specids = array();
				$spec_items = array();
				for ($k = 0; $k < $len; $k++) {
					$spec_id = "";
					$get_spec_id = $spec_ids[$k];
					$a = array(
						"uniacid" => $_W['uniacid'],
						"goodsid" => $id,
						"displayorder" => $k,
						"title" => $spec_titles[$get_spec_id]
					);
					if (is_numeric($get_spec_id)) {

						pdo_update("eso_sale_spec", $a, array("id" => $get_spec_id));
						$spec_id = $get_spec_id;
					} else {
						pdo_insert("eso_sale_spec", $a);
						$spec_id = pdo_insertid();
					}
					//子项
					$spec_item_ids = $_POST["spec_item_id_".$get_spec_id];
					$spec_item_titles = $_POST["spec_item_title_".$get_spec_id];
					$spec_item_shows = $_POST["spec_item_show_".$get_spec_id];

					$spec_item_oldthumbs = $_POST["spec_item_oldthumb_".$get_spec_id];
					$itemlen = count($spec_item_ids);
					$itemids = array();


					for ($n = 0; $n < $itemlen; $n++) {


						$item_id = "";
						$get_item_id = $spec_item_ids[$n];
						$d = array(
							"uniacid" => $_W['uniacid'],
							"specid" => $spec_id,
							"displayorder" => $n,
							"title" => $spec_item_titles[$n],
							"show" => $spec_item_shows[$n]
						);
						$f = "spec_item_thumb_" . $get_item_id;
						$old = $spec_item_oldthumbs[$k];
						if (!empty($files[$f]['tmp_name'])) {
							$upload = file_upload($files[$f]);
							if (is_error($upload)) {
								message($upload['message'], '', 'error');
							}
							$d['thumb'] = $upload['path'];
						} else if (!empty($old)) {
							$d['thumb'] = $old;
						}

						if (is_numeric($get_item_id)) {
							pdo_update("eso_sale_spec_item", $d, array("id" => $get_item_id));
							$item_id = $get_item_id;
						} else {
							pdo_insert("eso_sale_spec_item", $d);
							$item_id = pdo_insertid();
						}
						$itemids[] = $item_id;

						//临时记录，用于保存规格项
						$d['get_id'] = $get_item_id;
						$d['id']= $item_id;
						$spec_items[] = $d;
					}
					//删除其他的
					if(count($itemids)>0){
						pdo_query("delete from " . tablename('eso_sale_spec_item') . " where uniacid={$_W['uniacid']} and specid=$spec_id and id not in (" . implode(",", $itemids) . ")");
					}
					else{
						pdo_query("delete from " . tablename('eso_sale_spec_item') . " where uniacid={$_W['uniacid']} and specid=$spec_id");
					}

					//更新规格项id
					pdo_update("eso_sale_spec", array("content" => serialize($itemids)), array("id" => $spec_id));

					$specids[] = $spec_id;
				}

				//删除其他的
				if( count($specids)>0){
					pdo_query("delete from " . tablename('eso_sale_spec') . " where uniacid={$_W['uniacid']} and goodsid=$id and id not in (" . implode(",", $specids) . ")");
				}
				else{
					pdo_query("delete from " . tablename('eso_sale_spec') . " where uniacid={$_W['uniacid']} and goodsid=$id");
				}


				//保存规格

				$option_idss = $_POST['option_ids'];
				$option_productprices = $_POST['option_productprice'];
				$option_marketprices = $_POST['option_marketprice'];
				$option_costprices = $_POST['option_costprice'];
				$option_stocks = $_POST['option_stock'];
				$option_weights = $_POST['option_weight'];
				$len = count($option_idss);
				$optionids = array();
				for ($k = 0; $k < $len; $k++) {
					$option_id = "";
					$get_option_id = $_GPC['option_id_' . $ids][0];

					$ids = $option_idss[$k]; $idsarr = explode("_",$ids);
					$newids = array();
					foreach($idsarr as $key=>$ida){
						foreach($spec_items as $it){
							if($it['get_id']==$ida){
								$newids[] = $it['id'];
								break;
							}
						}
					}
					$newids = implode("_",$newids);

					$a = array(
						"title" => $_GPC['option_title_' . $ids][0],
						"productprice" => $_GPC['option_productprice_' . $ids][0],
						"costprice" => $_GPC['option_costprice_' . $ids][0],
						"marketprice" => $_GPC['option_marketprice_' . $ids][0],
						"stock" => $_GPC['option_stock_' . $ids][0],
						"weight" => $_GPC['option_weight_' . $ids][0],
						"goodsid" => $id,
						"specs" => $newids
					);

					$totalstocks+=$a['stock'];

					if (empty($get_option_id)) {
						pdo_insert("eso_sale_goods_option", $a);
						$option_id = pdo_insertid();
					} else {
						pdo_update("eso_sale_goods_option", $a, array('id' => $get_option_id));
						$option_id = $get_option_id;
					}
					$optionids[] = $option_id;
				}
				if (count($optionids) > 0) {
					pdo_query("delete from " . tablename('eso_sale_goods_option') . " where goodsid=$id and id not in ( " . implode(',', $optionids) . ")");
				}
				else{
					pdo_query("delete from " . tablename('eso_sale_goods_option') . " where goodsid=$id");
				}


				//总库存
				if ($totalstocks > 0) {
					pdo_update("eso_sale_goods", array("total" => $totalstocks), array("id" => $id));
				}
				//message('商品更新成功！', $this->createWebUrl('goods', array('op' => 'display')), 'success');
				message('商品更新成功！', $this->createWebUrl('goods', array('op' => 'post', 'id' => $id)), 'success');
			}
		} elseif ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}

			if (!empty($_GPC['cate_2'])) {
				$cid = intval($_GPC['cate_2']);
				$condition .= " AND ccate = '{$cid}'";
			} elseif (!empty($_GPC['cate_1'])) {
				$cid = intval($_GPC['cate_1']);
				$condition .= " AND pcate = '{$cid}'";
			}

			if (isset($_GPC['status'])) {
				$condition .= " AND status = '" . intval($_GPC['status']) . "'";
			}

			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}' and deleted=0 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 $condition");
			$pager = pagination($total, $pindex, $psize);
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id, thumb FROM " . tablename('eso_sale_goods') . " WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，商品不存在或是已经被删除！');
			}
//            if (!empty($row['thumb'])) {
//                file_delete($row['thumb']);
//            }
//            pdo_delete('eso_sale_goods', array('id' => $id));
			//修改成不直接删除，而设置deleted=1
			pdo_update("eso_sale_goods", array("deleted" => 1), array('id' => $id));

			message('删除成功！', referer(), 'success');
		} elseif ($operation == 'productdelete') {
			$id = intval($_GPC['id']);
			pdo_delete('eso_sale_product', array('id' => $id));
			message('删除成功！', '', 'success');
		}
		include $this->template('goods');
	}

	public function doWebOrder() {
		global $_W, $_GPC;

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$status = !isset($_GPC['status']) ? 1 : $_GPC['status'];
			$sendtype = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}

			if (!empty($_GPC['cate_2'])) {
				$cid = intval($_GPC['cate_2']);
				$condition .= " AND ccate = '{$cid}'";
			} elseif (!empty($_GPC['cate_1'])) {
				$cid = intval($_GPC['cate_1']);
				$condition .= " AND pcate = '{$cid}'";
			}

			if ($status != '-1') {
				$condition .= " AND status = '" . intval($status) . "'";
			}

			if(!empty($_GPC['shareid'])){
				$shareid = $_GPC['shareid'];
				$user = pdo_fetch("select * from ".tablename('eso_sale_member'). " where id = ".$shareid." and uniacid = ".$_W['uniacid']);
				$condition .= " AND shareid = '". intval($_GPC['shareid']). "' AND createtime>=".$user['flagtime']." AND from_user<>'".$user['from_user']."'";
			}

			if (!empty($sendtype)) {
				$condition .= " AND sendtype = '" . intval($sendtype) . "' AND status != '3'";
			}

			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_order') . " WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY status ASC, createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_order') . " WHERE uniacid = '{$_W['uniacid']}' $condition");
			$pager = pagination($total, $pindex, $psize);
			if (!empty($list)) {
				foreach ($list as $key=>$l){
					$commission = pdo_fetch("select total,commission, commission2, commission3 from ".tablename('eso_sale_order_goods')." where orderid = ".$l['id']);
					$list[$key]['commission'] = $commission['commission'] * $commission['total'];
					$list[$key]['commission2'] = $commission['commission2'] * $commission['total'];
					$list[$key]['commission3'] = $commission['commission3'] * $commission['total'];
				}
			}
			if (!empty($list)) {
				foreach ($list as &$row) {
					!empty($row['addressid']) && $addressids[$row['addressid']] = $row['addressid'];
					$row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('eso_sale_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
				}
				unset($row);
			}
			if (!empty($addressids)) {
				$address = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id IN ('" . implode("','", $addressids) . "')", array(), 'id');
			}
		} elseif ($operation == 'detail') {

			$members = pdo_fetchall("select id, realname from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and status = 1");
			$member = array();
			foreach($members as $m){
				$member[$m['id']] = $m['realname'];
			}
			$id = intval($_GPC['id']);

			$item = pdo_fetch("SELECT * FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
			if (empty($item)) {
				message("抱歉，订单不存在!", referer(), "error");
			}
			if (checksubmit('confirmsend')) {
				if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) {
					message('请输入快递单号！');
				}
				$item = pdo_fetch("SELECT transid FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 1);
				}
				pdo_update('eso_sale_order', array(
					'status' => 2,
					'remark' => $_GPC['remark'],
					'express' => $_GPC['express'],
					'expresscom' => $_GPC['expresscom'],
					'expresssn' => $_GPC['expresssn'],
				), array('id' => $id));
				message('发货操作成功！', referer(), 'success');
			}
			if (checksubmit('cancelsend')) {
				$item = pdo_fetch("SELECT transid FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 0, $_GPC['cancelreson']);
				}
				pdo_update('eso_sale_order', array(
					'status' => 1,
					'remark' => $_GPC['remark'],
				), array('id' => $id));
				message('取消发货操作成功！', referer(), 'success');
			}
			if (checksubmit('finish')) {

				$this->setOrderCredit($id);
				pdo_update('eso_sale_order', array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
				message('订单操作成功！', referer(), 'success');
			}
//            if (checksubmit('cancel')) {
//                pdo_update('eso_sale_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
//                message('取消完成订单操作成功！', referer(), 'success');
//            }
			if (checksubmit('cancelpay')) {
				pdo_update('eso_sale_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));

				//设置库存
				$this->setOrderStock($id, false);
				//减少积分
				//$this->setOrderCredit($orderid, false);

				message('取消订单付款操作成功！', referer(), 'success');
			}
			if (checksubmit('confrimpay')) {
				pdo_update('eso_sale_order', array('status' => 1, 'paytype' => 2, 'remark' => $_GPC['remark']), array('id' => $id));

				//设置库存
				$this->setOrderStock($id);
				//增加积分
				//$this->setOrderCredit($orderid);

				message('确认订单付款操作成功！', referer(), 'success');
			}
			if (checksubmit('close')) {
				$item = pdo_fetch("SELECT transid FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 0, $_GPC['reson']);
				}
				pdo_update('eso_sale_order', array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
				message('订单关闭操作成功！', referer(), 'success');
			}
			if (checksubmit('open')) {
				pdo_update('eso_sale_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
				message('开启订单操作成功！', referer(), 'success');
			}

			$dispatch = pdo_fetch("SELECT * FROM " . tablename('eso_sale_dispatch') . " WHERE id = :id", array(':id' => $item['dispatch']));
			if (!empty($dispatch) && !empty($dispatch['express'])) {
				$express = pdo_fetch("select * from " . tablename('eso_sale_express') . " WHERE id=:id limit 1", array(":id" => $dispatch['express']));
			}
			$item['user'] = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id = {$item['addressid']}");
			$goods = pdo_fetchall("SELECT g.id, g.title, g.status,g.thumb, g.unit,g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.price as orderprice FROM " . tablename('eso_sale_order_goods') . " o left join " . tablename('eso_sale_goods') . " g on o.goodsid=g.id "
				. " WHERE o.orderid='{$id}'");
			$item['goods'] = $goods;
		}
		include $this->template('order');
	}




	public function doWebOrdermy() {
		global $_W, $_GPC;

		//$from_user = $_GPC['from_user'];
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if(empty($_GPC['uid'])){
			message('请选择会员！', create_url('site/entry',
				array('do' => 'charge','op'=>'list', 'm' => 'eso_sale','uniacid'=>$_W['uniacid'])), 'success');
			exit();
		}
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$status = !isset($_GPC['status']) ? 1 : $_GPC['status'];
			$sendtype = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}

			if (!empty($_GPC['cate_2'])) {
				$cid = intval($_GPC['cate_2']);
				$condition .= " AND ccate = '{$cid}'";
			} elseif (!empty($_GPC['cate_1'])) {
				$cid = intval($_GPC['cate_1']);
				$condition .= " AND pcate = '{$cid}'";
			}

			if ($status != '-1') {
				$condition .= " AND status = '" . intval($status) . "'";
			}

			if(!empty($_GPC['shareid'])){
				$shareid = $_GPC['shareid'];
				$user = pdo_fetch("select * from ".tablename('eso_sale_member'). " where id = ".$shareid." and uniacid = ".$_W['uniacid']);
				$condition .= " AND shareid = '". intval($_GPC['shareid']). "' AND createtime>=".$user['flagtime']." AND from_user<>'".$user['from_user']."'";
			}

			if (!empty($sendtype)) {
				$condition .= " AND sendtype = '" . intval($sendtype) . "' AND status != '3'";
			}

			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_order') . " WHERE from_user = '{$_GPC['from_user']}' AND uniacid = '{$_W['uniacid']}'$condition ORDER BY status ASC, createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_order') . " WHERE from_user = '{$_GPC['from_user']}' AND uniacid = '{$_W['uniacid']}'$condition");
			$pager = pagination($total, $pindex, $psize);
			if (!empty($list)) {
				foreach ($list as $key=>$l){
					$commission = pdo_fetch("select total,commission, commission2, commission3 from ".tablename('eso_sale_order_goods')." where orderid = ".$l['id']);
					$list[$key]['commission'] = $commission['commission'] * $commission['total'];
					$list[$key]['commission2'] = $commission['commission2'] * $commission['total'];
					$list[$key]['commission3'] = $commission['commission3'] * $commission['total'];
				}
			}
			if (!empty($list)) {
				foreach ($list as &$row) {
					!empty($row['addressid']) && $addressids[$row['addressid']] = $row['addressid'];
					$row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('eso_sale_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
				}
				unset($row);
			}
			if (!empty($addressids)) {
				$address = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id IN ('" . implode("','", $addressids) . "')", array(), 'id');
			}
		} elseif ($operation == 'detail') {

			$members = pdo_fetchall("select id, realname from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and status = 1");
			$member = array();
			foreach($members as $m){
				$member[$m['id']] = $m['realname'];
			}
			$id = intval($_GPC['id']);

			$item = pdo_fetch("SELECT * FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
			if (empty($item)) {
				message("抱歉，订单不存在!", referer(), "error");
			}
			if (checksubmit('confirmsend')) {
				if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) {
					message('请输入快递单号！');
				}
				$item = pdo_fetch("SELECT transid FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 1);
				}
				pdo_update('eso_sale_order', array(
					'status' => 2,
					'remark' => $_GPC['remark'],
					'express' => $_GPC['express'],
					'expresscom' => $_GPC['expresscom'],
					'expresssn' => $_GPC['expresssn'],
				), array('id' => $id));
				message('发货操作成功！', referer(), 'success');
			}
			if (checksubmit('cancelsend')) {
				$item = pdo_fetch("SELECT transid FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 0, $_GPC['cancelreson']);
				}
				pdo_update('eso_sale_order', array(
					'status' => 1,
					'remark' => $_GPC['remark'],
				), array('id' => $id));
				message('取消发货操作成功！', referer(), 'success');
			}
			if (checksubmit('finish')) {

				$this->setOrderCredit($id);
				pdo_update('eso_sale_order', array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
				message('订单操作成功！', referer(), 'success');
			}
//            if (checksubmit('cancel')) {
//                pdo_update('eso_sale_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
//                message('取消完成订单操作成功！', referer(), 'success');
//            }
			if (checksubmit('cancelpay')) {
				pdo_update('eso_sale_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));

				//设置库存
				$this->setOrderStock($id, false);
				//减少积分
				//$this->setOrderCredit($orderid, false);

				message('取消订单付款操作成功！', referer(), 'success');
			}
			if (checksubmit('confrimpay')) {
				pdo_update('eso_sale_order', array('status' => 1, 'paytype' => 2, 'remark' => $_GPC['remark']), array('id' => $id));

				//设置库存
				$this->setOrderStock($id);
				//增加积分
				//$this->setOrderCredit($orderid);

				message('确认订单付款操作成功！', referer(), 'success');
			}
			if (checksubmit('close')) {
				$item = pdo_fetch("SELECT transid FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 0, $_GPC['reson']);
				}
				pdo_update('eso_sale_order', array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
				message('订单关闭操作成功！', referer(), 'success');
			}
			if (checksubmit('open')) {
				pdo_update('eso_sale_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
				message('开启订单操作成功！', referer(), 'success');
			}

			$dispatch = pdo_fetch("SELECT * FROM " . tablename('eso_sale_dispatch') . " WHERE id = :id", array(':id' => $item['dispatch']));
			if (!empty($dispatch) && !empty($dispatch['express'])) {
				$express = pdo_fetch("select * from " . tablename('eso_sale_express') . " WHERE id=:id limit 1", array(":id" => $dispatch['express']));
			}
			$item['user'] = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id = {$item['addressid']}");
			$goods = pdo_fetchall("SELECT g.id, g.title, g.status,g.thumb, g.unit,g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.price as orderprice FROM " . tablename('eso_sale_order_goods') . " o left join " . tablename('eso_sale_goods') . " g on o.goodsid=g.id "
				. " WHERE o.orderid='{$id}'");
			$item['goods'] = $goods;
		}
		include $this->template('ordermy');
	}




	//设置订单商品的库存 minus  true 减少  false 增加
	private function setOrderStock($id = '', $minus = true) {

		$goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice,g.total as goodstotal,o.total,o.optionid,g.sales FROM " . tablename('eso_sale_order_goods') . " o left join " . tablename('eso_sale_goods') . " g on o.goodsid=g.id "
			. " WHERE o.orderid='{$id}'");
		foreach ($goods as $item) {
			if ($minus) {
				//属性
				if (!empty($item['optionid'])) {
					pdo_query("update " . tablename('eso_sale_goods_option') . " set stock=stock-:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
				}
				$data = array();
				if (!empty($item['goodstotal']) && $item['goodstotal'] != -1) {
					$data['total'] = $item['goodstotal'] - $item['total'];
				}
				$data['sales'] = $item['sales'] + $item['total'];
				pdo_update('eso_sale_goods', $data, array('id' => $item['id']));
			} else {
				//属性
				if (!empty($item['optionid'])) {
					pdo_query("update " . tablename('eso_sale_goods_option') . " set stock=stock+:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
				}
				$data = array();
				if (!empty($item['goodstotal']) && $item['goodstotal'] != -1) {
					$data['total'] = $item['goodstotal'] + $item['total'];
				}
				$data['sales'] = $item['sales'] - $item['total'];
				pdo_update('eso_sale_goods', $data, array('id' => $item['id']));
			}
		}
	}

	public function doWebNotice() {
		global $_GPC, $_W;

		$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
		$operation = in_array($operation, array('display')) ? $operation : 'display';

		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;

		$starttime = empty($_GPC['starttime']) ? strtotime('-1 month') : strtotime($_GPC['starttime']);
		$endtime = empty($_GPC['endtime']) ? TIMESTAMP : strtotime($_GPC['endtime']) + 86399;

		$where .= " WHERE `uniacid` = :uniacid AND `createtime` >= :starttime AND `createtime` < :endtime";
		$paras = array(
			':uniacid' => $_W['uniacid'],
			':starttime' => $starttime,
			':endtime' => $endtime
		);
		$keyword = $_GPC['keyword'];
		if (!empty($keyword)) {
			$where .= " AND `feedbackid`=:feedbackid";
			$paras[':feedbackid'] = $keyword;
		}

		$type = empty($_GPC['type']) ? 0 : $_GPC['type'];
		$type = intval($type);
		if ($type != 0) {
			$where .= " AND `type`=:type";
			$paras[':type'] = $type;
		}
		$status = empty($_GPC['status']) ? 0 : intval($_GPC['status']);
		$status = intval($status);
		if ($status != -1) {
			$where .= " AND `status` = :status";
			$paras[':status'] = $status;
		}

		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('eso_sale_feedback') . $where, $paras);
		$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_feedback') . $where . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);
		$pager = pagination($total, $pindex, $psize);

		$transids = array();
		foreach ($list as $row) {
			$transids[] = $row['transid'];
		}
		if (!empty($transids)) {
			$sql = "SELECT * FROM " . tablename('eso_sale_order') . " WHERE uniacid='{$_W['uniacid']}' AND transid IN ( '" . implode("','", $transids) . "' )";
			$orders = pdo_fetchall($sql, array(), 'transid');
		}
		$addressids = array();
		foreach ($orders as $transid => $order) {
			$addressids[] = $order['addressid'];
		}
		$addresses = array();
		if (!empty($addressids)) {
			$sql = "SELECT * FROM " . tablename('eso_sale_address') . " WHERE uniacid='{$_W['uniacid']}' AND id IN ( '" . implode("','", $addressids) . "' )";
			$addresses = pdo_fetchall($sql, array(), 'id');
		}

		foreach ($list as &$feedback) {
			$transid = $feedback['transid'];
			$order = $orders[$transid];
			$feedback['order'] = $order;
			$addressid = $order['addressid'];
			$feedback['address'] = $addresses[$addressid];
		}

		include $this->template('notice');
	}

	public function getCartTotal() {
		global $_W;
		$from_user =	$this->getFromUser();
		$cartotal = pdo_fetchcolumn("select sum(total) from " . tablename('eso_sale_cart') . " where uniacid = '{$_W['uniacid']}' and from_user='".$from_user."'");
		return empty($cartotal) ? 0 : $cartotal;
	}

	private function getFeedbackType($type) {
		$types = array(1 => '维权', 2 => '告警');
		return $types[intval($type)];
	}

	private function getFeedbackStatus($status) {
		$statuses = array('未解决', '用户同意', '用户拒绝');
		return $statuses[intval($status)];
	}

	// 排行榜入口
	public function doMobilePhb(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';
		$month = date('m', strtotime("-1 month"));
		//上一个月第一天的时间戳
		$premonth = strtotime(date('Y-m-1 00:00:00', strtotime("-1 month")));
		$temptime = date('Y-m-1 00:00:00', strtotime("-1 month"));
		//上一个月最后一天的时间戳
		$premonthed = strtotime(date('Y-m-d 23:59:59', strtotime("$temptime +1 month -1 day")));

		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		$commission = pdo_fetchall("select sum(c.commission) as commission, m.realname, m.mobile from ".tablename('eso_sale_commission')." as c left join ".tablename('eso_sale_member')." as m on c.uniacid = m.uniacid and c.mid = m.id where c.flag = 0 and m.realname !='' and c.uniacid = ".$_W['uniacid']." and c.createtime >= ".$premonth." and c.createtime <= ".$premonthed." group by c.mid order by sum(c.commission) desc, c.createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(distinct c.mid) from ".tablename('eso_sale_commission')." as c left join ".tablename('eso_sale_member')." as m on c.uniacid = m.uniacid and c.mid = m.id where c.flag = 0 and c.uniacid = ".$_W['uniacid']." and m.realname !='' and c.createtime >= ".$premonth." and c.createtime <= ".$premonthed);
		$pager = pagination1($total, $pindex, $psize);

		include $this->template('phb');
	}

// 粉丝入口
	public function doMobileFansIndex(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';

		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		if(!empty($profile)){
			$count1 = pdo_fetchcolumn("select count(*) from ("."select from_user from ".tablename('eso_sale_order')." where  shareid = ".$profile['id'].'  group by from_user'.") x");

			$count1_2 = pdo_fetchcolumn("select count(mber.id) from ".tablename('eso_sale_member')." mber where mber.shareid = ".$profile['id']." and mber.from_user not in ("."select orders.from_user from ".tablename('eso_sale_order')." orders where  orders.shareid = ".$profile['id']." group by from_user)");
			$count1=$count1+$count1_2;
			if($count1>0)
			{
				$countall = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$profile['id']);
				$count2=0;
				$count3=0;
				if ($countall) {
					foreach ($countall as &$citem){
						$tcount2 = pdo_fetchcolumn("select count(id) from ".tablename('eso_sale_member')." where shareid = ".$citem);

						$count2=$count2+$tcount2;
						$count2all = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$citem);
						foreach ($count2all as &$citem2){
							$tcount3 = pdo_fetchcolumn("select count(*) from ("."select from_user from ".tablename('eso_sale_order')." where  shareid = ".$citem2.' and shareid!='.$citem.' and shareid!='.$profile['id'].' group by from_user'.") y"  );
							$count3=$count3+$tcount3;
						}


					}
				}
			}else
			{
				$count1=0;
				$count2=0;
				$count3=0;
			}
			$count1=$count1+$count2+$count3;


		}else
		{
			$count1=0;
		}

		$id = $profile['id'];
		if(intval($profile['id']) && $profile['status']==0){
			include $this->template('forbidden');
			exit;
		}
		if(empty($profile)){
			$rule = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE `uniacid` = :uniacid ",array(':uniacid' => $_W['uniacid']));
			$profile =fans_search($from_user, array('realname'));
			$cfg = $this->module['config'];
			$ydyy = $cfg['ydyy'];
			include $this->template('register');
			exit;
		}

		$theone = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE  uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));

		if($theone['promotertimes'] == 0 && $profile['flag'] == 0){
			$isorder = pdo_fetch('SELECT * FROM '.tablename('eso_sale_order')." WHERE status= '3' AND  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
			if(!$isorder){
				message('您还未通过分销员审核，请先购买一笔订单才能成为分销员！', $this->mturl('list',array('mid'=>$id)), 'success');


			}else{
				pdo_update('eso_sale_member', array('flag' => 1), array('id' => $profile['id']));
				$profile['flag'] = 1;
			}
		}else{
			if(empty($profile['flagtime'])||$profile['flag']!=1)
			{
				pdo_update('eso_sale_member', array('flagtime'=>TIMESTAMP), array('id' => $profile['id']));
			}
			pdo_update('eso_sale_member', array('flag' => 1), array('id' => $profile['id']));

		}


		load()->model('mc');
		$myheadimg = mc_fetch($_W['member']['uid']);

		$share = "eso_saleshareQrcode".$_W['uniacid'];
		if($_COOKIE[$share] != $_W['uniacid']."share".$id){
			include "mobile/phpqrcode.php";//引入PHP QR库文件
			$value = $_W['siteroot']."app/".$this->mturl('list',array('mid'=>$id));
			$errorCorrectionLevel = "L";
			$matrixPointSize = "4";
			$imgname = "share$id.png";
			$imgurl = "../addons/eso_sale/style/images/share/$imgname";
			QRcode::png($value, $imgurl, $errorCorrectionLevel, $matrixPointSize);
			setCookie($share, $_W['uniacid']."share".$id, time()+3600*24);
		}

		$commtime = pdo_fetch("select commtime, promotertimes from ".tablename('eso_sale_rules')." where uniacid = ".$_W['uniacid']);
		$commissioningpe = 0;
		if(empty($commtime) && $commtime['commtime']<=0){
			$commtime = array();
			$commtime['commtime']=0;
		}
		$moneytime = time()-3600*24*$commtime['commtime'];
		$userx = pdo_fetch("select * from ".tablename('eso_sale_member')." where from_user = '".$from_user."'");

		$commissioningpe = pdo_fetchcolumn("SELECT sum((g.commission*g.total)) FROM " .tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods')." as g on o.id = g.orderid and o.uniacid = g.uniacid WHERE o.shareid = ".$id." and o.uniacid = ".$_W['uniacid']." and (g.status = 0 or g.status = 1) and o.status >= 3 and o.from_user != '".$from_user."' and  g.createtime>=".$userx['flagtime']);
		if(empty($commissioningpe))
		{
			$commissioningpe =0;
		}


		include $this->template('fshome');
		//include $this->template('fansindex');

	}

	// 粉丝注册
	public function doMobileRegister(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE `uniacid` = :uniacid AND from_user=:from_user ",array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		$id = $profile['id'];
		if($op=='display'){
			$opp = $_GPC['opp'];
			$rule = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE `uniacid` = :uniacid ",array(':uniacid' => $_W['uniacid']));

			$fans = fans_search($from_user, array('realname'));
			if(empty($profile['realname'])){
				$profile['realname']=$fans['realname'];
			}
			$cfg = $this->module['config'];
			$ydyy = $cfg['ydyy'];
			include $this->template('register');
			exit;
		}
		if(!empty($profile)){
			$data=array(
				'realname'=>$_GPC['realname'],
				'mobile'=>$_GPC['mobile'],
				'pwd'=>$_GPC['password'],
				'bankcard'=>$_GPC['bankcard'],
				'banktype'=>$_GPC['banktype'],
				'alipay'=>$_GPC['alipay'],
				'wxhao'=>$_GPC['wxhao'],
			);
			//$pro = pdo_fetch('SELECT mobile,id FROM '.tablename('eso_sale_member')." WHERE `uniacid` = :uniacid AND from_user=:from_user ",array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
			/*
			if($data['mobile']==$profile['mobile']){
			}else{
				if($data['mobile']==$pro['mobile']){
					echo '-3';
					exit;
				}
			}*/

			pdo_update('eso_sale_member',$data, array('id'=>$profile['id']));

			//setcookie("$shareid", '');
			echo 2;
			exit;
		}

		//注册
		if($op=='add'){
			$shareid = 'eso_sale_sid07'.$_W['uniacid'];
			$seid=$_COOKIE[$shareid];
			if(empty($seid))
			{
				$seid=0;
			}

			$theone = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE  uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
			if($theone['promotertimes'] == 1){

				$data=array(
					'uniacid'=>$_W['uniacid'],
					'from_user'=> $from_user,
					'uid'=> $_W['member']['uid'],
					'realname'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile'],
					'pwd'=>$_GPC['password'],
					'alipay'=>$_GPC['alipay'],
					'wxhao'=>$_GPC['wxhao'],
					'commission'=>0,
					'createtime'=>TIMESTAMP,
					'flagtime'=>TIMESTAMP,
					'shareid'=> $seid,
					'status'=>1,
					'flag'=>1
				);
			}else{

				$data=array(
					'uniacid'=>$_W['uniacid'],
					'from_user'=> $from_user,
					'uid'=> $_W['member']['uid'],
					'realname'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile'],
					'pwd'=>$_GPC['password'],
					'alipay'=>$_GPC['alipay'],
					'wxhao'=>$_GPC['wxhao'],
					'commission'=>0,
					'createtime'=>TIMESTAMP,
					'flagtime'=>TIMESTAMP,
					'shareid'=> $seid,
					'status'=>1,
					'flag'=>0
				);
			}
			$profile = pdo_fetch('SELECT from_user,id FROM '.tablename('eso_sale_member')." WHERE `uniacid` = :uniacid AND from_user=:from_user ",array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));



			if($data['from_user']==$profile['from_user']){
				echo '-2';
				exit;
			}
			pdo_insert('eso_sale_member',$data);

			$theone = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE  uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));



			/*
			$profile = fans_search($from_user, array('realname', 'mobile'));
				if(empty($profile['realname'])|| empty($profile['mobile'])){
				
					fans_update($from_user, array("realname" => $_GPC['realname'],"mobile" => $_GPC['mobile']));
				
				}

		*/


			echo 1;
			exit;
		}
	}

	// 我的佣金
	public function doMobileCommission(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';


		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		$id = $profile['id'];
		if(intval($profile['id']) && $profile['status']==0){
			include $this->template('forbidden');
			exit;
		}
		if(empty($profile)){
			message('请先注册',$this->mturl('register'),'error');
			exit;
		}

		if($op=='display'){
			$commtime = pdo_fetch("select commtime, promotertimes from ".tablename('eso_sale_rules')." where uniacid = ".$_W['uniacid']);
			$commissioningpe =0;
			if(empty($commtime) && $commtime['commtime']<=0){
				$commtime = array();
				$commtime['commtime']=0;
			}
			$moneytime = time()-3600*24*$commtime['commtime'];
			$userx = pdo_fetch("select * from ".tablename('eso_sale_member')." where from_user = '".$from_user."'");

			$commissioningpe = pdo_fetchcolumn("SELECT sum((g.commission*g.total)) FROM " .tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods')." as g on o.id = g.orderid and o.uniacid = g.uniacid WHERE o.shareid = ".$id." and o.uniacid = ".$_W['uniacid']." and (g.status = 0 or g.status = 1) and o.status >= 3 and o.from_user != '".$from_user."' and  g.createtime>=".$userx['flagtime']);
			if(empty($commissioningpe))
			{
				$commissioningpe =0;
			}


			// 总佣金
			$commissioning = pdo_fetchcolumn("select sum(commission) from ".tablename('eso_sale_commission')." where flag = 0 and mid = ".$profile['id']." and uniacid = ".$_W['uniacid']);
			$commissioning = empty($commissioning)?0:$commissioning;
			// 可结佣
			//	$commissioningpe = $commissioningpe-$profile['commission'];

			// 已结佣
			$commissioned = $profile['commission'];
			$total = pdo_fetchcolumn("select count(id) from ". tablename('eso_sale_commission'). " where mid =". $profile['id']. " and flag = 0");
			if($_GPC['opp'] == 'more'){
				$opp = 'more';
				$pindex = max(1, intval($_GPC['page']));
				$psize = 15;
				// 账户充值记录
				$list = pdo_fetchall("select co.isshare,co.commission, co.createtime, og.orderid, og.goodsid, og.total,oo.ordersn from ". tablename('eso_sale_commission'). " as co left join ".tablename('eso_sale_order_goods')." as og on co.ogid = og.id and co.uniacid = og.uniacid left join ".tablename('eso_sale_order')." as oo on oo.id = og.orderid and co.uniacid = og.uniacid where co.mid =". $profile['id']. " and co.flag = 0 ORDER BY co.createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
				$pager = pagination1($total, $pindex, $psize);
			}else{
				// 账户充值记录
				$list = pdo_fetchall("select co.isshare,co.commission, co.createtime, og.orderid, og.goodsid, og.total,oo.ordersn from ". tablename('eso_sale_commission'). " as co left join ".tablename('eso_sale_order_goods')." as og on co.ogid = og.id and co.uniacid = og.uniacid left join ".tablename('eso_sale_order')." as oo on oo.id = og.orderid and co.uniacid = og.uniacid where co.mid =". $profile['id']. " and co.flag = 0 ORDER BY co.createtime DESC limit 10");
			}
			$addresss = pdo_fetchall("select id, realname from ".tablename('eso_sale_address')." where uniacid = ".$_W['uniacid']);
			$address = array();
			foreach($addresss as $adr){
				$address[$adr['id']] = $adr['realname'];
			}
			$goods = pdo_fetchall("select id, title from ".tablename('eso_sale_goods')." where uniacid = ".$_W['uniacid']);
			$good = array();
			foreach($goods as $g){
				$good[$g['id']] = $g['title'];
			}
		}

		// 申请佣金
		if($op=='commapply'){
			// 提现周期
			$commtime = pdo_fetch("select commtime, promotertimes from ".tablename('eso_sale_rules')." where uniacid = ".$_W['uniacid']);
			if(empty($commtime) && $commtime['commtime']<0){
				message("此功能还未开放，请耐心等待...");
			}
			$moneytime = time()-3600*24*$commtime['commtime'];
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;


			$user = pdo_fetch("select * from ".tablename('eso_sale_member')." where from_user = '".$from_user."'");

			$list = pdo_fetchall("SELECT o.createtime, g.commission, g.total, g.goodsid, g.id,o.ordersn FROM " .tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods')." as g on o.id = g.orderid and o.uniacid = g.uniacid WHERE o.shareid = ".$id." and o.uniacid = ".$_W['uniacid']." and g.status = 0 and o.status >= 3 and o.from_user != '".$from_user."' and g.createtime < ".$moneytime." and g.createtime>=".$user['flagtime']." ORDER BY o.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("SELECT count(g.id) FROM " .tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods')." as g on o.id = g.orderid and o.uniacid = g.uniacid WHERE o.shareid = ".$id." and o.uniacid = ".$_W['uniacid']." and o.status = 3 and g.createtime < ".$moneytime." and g.createtime>=".$user['flagtime']);

			if($profile['flag']==0){
				if($total>=$commtime['promotertimes']){
					pdo_update('eso_sale_member', array('flag'=>1), array('id'=>$profile['id']));
					$profile['flag'] = 1;
				}
			}
			$pager = pagination1($total, $pindex, $psize);
			$goods = pdo_fetchall("select id, title from ".tablename('eso_sale_goods'). " where uniacid = ".$_W['uniacid']. " and status = 1");
			$good = array();
			foreach($goods as $g){
				$good[$g['id']] = $g['title'];
			}
			include $this->template('commapply');
			exit;
		}
		// 处理申请
		if($op=='applyed'){
			if($profile['flag']==0){
				message('申请佣金失败！');
			}
			$isbank = pdo_fetch("select id, bankcard, banktype from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and from_user = '".$from_user."'");
			if(empty($isbank['bankcard']) || empty($isbank['banktype'])){
				message('请先完善银行卡信息！', $this->mturl('bankcard', array('id'=>$isbank['id'], 'opp'=>'complated')), 'error');
			}
			$update = array(
				'status'=>1,
				'applytime'=>time()
			);
			// 申请订单ID数组
			$selected = explode(',',trim($_GPC['selected']));
			for($i=0; $i<sizeof($selected); $i++){
				$temp = pdo_update('eso_sale_order_goods', $update, array('id'=>$selected[$i]));
			}
			if(!$temp){
				message('申请失败，请重新申请！', $this->mturl('commission', array('op'=>'commapply')), 'error');
			}else{
				message('申请成功！', $this->mturl('commission'), 'success');
			}
		}

		include $this->template('commission');
	}

	// 我的银行卡
	public function doMobileBankcard(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';

		$rule = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rule')." WHERE `uniacid` = :uniacid ",array(':uniacid' => $_W['uniacid']));
		if(empty($from_user)){
			message('你想知道怎么加入么?',$rule['gzurl'],'sucessr');
			exit;
		}

		$profile= pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		if(intval($profile['id']) && $profile['status']==0){
			include $this->template('forbidden');
			exit;
		}
		if(empty($profile)){
			message('请先注册',$this->mturl('register'),'error');
			exit;
		}
		if($op=='edit'){

			$data=array(
				'bankcard'=>$_GPC['bankcard'],
				'banktype'=>$_GPC['banktype'],
				'alipay'=>$_GPC['alipay'],
				'wxhao'=>$_GPC['wxhao']
			);
			if(!empty($data['bankcard']) && !empty($data['banktype'])){
				pdo_update('eso_sale_member',$data,array('from_user' => $from_user));
				if($_GPC['opp']=='complated'){
					echo 3;
					exit;
				}
				echo 1;

			}else{
				echo 0;
			}

			exit;
		}

		include $this->template('bankcard');
	}

	// 粉丝订单
	public function doMobileFansorder(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';

		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		$id = $profile['id'];
		if(intval($profile['id']) && $profile['status']==0){
			include $this->template('forbidden');
			exit;
		}
		if(empty($profile)){
			message('请先注册',$this->mturl('register'),'error');
			exit;
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT o.createtime,o.ordersn,o.status, g.commission, g.total, g.goodsid FROM " . tablename('eso_sale_order') . " as o left join ".tablename('eso_sale_order_goods')." as g on o.id = g.orderid and o.uniacid = g.uniacid WHERE o.shareid = ".$id." and o.uniacid = ".$_W['uniacid']." and o.from_user<>'".$profile['from_user']."' ORDER BY o.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$goods = pdo_fetchall("select id, title from ".tablename('eso_sale_goods'). " where uniacid = ".$_W['uniacid']. " and status = 1");
		$good = array();
		foreach($goods as $g){
			$good[$g['id']] = $g['title'];
		}
		$total = pdo_fetchcolumn('SELECT COUNT(id) FROM ' .tablename('eso_sale_order'). " WHERE uniacid = ".$_W['uniacid']." AND shareid = ".$id);
		$pager = pagination1($total, $pindex, $psize);

		include $this->template('fansorder');
	}

	// 活动细则
	public function doMobileRule(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';

		$rule = pdo_fetchcolumn('SELECT rule FROM '.tablename('eso_sale_rules')." WHERE uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
		$id = pdo_fetchcolumn('SELECT id FROM '.tablename('eso_sale_member')." WHERE uniacid = :uniacid AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));

		include $this->template('rule');
	}
	public function doMobileErwema(){
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';
		load()->model('mc');
		$myheadimg = mc_fetch($_W['member']['uid']);

		$share = "eso_saleshareQrcode".$_W['uniacid'];
		if($_COOKIE[$share] != $_W['uniacid']."share".$id){
			include "mobile/phpqrcode.php";//引入PHP QR库文件
			$value = $_W['siteroot']."app/".$this->mturl('list',array('mid'=>$id));
			$errorCorrectionLevel = "L";
			$matrixPointSize = "4";
			$imgname = "share$id.png";
			$imgurl = "../addons/eso_sale/style/images/share/$imgname";
			QRcode::png($value, $imgurl, $errorCorrectionLevel, $matrixPointSize);
			setCookie($share, $_W['uniacid']."share".$id, time()+3600*24);
		}
		include $this->template('homeerwema');
		//include $this->template('fansindex');
	}
//-----------------------------------web端

	// 粉丝管理
	public function doWebfansmanager(){
		global $_W,$_GPC;

		checklogin();
		$uniacid=$_W['uniacid'];
		$op= $operation = $_GPC['op']?$_GPC['op']:'display';

		// 粉丝列表
		if($op=='display'){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("select sale.*,mc_members.credit1 from ".tablename('eso_sale_member'). " sale left join ".tablename('mc_members'). " mc_members on sale.uid=mc_members.uid where sale.flag = 1 and sale.uniacid = ".$_W['uniacid']." ORDER BY sale.id DESC limit ".($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("select count(id) from". tablename('eso_sale_member'). "where flag = 1 and uniacid =".$_W['uniacid']);;
			$pager = pagination1($total, $pindex, $psize);

			$commissions = pdo_fetchall("select mid, sum(commission) as commission from ".tablename('eso_sale_commission')." where uniacid = ".$_W['uniacid']." and flag = 0 group by mid");
			// 还需结佣
			$commission = array();
			foreach($commissions as $c){
				$commission[$c['mid']] = $c['commission'];
			}
		}

		if($op=='nocheck'){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("select sale.*,mc_members.credit1 from ".tablename('eso_sale_member'). " sale left join ".tablename('mc_members'). " mc_members on sale.uid=mc_members.uid where sale.flag = 0 and sale.uniacid = ".$_W['uniacid']." ORDER BY sale.id DESC limit ".($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("select count(id) from". tablename('eso_sale_member'). "where flag = 0 and uniacid =".$_W['uniacid']);;
			$pager = pagination1($total, $pindex, $psize);

			include $this->template('fansmanagered');
			exit;
		}
		// 查找粉丝
		if($op=='sort'){
			$sort = array(
				'realname'=>$_GPC['realname'],
				'mobile'=>$_GPC['mobile']
			);
			if($_GPC['opp']=='nocheck'){
				$status = 0;
			} else {
				$status = 1;
			}
			// 符合条件的粉丝
			$list = pdo_fetchall("select * from". tablename('eso_sale_member')."where flag = ".$status." and uniacid =".$_W['uniacid'].".and realname like '%".$sort['realname']. "%' and mobile like '%".$sort['mobile']. "%' ORDER BY id DESC");
			$commissions = pdo_fetchall("select mid, sum(commission) as commission from ".tablename('eso_sale_commission')." where uniacid = ".$_W['uniacid']." and flag = 0 group by mid");
			// 还需结佣
			$commission = array();
			foreach($commissions as $c){
				$commission[$c['mid']] = $c['commission'];
			}
			if($_GPC['opp']=='nocheck'){
				include $this->template('fansmanagered');
				exit;
			}
		}

		// 删除粉丝
		if($op=='delete'){
			$temp = pdo_delete('eso_sale_member', array('id'=>$_GPC['id']));
			if(empty($temp)){
				if($_GPC['opp']=='nocheck'){
					message('删除失败，请重新删除！', $this->createWebUrl('fansmanager', array('op'=>'nocheck')), 'error');
				} else {
					message('删除失败，请重新删除！', $this->createWebUrl('fansmanager'), 'error');
				}
			}else{
				if($_GPC['opp']=='nocheck'){
					message('删除成功！', $this->createWebUrl('fansmanager', array('op'=>'nocheck')), 'success');
				} else {
					message('删除成功！', $this->createWebUrl('fansmanager'), 'success');
				}
			}
		}

		// 粉丝详情
		if($op=='detail'){
			$id = $_GPC['id'];
			$user = pdo_fetch("select * from ".tablename('eso_sale_member'). " where id = ".$id);
			if($_GPC['opp']=='nocheck'){
				include $this->template('fansmanagered_detail');
			} else {
				include $this->template('fansmanager_detail');
			}
			exit;
		}

		// 设置粉丝权限，类型
		if($op=='status'){
			$status = array(
				'status'=>$_GPC['status'],
				'flag'=>$_GPC['flag'],
				'content'=>trim($_GPC['content'])
			);
			if($_GPC['opp']=='nocheck'&&$_GPC['flag']==1){

				$status ['flagtime']=TIMESTAMP;
			}
			$temp = pdo_update('eso_sale_member', $status, array('id'=>$_GPC['id']));
			if(empty($temp)){
				if($_GPC['opp']=='nocheck'){
					message('设置用户权限失败，请重新设置！', $this->createWebUrl('fansmanager', array('op'=>'detail', 'opp'=>'nocheck', 'id'=>$_GPC['id'])), 'error');
				} else {
					message('设置用户权限失败，请重新设置！', $this->createWebUrl('fansmanager', array('op'=>'detail', 'id'=>$_GPC['id'])), 'error');
				}
			}else{
				if($_GPC['opp']=='nocheck'){
					message('设置用户权限成功！', $this->createWebUrl('fansmanager', array('op'=>'nocheck')), 'success');
				} else {
					message('设置用户权限成功！', $this->createWebUrl('fansmanager'), 'success');
				}
			}
		}

		// 充值
		if($op=='recharge'){
			$id = $_GPC['id'];
			if($_GPC['opp']=='recharged'){
				if(!is_numeric($_GPC['commission'])){
					message('佣金请输入合法数字！', '', 'error');
				}
				$recharged = array(
					'uniacid'=>$_W['uniacid'],
					'mid'=>$id,
					'flag'=>1,
					'content'=>trim($_GPC['content']),
					'commission'=>$_GPC['commission'],
					'createtime'=>time()
				);
				$temp = pdo_insert('eso_sale_commission', $recharged);
				// 已结佣金
				$commission = pdo_fetchcolumn("select commission from ".tablename('eso_sale_member'). " where id = ".$id);

				if(empty($temp)){
					message('充值失败，请重新充值！', $this->createWebUrl('fansmanager', array('op'=>'recharge', 'id'=>$_GPC['id'])), 'error');
				}else{
					pdo_update('eso_sale_member', array('commission'=>$commission+$_GPC['commission']), array('id'=>$id));
					message('充值成功！', $this->createWebUrl('fansmanager', array('op'=>'recharge', 'id'=>$_GPC['id'])), 'success');
				}
			}
			$user = pdo_fetch("select * from ".tablename('eso_sale_member'). " where id = ".$id);
			$commission = pdo_fetchcolumn("select sum(commission) from ".tablename('eso_sale_commission')." where mid = ".$id." and flag = 0 and uniacid = ".$_W['uniacid']);
			$commission = empty($commission)?0:$commission;
			// 可结佣金
			$commission = $commission - $user['commission'];
			// 充值记录
			$commissions = pdo_fetchall("select * from ".tablename('eso_sale_commission')." where mid = ".$id." and uniacid = ".$_W['uniacid']." and flag = 1");
			include $this->template('fansmanager_recharge');
			exit;
		}

		include $this->template('fansmanager');
	}

	// 佣金管理
	public function doWebCommission(){
		global $_W,$_GPC;
		load()->func('tpl');
		checklogin();
		$uniacid=$_W['uniacid'];
		$op= $operation = $_GPC['op']?$_GPC['op']:'display';

		$members = pdo_fetchall("select id, realname, mobile from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and status = 1");
		$member = array();
		foreach($members as $m){
			$member['realname'][$m['id']] = $m['realname'];
			$member['mobile'][$m['id']] = $m['mobile'];
		}
		// 正在申请
		if($op=='display'){
			if($_GPC['opp']=='check'){
				$shareid = $_GPC['shareid'];
				// 申请人的信息
				$user = pdo_fetch("select realname, mobile from ".tablename('eso_sale_member')." where id = ".$_GPC['shareid']);
				// 申请订单信息
				$info = pdo_fetch("select og.id, og.total, og.price, og.status, og.commission, og.commission2,og.commission3, og.applytime, og.content, g.title from ".tablename('eso_sale_order_goods')." as og left join ".tablename('eso_sale_goods')." as g on og.goodsid = g.id and og.uniacid = g.uniacid where og.id = ".$_GPC['id']);
				include $this->template('applying_detail');
				exit;
			}
			if($_GPC['opp']=='checked'){
				$checked = array(
					'status'=>$_GPC['status'],
					'checktime'=>time(),
					'content'=>trim($_GPC['content'])
				);
				$temp = pdo_update('eso_sale_order_goods', $checked, array('id'=>$_GPC['id']));
				if(empty($temp)){
					message('审核失败，请重新审核！', $this->createWebUrl('commission', array('opp'=>'check', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
				}else{
					message('审核成功！', $this->createWebUrl('commission'), 'success');
				}
			}
			if($_GPC['opp']=='sort'){
				$sort = array(
					'realname'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile']
				);
				//$shareid = pdo_fetchall("select id from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%'");
				$shareid = "select id from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%'";
				$list = pdo_fetchall("select o.shareid, o.status, g.id, g.applytime from ".tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = 1 and o.shareid in (".$shareid.") ORDER BY o.id desc");
				$total = sizeof($list);

			}else{
				$pindex = max(1, intval($_GPC['page']));
				$psize = 20;
				$list = pdo_fetchall("select o.shareid, o.status, g.id, g.applytime from ".tablename('eso_sale_order'). " as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = 1 ORDER BY o.id DESC limit ".($pindex - 1) * $psize . ',' . $psize);
				$total = pdo_fetchcolumn("select count(o.id) from ".tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = 1");
				$pager = pagination1($total, $pindex, $psize);
			}
			include $this->template('applying');
			exit;
		}

		// 审核通过
		if($op=='applyed'){
			if($_GPC['opp']=='jieyong'){
				$shareid = $_GPC['shareid'];
				// 申请人的信息
				$user = pdo_fetch("select id, realname, mobile,shareid from ".tablename('eso_sale_member')." where id = ".$_GPC['shareid']);
				// 申请订单信息
				$info = pdo_fetch("select og.id, og.total, og.price, og.status, og.commission, og.commission2,og.commission3, og.applytime, og.content, g.title from ".tablename('eso_sale_order_goods')." as og left join ".tablename('eso_sale_goods')." as g on og.goodsid = g.id and og.uniacid = g.uniacid where og.id = ".$_GPC['id']);

				// 佣金记录
				$commissions = pdo_fetchall("select * from ".tablename('eso_sale_commission')." where ogid = ".$_GPC['id'].' and mid='.$_GPC['shareid']);
				$commission = pdo_fetchcolumn("select sum(commission) from ".tablename('eso_sale_commission')." where isshare!=1 and ogid = ".$_GPC['id'].' and mid='.$_GPC['shareid']);
				$commission = empty($commission)?0:$commission;
				if(!empty($user['shareid']))
				{
					$commission2 = pdo_fetchcolumn("select sum(commission) from ".tablename('eso_sale_commission')." where isshare=1 and ogid = ".$_GPC['id'].' and mid='.$user['shareid']);
					$commission2 = empty($commission2)?0:$commission2;
					$user2 = pdo_fetch("select id, realname, mobile,shareid from ".tablename('eso_sale_member')." where id = ".$user['shareid']);

					if(!empty($user2['shareid']))
					{
						$commission3 = pdo_fetchcolumn("select sum(commission) from ".tablename('eso_sale_commission')." where isshare=1 and ogid = ".$_GPC['id'].' and mid='.$user2['shareid']);
						$commission3 = empty($commission3)?0:$commission3;
						$user3 = pdo_fetch("select id, realname, mobile,shareid from ".tablename('eso_sale_member')." where id = ".$user2['shareid']);
					}else
					{

						$commission3 =0;
					}




				}else
				{
					$commission2 =0;
				}

				include $this->template('applyed_detail');
				exit;
			}
			if($_GPC['opp']=='jieyonged'){
				if($_GPC['status']==2){
					if(!is_numeric($_GPC['commission'])||!is_numeric($_GPC['commission2'])||!is_numeric($_GPC['commission3'])){
						message('佣金请输入合法数字！', '', 'error');
					}
					$shareid = $_GPC['shareid'];
					$ogid = $_GPC['id'];
					$commission = array(
						'uniacid'=>$_W['uniacid'],
						'mid'=>$shareid,
						'ogid'=>$ogid,
						'commission'=>$_GPC['commission'],
						'content'=>trim($_GPC['content']),
						'isshare'=>0,
						'createtime'=>time()
					);
					if($_GPC['commission']>0)
					{
						$temp = pdo_insert('eso_sale_commission', $commission);
					}
					$user = pdo_fetch("select id,shareid from ".tablename('eso_sale_member')." where id = ".$_GPC['shareid']);

					if(!empty($user['shareid']))
					{
						$user2 = pdo_fetch("select id from ".tablename('eso_sale_member')." where flag=1 and id = ".$user['shareid']);
						if(!empty($user2)){
							if(!empty($_GPC['commission2'])){
								$commission2 = array(
									'uniacid'=>$_W['uniacid'],
									'mid'=>$user['shareid'],
									'ogid'=>$ogid,
									'commission'=>$_GPC['commission2'],
									'content'=>trim($_GPC['content']),
									'isshare'=>1,
									'createtime'=>time()
								);
								if($_GPC['commission2']>0)
								{
									pdo_insert('eso_sale_commission', $commission2);
								}
							}
						}
					}
					if(!empty($user2['id']))
					{
						$nuser2 = pdo_fetch("select shareid from ".tablename('eso_sale_member')." where id = ".$user2['id']);
					}
					if(!empty($nuser2['shareid']))
					{
						$nuser3 = pdo_fetch("select id from ".tablename('eso_sale_member')." where flag=1 and id = ".$nuser2['shareid']);
						if(!empty($nuser3)){
							if(!empty($_GPC['commission3'])){
								$commission3 = array(
									'uniacid'=>$_W['uniacid'],
									'mid'=>$nuser2['shareid'],
									'ogid'=>$ogid,
									'commission'=>$_GPC['commission3'],
									'content'=>trim($_GPC['content']),
									'isshare'=>1,
									'createtime'=>time()
								);
								if($_GPC['commission3']>0)
								{
									pdo_insert('eso_sale_commission', $commission3);
								}
							}
						}
					}





					if($_GPC['commission']>0&&!empty($shareid))
					{
						$recharged = array(
							'uniacid'=>$_W['uniacid'],
							'mid'=>$shareid,
							'flag'=>1,
							'content'=>trim($_GPC['content']),
							'commission'=>$_GPC['commission'],
							'createtime'=>time()
						);
						$temp = pdo_insert('eso_sale_commission', $recharged);

						if(empty($temp)){
							message('充值失败，请重新充值！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
						}else{
							$commission = pdo_fetchcolumn("select commission from ".tablename('eso_sale_member'). " where id = ".$shareid);

							pdo_update('eso_sale_member', array('commission'=>$commission+$_GPC['commission']), array('id'=>$shareid));
						}

					}


					if($_GPC['commission2']>0&&!empty($user['shareid']))
					{
						$recharged = array(
							'uniacid'=>$_W['uniacid'],
							'mid'=>$user['shareid'],
							'flag'=>1,
							'content'=>trim($_GPC['content']),
							'commission'=>$_GPC['commission2'],
							'createtime'=>time()
						);
						$temp = pdo_insert('eso_sale_commission', $recharged);

						if(empty($temp)){
							message('充值失败，请重新充值！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
						}else{
							$commission = pdo_fetchcolumn("select commission from ".tablename('eso_sale_member'). " where id = ".$user['shareid']);

							pdo_update('eso_sale_member', array('commission'=>$commission+$_GPC['commission2']), array('id'=>$user['shareid']));
						}

					}



					if($_GPC['commission3']>0&&!empty($nuser2['shareid']))
					{
						$recharged = array(
							'uniacid'=>$_W['uniacid'],
							'mid'=>$nuser2['shareid'],
							'flag'=>1,
							'content'=>trim($_GPC['content']),
							'commission'=>$_GPC['commission3'],
							'createtime'=>time()
						);
						$temp = pdo_insert('eso_sale_commission', $recharged);

						if(empty($temp)){
							message('充值失败，请重新充值！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
						}else{
							$commission = pdo_fetchcolumn("select commission from ".tablename('eso_sale_member'). " where id = ".$nuser2['shareid']);

							pdo_update('eso_sale_member', array('commission'=>$commission+$_GPC['commission3']), array('id'=>$nuser2['shareid']));
						}

					}


					message('充值成功！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'success');


					//if(empty($temp)){
					//		message('充值失败，请重新充值！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
					//	}else{
					//		message('审核成功！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'success');
					//	}
				}else{
					$checked = array(
						'status'=>$_GPC['status'],
						'content'=>trim($_GPC['content'])
					);
					$temp = pdo_update('eso_sale_order_goods', $checked, array('id'=>$_GPC['id']));
					if(empty($temp)){
						message('提交失败，请重新提交！', $this->createWebUrl('commission', array('op'=>'applyed', 'opp'=>'jieyong', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
					}else{
						message('提交成功！', $this->createWebUrl('commission', array('op'=>'applyed')), 'success');
					}
				}
			}
			if($_GPC['opp']=='sort'){
				$sort = array(
					'realname'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile']
				);
				//$shareid = pdo_fetchall("select id from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%'");
				$shareid = "select id from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%'";
				$list = pdo_fetchall("select o.shareid, o.status, g.id, g.checktime from ".tablename('eso_sale_order'). " as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = 2 and o.shareid in (".$shareid.") ORDER BY o.id desc");
				$total = sizeof($list);
			}else{
				$pindex = max(1, intval($_GPC['page']));
				$psize = 20;
				$list = pdo_fetchall("select o.shareid, o.status, g.id, g.checktime from ".tablename('eso_sale_order')." as o left join ".tablename('eso_sale_order_goods')." as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = 2 ORDER BY g.checktime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
				$total = pdo_fetchcolumn("select count(o.id) from ".tablename('eso_sale_order'). " as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = 2");
				$pager = pagination1($total, $pindex, $psize);
			}
			include $this->template('applyed');
			exit;
		}

		// 审核无效
		if($op=='invalid'){
			if($_GPC['opp']=='delete'){
				$delete = array(
					'status'=>-2
				);
				$temp = pdo_update('eso_sale_order_goods', $delete, array('id'=>$_GPC['id']));
				if(empty($temp)){
					message('删除失败，请重新删除！', $this->createWebUrl('commission', array('op'=>'invalid')), 'error');
				}else{
					message('删除成功！', $this->createWebUrl('commission', array('op'=>'invalid')), 'success');
				}
			}
			if($_GPC['opp']=='detail'){
				$shareid = $_GPC['shareid'];
				// 申请人的信息
				$user = pdo_fetch("select realname, mobile from ".tablename('eso_sale_member')." where id = ".$_GPC['shareid']);
				// 申请订单信息
				$info = pdo_fetch("select og.id, og.total, og.price, og.status, og.checktime, og.content, g.title from ".tablename('eso_sale_order_goods')." as og left join ".tablename('eso_sale_goods')." as g on og.goodsid = g.id and og.uniacid = g.uniacid where og.id = ".$_GPC['id']);
				include $this->template('invalid_detail');
				exit;
			}
			if($_GPC['opp']=='invalided'){
				$invalided = array(
					'status'=>$_GPC['status'],
					'content'=>trim($_GPC['content'])
				);
				$temp = pdo_update('eso_sale_order_goods', $invalided, array('id'=>$_GPC['id']));
				if(empty($temp)){
					message('提交失败，请重新提交！', $this->createWebUrl('commission', array('op'=>'invalid', 'opp'=>'detail', 'shareid'=>$_GPC['shareid'], 'id'=>$_GPC['id'])), 'error');
				}else{
					message('提交成功！', $this->createWebUrl('commission', array('op'=>'invalid')), 'success');
				}
			}
			if($_GPC['opp']=='sort'){
				$sort = array(
					'realname'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile']
				);
				//$shareid = pdo_fetchall("select id from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%'");
				$shareid = "select id from ".tablename('eso_sale_member')." where uniacid = ".$_W['uniacid']." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%'";
				$list = pdo_fetchall("select o.shareid, o.status, g.id, g.checktime from ".tablename('eso_sale_order'). " as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = -1 and o.shareid in (".$shareid.") ORDER BY o.id desc");
				$total = sizeof($list);
			}else{
				$pindex = max(1, intval($_GPC['page']));
				$psize = 20;
				$list = pdo_fetchall("select o.shareid, o.status, g.id, g.checktime from ".tablename('eso_sale_order'). " as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = -1 ORDER BY o.id DESC limit ".($pindex - 1) * $psize . ',' . $psize);
				$pager = pagination1($total, $pindex, $psize);
				$total = pdo_fetchcolumn("select count(o.id) from ".tablename('eso_sale_order'). " as o left join ".tablename('eso_sale_order_goods'). " as g on o.id = g.orderid and o.uniacid = g.uniacid where o.uniacid = ".$_W['uniacid']." and g.status = -1");
			}
			include $this->template('invalid');
			exit;
		}
	}

	// 充值记录导出
	public function doWebOutCommission(){
		global $_W,$_GPC;

		checklogin();
		$uniacid=$_W['uniacid'];
		$op= $operation = $_GPC['op']?$_GPC['op']:'display';

		//$sql = "SELECT c.id,c.uniacid,c.mid,c.ogid,c.commission,c.content,c.status,c.createtime,m.realname,m.mobile FROM `ims_eso_sale_commission` AS c LEFT JOIN `ims_eso_sale_member` AS m ON c.mid=m.id AND c.uniacid=".$_W['uniacid']." ";
		//$list = pdo_fetchall($sql);
		$starttime = strtotime($_GPC['start_time']);
		$endtime = strtotime($_GPC['end_time']);
		$info = pdo_fetch("select og.id, og.total, og.price, og.status, og.commission, og.applytime, og.content, g.title from ".tablename('eso_sale_order_goods')." as og left join ".tablename('eso_sale_goods')." as g on og.goodsid = g.id and og.uniacid = g.uniacid WHERE og.createtime>= ".$starttime." AND og.createtime<=".$endtime." ");
		//var_dump($info);
		$commissionList = pdo_fetchall("SELECT c.*,m.realname,m.mobile,m.bankcard,m.alipay,m.wxhao FROM `ims_eso_sale_commission` AS c LEFT JOIN `ims_eso_sale_member` AS m ON c.mid=m.id WHERE c.createtime>=".$starttime." AND c.createtime<=".$endtime." AND c.isout = 0 AND c.flag = 0 AND c.uniacid=".$_W['uniacid']."  " );
		if(empty($commissionList)){
			message('已没有需要导出的数据了！');
			exit;
		}
		//var_dump($commissionList);
		$list = array();
		foreach($commissionList as $k=>$v){
			$ogid = $v['ogid'];
			$info = pdo_fetch("select og.id, og.checktime, og.content from ".tablename('eso_sale_order_goods')." as og left join ".tablename('eso_sale_goods')." as g on og.goodsid = g.id and og.uniacid = g.uniacid where og.id = ".$ogid);
			pdo_update('eso_sale_commission', array('isout'=>1), array('id'=>$v['id']));
			$list[$k]['realname'] = $v['realname'];
			$list[$k]['mobile'] = $v['mobile'];
			$list[$k]['bankcard'] = $v['bankcard'];
			$list[$k]['alipay'] = $v['alipay'];
			$list[$k]['wxhao'] = $v['wxhao'];
			$list[$k]['checktime'] = date('Y-m-d H:m:s' ,$info['checktime']);
			$list[$k]['commissiontotal'] = $v['commission'];
			$list[$k]['content'] = $info['content'];
		}

		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');

		require_once './source/modules/public/Classes/PHPExcel.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("伊索网络")
			->setLastModifiedBy("伊索网络")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");


		// Add some data

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '真实姓名')
			->setCellValue('B1', '手机号码')
			->setCellValue('C1', '审核时间')
			->setCellValue('D1', '申请佣金')
			->setCellValue('E1', '银行卡号')
			->setCellValue('F1', '支付宝号')
			->setCellValue('G1', '微信号码')
			->setCellValue('H1', '备注');

		foreach($list as $i=>$v){
			$i = $i+2;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i, $v['realname'])
				->setCellValue('B'.$i, $v['mobile'])
				->setCellValue('C'.$i, $v['checktime'])
				->setCellValue('D'.$i, $v['commissiontotal'])
				->setCellValue('E'.$i,' '.$v['bankcard'].' ')
				->setCellValue('F'.$i,' '.$v['alipay'].' ')
				->setCellValue('G'.$i,' '.$v['wxhao'].' ')
				->setCellValue('H'.$i, $v['content']);
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);

		// Rename worksheet
		$time=time();
		$objPHPExcel->getActiveSheet()->setTitle('微商城佣金充值'.$time);


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)]

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="moon_'.$time.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}

	// 细则与条款
	public function doWebRules(){
		global $_W,$_GPC;

		checklogin();
		$uniacid=$_W['uniacid'];
		$op= $operation = $_GPC['op']?$_GPC['op']:'display';
		$theone = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE  uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
		$id = $theone['id'];
		if (checksubmit('submit')) {
			$clickcredit = $_GPC['clickcredit'];
			//$commtime = $_GPC['commtime'];

			/*if(!is_numeric($commtime)){
				message('请输入合法数字周期！');
			}*/
			if(!is_numeric($clickcredit)){
				message('请输入合法数字！');
			}
			/*if(empty($promotertimes)){
				$promotertimes = 1;
			} else {
				$promotertimes = is_numeric($promotertimes)?$promotertimes:message('请输入合法数字次数！');
			}*/
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'clickcredit' => $clickcredit,
				'rule' => htmlspecialchars_decode($_GPC['rule']),
				'terms' => htmlspecialchars_decode($_GPC['terms']),
				'commtime' => 0,
				'promotertimes' => $_GPC['promotertimes'],
				'createtime' => TIMESTAMP
			);
			if(empty($id)) {
				pdo_insert('eso_sale_rules', $insert);
				!pdo_insertid() ?	message('保存失败, 请稍后重试.','error') : '';
			} else {
				if(pdo_update('eso_sale_rules', $insert,array('id' => $id)) === false){
					message('更新失败, 请稍后重试.','error');
				}
			}
			message('更新成功！', $this->createWebUrl('rules'), 'success');
		}
		include $this->template('rules');
	}

	public function doMobilelist() {
		global $_GPC, $_W;
		$from_user = $this->getFromUser();
		// $cart = $this->getCartGoods();
		$day_cookies = 15;
		$shareid = 'eso_sale_sid07'.$_W['uniacid'];
        $uniacid= $_W['uniacid'];
		if((($_GPC['mid']!=$_COOKIE[$shareid]) && !empty($_GPC['mid']))){
			$this->shareClick($_GPC['mid']);
			setcookie($shareid, $_GPC['mid'], time()+3600*24*$day_cookies);

		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 4;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
			$_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('eso_sale_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
		}
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}
		$children = array();
		$category = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_category') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][$row['id']] = $row;
				unset($category[$index]);
			}
		}
		$recommandcategory = array();
		foreach ($category as &$c) {
			if ($c['isrecommand'] == 1) {
				$c['list'] = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}' and deleted=0 AND status = '1'  and pcate='{$c['id']}'  ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
				$c['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0  AND status = '1' and pcate='{$c['id']}'");
				$c['pager'] = pagination($c['total'], $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
				$recommandcategory[] = $c;
			}
			if (!empty($children[$c['id']])) {
				foreach ($children[$c['id']] as &$child) {
					if ($child['isrecommand'] == 1) {
						$child['list'] = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1'  and pcate='{$c['id']}' and ccate='{$child['id']}'  ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
						$child['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0  AND status = '1' and pcate='{$c['id']}' and ccate='{$child['id']}' ");
						$child['pager'] = pagination($child['total'], $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
						$recommandcategory[] = $child;
					}
				}
				unset($child);
			}
		}
		unset($c);
		$carttotal = $this->getCartTotal();

		//幻灯片
		$advs = pdo_fetchall("select * from " . tablename('eso_sale_adv') . " where enabled=1 and uniacid= '{$_W['uniacid']}'  order by displayorder asc");
		foreach ($advs as &$adv) {
			if (substr($adv['link'], 0, 5) != 'http:') {
				$adv['link'] = "http://" . $adv['link'];
			}
		}
		unset($adv);

		//首页推荐
		$rpindex = max(1, intval($_GPC['rpage']));
		$rpsize = 6;
		$condition = ' and isrecommand=1';
		//  $rlist = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY displayorder DESC, sales DESC LIMIT " . ($rpindex - 1) * $rpsize . ',' . $rpsize);
		$rlist = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY displayorder DESC, sales DESC ");

		$cfg = $this->module['config'];
		if(empty($cfg['indexss'])) {
			$cfg['indexss']=5;
		}
		$islist = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' and istime='1' ORDER BY displayorder DESC, sales DESC limit {$cfg['indexss']}");
		$member = pdo_fetch( " SELECT * FROM ".tablename('eso_sale_member')." WHERE from_user='".$from_user."' AND uniacid=".$_W['uniacid']." " );
		$logo = $cfg['logo'];
		$ydyy = $cfg['ydyy'];
		//$description = $cfg['description'];
		$shareurl = $_W['siteroot']."app/".$this->mturl('list',array('mid'=>$member['id']));
		$description = $_W['account']['name'] . '分销系统，让分佣飞一会！';
		include $this->template('list');
	}

	public function doMobilelistmore_rec() {
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 6;
		$condition = ' and isrecommand=1 ';
		$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		include $this->template('list_more');
	}

	public function doMobilelistmore() {
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 6;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
			$_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('eso_sale_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
		}
		$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}' AND status = '1' $condition ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		include $this->template('list_more');
	}

	public function doMobilelist2() {

		global $_GPC, $_W;
        $uniacid= $_W['uniacid'];
		$from_user =	$this->getFromUser();
		$pindex = max(1, intval($_GPC["page"]));
		$psize = 10;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
			$_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('eso_sale_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
		}
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}
		$sort = empty($_GPC['sort']) ? 0 : $_GPC['sort'];
		$sortfield = "displayorder asc";

		$sortb0 = empty($_GPC['sortb0']) ? "desc" : $_GPC['sortb0'];
		$sortb1 = empty($_GPC['sortb1']) ? "desc" : $_GPC['sortb1'];
		$sortb2 = empty($_GPC['sortb2']) ? "desc" : $_GPC['sortb2'];
		$sortb3 = empty($_GPC['sortb3']) ? "asc" : $_GPC['sortb3'];

		if ($sort == 0) {
			$sortb00 = $sortb0 == "desc" ? "asc" : "desc";
			$sortfield = "createtime " . $sortb0;
			$sortb11 = "desc";
			$sortb22 = "desc";
			$sortb33 = "asc";
		} else if ($sort == 1) {
			$sortb11 = $sortb1 == "desc" ? "asc" : "desc";
			$sortfield = "sales " . $sortb1;
			$sortb00 = "desc";
			$sortb22 = "desc";
			$sortb33 = "asc";
		} else if ($sort == 2) {
			$sortb22 = $sortb2 == "desc" ? "asc" : "desc";
			$sortfield = "viewcount " . $sortb2;
			$sortb00 = "desc";
			$sortb11 = "desc";
			$sortb33 = "asc";
		} else if ($sort == 3) {
			$sortb33 = $sortb3 == "asc" ? "desc" : "asc";
			$sortfield = "marketprice " . $sortb3;
			$sortb00 = "desc";
			$sortb11 = "desc";
			$sortb22 = "desc";
		}
		$sorturl = $this->createMobileUrl('list2', array("keyword" => $_GPC['keyword'], "pcate" => $_GPC['pcate'], "ccate" => $_GPC['ccate']), true);
		if (!empty($_GPC['isnew'])) {
			$condition .= " AND isnew = 1";
			$sorturl.="&isnew=1";
		}

		if (!empty($_GPC['ishot'])) {
			$condition .= " AND ishot = 1";
			$sorturl.="&ishot=1";
		}
		if (!empty($_GPC['isdiscount'])) {
			$condition .= " AND isdiscount = 1";
			$sorturl.="&isdiscount=1";
		}
		if (!empty($_GPC['istime'])) {
			$condition .= " AND istime = 1 and " . time() . ">=timestart and " . time() . "<=timeend";
			$sorturl.="&istime=1";
		}

		$children = array();



		$category = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_category') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][$row['id']] = $row;
				unset($category[$index]);
			}
		}
		//   $list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY $sortfield LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY $sortfield  ");
		foreach ($list as &$r) {
			if ($r['istime'] == 1) {
				$arr = $this->time_tran($r['timeend']);
				$r['timelaststr'] = $arr[0];
				$r['timelast'] = $arr[1];
			}
		}
		unset($r);


		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0  AND status = '1' $condition");
		$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
		$carttotal = $this->getCartTotal();
		$cfg = $this->module['config'];
		$ydyy = $cfg['ydyy'];
		$logo = $cfg['logo'];
		$description = $_W['account']['name'] . '分销系统，让分佣飞一会！';;
		include $this->template('list2');
	}

	//商品分类页面
	public function doMobilelistCategory() {
		global $_GPC, $_W;
        $uniacid= $_W['uniacid'];
		$from_user =$this->getFromUser();
		$category = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_category') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][$row['id']] = $row;
				unset($category[$index]);
			}
		}
		$carttotal = $this->getCartTotal();
		$cfg = $this->module['config'];
        $logo = $cfg['logo'];
		$ydyy = $cfg['ydyy'];
		include $this->template('list_category');
	}

	//推广页面

	public function doMobiletuiguang() {
		global $_GPC, $_W;
		$carttotal = $this->getCartTotal();
		$share = "eso_saleshareQrcode".$_W['uniacid'];
		$gid = $_GPC['gid'];
		$from_user =	$this->getFromUser();
		$goods = pdo_fetch("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE id = :id", array(':id' => $gid));
		$rule = pdo_fetchcolumn('SELECT rule FROM '.tablename('eso_sale_rules')." WHERE uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		$id = $profile['id'];
		if(intval($profile['id']) && $profile['status']==0){
			include $this->template('forbidden');
			exit;
		}
		if(empty($profile)){
			$rule = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE `uniacid` = :uniacid ",array(':uniacid' => $_W['uniacid']));

			include $this->template('register');
			exit;
		}
		/*
	$share = $id."eso_saleshareQrcode".$_W['uniacid'];	

		include "mobile/phpqrcode.php";//引入PHP QR库文件
		$value = $_W['siteroot']."app/".$this->mturl('detail',array('mid'=>$id,'id'=>$gid));
		$errorCorrectionLevel = "L";
		$matrixPointSize = "4";
		$imgname = "share.png";
		$imgurl = "../addons/eso_sale/erweima/$imgname";
		$a = QRcode::png($value, $imgurl, $errorCorrectionLevel, $matrixPointSize);
		//setCookie($share, $_W['uniacid']."share".$id, time()+3600*24);
	//include $this->template('tuiguang');*/
		$cfg = $this->module['config'];

		$logo = $cfg['logo'];
		$description = $_W['account']['name'] . '分销系统，让分佣飞一会！';;
		$shareurl = $_W['siteroot']."app/".$this->mturl('detail',array('mid'=>$id,'id'=>$gid));
		include $this->template('tgym');
	}



	function time_tran($the_time) {

		$timediff = $the_time - time();
		$days = intval($timediff / 86400);
		if (strlen($days) <= 1) {
			$days = "0" . $days;
		}
		$remain = $timediff % 86400;
		$hours = intval($remain / 3600);
		;
		if (strlen($hours) <= 1) {
			$hours = "0" . $hours;
		}
		$remain = $remain % 3600;
		$mins = intval($remain / 60);
		if (strlen($mins) <= 1) {
			$mins = "0" . $mins;
		}
		$secs = $remain % 60;
		if (strlen($secs) <= 1) {
			$secs = "0" . $secs;
		}
		$ret = "";
		if ($days > 0) {
			$ret.=$days . " 天 ";
		}
		if ($hours > 0) {
			$ret.=$hours . ":";
		}
		if ($mins > 0) {
			$ret.=$mins . ":";
		}

		$ret.=$secs;

		return array("倒计时 " . $ret, $timediff);
	}
	public function doMobileMyfans() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		//	$count1_1 = pdo_fetchcolumn("select count(id) from ".tablename('eso_sale_member')." where shareid = ".$profile['id']);
		$count1 = pdo_fetchcolumn("select count(*) from ("."select from_user from ".tablename('eso_sale_order')." where  shareid = ".$profile['id'].'  group by from_user'.") x");

		$count1_2 = pdo_fetchcolumn("select count(mber.id) from ".tablename('eso_sale_member')." mber where mber.shareid = ".$profile['id']." and mber.from_user not in ("."select orders.from_user from ".tablename('eso_sale_order')." orders where  orders.shareid = ".$profile['id']." group by from_user)");
		$count1=$count1+$count1_2;
		if($count1>0)
		{
			$countall = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$profile['id']);
			$count2=0;
			$count3=0;
			foreach ($countall as &$citem){
				$tcount2 = pdo_fetchcolumn("select count(id) from ".tablename('eso_sale_member')." where shareid = ".$citem);
				$count2=$count2+$tcount2;
				$count2all = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$citem);
				foreach ($count2all as &$citem2){

					$tcount3 = pdo_fetchcolumn("select count(*) from ("."select from_user from ".tablename('eso_sale_order')." where  shareid = ".$citem2.' and shareid!='.$citem.' and shareid!='.$profile['id'].' group by from_user'.") y"  );


					$count3=$count3+$tcount3;
				}


			}
		}else
		{
			$count1=0;
			$count2=0;
			$count3=0;
		}
		include $this->template('myfans');
	}

	public function doMobileMyfansDetail() {
		global $_W, $_GPC;
		$level=$_GPC['level'];
		$from_user =	$this->getFromUser();
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));

		if($level=='1')
		{
			$fansall=array();
			$fansall[0] = pdo_fetchall("select mc_members.* from ".tablename('mc_members')." mc_members where mc_members.uid in ("."select orders.uid from ".tablename('eso_sale_order')." orders where  orders.shareid = ".$profile['id'].'  group by orders.from_user'.")");
			$fansall[1] =  pdo_fetchall("select mc_members.* from ".tablename('mc_members')." mc_members where mc_members.uid in ("."select mber.uid from ".tablename('eso_sale_member')." mber where mber.shareid = ".$profile['id']." and mber.from_user not in ("."select orders.from_user from ".tablename('eso_sale_order')." orders where  orders.shareid = ".$profile['id']." group by orders.from_user)".")");


		}
		if($level=='2')
		{
			$fansall=array();

			$countall = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$profile['id']);

			$rowindex =0;
			foreach ($countall as &$citem){

				$fansall[$rowindex] = pdo_fetchall("select mc_members.* from ".tablename('mc_members')." mc_members where mc_members.uid in ("."select mber.uid from ".tablename('eso_sale_member')." mber where mber.shareid = ".$citem.")");



			}
		}
		if($level=='3')
		{
			$countall = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$profile['id']);

			$fansall=array();
			$rowindex =0;
			foreach ($countall as &$citem){
				$count2all = pdo_fetch("select id from ".tablename('eso_sale_member')." where shareid = ".$citem);
				$str="";
				foreach ($count2all as &$citem2){
					$str=$str.$citem2.',';
				}
				$mids='('.$str.'0'.')';
				$fansall[$rowindex] = pdo_fetchall("select mc_members.* from ".tablename('mc_members')." mc_members where mc_members.uid in ("."select orders.uid from ".tablename('eso_sale_order')." orders where  orders.shareid = ".$citem2.' and orders.shareid!='.$citem.' and orders.shareid!='.$profile['id'].' group by orders.from_user'.")" );
				$rowindex=$rowindex+1;
			}
		}
		include $this->template('myfansDetail');
	}


	public function doMobileMyCart() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();
		$op = $_GPC['op'];
		if ($op == 'add') {
			$goodsid = intval($_GPC['id']);
			$total = intval($_GPC['total']);
			$total = empty($total) ? 1 : $total;
			$optionid = intval($_GPC['optionid']);
			$goods = pdo_fetch("SELECT id, type, total,marketprice,maxbuy FROM " . tablename('eso_sale_goods') . " WHERE id = :id", array(':id' => $goodsid));
			if (empty($goods)) {
				$result['message'] = '抱歉，该商品不存在或是已经被删除！';
				message($result, '', 'ajax');
			}
			$marketprice = $goods['marketprice'];
			if (!empty($optionid)) {
				$option = pdo_fetch("select marketprice from " . tablename('eso_sale_goods_option') . " where id=:id limit 1", array(":id" => $optionid));
				if (!empty($option)) {
					$marketprice = $option['marketprice'];
				}
			}

			$row = pdo_fetch("SELECT id, total FROM " . tablename('eso_sale_cart') . " WHERE from_user = :from_user AND uniacid = '{$_W['uniacid']}' AND goodsid = :goodsid  and optionid=:optionid", array(':from_user' => $from_user, ':goodsid' => $goodsid,':optionid'=>$optionid));
			if ($row == false) {
				//不存在
				$data = array(
					'uniacid' => $_W['uniacid'],
					'goodsid' => $goodsid,
					'goodstype' => $goods['type'],
					'marketprice' => $marketprice,
					'from_user' => $from_user,
					'total' => $total,
					'optionid' => $optionid
				);
				pdo_insert('eso_sale_cart', $data);
			} else {
				//累加最多限制购买数量
				$t = $total + $row['total'];
				if (!empty($goods['maxbuy'])) {
					if ($t > $goods['maxbuy']) {
						$t = $goods['maxbuy'];
					}
				}
				//存在
				$data = array(
					'marketprice' => $marketprice,
					'total' => $t,
					'optionid' => $optionid
				);
				pdo_update('eso_sale_cart', $data, array('id' => $row['id']));
			}

			//返回数据
			$carttotal = $this->getCartTotal();

			$result = array(
				'result' => 1,
				'total' => $carttotal
			);
			die(json_encode($result));
		} else if ($op == 'clear') {
			pdo_delete('eso_sale_cart', array('from_user' => $from_user, 'uniacid' => $_W['uniacid']));
			die(json_encode(array("result" => 1)));
		} else if ($op == 'remove') {
			$id = intval($_GPC['id']);
			pdo_delete('eso_sale_cart', array('from_user' => $from_user, 'uniacid' => $_W['uniacid'], 'id' => $id));
			die(json_encode(array("result" => 1, "cartid" => $id)));
		} else if ($op == 'update') {
			$id = intval($_GPC['id']);
			$num = intval($_GPC['num']);
			$sql = "update " . tablename('eso_sale_cart') . " set total=$num where id=:id";
			pdo_query($sql, array(":id" => $id));
			die(json_encode(array("result" => 1)));
		} else {
			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_cart') . " WHERE  uniacid = '{$_W['uniacid']}' AND from_user = '".$from_user."'");
			$totalprice = 0;
			if (!empty($list)) {
				foreach ($list as &$item) {
					$goods = pdo_fetch("SELECT  title, thumb, marketprice, unit, total,maxbuy FROM " . tablename('eso_sale_goods') . " WHERE id=:id limit 1", array(":id" => $item['goodsid']));
					//属性
					$option = pdo_fetch("select title,marketprice,stock from " . tablename("eso_sale_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
					if ($option) {
						$goods['title'] = $goods['title'];
						$goods['optionname'] = $option['title'];
						$goods['marketprice'] = $option['marketprice'];
						$goods['total'] = $option['stock'];
					}
					$item['goods'] = $goods;
					$item['totalprice'] = (floatval($goods['marketprice']) * intval($item['total']));
					$totalprice += $item['totalprice'];
				}
				unset($item);
			}
			include $this->template('cart');
		}
	}

	public function doMobileConfirm() {
		global $_W,$_GPC;
		$from_user =$this->getFromUser();
		$uniacid=$_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';

		$totalprice = 0;
		$allgoods = array();

		$id = intval($_GPC['id']);
		$optionid = intval($_GPC['optionid']);
		$total = intval($_GPC['total']);
		if (empty($total)) {
			$total = 1;
		}
		$direct = false; //是否是直接购买
		$returnurl = ""; //当前连接

		if (!empty($id)) {
			$item = pdo_fetch("select id,thumb,ccate,title,weight,marketprice,total,type,totalcnf,sales,unit,istime,timeend from " . tablename("eso_sale_goods") . " where id=:id limit 1", array(":id" => $id));

			if ($item['istime'] == 1) {
				if (time() > $item['timeend']) {
					message('抱歉，商品限购时间已到，无法购买了！', referer(), "error");
				}
			}

			if (!empty($optionid)) {
				$option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("eso_sale_goods_option") . " where id=:id limit 1", array(":id" => $optionid));
				if ($option) {
					$item['optionid'] = $optionid;
					$item['title'] = $item['title'];
					$item['optionname'] = $option['title'];
					$item['marketprice'] = $option['marketprice'];
					$item['weight'] = $option['weight'];
				}
			}
			$item['stock'] = $item['total'];
			$item['total'] = $total;
			$item['totalprice'] = $total * $item['marketprice'];
			$allgoods[] = $item;
			$totalprice+= $item['totalprice'];
			if ($item['type'] == 1) {
				$needdispatch = true;
			}
			$direct = true;
			$returnurl = $this->mturl("confirm", array("id" => $id, "optionid" => $optionid, "total" => $total));
		}
		if (!$direct) {
			//如果不是直接购买（从购物车购买）
			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_cart') . " WHERE  uniacid = '{$_W['uniacid']}' AND from_user = '".$from_user."'");
			if (!empty($list)) {
				foreach ($list as &$g) {
					$item = pdo_fetch("select id,thumb,ccate,title,weight,marketprice,total,type,totalcnf,sales,unit from " . tablename("eso_sale_goods") . " where id=:id limit 1", array(":id" => $g['goodsid']));
					//属性
					$option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("eso_sale_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
					if ($option) {
						$item['optionid'] = $g['optionid'];
						$item['title'] = $item['title'];
						$item['optionname'] = $option['title'];
						$item['marketprice'] = $option['marketprice'];
						$item['weight'] = $option['weight'];
					}
					$item['stock'] = $item['total'];
					$item['total'] = $g['total'];
					$item['totalprice'] = $g['total'] * $item['marketprice'];
					$allgoods[] = $item;
					$totalprice+= $item['totalprice'];
					if ($item['type'] == 1) {
						$needdispatch = true;
					}
				}
				unset($g);
			}
			$returnurl = $this->mturl("confirm");
		}

		if (count($allgoods) <= 0) {
			header("location: " . $this->mturl('myorder'));
			exit();
		}
		//配送方式
		$dispatch = pdo_fetchall("select id,dispatchname,firstprice,firstweight,secondprice,secondweight from " . tablename("eso_sale_dispatch") . " WHERE uniacid = {$_W['uniacid']} order by displayorder desc");
		foreach ($dispatch as &$d) {

			$weight = 0;

			foreach ($allgoods as $g) {
				$weight+=$g['weight'] * $g['total'];
			}

			$price = 0;
			if ($weight <= $d['firstweight']) {
				$price = $d['firstprice'];
			} else {
				$price = $d['firstprice'];
				$secondweight = $weight - $d['firstweight'];
				if ($secondweight % $d['secondweight'] == 0) {
					$price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
				} else {
					$price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
				}
			}
			$d['price'] = $price;
		}
		unset($d);

		if (checksubmit('submit')) {
			$address = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id = :id", array(':id' => intval($_GPC['address'])));
			if (empty($address)) {
				message('抱歉，请您填写收货地址！');
			}
			//商品价格
			$goodsprice = 0;
			foreach ($allgoods as $row) {
				if ($item['stock'] != -1 && $row['total'] > $item['stock']) {
					message('抱歉，“' . $row['title'] . '”此商品库存不足！', $this->mturl('confirm'), 'error');
				}
				$goodsprice+= $row['totalprice'];
			}
			//运费
			$dispatchid = intval($_GPC['dispatch']);
			$dispatchprice = 0;
			foreach ($dispatch as $d) {
				if ($d['id'] == $dispatchid) {
					$dispatchprice = $d['price'];
				}
			}

			$shareId =	$this->getShareId();
			$data = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $_W['member']['uid'],
				'from_user' => $from_user,
				'ordersn' => date('md') . random(4, 1),
				'price' => $goodsprice + $dispatchprice,
				'dispatchprice' => $dispatchprice,
				'goodsprice' => $goodsprice,
				'status' => 0,
				'sendtype' => intval($_GPC['sendtype']),
				'dispatch' => $dispatchid,
				'paytype' => '2',
				'goodstype' => intval($cart['type']),
				'remark' => $_GPC['remark'],
				'addressid' => $address['id'],
				'createtime' => TIMESTAMP,								'shareid' => $shareId
			);
			pdo_insert('eso_sale_order', $data);
			$orderid = pdo_insertid();
			//插入订单商品
			foreach ($allgoods as $row) {
				if (empty($row)) {
					continue;
				}
				$d = array(
					'uniacid' => $_W['uniacid'],
					'goodsid' => $row['id'],
					'orderid' => $orderid,
					'total' => $row['total'],
					'price' => $row['marketprice'],
					'createtime' => TIMESTAMP,
					'optionid' => $row['optionid']
				);
				$o = pdo_fetch("select title from ".tablename('eso_sale_goods_option')." where id=:id limit 1",array(":id"=>$row['optionid']));
				if(!empty($o)){
					$d['optionname'] = $o['title'];
				}
				//获取商品id
				$ccate = $row['ccate'];
				//$commission = pdo_fetchcolumn( " SELECT commission FROM ".tablename('eso_sale_category')."  WHERE id=".$ccate);
				$commission = pdo_fetchcolumn( " SELECT commission FROM ".tablename('eso_sale_goods')."  WHERE id=".$row['id']);
				$commission2 = pdo_fetchcolumn( " SELECT commission2 FROM ".tablename('eso_sale_goods')."  WHERE id=".$row['id']);
				$commission3 = pdo_fetchcolumn( " SELECT commission3 FROM ".tablename('eso_sale_goods')."  WHERE id=".$row['id']);

				if($commission == false || $commission == null || $commission <0){
					$commission = $this->module['config']['globalCommission'];
				}
				if($commission2 == false || $commission2 == null || $commission2 <0){
					$commission2 = $this->module['config']['globalCommission2'];
				}
				if($commission3 == false || $commission3 == null || $commission3 <0){
					$commission3 = $this->module['config']['globalCommission3'];
				}
				$commissionTotal = $row['marketprice']  * $commission /100;
				$d['commission'] = $commissionTotal;
				$commissionTotal2 = $commissionTotal  * $commission2 /100;
				$d['commission2'] = $commissionTotal2;
				$commissionTotal3 = $commissionTotal2  * $commission3 /100;
				$d['commission3'] = $commissionTotal3;
				pdo_insert('eso_sale_order_goods', $d);
			}
			//清空购物车
			if (!$direct) {
				pdo_delete("eso_sale_cart", array("uniacid" => $_W['uniacid'], "from_user" => $from_user));
			}
			//$this->setCartGoods(array());
			//变更商品库存
			$this->setOrderStock($orderid);

			//
			//  message('提交订单成功，现在跳转至付款页面...', $this->mturl('pay', array('orderid' => $orderid)), 'success');

			die("<script>alert('提交订单成功,现在跳转到付款页面...');location.href='" . $this->mturl('pay', array('orderid' => $orderid)) . "';</script>");
		}
		$carttotal = $this->getCartTotal();
		$profile = fans_search($from_user, array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE isdefault = 1 and openid = :openid limit 1", array(':openid' => $from_user));
		include $this->template('confirm');
	}

	//设置订单积分
	public function setOrderCredit($orderid, $add = true) {
		global $_W;
		$order = pdo_fetch("SELECT * FROM " . tablename('eso_sale_order') . " WHERE id = :id limit 1", array(':id' => $orderid));
		if (empty($order)) {
			return false;
		}
		$sql = 'SELECT `goodsid`, `total` FROM ' . tablename('eso_sale_order_goods') . ' WHERE `orderid` = :orderid';
		$orderGoods = pdo_fetchall($sql, array(':orderid' => $orderid));
		if (!empty($orderGoods)) {
			$credit = 0;
			$sql = 'SELECT `credit` FROM ' . tablename('eso_sale_goods') . ' WHERE `id` = :id';
			foreach ($orderGoods as $goods) {
				$goodsCredit = pdo_fetchcolumn($sql, array(':id' => $goods['goodsid']));
				$credit += $goodsCredit * $goods['total'];
			}
		}
		//增加积分
		if (!empty($credit)) {
			load()->model('mc');
			load()->func('compat.biz');
			$uid = mc_openid2uid($order['from_user']);
			$fans = fans_search($uid, array("credit1"));
			if (!empty($fans)) {
				if (!empty($add)) {
					mc_credit_update($_W['member']['uid'], 'credit1', $credit, array('0' => $_W['member']['uid'], '购买商品赠送'));
				} else {
					mc_credit_update($_W['member']['uid'], 'credit1', 0 - $credit, array('0' => $_W['member']['uid'], '微商城操作'));
				}
			}
		}
	}

	public function doMobilePay() {
		global $_W, $_GPC;
		$from_user =$this->getFromUser();
		$orderid = intval($_GPC['orderid']);
		$order = pdo_fetch("SELECT * FROM " . tablename('eso_sale_order') . " WHERE id = :id", array(':id' => $orderid));
		if ($order['status'] != '0') {
			message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
		}
		if (checksubmit('codsubmit')) {

			$ordergoods = pdo_fetchall("SELECT goodsid, total,optionid FROM " . tablename('eso_sale_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
			if (!empty($ordergoods)) {
				$goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total,credit FROM " . tablename('eso_sale_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
			}



			//邮件提醒
			if (!empty($this->module['config']['noticeemail'])) {

				$address = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id = :id", array(':id' => $order['addressid']));

				$body = "<h3>购买商品清单</h3> <br />";

				if (!empty($goods)) {
					foreach ($goods as $row) {

						//属性
						// $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("eso_sale_goods_option") . " where id=:id limit 1", array(":id" => $ordergoods[$row['id']]['optionid']));
						//if ($option) {
						//    $row['title'] = "[" . $option['title'] . "]" . $row['title'];
						//}
						$body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} <br />";
					}
				}
				$paytype = $order['paytype']=='3'?'货到付款':'已付款';
				$body .= "<br />总金额：{$order['price']}元 （{$paytype}）<br />";
				$body .= "<h3>购买用户详情</h3> <br />";
				$body .= "真实姓名：$address[realname] <br />";
				$body .= "地区：$address[province] - $address[city] - $address[area]<br />";
				$body .= "详细地址：$address[address] <br />";
				$body .= "手机：$address[mobile] <br />";
				load()->func('communication');
				ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
			}

			pdo_update('eso_sale_order', array('status' => '1', 'paytype' => '3'), array('id' => $orderid));


			message('订单提交成功，请您收到货时付款！', $this->createMobileUrl('myorder'), 'success');
		}

		if (checksubmit()) {
			if ($order['paytype'] == 1 && $_W['fans']['credit2'] < $order['price']) {
				message('抱歉，您帐户的余额不够支付该订单，请充值！', create_url('mobile/module/charge', array('name' => 'member', 'uniacid' => $_W['uniacid'])), 'error');
			}
			if ($order['price'] == '0') {
				$this->payResult(array('tid' => $orderid, 'from' => 'return', 'type' => 'credit2'));
				exit;
			}
		}
		// 商品编号
		$sql = 'SELECT `goodsid` FROM ' . tablename('eso_sale_order_goods') . " WHERE `orderid` = :orderid";
		$goodsId = pdo_fetchcolumn($sql, array(':orderid' => $orderid));
		// 商品名称
		$sql = 'SELECT `title` FROM ' . tablename('eso_sale_goods') . " WHERE `id` = :id";
		$goodsTitle = pdo_fetchcolumn($sql, array(':id' => $goodsId));

		$params['tid'] = $orderid;
		$params['user'] = $from_user;
		$params['fee'] = $order['price'];
		$params['title'] = $goodsTitle;
		$params['ordersn'] = $order['ordersn'];
		$params['virtual'] = $order['goodstype'] == 2 ? true : false;
		include $this->template('pay');
	}
	private function sendMobilePayMsg($order,$goods,$paytype,$ordergoods)
	{

		$address = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id = :id", array(':id' => $order['addressid']));
		$cfg = $this->module['config'];
		$template_id=$cfg['paymsgTemplateid'];
		if (!empty($template_id)) {
			include  'messagetemplate/pay.php';
			$this->sendtempmsg($template_id, '', $data, '#FF0000');
		}

	}

	public function doMobileContactUs() {
		global $_W;
		$cfg = $this->module['config'];

		include $this->template('contactus');
	}

	public function doMobileMyOrder() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();
		$op = $_GPC['op'];
		if ($op == 'confirm') {
			$orderid = intval($_GPC['orderid']);
			$order = pdo_fetch("SELECT * FROM " . tablename('eso_sale_order') . " WHERE id = :id AND from_user = :from_user", array(':id' => $orderid, ':from_user' => $from_user ));
			if (empty($order)) {
				message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
			}
			pdo_update('eso_sale_order', array('status' => 3), array('id' => $orderid, 'from_user' => $from_user ));
			message('确认收货完成！', $this->mturl('myorder'), 'success');
		} else if ($op == 'detail') {

			$orderid = intval($_GPC['orderid']);
			$item = pdo_fetch("SELECT * FROM " . tablename('eso_sale_order') . " WHERE uniacid = '{$_W['uniacid']}' AND from_user = '".$from_user."' and id='{$orderid}' limit 1");
			if (empty($item)) {
				message('抱歉，您的订单不存或是已经被取消！', $this->mturl('myorder'), 'error');
			}
			$goodsid = pdo_fetchall("SELECT goodsid,total FROM " . tablename('eso_sale_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');

			$goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice,o.total,o.optionid FROM " . tablename('eso_sale_order_goods') . " o left join " . tablename('eso_sale_goods') . " g on o.goodsid=g.id "
				. " WHERE o.orderid='{$orderid}'");
			foreach ($goods as &$g) {
				//属性
				$option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("eso_sale_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
				if ($option) {
					$g['title'] = "[" . $option['title'] . "]" . $g['title'];
					$g['marketprice'] = $option['marketprice'];
				}
			}
			unset($g);

			$dispatch = pdo_fetch("select id,dispatchname from " . tablename('eso_sale_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
			include $this->template('order_detail');
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			$status = intval($_GPC['status']);
			$where = " uniacid = '{$_W['uniacid']}' AND from_user = '".$from_user."'";
			;
			if ($status == 2) {
				$where.=" and ( status=1 or status=2 )";
			} else {
				$where.=" and status=$status";
			}

			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('eso_sale_order') . " WHERE uniacid = '{$_W['uniacid']}' AND from_user = '".$from_user."'");
			$pager = pagination($total, $pindex, $psize);

			if (!empty($list)) {
				foreach ($list as &$row) {
					$goodsid = pdo_fetchall("SELECT goodsid,total FROM " . tablename('eso_sale_order_goods') . " WHERE orderid = '{$row['id']}'", array(), 'goodsid');
					$goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice,o.total,o.optionid FROM " . tablename('eso_sale_order_goods') . " o left join " . tablename('eso_sale_goods') . " g on o.goodsid=g.id "
						. " WHERE o.orderid='{$row['id']}'");
					foreach ($goods as &$item) {
						//属性
						$option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("eso_sale_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
						if ($option) {
							$item['title'] = "[" . $option['title'] . "]" . $item['title'];
							$item['marketprice'] = $option['marketprice'];
						}
					}
					unset($item);
					$row['goods'] = $goods;
					$row['total'] = $goodsid;
					$row['dispatch'] = pdo_fetch("select id,dispatchname from " . tablename('eso_sale_dispatch') . " where id=:id limit 1", array(":id" => $row['dispatch']));
				}
			}
			$carttotal = $this->getCartTotal();
			load()->model('mc');
			$fans = mc_fetch($_W['member']['uid']);
			include $this->template('order');
		}
	}
	private function shareClick($mid)
	{
		global $_W, $_GPC;
		//	$fromuser= $_SERVER["REMOTE_ADDR"];
		$fromuser =	$this->getFromUser();
		$share = pdo_fetch("SELECT * FROM " . tablename('eso_sale_share_history') . " WHERE sharemid =:mid and from_user=:from_user and uniacid=:uniacid", array(':mid' => $mid,':from_user' =>$fromuser,':uniacid' => $_W['uniacid']));

		$member = pdo_fetch('SELECT * FROM ' . tablename('eso_sale_member') . " WHERE uniacid = '{$_W['uniacid']}' AND id = '{$mid}'");

		if(empty($share))
		{
			if((!empty($member)))
			{
				$data = array(
					'uniacid' => $_W['uniacid'],
					'from_user' => $fromuser,
					'sharemid' => $mid
				);
				pdo_insert('eso_sale_share_history', $data);
				pdo_update('eso_sale_member', array('clickcount' => $member['clickcount']+1), array('id' => $mid));
				$theone = pdo_fetch('SELECT * FROM '.tablename('eso_sale_rules')." WHERE  uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));

				if((!empty($theone['clickcredit'])))
				{

					$fans = pdo_fetch('SELECT * FROM '.tablename('mc_mapping_fans')." WHERE  uniacid = :uniacid and openid=:openid" , array(':uniacid' => $_W['uniacid'],':openid' => $member['from_user']));
					if((!empty($fans)))
					{
						pdo_update('mc_members', array('credit1' => $fans['credit1']+$theone['clickcredit']), array('uid' => $fans['uid']));
					}
				}

			}
		}


	}
	public function doMobileDetail() {
		global $_W, $_GPC;
		$from_user = $this->getFromUser();
		$day_cookies = 15;
		$shareid = 'eso_sale_sid07'.$_W['uniacid'];
		
		$carttotal = $this->getCartTotal();
		$share = "eso_saleshareQrcode".$_W['uniacid'];
		$gid = $_GPC['gid'];
		$from_user =	$this->getFromUser();
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		$id = $profile['id'];
		
		if((($_GPC['mid']!=$_COOKIE[$shareid]) && !empty($_GPC['mid']))){
			$this->shareClick($_GPC['mid']);
			setcookie($shareid, $_GPC['mid'], time()+3600*24*$day_cookies);

		}

		$goodsid = intval($_GPC['id']);
		$goods = pdo_fetch("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE id = :id", array(':id' => $goodsid));
		$ccate = intval($goods['ccate']);
		//$commission = pdo_fetchcolumn( " SELECT commission FROM ".tablename('eso_sale_category')." WHERE id=".$ccate." " );
		$commission = pdo_fetchcolumn( " SELECT commission FROM ".tablename('eso_sale_goods')." WHERE id=".$goodsid." " );
		$member = pdo_fetch( " SELECT * FROM ".tablename('eso_sale_member')." WHERE from_user='".$from_user."' AND uniacid=".$_W['uniacid']." " );
		if($commission == false || $commission == null || $commission <0){
			$commission = $this->module['config']['globalCommission'];

		}
		if (empty($goods)) {
			message('抱歉，商品不存在或是已经被删除！');
		}
		if ($goods['istime'] == 1) {
			$backUrl = $this->createMobileUrl('list');
			$backUrl = $_W['siteroot'] . 'app' . ltrim($backUrl, '.');
			if (time() < $goods['timestart']) {
				message('抱歉，还未到购买时间, 暂时无法购物哦~', $backUrl, "error");
			}
			if (time() > $goods['timeend']) {
				message('抱歉，商品限购时间已到，不能购买了哦~', $backUrl, "error");
			}
		}
		//浏览量
		pdo_query("update " . tablename('eso_sale_goods') . " set viewcount=viewcount+1 where id=:id and uniacid='{$_W['uniacid']}' ", array(":id" => $goodsid));
		$piclist1 = array(array("attachment" => $goods['thumb']));
		$piclist = array();
		if (is_array($piclist1)) {
			foreach($piclist1 as $p){
				$piclist[] = is_array($p)?$p['attachment']:$p;
			}
		}
		if ($goods['thumb_url'] != 'N;') {
			$urls = unserialize($goods['thumb_url']);
			if (is_array($urls)) {
				foreach($urls as $p){
					$piclist[]  = is_array($p)?$p['attachment']:$p;
				}
			}
		}
		$marketprice = $goods['marketprice'];
		$productprice= $goods['productprice'];
		$originalprice = $goods['originalprice'];
		$stock = $goods['total'];


		//规格及规格项
		$allspecs = pdo_fetchall("select * from " . tablename('eso_sale_spec') . " where goodsid=:id order by displayorder asc", array(':id' => $goodsid));
		foreach ($allspecs as &$s) {
			$s['items'] = pdo_fetchall("select * from " . tablename('eso_sale_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(":specid" => $s['id']));
		}
		unset($s);

		//处理规格项
		$options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('eso_sale_goods_option') . " where goodsid=:id order by id asc", array(':id' => $goodsid));

		//排序好的specs
		$specs = array();
		//找出数据库存储的排列顺序
		if (count($options) > 0) {
			$specitemids = explode("_", $options[0]['specs'] );
			foreach($specitemids as $itemid){
				foreach($allspecs as $ss){
					$items=  $ss['items'];
					foreach($items as $it){
						if($it['id']==$itemid){
							$specs[] = $ss;
							break;
						}
					}
				}
			}
		}

		if (!empty($goods['hasoption'])) {
			$options = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods_option') . " WHERE goodsid=:goodsid order by thumb asc,displayorder asc", array(":goodsid" => $goods['id']));
			foreach ($options as $o) {
				if ($marketprice >= $o['marketprice']) {
					$marketprice = $o['marketprice'];
				}
				if ($productprice >= $o['productprice']) {
					$productprice = $o['productprice'];
				}
				if ($stock <= $o['stock']) {
					$stock = $o['stock'];
				}
			}
		}
		$params = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods_param') . " WHERE goodsid=:goodsid order by displayorder asc", array(":goodsid" => $goods['id']));
		$carttotal = $this->getCartTotal();
		$rmlist = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_goods') . " WHERE uniacid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' and ishot='1' ORDER BY displayorder DESC, sales DESC limit 4 ");
		$member = pdo_fetch( " SELECT * FROM ".tablename('eso_sale_member')." WHERE from_user='".$from_user."' AND uniacid=".$_W['uniacid']." " );
		$cfg = $this->module['config'];
        $logo = $cfg['logo'];
		$ydyy = $cfg['ydyy'];
		$shareurl = $_W['siteroot']."app/".$this->mturl('detail',array('mid'=>$member['id'],'id'=>$goodsid));
		$description = $_W['account']['name'] . '分销系统，让分佣飞一会！';;
		include $this->template('detail');
	}

	public function doMobileCheck() {
		global $_W;
		checkauth();
	}

	public function doMobileAddress() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();
		$from = $_GPC['from'];
		$returnurl = urldecode($_GPC['returnurl']);
		$this->checkAuth();
		// $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'post';
		$operation = $_GPC['op'];

		if ($operation == 'post') {
			$id = intval($_GPC['id']);
			$data = array(
				'uniacid' => $_W['uniacid'],
				'openid' => $from_user,
				'realname' => $_GPC['realname'],
				'mobile' => $_GPC['mobile'],
				'province' => $_GPC['province'],
				'city' => $_GPC['city'],
				'area' => $_GPC['area'],
				'address' => $_GPC['address'],
			);
			if (empty($_GPC['realname']) || empty($_GPC['mobile']) || empty($_GPC['address'])) {
				message('请输完善您的资料！');
			}
			if (!empty($id)) {
				unset($data['uniacid']);
				unset($data['openid']);
				pdo_update('eso_sale_address', $data, array('id' => $id));
				message($id, '', 'ajax');
			} else {
				pdo_update('eso_sale_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $from_user));
				$data['isdefault'] = 1;
				pdo_insert('eso_sale_address', $data);
				$profile = fans_search($from_user, array('realname', 'mobile'));
				if(empty($profile['realname'])|| empty($profile['mobile'])){

					fans_update($from_user, array("mobile" => $_GPC['mobile']));

				}
				$id = pdo_insertid();
				if (!empty($id)) {
					message($id, '', 'ajax');
				} else {
					message(0, '', 'ajax');
				}
			}
		} elseif ($operation == 'default') {
			$id = intval($_GPC['id']);
			$address = pdo_fetch("select isdefault from " . tablename('eso_sale_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
			if(!empty($address) && empty($address['isdefault'])){
			pdo_update('eso_sale_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' =>$from_user));
			pdo_update('eso_sale_address', array('isdefault' => 1), array('id' => $id));
			}
			message(1, '', 'ajax');
		} elseif ($operation == 'detail') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id, realname, mobile, province, city, area, address FROM " . tablename('eso_sale_address') . " WHERE id = :id", array(':id' => $id));
			message($row, '', 'ajax');
		} elseif ($operation == 'remove') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$address = pdo_fetch("select isdefault from " . tablename('eso_sale_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and openid='".$from_user."' limit 1 ");

				if (!empty($address)) {
					//pdo_delete("eso_sale_address",  array('id'=>$id, 'uniacid' => $_W['uniacid'], 'openid' => $from_user));
					//修改成不直接删除，而设置deleted=1
					pdo_update("eso_sale_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'uniacid' => $_W['uniacid'], 'openid' => $from_user));

					if ($address['isdefault'] == 1) {
						//如果删除的是默认地址，则设置是新的为默认地址
						$maxid = pdo_fetchcolumn("select max(id) as maxid from " . tablename('eso_sale_address') . " where uniacid='{$_W['uniacid']}' and openid='".$from_user."' limit 1 ");
						if (!empty($maxid)) {
							pdo_update('eso_sale_address', array('isdefault' => 1), array('id' => $maxid, 'uniacid' => $_W['uniacid'], 'openid' => $from_user));
							die(json_encode(array("result" => 1, "maxid" => $maxid)));
						}
					}
				}
			}
			die(json_encode(array("result" => 1, "maxid" => 0)));
		} else {
			$profile = fans_search($from_user, array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
			$address = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $from_user));
			$carttotal = $this->getCartTotal();
			include $this->template('address');
		}
	}

	private function checkAuth() {
		global $_W;
		checkauth();
	}

	private function changeWechatSend($id, $status, $msg = '') {
		global $_W;
		$paylog = pdo_fetch("SELECT plid, openid, tag, eso_tag FROM " . tablename('core_paylog') . " WHERE tid = '{$id}' AND status = 1 AND type = 'wechat'");
		if (!empty($paylog['openid'])) {
			$paylog['tag'] = iunserializer($paylog['tag']);
			$acid = $paylog['tag']['acid'];
			$account = account_fetch($acid);
			$payment = uni_setting($account['uniacid'], 'payment');
			if ($payment['payment']['wechat']['version'] == '2') {
				return true;
			}
			$send = array(
				'appid' => $_W['account']['payment']['wechat']['appid'],
					'openid' => $paylog['openid'],
					'transid' => $paylog['tag']['transaction_id'],
					'out_trade_no' => $paylog['plid'],
					'deliver_timestamp' => TIMESTAMP,
					'deliver_status' => $status,
					'deliver_msg' => $msg,
			);
			$sign = $send;
			$sign['appkey'] = $_W['account']['payment']['wechat']['signkey'];
			ksort($sign);
			$string = '';
			foreach ($sign as $key => $v) {
				$key = strtolower($key);
				$string .= "{$key}={$v}&";
			}
			$send['app_signature'] = sha1(rtrim($string, '&'));
			$send['sign_method'] = 'sha1';
			$account = WeAccount::create($acid);
			$response = $account->changeOrderStatus($send);
			if (is_error($response)) {
				message($response['message']);
			}
		}
	}

	public function payResult($params) {
		global $_W;
		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		$paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');
		$data['paytype'] = $paytype[$params['type']];
		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}
		if ($params['type'] == 'delivery') {
			$data['status'] = 1;
		}
		$sql = 'SELECT `goodsid` FROM ' . tablename('eso_sale_order_goods') . ' WHERE `orderid` = :orderid';
		$goodsId = pdo_fetchcolumn($sql, array(':orderid' => $params['tid']));
		$sql = 'SELECT `total`, `totalcnf` FROM ' . tablename('eso_sale_goods') . ' WHERE `id` = :id';
		$goodsInfo = pdo_fetch($sql, array(':id' => $goodsId));
		// 更改库存
		if ($goodsInfo['totalcnf'] == '1' && !empty($goodsInfo['total'])) {
			pdo_update('eso_sale_goods', array('total' => $goodsInfo['total'] - 1), array('id' => $goodsId));
		}
		pdo_update('eso_sale_order', $data, array('id' => $params['tid']));
		if ($params['from'] == 'return') {
			//积分变更
			$this->setOrderCredit($params['tid']);
			//邮件提醒
			if (!empty($this->module['config']['noticeemail'])) {
				$order = pdo_fetch("SELECT `price`, `paytype`, `from_user`, `addressid` FROM " . tablename('eso_sale_order') . " WHERE id = '{$params['tid']}'");
				$ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename('eso_sale_order_goods') . " WHERE orderid = '{$params['tid']}'", array(), 'goodsid');
				$goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total FROM " . tablename('eso_sale_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
				$address = pdo_fetch("SELECT * FROM " . tablename('eso_sale_address') . " WHERE id = :id", array(':id' => $order['addressid']));
				$body = "<h3>购买商品清单</h3> <br />";
				if (!empty($goods)) {
					foreach ($goods as $row) {
						$body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} <br />";
					}
				}
				$paytype = $order['paytype'] == '3' ? '货到付款' : '已付款';
				$body .= "<br />总金额：{$order['price']}元 （{$paytype}）<br />";
				$body .= "<h3>购买用户详情</h3> <br />";
				$body .= "真实姓名：{$address['realname']} <br />";
				$body .= "地区：{$address['province']} - {$address['city']} - {$address['area']}<br />";
				$body .= "详细地址：{$address['address']} <br />";
				$body .= "手机：{$address['mobile']} <br />";
				load()->func('communication');
				ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
			}
			$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
			$credit = $setting['creditbehaviors']['currency'];
			if ($params['type'] == $credit) {
				message('支付成功！', $this->createMobileUrl('myorder'), 'success');
			} else {
				message('支付成功！', '../../app/' . $this->createMobileUrl('myorder'), 'success');
			}
		}
	}

	public function doWebOption() {
		$tag = random(32);
		global $_GPC;
		include $this->template('option');
	}

	public function doWebSpec() {

		global $_GPC;
		$spec = array(
			"id" => random(32),
			"title" => $_GPC['title']
		);
		include $this->template('spec');
	}

	public function doWebSpecItem() {
		global $_GPC;
		load()->func('tpl');
		$spec = array(
			"id" => $_GPC['specid']
		);
		$specitem = array(
			"id" => random(32),
			"title" => $_GPC['title'],
			"show" => 1
		);
		include $this->template('spec_item');
	}

	public function doWebParam() {
		$tag = random(32);
		global $_GPC;
		include $this->template('param');
	}

	public function doWebExpress() {
		global $_W, $_GPC;
		// pdo_query('DROP TABLE ims_eso_sale_express');
		//pdo_query("CREATE TABLE IF NOT EXISTS `ims_eso_sale_express` (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',  `express_name` varchar(50) NOT NULL COMMENT '分类名称',  `express_price` varchar(10) NOT NULL DEFAULT '0',  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',  `express_area` varchar(50) NOT NULL COMMENT '配送区域',  `enabled` tinyint(1) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");
		//pdo_query("ALTER TABLE  `ims_eso_sale_order` ADD  `expressprice` VARCHAR( 10 ) NOT NULL AFTER  `totalnum` ;");
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_express') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				if (empty($_GPC['express_name'])) {
					message('抱歉，请输入物流名称！');
				}
				$data = array(
					'uniacid' => $_W['uniacid'],
					'displayorder' => intval($_GPC['express_name']),
					'express_name' => $_GPC['express_name'],
					'express_url' => $_GPC['express_url'],
					'express_area' => $_GPC['express_area'],
				);
				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('eso_sale_express', $data, array('id' => $id));
				} else {
					pdo_insert('eso_sale_express', $data);
					$id = pdo_insertid();
				}
				message('更新物流成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
			}
			//修改
			$express = pdo_fetch("SELECT * FROM " . tablename('eso_sale_express') . " WHERE id = '$id' and uniacid = '{$_W['uniacid']}'");
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$express = pdo_fetch("SELECT id  FROM " . tablename('eso_sale_express') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
			if (empty($express)) {
				message('抱歉，物流方式不存在或是已经被删除！', $this->createWebUrl('express', array('op' => 'display')), 'error');
			}
			pdo_delete('eso_sale_express', array('id' => $id));
			message('物流方式删除成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('express', TEMPLATE_INCLUDEPATH, true);
	}

	public function doWebDispatch() {
		global $_W, $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {

			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_dispatch') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {

			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'displayorder' => intval($_GPC['dispatch_name']),
					'dispatchtype' => intval($_GPC['dispatchtype']),
					'dispatchname' => $_GPC['dispatchname'],
					'express' => $_GPC['express'],
					'firstprice' => $_GPC['firstprice'],
					'firstweight' => $_GPC['firstweight'],
					'secondprice' => $_GPC['secondprice'],
					'secondweight' => $_GPC['secondweight'],
					'description' => $_GPC['description']
				);
				if (!empty($id)) {
					pdo_update('eso_sale_dispatch', $data, array('id' => $id));
				} else {
					pdo_insert('eso_sale_dispatch', $data);
					$id = pdo_insertid();
				}
				message('更新配送方式成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
			}
			//修改
			$dispatch = pdo_fetch("SELECT * FROM " . tablename('eso_sale_dispatch') . " WHERE id = '$id' and uniacid = '{$_W['uniacid']}'");
			$express = pdo_fetchall("select * from " . tablename('eso_sale_express') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$dispatch = pdo_fetch("SELECT id  FROM " . tablename('eso_sale_dispatch') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
			if (empty($dispatch)) {
				message('抱歉，配送方式不存在或是已经被删除！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
			}
			pdo_delete('eso_sale_dispatch', array('id' => $id));
			message('配送方式删除成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('dispatch', TEMPLATE_INCLUDEPATH, true);
	}

	public function doWebAdv() {
		global $_W, $_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('eso_sale_adv') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {

			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'advname' => $_GPC['advname'],
					'link' => $_GPC['link'],
					'enabled' => intval($_GPC['enabled']),
					'displayorder' => intval($_GPC['displayorder']),
					'thumb'=>$_GPC['thumb']
				);
				if (!empty($id)) {
					pdo_update('eso_sale_adv', $data, array('id' => $id));
				} else {
					pdo_insert('eso_sale_adv', $data);
					$id = pdo_insertid();
				}
				message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
			}
			$adv = pdo_fetch("select * from " . tablename('eso_sale_adv') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$adv = pdo_fetch("SELECT id  FROM " . tablename('eso_sale_adv') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
			if (empty($adv)) {
				message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
			}
			pdo_delete('eso_sale_adv', array('id' => $id));
			message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
	}

	public function doMobileAjaxdelete() {
		global $_GPC;
		$delurl = $_GPC['pic'];
		if (file_delete($delurl)) {
			echo 1;
		} else {
			echo 0;
		}
	}





	/**积分兑换功能 开始**/
	public function doWebAward() {
		load()->func('tpl');
		// 1. display credit
		// 2. add credit
		// 3. delete credit
		// 4. update credit
		global $_W;
		global $_GPC; // 获取query string中的参数
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		if ($operation == 'post') { // 增加或者更新兑换商品
			$award_id = intval($_GPC['award_id']);
			if (!empty($award_id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename('eso_sale_credit_award')." WHERE award_id = :award_id" , array(':award_id' => $award_id));
				if (empty($item)) {
					message('抱歉，兑换商品不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('请输入兑换商品名称！');
				}
				if (empty($_GPC['credit_cost'])) {
					message('请输入兑换商品需要消耗的积分数量！');
				}
				if (empty($_GPC['price'])) {
					message('请输入商品实际价值！');
				}
				$credit_cost = intval($_GPC['credit_cost']);
				$price = intval($_GPC['price']);
				$amount = intval($_GPC['amount']);
				$data = array(
					'uniacid' => $_W['uniacid'],
					'title' => $_GPC['title'],
					'logo' => $_GPC['logo'],
					'deadline' => $_GPC['deadline'],
					'amount' => $amount,
					'credit_cost' => $credit_cost,
					'price' => $price,
					'content' => $_GPC['content'],
					'createtime' => TIMESTAMP,
				);
				if (!empty($award_id)) {
					pdo_update('eso_sale_credit_award', $data, array('award_id' => $award_id));
				} else {
					pdo_insert('eso_sale_credit_award', $data);
				}
				message('商品更新成功！', create_url('site/entry/award', array('m' => 'eso_sale', 'op' => 'display')), 'success');
			}
		}
		else if ($operation == 'delete') { //删除商品
			$award_id = intval($_GPC['award_id']);
			$row = pdo_fetch("SELECT award_id FROM ".tablename('eso_sale_credit_award')." WHERE award_id = :award_id", array(':award_id' => $award_id));
			if (empty($row)) {
				message('抱歉，商品'.$award_id.'不存在或是已经被删除！');
			}
			pdo_delete('eso_sale_credit_award', array('award_id' => $award_id));
			message('删除成功！', referer(), 'success');
		} else if ($operation == 'display') {
			$condition = '';
			$list = pdo_fetchall("SELECT * FROM ".tablename('eso_sale_credit_award')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY createtime DESC");
		}
		include $this->template('credit_award');
	}

	public function doWebCredit() {
		// 1. display reservation
		// 2. add credit
		// 3. delete credit
		// 4. update credit
		global $_W;
		global $_GPC; // 获取query string中的参数
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'delete') { //删除兑换请求
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('eso_sale_credit_request')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，编号为'.$id.'的兑换请求不存在或是已经被删除！');
			}
			pdo_delete('eso_sale_credit_request', array('id' => $id));
			message('删除成功！', referer(), 'success');
		} else if ($operation == 'display') {
			$condition = '';
			$sql = "SELECT * FROM ".tablename('eso_sale_credit_award')." as t1,".tablename('eso_sale_credit_request')."as t2 WHERE t1.award_id=t2.award_id AND t1.uniacid = '{$_W['uniacid']}' ORDER BY t2.createtime DESC";
			$list = pdo_fetchall($sql);
			$ar = pdo_fetchall($sql, array(), 'from_user');
			$fans = fans_search(array_keys($ar), array('realname', 'mobile', 'credit1', 'residedist'));
		}
		include $this->template('credit_request');
	}

	public function doMobileAward() {
		global $_W, $_GPC;

		$from_user =	$this->getFromUser();
		$award_list = pdo_fetchall("SELECT * FROM ".tablename('eso_sale_credit_award')." WHERE uniacid = '{$_W['uniacid']}' and NOW() < deadline and amount > 0");
		$profile = fans_search($from_user);
		include $this->template('credit_award_new');
	}

	public function doMobileFillInfo() {
		global $_W, $_GPC;

		$from_user =	$this->getFromUser();
		$award_id = intval($_GPC['award_id']);
		$profile = fans_search($from_user);
		$award_info = pdo_fetch("SELECT * FROM ".tablename('eso_sale_credit_award')." WHERE award_id = $award_id AND uniacid = '{$_W['uniacid']}'");
		include $this->template('credit_fillinfo_new');
	}

	public function doMobileCredit() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();

		$award_id = intval($_GPC['award_id']);
		if (!empty($_GPC['award_id']))
		{
			$fans = fans_search($from_user , array('credit1'));
			$award_info = pdo_fetch("SELECT * FROM ".tablename('eso_sale_credit_award')." WHERE award_id = $award_id AND uniacid = '{$_W['uniacid']}'");
			if ($fans['credit1'] >= $award_info['credit_cost'] && $award_info['amount'] > 0)
			{
				$data = array(
					'amount' => $award_info['amount'] - 1
				);
				pdo_update('eso_sale_credit_award', $data, array('uniacid' => $_W['uniacid'], 'award_id' => $award_id));

				$data = array(
					'uniacid' => $_W['uniacid'],
					'from_user' => $from_user ,
					'award_id' => $award_id,
					'createtime' => TIMESTAMP
				);
				pdo_insert('eso_sale_credit_request', $data);

				$data = array(
					'realname' => $_GPC['realname'],
					'mobile' => $_GPC['mobile'],
					'credit1' => $fans['credit1'] - $award_info['credit_cost'],
					'residedist' => $_GPC['residedist'],
				);
				fans_update($from_user , $data);

				// navigate to user profile page
				message('积分兑换成功！', $this->createMoblieUrl('mycredit', array('uniacid' => $_W['uniacid'], 'name' => 'eso_sale', 'do' => 'mycredit','op' => 'display')), 'success');
			}
			else
			{
				message('积分不足或商品已经兑空，请重新选择商品！<br>当前商品所需积分:'.$award_info['credit_cost'].'<br>您的积分:'.$fans['credit1']
					. '. 商品剩余数量:' . $award_info['amount']
					. '<br><br>小提示：<br>每日签到，在线订票，宾馆预订可以赚取积分',

					$this->createMoblieUrl('award', array('uniacid' => $_W['uniacid'], 'name' => 'eso_sale')), 'error');
			}
		}
		else
		{
			message('请选择要兑换的商品！', $this->createMoblieUrl('award', array('uniacid' => $_W['uniacid'], 'name' => 'eso_sale')), 'error');
		}
	}

	public function doMobileSearch() {
		global $_GPC, $_W;
		$keyword = $_GPC['keyword'];
		$url = $_W['siteroot']."app/".$this->mturl('list2', array('name' =>'eso_sale','uniacid'=>$_W['uniacid'], 'keyword'=>$keyword, 'sort'=>1));
		header("location:$url");
		$cfg = $this->module['config'];
		$ydyy = $cfg['ydyy'];
		include $this->template('list2');
	}

	public function doMobileMycredit() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();
		$award_list = pdo_fetchall("SELECT * FROM ".tablename('eso_sale_credit_award')." as t1,".tablename('eso_sale_credit_request')."as t2 WHERE t1.award_id=t2.award_id AND from_user='".$from_user."' AND t1.uniacid = '{$_W['uniacid']}' ORDER BY t2.createtime DESC");
		$profile = fans_search($from_user);
		$user = pdo_fetchall('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		include $this->template('credit_mycredit_new');
	}
	/**积分兑换功能 结束**/



	public function doMobileZhifu() {
		global $_GPC,$_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$uniacid=$_W['uniacid'];
		//$from_user=$_W['fans']['from_user'];
		$from_user =	$this->getFromUser();
		$cfg = $this->module['config'];
		$zhifucommission = $cfg['zhifuCommission'];
		/*
			if(empty($from_user)){
				message('请选择会员！', $this->mturl('zhifu',array('mid'=>$id)), 'success');
			}
*/
		//$profile=fans_search($_GPC['from_user']);
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		/*
			if(!$profile){
				message('请选择会员！', $this->mturl('zhifu',array('mid'=>$id)), 'success');
			}
			*/
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_paylog')." WHERE  openid='".$from_user."' AND `uniacid` = ".$_W['uniacid']);
		$pager = pagination($total, $pindex, $psize);
		$list = pdo_fetchall("SELECT * FROM ".tablename('core_paylog')." WHERE openid='".$from_user."' AND uniacid=".$_W['uniacid']." ORDER BY plid DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
		include $this->template('dakuan');
	}

	public function doWebZhifu() {
		global $_GPC,$_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$uniacid=$_W['uniacid'];
		$from_user=$_W['fans']['from_user'];
		$op = trim($_GPC['op']) ? trim($_GPC['op']) :'list';
		$cfg = $this->module['config'];
		$zhifucommission = $cfg['zhifuCommission'];
		if(!$zhifucommission){
			message('请先在参数设置，设置佣金打款限额！', $this->createWebUrl('Commission'), 'success');
		}
		if(empty($_GPC['mobile'])){
			$mobile = 0;
		}else{
			$mobile = $_GPC['mobile'];
		}
		if($op=='list'){
			if($_GPC['submit'] == '搜索'){
				//$list = pdo_fetchall("SELECT * FROM ".tablename('fans')."  WHERE uniacid=".$_W['uniacid']."  AND mobile = '".$_GPC['mobile']."'  LIMIT 20");
				$list = pdo_fetchall("select * from ".tablename('eso_sale_member'). " where mobile = ".$mobile." and status = 1 and flag = 1 and (commission - zhifu) >= ".$zhifucommission." and uniacid = ".$_W['uniacid']);
				$total=count($list);
				include $this->template('zhifu');
				exit();
			}

			if(intval($_GPC['so']) == 1) {

				$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('eso_sale_member')." WHERE status = 1 and flag = 1 and (commission - zhifu) >= ".$zhifucommission." and uniacid = :uniacid ", array(':uniacid' => $_W['uniacid']));
				$pager = pagination($total, $pindex, $psize);
				$list = pdo_fetchall("SELECT * FROM ".tablename('eso_sale_member')."  WHERE uniacid=".$_W['uniacid']."  AND status = 1 and flag = 1 and (commission - zhifu) >= ".$zhifucommission." ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
			} else {

				$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('eso_sale_member')." WHERE status = 1 and flag = 1 and (commission - zhifu) >= ".$zhifucommission." AND `uniacid` = :uniacid", array(':uniacid' => $_W['uniacid']));
				$pager = pagination($total, $pindex, $psize);
				$list = pdo_fetchall("SELECT * FROM ".tablename('eso_sale_member')." WHERE uniacid=".$_W['uniacid']."  AND status = 1 and flag = 1 and (commission - zhifu) >= ".$zhifucommission." ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
			}

			include $this->template('zhifu');
		}
		if($op=='post'){
			if(empty($_GPC['from_user'])){
				message('请选择会员！', $this->createWebUrl('zhifu', array('op'=>'list', 'name' => 'eso_sale','uniacid'=>$_W['uniacid'])), 'success');
			}
			if(checksubmit()){
				$chargenum=intval($_GPC['chargenum']);

				if($chargenum){
					pdo_query("update ".tablename('eso_sale_member')." SET zhifu=zhifu+'".$chargenum."' WHERE from_user='".$_GPC['from_user']."' AND  uniacid=".$_W['uniacid']."  ");
					$paylog=array(
						'type'=>'zhifu',
						'uniacid'=>$uniacid,
						'openid'=>$_GPC['from_user'],
						'createtime'=>date('Y-m-d H:i:s'),
						'fee'=>$chargenum,
						'module'=>'eso_sale',
						'eso_tag'=>' 后台打款'.$chargenum.'元'
					);
					pdo_insert('core_paylog',$paylog);
				}

			}
			$from_user = $_GPC['from_user'];
			//$profile=fans_search($_GPC['from_user']);
			$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));

			if(!$profile){
				message('请选择会员！', $this->createWebUrl('zhifu', array('op'=>'list', 'name' => 'eso_sale','uniacid'=>$_W['uniacid'])), 'success');
			}


			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_paylog')." WHERE  openid='".$_GPC['from_user']."' AND `uniacid` = ".$_W['uniacid']);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM ".tablename('core_paylog')." WHERE openid='".$_GPC['from_user']."' AND uniacid=".$_W['uniacid']." ORDER BY plid DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
			$mlist=pdo_fetchall("SELECT `name`,`title` FROM ".tablename('modules'));
			$mtype=array();
			foreach($mlist as $k=>$v){
				$mtype[$v['name']]=	$v['title'];
			}



			include $this->template('zhifu_post');
		}



	}


	public function doWebCharge() {
		global $_GPC,$_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$uniacid = $_W['uniacid'];
		$op = trim($_GPC['op']) ? trim($_GPC['op']) :'list';

		if($op=='list'){
			if($_GPC['submit'] == '搜索'){
				if ($_GPC['mobile']) {
					$list = pdo_fetchall("SELECT * FROM ".tablename('mc_members')."  WHERE uniacid=".$_W['uniacid']."  AND mobile = '".$_GPC['mobile']."' LIMIT 20");
				}else{
					$list = pdo_fetchall("SELECT * FROM ".tablename('mc_members')."  WHERE uniacid=".$_W['uniacid']."  AND mobile<>'' LIMIT 20");
				}
				$total = count($list);
				include $this->template('charge');
				exit();
			}

			if(intval($_GPC['so']) == 1) {

				$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_members')." WHERE uniacid = :uniacid  AND mobile<>'' ", array(':uniacid' => $_W['uniacid']));
				$pager = pagination($total, $pindex, $psize);
				$list = pdo_fetchall("SELECT * FROM ".tablename('mc_members')."  WHERE uniacid=".$_W['uniacid']."  AND mobile<>'' ORDER BY uid DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
			} else {

				$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_members')." WHERE `uniacid` = :uniacid  AND mobile<>''", array(':uniacid' => $_W['uniacid']));
				$pager = pagination($total, $pindex, $psize);
				$list = pdo_fetchall("SELECT * FROM ".tablename('mc_members')." WHERE uniacid=".$_W['uniacid']."  AND mobile<>'' ORDER BY uid DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
			}


			include $this->template('charge');
		}

		if($op=='post'){
			if(empty($_GPC['uid'])){
				message('请选择会员！',
					create_url('site/entry', array('do' => 'charge','op'=>'list', 'm' => 'eso_sale','uniacid'=>$_W['uniacid'])), 'success');
			}
			$mapping_fans = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE `uid` = :uid", array(':uid' => $_GPC['uid']));

			if(checksubmit()){
				$chargenum = intval($_GPC['chargenum']);

				if($chargenum){
					pdo_query("update ".tablename('mc_members')." SET credit2=credit2+'".$chargenum."' WHERE uid='".$_GPC['uid']."' AND  uniacid=".$_W['uniacid']."  ");
					$paylog=array(
						'type'=>'charge',
						'uniacid'=>$uniacid,
						'openid'=>$mapping_fans['openid'],
						'createtime'=>date('Y-m-d H:i:s'),
						'fee'=>$chargenum,
						'module'=>'eso_sale',
						'eso_tag'=> '后台分销系统充值'.$chargenum.'元'
					);
					pdo_insert('core_paylog',$paylog);
				}

			}

			$profile = fans_search($_GPC['uid']);
			if(!$profile){
				message('请选择会员！',
					create_url('site/entry', array('do' => 'charge','op'=>'list', 'm' => 'eso_sale','uniacid'=>$_W['uniacid'])), 'success');
			}


			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_paylog')." WHERE  openid='".$mapping_fans['openid']."' AND `uniacid` = ".$_W['uniacid']);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM ".tablename('core_paylog')." WHERE openid='".$mapping_fans['openid']."' AND uniacid=".$_W['uniacid']." ORDER BY plid DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
			$mlist = pdo_fetchall("SELECT `name`,`title` FROM ".tablename('modules'));
			$mtype = array();
			foreach($mlist as $k=>$v){
				$mtype[$v['name']]=	$v['title'];
			}

			include $this->template('charge_post');
		}



	}



	public function doMobileXoauth() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];//当前公众号ID
		//用户不授权返回提示说明
		if ($_GPC['code']=="authdeny"){
			exit();
		}
		//高级接口取未关注用户Openid
		if (isset($_GPC['code'])){
			//第二步：获得到了OpenID
			$appid = $_W['account']['key'];
			$secret = $_W['account']['secret'];
			$serverapp = $_W['account']['level'];


			if ($serverapp==2) {
				if(empty($appid) || empty($secret)){
					return ;
				}
			}
			$state = $_GPC['state'];
			//1为关注用户, 0为未关注用户


			//查询活动时间
			$code = $_GPC['code'];
			$oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
			$content = ihttp_get($oauth2_code);
			$token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {

				echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				exit;
			}
			$from_user = $token['openid'];
			//再次查询是否为关注用户
			$profile  = fans_search($from_user, array('follow'));

			//关注用户直接获取信息
			if ($profile['follow']==1){
				$state = 1;
			}


			//未关注用户和关注用户取全局access_token值的方式不一样
			if ($state==1 && $serverapp == 2){
				$access_token =$this->get_weixin_token();
				$oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			}else{
				$access_token = $token['access_token'];
				$oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			}

			//使用全局ACCESS_TOKEN获取OpenID的详细信息
			$content = ihttp_get($oauth2_url);
			$info = @json_decode($content['content'], true);
			if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
				echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
				exit;
			}

			//		if (!empty($info["headimgurl"])) {
			//$info['avatar']='resource/attachment/avatar/'.$info["openid"].'.jpg';
			//$imgfile=$info['avatar'];
			//	$this->GrabImage($info['headimgurl'],$imgfile);

			//file_write($info['avatar'], $filedata);
			//	}else{
			//$info['headimgurl']='avatar_11.jpg';
			//}
			if ($serverapp == 2) {//普通号
				$row = array(
					'uniacid' => $_W['uniacid'],
					'nickname'=>$info["nickname"],
					'realname'=>$info["nickname"],
					'gender' => $info['sex']
				);

				if(!empty($info["country"])){
					$row['country']=$info["country"];
				}
				if(!empty($info["province"])){
					$row['province']=$info["province"];
				}
				if(!empty($info["city"])){
					$row['city']=$info["city"];
				}

				fans_update($from_user, $row);
				/*if(!empty($info["headimgurl"])){
					pdo_update('fans', array('avatar'=>$info["headimgurl"]), array('from_user' => $from_user));
				}*/
			}

			if($serverapp != 2  && !(empty($from_user))) {//普通号
				$row = array(
					'nickname'=> $info["nickname"],
					'realname'=> $info["nickname"],
					'gender'  => $info['sex']
				);


				if(!empty($info["country"])){
					$row['country']=$info["country"];
				}
				if(!empty($info["province"])){
					$row['province']=$info["province"];
				}
				if(!empty($info["city"])){
					$row['city']=$info["city"];
				}

				fans_update($from_user, $row);
				/*if(!empty($info["headimgurl"])){
					pdo_update('fans', array('avatar'=>$info["headimgurl"]), array('from_user' => $from_user));
				}*/
			}

			$oauth_openid="eso_sale_t150122".$_W['uniacid'];
			setcookie($oauth_openid, $from_user, time()+3600*(24*5));
			$url=$_COOKIE["xoauthURL"];
			header("location:$url");
			exit;
		}else{
			echo '<h1>网页授权域名设置出错!</h1>';
			exit;
		}

	}

	function GrabImage($url,$filename="") {
		if($url=="") return false;

		if($filename=="") {
			$ext=strrchr($url,".");
			if($ext!=".gif" && $ext!=".jpg" && $ext!=".png") return false;
			$filename=date("YmdHis").$ext;
		}

		ob_start();
		readfile($url);
		$img = ob_get_contents();
		ob_end_clean();
		$size = strlen($img);

		$fp2=@fopen($filename, "a");
		fwrite($fp2,$img);
		fclose($fp2);

		return $filename;
	}


	private  function getShareId() {
		global $_W, $_GPC;
		$from_user =	$this->getFromUser();
		$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $from_user));
		$shareid = 'eso_sale_sid07'.$_W['uniacid'];


		if(empty($profile['shareid']))
		{

			if(!empty($_COOKIE[$shareid]))
			{
				if($profile['id']!=$_COOKIE[$shareid])
				{
					pdo_update('eso_sale_member', array('shareid'=>$_COOKIE[$shareid]), array('from_user' => $from_user,':uniacid' => $_W['uniacid']));

					return $_COOKIE[$shareid];
				}
			}
			return 0;
		}else
		{
			return $profile['shareid'];
		}
	}
	private function setmid($fuser)
	{
		global $_W,$_GPC;
		if (empty($_COOKIE["mid"])) {
			$profile = pdo_fetch('SELECT * FROM '.tablename('eso_sale_member')." WHERE  uniacid = :uniacid  AND from_user = :from_user" , array(':uniacid' => $_W['uniacid'],':from_user' => $fuser));
			if(!empty($profile['id']))
			{

				setcookie("mid",$profile['id']);

			}
		}

	}

	private function get_weixin_token() {
		global $_W, $_GPC;
		$account=$_W['account'];
		if(is_array($account['access_token']) && !empty($account['access_token']['token']) && !empty($account['access_token']['expire']) && $account['access_token']['expire'] > TIMESTAMP) {
			return $account['access_token']['token'];
		} else {
			if(empty($account['uniacid'])) {
				message('参数错误.');
			}
			$appid = $account['key'];
			$secret = $account['secret'];


			if (empty($appid) || empty($secret)) {
				message('请填写公众号的appid及appsecret, (需要你的号码为微信服务号)！', create_url('account/post', array('id' => $account['uniacid'])), 'error');
			}
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
			$content = ihttp_get($url);
			if(empty($content)) {
				message('获取微信公众号授权失败, 请稍后重试！');
			}
			$token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token)) {
				message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: <br />' . $token);
			}
			if(empty($token['access_token']) || empty($token['expires_in'])) {
				message('解析微信公众号授权失败, 请稍后重试！');
			}
			$record = array();
			$record['token'] = $token['access_token'];
			$record['expire'] = TIMESTAMP + $token['expires_in'];
			$row = array();
			$row['access_token'] = iserializer($record);
			pdo_update('wechats', $row, array('uniacid' => $account['uniacid']));
			return $record['token'];
		}
	}


	private  function getFromUser() {
		global $_W,$_GPC;
		if(false)
		{
			return $_W['fans']['from_user'];
		}
		//return;
		$oauth_openid = "eso_sale_t150122".$_W['uniacid'];
		//是否为高级号
		$serverapp = $_W['account']['level'];
		if ($serverapp==2) {

			$appid = $_W['account']['key'];
			$secret = $_W['account']['secret'];

			if(!empty($appid) && !empty($secret)){
				checkauth();
				$this->setmid($_W['fans']['from_user']);
				return $_W['fans']['from_user'];
			}
		}else{
			checkauth();
			$this->setmid($_W['fans']['from_user']);
			return $_W['fans']['from_user'];
		}


		if (empty($_COOKIE[$oauth_openid])) {
			//借用的
			$url = $_W['siteroot']."app/".$this->mturl('xoauth');

			setcookie("xoauthURL", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", time()+3600*(24*5));
			$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
			//exit($oauth2_code);
			//		message($oauth2_code);
			header("location:$oauth2_code");
			exit;
		}else{
			$this->setmid($_COOKIE[$oauth_openid]);
			return 	$_COOKIE[$oauth_openid];
		}
	}


	//发送模板消息
	public function sendtempmsg($template_id, $url, $data, $topcolor) {
		global $_W,$_GPC;
		//取TOKEN
		$from_user =$this->getFromUser();
		//	$tokens= $this->get_weixin_token();
		$tokens =$this->get_weixin_token();
		if(empty($tokens))
		{
			return;
		}
		//
		$postarr = '{"touser":"'.$from_user.'","template_id":"'.$template_id.'","url":"'.$url.'","topcolor":"'.$topcolor.'","data":'.$data.'}';
		$res = ihttp_post('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$tokens,$postarr);
		//$res = $res['content'];
//		$res = $res['content'];
//		$res = json_decode($res, true);


		return true;

	}

	function mturl($do, $query = array(), $noredirect = true) {
		return $this->createMobileUrl($do, $query, $noredirect);
	}
}





/*
$url = $this->mturl('index');
die('<script>location.href = "'.$url.'";</script>');
header("location:$url");
exit;
*/
/**
 * 生成分页数据
 * @param int $currentPage 当前页码
 * @param int $totalCount 总记录数
 * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
 * @param int $pageSize 分页大小
 * @return string 分页HTML
 */
function pagination1($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')) {
	global $_W;
	$pdata = array(
		'tcount' => 0,
		'tpage' => 0,
		'cindex' => 0,
		'findex' => 0,
		'pindex' => 0,
		'nindex' => 0,
		'lindex' => 0,
		'options' => ''
	);
	if($context['ajaxcallback']) {
		$context['isajax'] = true;
	}

	$pdata['tcount'] = $tcount;
	$pdata['tpage'] = ceil($tcount / $psize);
	if($pdata['tpage'] <= 1) {
		return '';
	}
	$cindex = $pindex;
	$cindex = min($cindex, $pdata['tpage']);
	$cindex = max($cindex, 1);
	$pdata['cindex'] = $cindex;
	$pdata['findex'] = 1;
	$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
	$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
	$pdata['lindex'] = $pdata['tpage'];

	if($context['isajax']) {
		if(!$url) {
			$url = $_W['script_name'] . '?' . http_build_query($_GET);
		}
		$pdata['faa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['paa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['naa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['laa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
	} else {
		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$_GET['page'] = $pdata['findex'];
			$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['pindex'];
			$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['nindex'];
			$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['lindex'];
			$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
		}
	}

	$html = '<div class="pagination pagination-centered"><ul>';
	if($pdata['cindex'] > 1) {
		$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
		$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
	}
	//页码算法：前5后4，不足10位补齐
	if(!$context['before'] && $context['before'] != 0) {
		$context['before'] = 5;
	}
	if(!$context['after'] && $context['after'] != 0) {
		$context['after'] = 4;
	}

	if($context['after'] != 0 && $context['before'] != 0) {
		$range = array();
		$range['start'] = max(1, $pdata['cindex'] - $context['before']);
		$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
		if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
			$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
			$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
		}
		for ($i = $range['start']; $i <= $range['end']; $i++) {
			if($context['isajax']) {
				$aa = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
			} else {
				if($url) {
					$aa = 'href="?' . str_replace('*', $i, $url) . '"';
				} else {
					$_GET['page'] = $i;
					$aa = 'href="?' . http_build_query($_GET) . '"';
				}
			}
			//$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
		}
	}

	if($pdata['cindex'] < $pdata['tpage']) {
		$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
		$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
	}
	$html .= '</ul></div>';
	return $html;
}

function haha($hehe){
	$phone = $hehe;
	$mphone = substr($phone,3,6);
	$lphone = str_replace($mphone,"****",$phone);
	return $lphone;
}


function hehe($string = null) {
	// 将字符串分解为单元
	$name = $string;
	preg_match_all("/./us", $string, $match);
	if(count($match[0])>7){
		$mname = '';
		for($i=0; $i<7; $i++){
			$mname = $mname.$match[0][$i];
		}
		$name = $mname.'..';
	}
	return $name;
}



function img_url($img = '') {
	global $_W;
	if (empty($img)) {
		return "";
	}
	if (substr($img, 0, 6) == 'avatar') {
		return $_W['siteroot'] . "resource/image/avatar/" . $img;
	}
	if (substr($img, 0, 8) == './themes') {
		return $_W['siteroot'] . $img;
	}
	if (substr($img, 0, 1) == '.') {
		return $_W['siteroot'] . substr($img, 2);
	}
	if (substr($img, 0, 5) == 'http:') {
		return $img;
	}
	return $_W['attachurl'] . $img;
}


	