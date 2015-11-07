<?php
/**
*$paytype 付款方式
*$goods订单列表
*$order 订单信息
*$address 联系地址
*/
 $body = "";

 if (!empty($goods)) {
    foreach ($goods as $row) {

                        $body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} \n";
                    }
        }
      $body .= "总金额：{$order['price']}元 (".$paytype.")\n";
   		$body .= "真实姓名：$address[realname] \n";
      $body .= "地区：$address[province] - $address[city] - $address[area]\n";
      $body .= "详细地址：$address[address] \n";
      $body .= "手机：$address[mobile] \n";
      $body .= "订单提交成功，请您收到货时付款！";
      
      //发送格式
      //此格式的消息模板为：
      //		您好，您已购买成功。
			//		商品信息：{{name.DATA}}
			//		{{remark.DATA}}
      $datas=array(
							'name'=>array('value'=>'','color'=>'#173177'),
							'remark'=> array('value'=>$body,'color'=>'#173177')
			);
			$data=json_encode($datas); //发送的消息模板数据
	  
?>
	