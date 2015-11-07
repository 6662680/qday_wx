<?php
/**
 * 二手市场模块微站定义
 *
 * @author Thinkidea
 * @url http://bbs.thinkidea.net/
 */
defined('IN_IA') or exit('Access Denied');


class thinkidea_SecondmarketModuleSite extends WeModuleSite {

	public $table_reply = 'thinkidea_secondmarket_reply';
	public $table_category = 'thinkidea_secondmarket_category';
	public $table_goods = 'thinkidea_secondmarket_goods';
	
		
	public function getHomeTiles() {
		global $_W;
		$urls = array();
		$list = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE weid = '{$_W['weid']}' AND module = 'xfmarket'");
		if (!empty($list)) {
			foreach ($list as $row) {
				$urls[] = array('title'=>$row['name'], 'url'=> $this->createMobileUrl('list', array('rid' => $row['id'])));
			}
		}
		return $urls;
	}

	public function doMobileAdd(){
		global $_W,$_GPC;
		load()->func('file');
		load()->func('tpl');
		$categorys = pdo_fetchall("SELECT * FROM".tablename($this->table_category)."WHERE weid='{$_W['weid']}'");
		$data = array(
				'weid'        => $_W['weid'],
				'openid'      => $_W['fans']['from_user'],
				'title'       => $_GPC['title'],
				'rolex'       => $_GPC['rolex'],
				'price'       => $_GPC['price'],
				'realname'    => $_GPC['realname'],
				'sex'         => $_GPC['sex'],
				'mobile'      => $_GPC['mobile'],
				'description' => $_GPC['description'],
				'createtime'  => TIMESTAMP,
				'pcate'       => $_GPC['pcate'],
				'status'      => 0,
				'thumb1'      => $_GPC['thumb1'],
				'thumb2'      => $_GPC['thumb2'],
				'thumb3'      => $_GPC['thumb3'],
		);
		
		if (!empty($_GPC['id'])) {
			$good = pdo_fetch("SELECT * FROM".tablename($this->table_goods)."WHERE id='{$_GPC['id']}'");
		}

        if(!empty($_GPC['image'])){

            load()->classs('weixin.account');
            $accObj= new WeixinAccount();
            $access_token = $accObj->fetch_available_token();

            $images = explode(",",$_GPC['image']);

            foreach($images as $key => $image){

                //下载图片
                $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$image";

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $package = curl_exec($ch);
                $httpinfo = curl_getinfo($ch);
                curl_close($ch);
                $fileInfo = array_merge(array('header' => $httpinfo), array('body' => $package));

                $rand = rand(100, 999);
                $filename = date("YmdHis") . $rand.".jpg";

                $filepath = ATTACHMENT_ROOT."/images/thinkidea_secondmarket/".$filename;

                $filecontent = $fileInfo["body"];

                $dir_name = ATTACHMENT_ROOT."/images/thinkidea_secondmarket";

                if(!is_dir($dir_name)) {
                    $dir = mkdir($dir_name, 0777, true);
                }

                if(false !== $dir){
                    $local_file = fopen($filepath, 'w');

                    if (false !== $local_file){

                        if (false !== fwrite($local_file, $filecontent)) {
                            fclose($local_file);
                            $info['img'] = "/images/thinkidea_secondmarket/".$filename;
                            if($key <= 2){
                                $data['thumb'.($key+1)] = $info['img'];
                            }
                        }
                    }else{
                        message("图片上传失败，请联系管理员！","javascript:WeixinJSBridge.call('closeWindow');","error");
                    }
                }else{
                    message("目录创建失败！","javascript:WeixinJSBridge.call('closeWindow');","error");
                }

            }
        }

		if ($_W['ispost']) {
			if (empty($_GPC['id'])) {
				pdo_insert($this->table_goods,$data);
				message('发布成功',$this->createMobileUrl('list'),'success');
			}else{
				pdo_update($this->table_goods,$data,array('id' => $_GPC['id']));
				message('更新成功',$this->createMobileUrl('list'),'success');
			}
	
		}
	
	
		include $this->template('add');
	}

    public function doMobileList2(){

        global $_GPC,$_W;
        //必须关注
        //$this->checkAuth();
        //必须关注

        if(!empty($_GPC['keyword'])){
            $keyword = "%{$_GPC['keyword']}%";
            $condition = " AND title LIKE '{$keyword}'";
        }

        $st = '';
        if (!empty($this->module['config']['status'])) {
            $st = " AND status='1' ";
        }

        if(!empty($_GPC['pcate'])){
            $condition .= " AND pcate = '{$_GPC['pcate']}'";
        }

        $sql = "SELECT * FROM ".tablename($this->table_goods)." WHERE weid='{$_W['weid']}' $st $condition ORDER BY `createtime` DESC LIMIT 0, 10";

        $list = pdo_fetchall($sql);
        foreach($list as $key => $value){

            $tmp = TIMESTAMP - $value['createtime'];

            if($tmp < 60){
                $re = $tmp.'秒';
            }else if($tmp < 3600){
                $re = floor($tmp/60).'分钟';
            }else if($tmp < 86400){
                $re = floor($tmp/3600).'小时';
            }else{
                $re = floor($tmp/86400).'天';
            }

            $list[$key]['createtime'] = $re;
        }
        include $this->template('list2');
    }


	public function doMobileList(){
		global $_GPC,$_W;
		
		//必须关注
		//$this->checkAuth();
		//必须关注
		$pcate = intval($_GPC['pcate']);
		//分类显示
		//2015.2.2增加父分类AND parentid = 0
		$categorys = pdo_fetchall("SELECT * FROM".tablename($this->table_category)."WHERE weid='{$_W['weid']}' AND parentid = 0 AND enabled='1'");

        $tmp = array();
        foreach ($categorys AS $c){
            array_push($tmp, $c['id']);
        }
        $pids = implode(",", $tmp);

        if(!empty($pids)){
            $categorys_2 = pdo_fetchall("SELECT * FROM".tablename($this->table_category)."WHERE weid='{$_W['weid']}' AND parentid in ({$pids}) AND enabled='1'");
            foreach($categorys as $key => $category){
                foreach($categorys_2 as $k => $c){
                    if($category['id'] == $c['parentid']){
                        $categorys[$key]['two'][$k] = $c;
                    }
                }
            }

        }

        $new = pdo_fetchall("SELECT * FROM ".tablename($this->table_goods)." WHERE weid = :weid ORDER BY id desc limit 5", array(':weid' => $_W['weid']));

        foreach($new as $key => $value){

            $tmp = TIMESTAMP - $value['createtime'];

            if($tmp < 60){
                $re = $tmp.'秒';
            }else if($tmp < 3600){
                $re = floor($tmp/60).'分钟';
            }else if($tmp < 86400){
                $re = floor($tmp/3600).'小时';
            }else{
                $re = floor($tmp/86400).'天';
            }

            $new[$key]['createtime'] = $re;
        }



        //分享数据
		$rid = intval($_GPC['rid']);
		//if (!empty($rid)) {
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ", array(':rid' => $rid));
		$sharepic = $_W['attachurl'].$reply['picture'];
		$description = $reply['description'];
		$title = $reply['title'];


		//首页幻灯
		$tmp = $this->module['config'];
		//==========增加超链接2015.2.2========
		$siders = array();
		for ($i = 1;$i < 4;$i++){
			array_push($siders, array( $tmp['sider'.$i], $tmp['sider'.$i.'_link'] ));
		}
		include $this->template('list');
	}

	public function doMobileDetail(){
		global $_W,$_GPC;		
		$id = intval($_GPC['id']);
		$detail = pdo_fetch("SELECT * FROM".tablename($this->table_goods)."WHERE id='{$id}'");

        $size = 1;

        if(empty($detail['thumb1'])){
            $size = 1;
        }
        if(!empty($detail['thumb2'])){
            $size += 1;
        }
        if(!empty($detail['thumb3'])){
            $size += 1;
        }

        $user = mc_fansinfo($_W['openid'],$_W['acid']);

        $url = "http://virtual.paipai.com/extinfo/GetMobileProductInfo?mobile={$detail['mobile']}&amount=10000";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        $a = explode(')',substr($res,1));

        $c = '"'.iconv("gbk","UTF-8",$a[0]).'"';

        $mobile = json_decode($c, true);


        $title = $detail['title'];
		$_share_img = $_W['attachurl'].$detail['thumb1'];

        $_share = array(
            'title'   => $title,
            'link'    => '',
            'imgUrl'  => $_share_img,
            'content' => $title
        );

		include $this->template('detail');
	}

	public function doMobileMygoods(){
		global $_W,$_GPC;

        $title = "我的发布信息";
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$list = pdo_fetchall("SELECT * FROM".tablename($this->table_goods)."WHERE openid='{$_W['fans']['from_user']}' LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename($this->table_goods)."WHERE openid='{$_W['fans']['from_user']}'");
		$pager  = pagination($total, $pindex, $psize);
		if ($_GPC['op'] == 'delete') {
			pdo_delete($this->table_goods,array('id' => $_GPC['id']));
			message('删除成功',$this->createMobileUrl('Mygoods'),'success');
		}
		include $this->template('mygoods');
	}

	public function doWebGoods(){
		global $_GPC,$_W;
		$item = pdo_fetchall("SELECT * FROM".tablename($this->table_goods)."WHERE weid='{$_W['weid']}'");
		
		$goods = array();
		foreach ($item as $key => $value) {
			$category = pdo_fetch("SELECT * FROM".tablename($this->table_category)."WHERE id='{$value['pcate']}'");
			$goods[] = array(
					'id' => $value['id'],
					'title' => $value['title'],
					'rolex' => $value['rolex'],
					'price' => $value['price'],
					'realname' => $value['realname'],
					'sex' => $value['sex'],
					'mobile' => $value['mobile'],
					'name' => $category['name'],
					'createtime' => $value['createtime'],
					'status' => $value['status'],
					'weid' => $value['weid'],
			);
		}
		if ($_GPC['foo'] == 'delete') {
			pdo_delete($this->table_goods,array('id' => $_GPC['id']));
			message('删除成功',referer(),'success');
		}
		if ($_GPC['foo'] == 'update') {
			//echo $_GPC['id'].$_GPC['status'];exit;
			pdo_query("UPDATE ".tablename($this->table_goods)." SET status='{$_GPC['status']}' WHERE id='{$_GPC['id']}'");
			message('更新成功',referer(),'success');
		}
		include $this->template('goods');
	}
	//分类
	public function doWebCategory(){
		global $_GPC,$_W;

		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$id = intval($_GPC['id']);
		
		if ($op == 'post') {
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM".tablename($this->table_category)."WHERE id='{$id}'");
			}
			if ($_W['ispost']) {
				$data = array(
						'weid'    => $_W['weid'],
						'name'    => $_GPC['cname'],
						'parentid' => $_GPC['parentid'],
						'enabled' => $_GPC['enabled'] ? 1 : 0,
				);
				if (empty($id)) {
					pdo_insert($this->table_category,$data);
					message('添加成功',$this->createWebUrl('Category'),'success');
				}else{
					pdo_update($this->table_category,$data,array('id' => $id));
					message('更新成功',$this->createWebUrl('Category'),'success');
				}
			}
		}elseif($op == 'display'){
			$o = '';
			
			$parents = pdo_fetchall("SELECT * FROM".tablename($this->table_category)." WHERE weid = '{$_W['weid']}' AND parentid = 0");
			foreach ($parents AS $parent){
				$enable = intval($parent['enabled']) ? '<button class="btn btn-success btn-sm">是</button>' : '<button class="btn btn-danger btn-sm">否</button>';
				$o .= "<tr><td><input type=\"checkbox\" name=\"select[]\" value=\"{$parent['id']}\" /></td>";
				$o .= "<td>". $parent['name'] ."</td>";
				$o .= "<td> —— </td>";
				$o .= "<td>".$enable. "</td>";
				$o .= "<td><a href=". $this->createWebUrl('category',array('op' => 'post','id' => $parent['id'])) ." >编辑</a></td></tr>";
				
				$subcates = pdo_fetchall("SELECT * FROM ".tablename($this->table_category)." WHERE parentid = {$parent['id']}");
				foreach ($subcates AS $subcate){
					$enable = intval($subcate['enabled']) ? '<button class="btn btn-success btn-sm">是</button>' : '<button class="btn btn-danger btn-sm">否</button>';
					$o .= "<tr><td><input type=\"checkbox\" name=\"select[]\" value=\"{$subcate['id']}\" /></td>";
					$o .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;|——". $subcate['name'] ."</td>";
					$o .= "<td>". $parent['name'] ."</td>";
					$o .= "<td>". $enable ."</td>";
					$o .= "<td><a href=". $this->createWebUrl('category',array('op' => 'post','id' => $subcate['id'])).">编辑</a></td></tr>";
				}
			}
		}
		
		if(checksubmit('delete')){
			pdo_delete($this->table_category, " id  IN  ('".implode(",", $_GPC['select'])."')");
			message('删除成功',referer(),'success');
		}
		
		//增加父栏目
		$categorys = pdo_fetchall("SELECT * FROM".tablename($this->table_category)."WHERE weid = :weid AND parentid = 0", array(':weid' => $this->weid));
		
		include $this->template('category');
	}
	
	public function doWebGoodsmanage() {
		//这个操作被定义用来呈现 管理中心导航菜单
	}
	
	private function checkAuth() {
		global $_W;
		checkauth();
	}
}