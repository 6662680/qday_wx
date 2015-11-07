<?php
/**
 * 扫码查快递模块处理程序
 *
 * @author 华轩科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_expressModuleProcessor extends WeModuleProcessor {
	public function respond() {
		if( $this->message['event'] == 'scancode_waitmsg' ){
			$qrtype = $this->message['scancodeinfo']['scantype'];
			if ($qrtype == 'barcode') {
				$CodeInfo = $this->message['scancodeinfo']['scanresult'];
				$Codearr = explode(",",$CodeInfo);
				$Code = $Codearr['1'];
			}else{
				$Code = $this->message['scancodeinfo']['scanresult'];
			}
		}else{
			$rid = $this->rule;
			$sql = "SELECT * FROM " . tablename('rule_keyword') . " WHERE `rid`=:rid LIMIT 1";
			$row = pdo_fetch($sql, array(':rid' => $rid));
			$keywords = $row['content'];  // 取得正则表达式			
			//查询防伪码
			preg_match('/'.$keywords.'(.*)/', $this->message['content'], $match);
			$Code = $match[1];
		}
		//return $this->respText($Code);
		$express = new Express();
		$result  = $express -> getorder($Code);
		if ($result['status'] == '200') {
			$time = $result['updatetime'];
			foreach ($result['data'] as $trace) {
				$reply .= "{$trace['time']}\n{$trace['context']}\n-------\n";
			}
			$msg = "快递单号：\n".$Code."\n最后更新：\n".$time."\n流转情况：\n".$reply;
		}else{
			$msg = '查询失败 '.$result['message'];
		}
		return $this->respText($msg);
	}
}

class Express
{
    /*
     * 网页内容获取方法
    */
    private function getcontent($url)
    {
        if (function_exists("file_get_contents")) {
            $file_contents = file_get_contents($url);
        } else {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }
    /*
     * 获取对应名称和对应传值的方法
    */
    private function expressname($order)
    {
        $name   = json_decode($this->getcontent("http://www.kuaidi100.com/autonumber/auto?num={$order}"), true);
        $result = $name[0]['comCode'];
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }
    /*
     * 返回$data array      快递数组查询失败返回false
     * @param $order        快递的单号
     * $data['ischeck'] ==1 已经签收
     * $data['data']        快递实时查询的状态 array
    */
    public function getorder($order)
    {
        $keywords = $this->expressname($order);
        if (!$keywords) {
            return false;
        } else {
            $result = $this->getcontent("http://www.kuaidi100.com/query?type={$keywords}&postid={$order}");
            $data   = json_decode($result, true);
            return $data;
        }
    }
}