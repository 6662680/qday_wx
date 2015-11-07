<?php

if ( ! function_exists('img_url'))
{
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
}

/**
 * 补全数据表
 */
if (!function_exists('ct'))
{
    function ct($str) {
        return 'eso_runman_'.$str;
    }
}

/**
 * 补全数据表（含前缀）
 */
if (!function_exists('cta'))
{
    function cta($str) {
        return tablename('eso_red_'.$str);
    }
}

/**
 * 获取地址
 */
if (!function_exists('urido'))
{
    function urido($do = '', $hash = '') {
        $arr = array();
        $arr['m'] = $_GET['m'];
        $arr['rid'] = intval($_GET['rid']);
        if ($do === 0) {
            $do = $_GET['do'];
        }elseif ($do == "manage") {
            unset($arr['rid']);
        }
        //
        if ($do) {
            $arr['do'] = $do;
        }
        $_url = url('site/entry', $arr);
        if ($hash) {
            $_url = get_subto($_url, '', '#');
        }
        return $_url;
    }
}

/**
 * 获取地址
 */
if (!function_exists('urwdo'))
{
    function urwdo($do = '',$hash = '',$domain = 0) {
        $arr = array();
        $arr['m'] = $_GET['m'];
        $arr['rid'] = intval($_GET['rid']);
        if ($do === 0) {
            $do = $_GET['do'];
        }elseif ($do == "manage") {
            unset($arr['rid']);
        }
        if ($do) {
            $arr['do'] = $do;
        }
        //
        $_url = url('entry', $arr);
        if (!$hash) {
            $_url = get_subto($_url, '', '#');
        }
		if ($domain) {
			global $_W;
			$_url = $_W['siteroot']."app/".$_url;
			$_url = str_replace('/./','/',$_url);
		}
        return $_url;
    }
}

/**
 * 返回含 rid 数组/字符
 */
if (!function_exists('merge'))
{
    function merge($str = '') {
        if ($str === 0) {
            return array('rid'=>intval($_GET['rid']));
        }elseif (empty($str)) {
            return ' `rid`='.intval($_GET['rid']).' ';
        }elseif (is_array($str)) {
            return array_merge($str, array('rid'=>intval($_GET['rid'])));
        }else{
            return $str.' AND `rid`='.intval($_GET['rid']).' ';
        }
    }
}

/**
 * 获取值
 */
if (!function_exists('value'))
{
    function value($obj, $key='', $null_is_arr = false, $default = ''){
        if (!empty($key)){
            $arr = explode(".", str_replace("|", ".", $key));
            foreach ($arr as $val){
                if (isset($obj[$val])){
                    $obj = $obj[$val];
                }else{
                    $obj = "";break;
                }
            }
        }
        if ($default && empty($obj)) $obj = $default;
        if ($null_is_arr && empty($obj)) $obj = array();
        return $obj;
    }
}

/**
 * $a=$b 则返回$c
 */
if (!function_exists('isto'))
{
    function isto($a, $b, $c){
        if ($a == $b){
            return $c;
        }else{
            return "";
        }
    }
}

/**
 * $n=$v 则返回selected="selected"
 */
if (!function_exists('sel'))
{
    function sel($n, $v, $d = false){
        if ($d && empty($v)) return 'selected="selected"';
        return ($n == $v)?' selected="selected"':'';
    }
}

/**
 * 给文字加颜色标签font
 */
if (!function_exists('col'))
{
    function col($n, $c=''){
        if (!empty($c)){
            return "<font color='".$c."'>".$n."</font>";
        }else{
            return $n;
        }
    }
}

/**
 * 给文字加颜色标签style
 */
if (!function_exists('cot'))
{
    function cot($color){
        if ($color){
            return " style='color:".$color.";'";
        }
    }
}

/**
 * $n=$v 则返回checked="true"
 */
if (!function_exists('che'))
{
    function che($n, $v, $d = false){
        if ($d && empty($v)) return 'checked="true"';
        $val = " value=\"".$v."\"";
        if (is_array($n)){
            $val.= (in_array($v, $n))?' checked="true"':'';
        }else{
            $val.= ($n == $v)?' checked="true"':'';
        }
        return $val;
    }
}

/**
 * $n 包含,$v, 则返回checked="true"
 */
if (!function_exists('ches'))
{
    function ches($n, $v, $d = false){
        if ($d && empty($v)) return 'checked="true"';
        $val = " value=\"".$v."\"";
        $val.= (strpos($n, ",".$v.",") !== false)?' checked="true"':'';
        return $val;
    }
}

/**
 * $n[$v] 存在则返回checked="true"
 */
if (!function_exists('chi'))
{
    function chi($n, $v){
        $val = '';
        if (!empty($v)){
            if (isset($n[$v])){
                $val = ' checked="true"';
            }
        }
        return $val;
    }
}

/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
if (!function_exists('string2array'))
{
    function string2array($data) {
        if(is_array($data)) return $data;
        if($data == '') return array();
        @eval("\$array = $data;");
        $array = isset($array)?$array:array();
        return is_array($array)?$array:array();
    }
}

/**
 * 将数组转换为字符串
 *
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
if (!function_exists('array2string'))
{
    function array2string($data, $isformdata = 1) {
        if($data == '') return '';
        if($isformdata) $data = new_stripslashes($data);
        return var_export($data, TRUE);
    }
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
if (!function_exists('new_stripslashes'))
{
    function new_stripslashes($string) {
        if(!is_array($string)) return stripslashes($string);
        foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
        return $string;
    }
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
if (!function_exists('new_addslashes'))
{
    function new_addslashes($string){
        if(!is_array($string)) return addslashes($string);
        foreach($string as $key => $val) $string[$key] = new_addslashes($val);
        return $string;
    }
}

/**
 * 截取指定字符串
 * @param $str
 * @param string $ta
 * @param string $tb
 * @return string
 */
if (!function_exists('get_subto'))
{
    function get_subto($str, $ta = '', $tb = ''){
        if ($ta && strpos($str, $ta) !== false){
            $str = substr($str, strpos($str, $ta) + strlen($ta));
        }
        if ($tb && strpos($str, $tb) !== false){
            $str = substr($str, 0, strpos($str, $tb));
        }
        return $str;
    }
}

if (!function_exists('gourl'))
{
	function gourl($url) {
		if (empty($url)){
			$url = get_url();
		}
		header("Location: ".$url);
		exit();
	}
}

/**
 * 获取当前页面地址
 * @return string
 */
if (!function_exists('get_url'))
{
    function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
        $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}

/**
 * @param $str	变量,用半角逗号隔开
 * @param string $amp	“&”的显示方式
 * @param string $baoliu	采用保留方式
 * @param array $array	链接自定变量
 * @param string $allurl	留空默认保留全路径
 * @return string
 */
if (!function_exists('get_link'))
{
	function get_link($str, $amp = '', $baoliu = '', $array = array(), $allurl = '') {
		if (!$amp) $amp = '&';
		$str = str_replace("|", ",", $str);
		$arr = explode(',',$str);
		$get = !empty($array)?$array:$_GET;
		if ($baoliu){
			$get = array();
			foreach($arr as $key=>$value){
				$get[$value] = $_GET[$value];
			}
		}else{
			foreach($arr as $key=>$value){
				unset($get[$value]);
			}
		}
		$url ='';
		if (!empty($get)){
			//ksort($get);
			foreach($get as $k=>$v){
				$url .="{$k}={$v}{$amp}";
			}
		}
		$url=!empty($url)?"?".substr($url,0,-(strlen($amp))):'?index='.generate_password(5);
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		if ($allurl){
			return $_SERVER['PHP_SELF'].$url;
		}else{
			return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$_SERVER['PHP_SELF'].$url;
		}
	}
}

/**
 *
 * @param $str
 * @return string
 */
if (!function_exists('get_request'))
{
	function get_request($str) {
		$str=rawurldecode($str);
		$strtrim=rtrim($str,']');
		if (substr($strtrim,0,4)=='GET['){
			$getkey=substr($strtrim,4);
			return $_GET[$getkey];
		}elseif (substr($strtrim,0,5)=='POST['){
			$getkey=substr($strtrim,5);
			return $_POST[$getkey];
		}elseif (substr($strtrim,0,6)=='PGEST['){
			$getkey=substr($strtrim,6);
			return $_POST[$getkey]?$_POST[$getkey]:$_GET[$getkey];
		}else{
			return $str;
		}
	}
}


/**
 * 随机字符串
 * @param $length 随机字符长度;
 * @param $type   1数字、2大小写字母、21小写字母、22大写字母、默认全部;
 */
if (!function_exists('generate_password'))
{
	function generate_password( $length = 8 ,$type = '') {
		// 密码字符集，可任意添加你需要的字符
		switch ($type){
			case '1':
				$chars = '0123456789';
				break;
			case '2':
				$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case '21':
				$chars = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case '22':
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			default:
				$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				break;
		}
		$passwordstr = '';
		for ( $i = 0; $i < $length; $i++ ){
			$passwordstr .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
		return $passwordstr;
	}
}

/**
 * 去除html
 * @param $text
 * @param $length
 */
if (!function_exists('get_html'))
{
    function get_html($text, $length = 255){
        if (istrlen($text) > $length) {
            $text = cutstr(strip_tags($text), $length, true);
        }else{
            $text = cutstr(strip_tags($text), $length);
        }
        return $text;
    }
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
if (!function_exists('safe_replace'))
{
    function safe_replace($string) {
        $string = str_replace('%20','',$string);
        $string = str_replace('%27','',$string);
        $string = str_replace('%2527','',$string);
        $string = str_replace('*','',$string);
        $string = str_replace('"','&quot;',$string);
        $string = str_replace("'",'',$string);
        $string = str_replace('"','',$string);
        $string = str_replace(';','',$string);
        $string = str_replace('<','&lt;',$string);
        $string = str_replace('>','&gt;',$string);
        $string = str_replace("{",'',$string);
        $string = str_replace('}','',$string);
        $string = str_replace('\\','',$string);
        return $string;
    }
}

/**
 * 状态颜色
 */
if (!function_exists('mallstatus'))
{
	function mallstatus($status){
		$_cl = "#FF6600";
		if ($status == "交易成功") {
			$_cl = "#008C46";
		}elseif ($status == "交易关闭") {
			$_cl = "#999999";
		}elseif ($status == "商家已确认") {
			$_cl = "#14AD57";
		}elseif ($status == "商家已发货") {
			$_cl = "#7B61E4";
		}elseif ($status == "已付款") {
			$_cl = "#E850EE";
		}
		return '<font color="'.$_cl.'">'.$status.'</font>';
	}
}

/**
 * 存 cookie
 * @param $goodsid
 * @param null $val
 * @return mixed
 */
if (!function_exists('cate_cookie'))
{
	function cate_cookie($goodsid, $val = null) {
		$cname = "c_buy_".$goodsid;
		if ($val === null) {
			return (isset($_COOKIE[$cname]))?intval($_COOKIE[$cname]):'0';
		}elseif ($val == "-1") {
			$_COOKIE[$cname] = 0;
			setcookie($cname, "", time()-3600);
		}else{
			$_COOKIE[$cname] = intval($val);
			setcookie($cname, intval($val), time()+3600);
		}
	}
}

/**
 * 是否筛选属性
 * @param $setting
 * @return string
 */
if (!function_exists('cate_isattr'))
{
    function cate_isattr($setting) {
        $setting = string2array($setting);
        $attrdefault = value($setting, 'attrdefault');
        if ($attrdefault) {
            //默认属性
            return $attrdefault;
        }else{
            //无默认属性
            $attrA = array();
            $goodsattr = value($setting,'attr',true);
            foreach($goodsattr AS $arrval){
                if (!is_array($arrval)) continue;
                foreach($arrval AS $key=>$val){
                    if (!is_array($val)) continue;
                    if (count($val) > 1){
                        $attrA[$key] = $val;
                        break;
                    }
                }
            }
            return ($attrA)?1:0;
        }
    }
}

/**
 * 新建目录
 */
if (!function_exists('make_dir'))
{
    function make_dir($path){
        if(!file_exists($path)){
            make_dir(dirname($path));
            @mkdir($path,0777);
            @chmod($path,0777);
        }
    }
}

/**
 * 获取IP
 * @return string
 */
if (!function_exists('get_ip'))
{
    function get_ip(){
        if (getenv('HTTP_CLIENT_IP') and strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {
            $onlineip=getenv('HTTP_CLIENT_IP');
        }elseif (getenv('HTTP_X_FORWARDED_FOR') and strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {
            $onlineip=getenv('HTTP_X_FORWARDED_FOR');
        }elseif (getenv('REMOTE_ADDR') and strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {
            $onlineip=getenv('REMOTE_ADDR');
        }elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] and strcasecmp($_SERVER['REMOTE_ADDR'],'unknown')) {
            $onlineip=$_SERVER['REMOTE_ADDR'];
        }else{
            $onlineip = "";
        }
        preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $onlineip, $match);
        return $onlineip = $match[0] ? $match[0] : 'unknown';
    }
}



if (!function_exists('get_total'))
{
	function db_total($sql, $wherearr = array()) {
		if (strpos(strtolower($sql),'select') === false) {
			$sql = "SELECT COUNT(*) AS num FROM  ".$sql;
		}
		if (is_array($wherearr) && !empty($wherearr)){
			$wheresql = "1=1";
			foreach ($wherearr as $key=>$val) {
				$wheresql.= " AND `{$key}`='{$val}'";
			}
			$sql.= " WHERE ".$wheresql;
		}
		$row = pdo_fetchall($sql);
		$v=0;
		if (!empty($row) && is_array($row)){
			foreach($row as $n){
				$v = $v + $n['num'];
			}
		}
		return $v;
	}
}

if (!function_exists('db_getone'))
{
	function db_getone($sql, $wherearr = array(), $ordersql = '') {
		if (strpos(strtolower($sql),'select') === false) {
			$sql = "SELECT * FROM  ".$sql;
		}
		if (is_array($wherearr) && !empty($wherearr)){
			$wheresql = "1=1";
			foreach ($wherearr as $key=>$val) {
				$wheresql.= " AND `{$key}`='{$val}'";
			}
			$sql.= " WHERE ".$wheresql;
		}
		if ($ordersql){
			$sql.= " ORDER BY ".$ordersql;
		}
		return pdo_fetch($sql);
	}
}

if (!function_exists('db_getall'))
{
	function db_getall($sql, $wherearr = array(), $ordersql = '') {
		if (strpos(strtolower($sql),'select') === false) {
			$sql = "SELECT * FROM  ".$sql;
		}
		if (is_array($wherearr) && !empty($wherearr)){
			$wheresql = "1=1";
			foreach ($wherearr as $key=>$val) {
				$wheresql.= " AND `{$key}`='{$val}'";
			}
			$sql.= " WHERE ".$wheresql;
		}
		if ($ordersql){
			$sql.= " ORDER BY ".$ordersql;
		}
		return pdo_fetchall($sql);
	}
}

if (!function_exists('db_query'))
{
	function db_update($table, $data = array(), $where = array()) {
		$newdate = db_data_preg($data);
		if ($newdate) {
			return db_query("UPDATE ". $table." SET ".implode(',', $newdate), $where);
		}else{
			$table = preg_replace("/`".$GLOBALS['_W']['config']['db']['tablepre']."(.*?)`/isU", "$1", $table);
			$table = preg_replace("/".$GLOBALS['_W']['config']['db']['tablepre']."/isU", '', $table);
			return pdo_update($table, $data, $where);
		}
	}
}

if (!function_exists('db_query'))
{
	function db_query($sql, $wherearr = array()) {
		if (is_array($wherearr) && !empty($wherearr)){
			$wheresql = "1=1";
			foreach ($wherearr as $key=>$val) {
				$wheresql.= " AND `{$key}`='{$val}'";
			}
			$sql.= " WHERE ".$wheresql;
		}
		return pdo_query($sql);
	}
}

if (!function_exists('db_insert'))
{
	function db_insert($table, $data = array(), $retid = false){
		$table = preg_replace("/`".$GLOBALS['_W']['config']['db']['tablepre']."(.*?)`/isU", "$1", $table);
		$table = preg_replace("/".$GLOBALS['_W']['config']['db']['tablepre']."/isU", '', $table);
		if ($retid) {
			if (pdo_insert($table, $data, $retid)) {
				return pdo_insertid();
			}else{
				return 0;
			}
		}else{
			return pdo_insert($table, $data, $retid);
		}
	}
}

if (!function_exists('db_delete'))
{
	function db_delete($table, $where = array(), $glue = 'AND'){
		$table = preg_replace("/`".$GLOBALS['_W']['config']['db']['tablepre']."(.*?)`/isU", "$1", $table);
		$table = preg_replace("/".$GLOBALS['_W']['config']['db']['tablepre']."/isU", '', $table);
		return pdo_delete($table, $where, $glue);
	}
}

if (!function_exists('db_data_preg'))
{
	function db_data_preg($data) {
		if (empty($data)) return $data;
		$fields = array();
		$isfields = false;
		foreach ($data as $key => $value) {
			preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $key, $match);
			if (isset($match[3])) {
				if (is_numeric($value)) {
					$fields[] = db_column_quote($match[1]) . ' = ' . db_column_quote($match[1]) . ' ' . $match[3] . ' ' . $value;
					$isfields = true;
				}
			}else{
				$column = db_column_quote($key);
				switch (gettype($value)){
					case 'NULL':
						$fields[] = $column . ' = NULL';
						break;
					case 'array':
						preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);
						if (isset($column_match[0])) {
							$fields[] = db_column_quote($column_match[1]) . ' = ' . json_encode($value);
						}else{
							$fields[] = $column . ' = ' . serialize($value);
						}
						break;
					case 'boolean':
						$fields[] = $column . ' = ' . ($value ? '1' : '0');
						break;
					case 'integer':
					case 'double':
					case 'string':
						$fields[] = $column . ' = ' . $value;
						break;
				}
			}
		}
		return $isfields?$fields:array();
	}
}

if (!function_exists('db_column_quote'))
{
	function db_column_quote($string) {
		return '`' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\))/', '', $string)) . '`';
	}
}


function val_l($val = 0) {
	if ($val > 90) {
		return 'l3';
	}elseif ($val > 80) {
		return 'l2';
	}else{
		return 'l1';
	}
}