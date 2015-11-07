<?php
/**
 * 
 *
 * 
 */
defined('IN_IA') or exit('Access Denied');

require_once IA_ROOT ."/addons/xfeng_community/model.php";
class Xfeng_communityModuleSite extends WeModuleSite {

	//后台程序 inc/web文件夹下
	public function __web($f_name){
		include_once  'inc/web/'.strtolower(substr($f_name,5)).'.inc.php';
	}
	//后台小区信息
	public function doWebRegion(){
		$this->__web(__FUNCTION__);
	}
	//后台小区公告
	public function doWebAnnouncement(){
		$this->__web(__FUNCTION__);
	}
	//后台小区用户
	public function doWebMember(){
		$this->__web(__FUNCTION__);
	}
	//后台小区报修
	public function doWebRepair(){
		$this->__web(__FUNCTION__);
	}
	//后台常用号码
	public function doWebPhone(){
		$this->__web(__FUNCTION__);
	}	
	//后台投诉
	public function doWebReport(){
		$this->__web(__FUNCTION__);
	}
	//后台家政服务
	public function doWebHomemaking(){
		$this->__web(__FUNCTION__);
	}
	//后台房屋租赁
	public function doWebHouselease(){
		$this->__web(__FUNCTION__);
	}
	//后台物业团队介绍
	public  function doWebProperty(){
		$this->__web(__FUNCTION__);	
	}
	//后台导航扩展
	public function doWebnavExtension(){
		$this->__web(__FUNCTION__);	
	}
	//后台幻灯片设置
	public function doWebSlide(){
		$this->__web(__FUNCTION__);	
	}	
	//后台-小区活动
	public function doWebActivity() {
 		$this->__web(__FUNCTION__);
	}
	//后台-常用查询
	public function doWebSearch() {
		$this->__web(__FUNCTION__);
	}
	//后台-二手市场
	public function doWebFled(){
		$this->__web(__FUNCTION__);
	}
	//后台-小区拼车
	public function doWebCarpool(){
		$this->__web(__FUNCTION__);
	}
	//后台-小区商家
	public function doWebBusiness(){
		$this->__web(__FUNCTION__);
	}
	//后台-查物业费
	public function doWebPropertyfree(){
		$this->__web(__FUNCTION__);
	}
	//后台-分类管理
	public function doWebServicecategory(){
		$this->__web(__FUNCTION__);
	}
	//后台-黑名单管理
	public function doWebBlack(){
		$this->__web(__FUNCTION__);
	}
	//前台程序 inc/app文件夹下
	public function __app($f_name){
		include_once  'inc/app/'.strtolower(substr($f_name,8)).'.inc.php';
	}
	//前台手机首页
    public function doMobileHome(){
    	$this->__app(__FUNCTION__);
    }
    //前台手机住户注册页面
    public function doMobileRegister(){
    	$this->__app(__FUNCTION__);	 
    }
    //注册短信验证
    public  function doMobileVerifycode(){
		$this->__app(__FUNCTION__);	 
	}
    //前台个人页面
	public function doMobileMember(){
		$this->__app(__FUNCTION__);	
	}
    //前台手机公告页面
    public function doMobileAnnouncement(){
    	$this->__app(__FUNCTION__);	
    }
    //前台手机常用电话页面
    public function doMobilePhone(){
    	$this->__app(__FUNCTION__);	
    }
    //前台报修
    public function doMobileRepair(){
    	$this->__app(__FUNCTION__);	
    }
	//前台-小区活动首页
	public function doMobileActivity() {
 		$this->__app(__FUNCTION__);
	}
	//前台投诉
    public function doMobileReport(){
    	$this->__app(__FUNCTION__);
    } 
    //前台家政服务
    public function doMobileHomemaking(){
    	$this->__app(__FUNCTION__);
    }
    //前台房屋租赁
   	public function doMobileHouselease(){
   		$this->__app(__FUNCTION__);
   	}
   	//前台团队介绍
    public function doMobileProperty(){
   		$this->__app(__FUNCTION__);
    }
	//前台-小区活动详细页
	public function doMobileDetail(){
		$this->__app(__FUNCTION__);
	}
	//前台-小区活动报名页面
	public function doMobileRes(){
		$this->__app(__FUNCTION__);
	}
	//前台-小区常用查询
	public function doMobileSearch(){
		$this->__app(__FUNCTION__);
	}
	//前台-小区二手市场
	public function doMobileFled(){
		$this->__app(__FUNCTION__);
	}
	//前台-小区拼车
	public function doMobileCar(){
		$this->__app(__FUNCTION__);
	}
	//前台-小区商家
	public function doMobileBusiness(){
		$this->__app(__FUNCTION__);
	}
	//前台-查物业费
	public function doMobilePropertyfree(){
		$this->__app(__FUNCTION__);
	}
	//获取当前公众号所有小区信息
	public function regions(){
		global $_W;
		$regions = pdo_fetchall("SELECT * FROM".tablename('xcommunity_region')."WHERE weid='{$_W['weid']}'");
		return $regions;
	}
	//判断是否注册成为小区用户
    public function changemember(){
    	global $_GPC,$_W;
    	$member  = pdo_fetch("SELECT * FROM".tablename('xcommunity_member')."WHERE openid='{$_W['fans']['from_user']}'");
		if (empty($member)) {
			header("Location:".$this->createMobileUrl('register'));
			exit;
		}else{
			return $member;
		}
    }
    //报修前台处理提交补充信息，isreply=0为前台提交，isreply=1为后台管理回复
    public function doMobileReply(){
    	global $_GPC,$_W;
    	if(checksubmit('submit')){
    		$data = array(
				'weid'       =>$_W['weid'],
				'openid'     =>$_W['fans']['from_user'],
				'reportid'   =>$_GPC['id'],
				'isreply'    =>0,
				'content'    =>$_GPC['content'],
				'createtime' =>$_W['timestamp'],
    			);
    		pdo_insert('xcommunity_reply',$data);
    	} 
    	message('提交成功',referer(),'success');	
    }	
	//报修投诉短信提醒
	public function Resms($con){
		global $_W,$_GPC;
		if($this->module['config']['report_type']){
			//查小区物业电话
			
			$sql_1     = "select * from ".tablename('xcommunity_member')."where openid='{$_W['fans']['from_user']}'";
			$it        = pdo_fetch($sql_1);
			$mobile    = $it['mobile'];
			$sql       = "select * from".tablename('xcommunity_region')." where title="."'".$it['regionname']."'";
			$row       = pdo_fetch($sql);
			$phone     = $row['linkway'];
			$tpl_id    = $sms['sms_reportid'];
			$content   = $con;
			$company   = $this->module['config']['cname'];
			$tpl_value = urlencode("#content#=$content&#mobile#=$mobile&#company#=$company");
			$appkey    = $sms['sms_account'];
			$params    = "mobile=".$phone."&tpl_id=".$tpl_id."&tpl_value=".$tpl_value."&key=".$appkey;
			$url       = 'http://v.juhe.cn/sms/send';
			//print_r($url);exit;
			$content   = ihttp_post($url,$params);
			
		}
	}
	//GPRS无线打印
	public function doWebPrint(){
		global $_GPC,$_W;
		$usr=!empty($_GET['usr'])?$_GET['usr']:'355839028553370';
		//获取订单
		if($usr != $this->moduel['config']['print_usr']){
			exit;
		}
		$weid=$set['weid'];
		$item = pdo_fetch("SELECT * FROM ".tablename('xcommunity_report')." WHERE weid = :weid AND print_sta=-1  limit 1", array(':weid' => $weid));
		//没有新信息
		if($item==false){	
			exit;
		}
		if(intval($set['print_nums'])<1 || intval($set['print_nums'])>4){
			$set['print_nums']=1;
		}
		$member = pdo_fetch("SELECT * FROM ".tablename('xcommunity_member')." WHERE weid = :weid AND openid='{$item['openid']}'  limit 1", array(':weid' => $weid));
		$content.='类型:'.$item['category']."\n";
		$content.='内容:'.$item['content']."\n";
		$content.='所属小区:'.$member['regionname']."\n";
		$content.='地址:'.$member['address']."\n";
		$content.='业主:'.$member['realname']."\n";
		$content.='电话:'.$member['mobile']."\n";
		$content.='日期:'.date('Y-m-d H:i:s', $item['createtime'])."\n";
		$content=iconv("UTF-8","GB2312//IGNORE",$content);
		$setting='<setting>124:'.$set['print_nums'].'|134:0</setting>';
		$setting=iconv("UTF-8","GB2312//IGNORE",$setting);
		echo '<?xml version="1.0" encoding="GBK"?><r><id>'.$item['id'].'</id><time>'.$dtime.'</time><content>'.$content.'</content>'.$setting.'</r>';
		pdo_update('xcommunity_report',array('print_sta'=>0),array('id'=>$item['id']));
	}	
	/**
	* 读取excel $filename 路径文件名 $indata 返回数据的编码 默认为utf8
	*以下基本都不要修改
	*/
	public function read($filename,$encode='utf-8'){
		require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load($filename);
		$indata = $objPHPExcel->getSheet(0)->toArray();
		return $indata;
			
	 } 
	 //处理图片上传;
	 public function doMobileimgupload(){
			global $_W,$_GPC;
				
			if(!empty($_GPC['pic'])){
				preg_match("/data\:image\/([a-z]{1,5})\;base64\,(.*)/",$_GPC['pic'],$r);
				$imgname = 'bl'.time().rand(10000,99999).'.'.$r[1];
				$path = IA_ROOT.'/'.$_W['config']['upload']['attachdir'].'/images/';
				$f =fopen($path.$imgname,'w+');
				fwrite($f,base64_decode($r[2]));
				fclose($f);
				$imgurl = $_W['attachurl'].'/images/'.$imgname;
				$is = pdo_insert('xfcommunity_images',array('src'=>$imgurl));
				$id = pdo_insertid();
				if(empty($is)){
				 exit(json_encode(array(
					  'errCode'=>1,
					  'message'=>'上传出现错误',
					  'data'=>array('id'=>$_GPC['t'],'picId'=>$id)
				  )));
				}else{
				  exit(json_encode(array(
					  'errCode'=>0,
					  'message'=>'上传成功',
					  'data'=>array('id'=>$_GPC['id'],'picId'=>$id)
				  )));
				}
			}
			
		}  
	 
}






