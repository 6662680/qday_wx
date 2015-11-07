<?php

defined('IN_IA') or exit('Access Denied');
class WechatUtil {

  public static function fans_search($from_user, $fields = array()) {
//    $uid = pdo_fetchcolumn("SELECT a.uid FROM "
//      . tablename('mc_mapping_fans') . " a inner join "
//      . tablename('mc_members') . " b on a.uid=b.uid "
//      . " WHERE a.openid='{$from_user}' LIMIT 1");
//    return fans_search($uid, $fields);
      load()->model('mc');
      $uid = mc_openid2uid($from_user);
      return mc_fetch($uid,$fields);
  }

  public static function createMobileUrl($do, $modulename, $query = array(), $noredirect = false) {
    global $_W;
    $query['do'] = $do;
    $query['m'] = strtolower($modulename);
    return self::murl('entry', $query, $noredirect);
  }

  public static function murl($segment, $params = array(), $noredirect = false) {
    global $_W;
    list($controller, $action, $do) = explode('/', $segment);
    $url = './app/index.php?i=' . $_W['uniacid'] . '&';
    if(!empty($controller)) {
      $url .= "c={$controller}&";
    }
    if(!empty($action)) {
      $url .= "a={$action}&";
    }
    if(!empty($do)) {
      $url .= "do={$do}&";
    }
    if(!empty($params)) {
      $queryString = http_build_query($params, '', '&');
      $url .= $queryString;
      if($noredirect === false) {
        $url .= '&wxref=mp.weixin.qq.com#wechat_redirect';
      }
    }
    return $url;
  }

  public static function curl_file_get_contents($durl){
    $r = null;
    if(function_exists('curl_init') && function_exists('curl_exec')) {
      WeUtility::logging("using curl");
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $durl);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $r = curl_exec($ch);
      curl_close($ch);
    }
    return $r;
  }

public static function http_request($url, $post = '', $extra = array(), $timeout = 60000)
{
  $timeout = intval($timeout / 1000);
  $timeout = (0 == $timeout) ? 1 : $timeout;
  load()->func('communication');
  //return ihttp_request($url, $post, $extra, $timeout);
  $urlset = parse_url($url);
  if(empty($urlset['path'])) {
    $urlset['path'] = '/';
  }
  if(!empty($urlset['query'])) {
    $urlset['query'] = "?{$urlset['query']}";
  }
  if(empty($urlset['port'])) {
    $urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
  }
  if (strexists($url, 'https://') && !extension_loaded('openssl')) {
    if (!extension_loaded("openssl")) {
      message('请开启您PHP环境的openssl');
    }
  }
  if(function_exists('curl_init') && function_exists('curl_exec')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlset['scheme']. '://' .$urlset['host'].($urlset['port'] == '80' ? '' : ':'.$urlset['port']).$urlset['path'].$urlset['query']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if($post) {
      curl_setopt($ch, CURLOPT_POST, 1);
      if (is_array($post)) {
        $filepost = false;
        foreach ($post as $name => $value) {
          if (substr($value, 0, 1) == '@') {
            $filepost = true;
            break;
          }
        }
        if (!$filepost) {
          $post = http_build_query($post);
        }
      }
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    if (defined('CURL_SSLVERSION_TLSv1')) {
      curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
    if (!empty($extra) && is_array($extra)) {
      $headers = array();
      foreach ($extra as $opt => $value) {
        if (strexists($opt, 'CURLOPT_')) {
          curl_setopt($ch, constant($opt), $value);
        } elseif (is_numeric($opt)) {
          curl_setopt($ch, $opt, $value);
        } else {
          $headers[] = "{$opt}: {$value}";
        }
      }
      if(!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      }
    }
    $data = curl_exec($ch);
    $status = curl_getinfo($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($errno || empty($data)) {
      return error(1, $error);
    } else {
      return ihttp_response_parse($data);
    }
  }
  $method = empty($post) ? 'GET' : 'POST';
  $fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
  $fdata .= "Host: {$urlset['host']}\r\n";
  if(function_exists('gzdecode')) {
    $fdata .= "Accept-Encoding: gzip, deflate\r\n";
  }
  $fdata .= "Connection: close\r\n";
  if (!empty($extra) && is_array($extra)) {
    foreach ($extra as $opt => $value) {
      if (!strexists($opt, 'CURLOPT_')) {
        $fdata .= "{$opt}: {$value}\r\n";
      }
    }
  }
  $body = '';
  if ($post) {
    if (is_array($post)) {
      $body = http_build_query($post);
    } else {
      $body = urlencode($post);
    }
    $fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
  } else {
    $fdata .= "\r\n";
  }
  if($urlset['scheme'] == 'https') {
    $fp = fsockopen('ssl://' . $urlset['host'], $urlset['port'], $errno, $error);
  } else {
    $fp = fsockopen($urlset['host'], $urlset['port'], $errno, $error);
  }
  stream_set_blocking($fp, true);
  stream_set_timeout($fp, $timeout);
  if (!$fp) {
    return error(1, $error);
  } else {
    fwrite($fp, $fdata);
    $content = '';
    while (!feof($fp))
      $content .= fgets($fp, 512);
    fclose($fp);
    return ihttp_response_parse($content, true);
  }
}

  /* 为了兼容json_encode把中文变成Uniconde问题 */
  /**************************************************************
       *
       *    使用特定function对数组中所有元素做处理
       *    @param  string  &$array     要处理的字符串
       *    @param  string  $function   要执行的函数
       *    @return boolean $apply_to_keys_also     是否也应用到key上
       *    @access public
       *
     *************************************************************/
    private static function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
              self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
     
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
     
    /**************************************************************
     *
     *    将数组转换为JSON字符串（兼容中文）
     *    @param  array   $array      要转换的数组
     *    @return string      转换得到的json字符串
     *    @access public
     *
     *************************************************************/
    public static function json_encode($array) {
        self::arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

    public static function saveFile($file_url, $save_as_filename) {
      $data = file_get_contents($file_url);
      $fp = @fopen($save_as_filename, "w"); //以写方式打开文件
      @fwrite($fp,$data); //
      fclose($fp);//完工，哈
      return $save_as_filename;
    }


  public static function decode_channel_param($item, $p)
  {
    $gpc = unserialize($p);
    $item['qrleft'] = intval($gpc['qrleft']) ? intval($gpc['qrleft']) : 145;
    $item['qrtop'] = intval($gpc['qrtop']) ? intval($gpc['qrtop']) : 475;
    $item['qrwidth'] = intval($gpc['qrwidth']) ? intval($gpc['qrwidth']) : 240;
    $item['qrheight'] = intval($gpc['qrheight']) ? intval($gpc['qrheight']) : 240;
    $item['avatarleft'] = intval($gpc['avatarleft']) ? intval($gpc['avatarleft']) : 40;
    $item['avatartop'] = intval($gpc['avatartop']) ? intval($gpc['avatartop']) : 40;
    $item['avatarwidth'] = intval($gpc['avatarwidth']) ? intval($gpc['avatarwidth']) : 96;
    $item['avatarheight'] = intval($gpc['avatarheight']) ? intval($gpc['avatarheight']) : 96;
    $item['avatarenable'] = intval($gpc['avatarenable']);
    $item['nameleft'] = intval($gpc['nameleft']) ? intval($gpc['nameleft']) : 150;
    $item['nametop'] = intval($gpc['nametop']) ? intval($gpc['nametop']) : 50;
    $item['namesize'] = intval($gpc['namesize']) ? intval($gpc['namesize']) : 30;
    $item['namecolor'] = $gpc['namecolor'];
    $item['nameenable'] = intval($gpc['nameenable']);
    $item['genqr_info1'] = empty($gpc['genqr_info1']) ? '正在为您生成二维码中，请稍候。大约需要等待6秒钟，稍等哦。。' : $gpc['genqr_info1'];
    $item['genqr_info2'] = empty($gpc['genqr_info2']) ? '头像生成中，马上就好哦。。' : $gpc['genqr_info2'];
    $item['genqr_info3'] = empty($gpc['genqr_info3']) ? '在为您生成二维码中，请稍候。。。' : $gpc['genqr_info3'];
    return $item;
  }

  public static function encode_channel_param($gpc) {
    $params = array(
      'qrleft'=>intval($gpc['qrleft']),
      'qrtop'=>intval($gpc['qrtop']),
      'qrwidth'=>intval($gpc['qrwidth']),
      'qrheight'=>intval($gpc['qrheight']),
      'avatarleft'=>intval($gpc['avatarleft']),
      'avatartop'=>intval($gpc['avatartop']),
      'avatarwidth'=>intval($gpc['avatarwidth']),
      'avatarheight'=>intval($gpc['avatarheight']),
      'avatarenable'=>intval($gpc['avatarenable']),
      'nameleft'=>intval($gpc['nameleft']),
      'nametop'=>intval($gpc['nametop']),
      'namesize'=>intval($gpc['namesize']),
      'namecolor'=>intval($gpc['namecolor']),
      'nameenable'=>intval($gpc['nameenable']),
      'genqr_info1' => $gpc['genqr_info1'],
      'genqr_info2' => $gpc['genqr_info2'],
      'genqr_info3' => $gpc['genqr_info3'],
    );
    return serialize($params);
  }

}



