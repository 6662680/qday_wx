<?php
defined('IN_IA') or exit('Access Denied');

  function getMobileDisplayRaw($data, $type) {
    global $_W;
    $str = '';
    switch($type) {
    case 'hidden':
    case 'tel':
    case 'option':
      $str = '';
      break;
    case 'text':
    case 'number':
    case 'textarea':
      $str = $data;
      break;
    case 'richtext':
      $str = htmlspecialchars_decode($data);
      break;
    case 'time':
      $str = date('Y-m-d H:i', $data);
      break;
    case 'image':
      $str = (strpos($data, 'http://') === FALSE) ? $_W['attachurl'] . $data : $data;
      break;
    default:
      break;
    }
    return $str;
  }


  function getMobileDisplayTpl($data, $conf) {
    global $_W;
    $str = '';
    switch($conf['type']) {
    case 'hidden':
    case 'tel':
    case 'option':
      $str = '';
      break;
    case 'text':
    case 'number':
    case 'textarea':
      $str = $data;
      break;
    case 'richtext':
      $str = htmlspecialchars_decode($data);
      break;
    case 'time':
      $str = date('Y-m-d H:i', $data);
      break;
    case 'image':
      $url =(strpos($data, 'http://') === FALSE) ? $_W['attachurl'] . $data : $data;
      $str = "<img width='150px' src='" . $url . "' />";
      break;
    default:
      break;
    }
    return $str;
  }


  function getWebDisplayTpl($data, $conf) {
    global $_W;
    $str = '';
    switch($conf['type']) {
    case 'hidden':
    case 'text':
    case 'number':
    case 'tel':
    case 'option':
      $str = (strlen($data) < 400) ? $data : substr($data, 0, 400) . "....";
      break;
    case 'textarea':
      $str = (strlen($data) < 400) ? $data : substr($data, 0, 400) . "....";
      break;
    case 'richtext':
      $str = htmlspecialchars_decode($data);
      break;
    case 'time':
      $str = date('Y-m-d H:i', $data);
      break;
    case 'image':
      $url =(strpos($data, 'http://') === FALSE) ? $_W['attachurl'] . $data : $data;
      $str = "<img width='150px' src='" . $url . "' />";
      break;
    default:
      break;
    }
    return $str;
  }
  
  function getMobileDisplayTitleTpl($conf) {
    $str = '';
    switch($conf['type']) {
    case 'hidden':
      $str = '';
      break;
    default:
      $str = '<b>' . $conf['title'] . '</b>';
      break;
    }
    return $str;
  }


  function getMobilePostTitleTpl($conf) {
    $str = '';
    switch($conf['type']) {
    case 'hidden':
      $str = '';
      break;
    default:
      $str = '<b>' . $conf['title'] . '</b>';
      break;
    }
    return $str;
  }

  function getMobilePostTpl($data, $conf) {
    $str = '';
    switch($conf['type']) {
    case 'hidden':
      $str = "<input type='hidden' id='{$conf['name']}' name='{$conf['name']}' value='{$data}' />";
      break;
    case 'time':
      $str = tpl_form_field_date($conf['name'], date('Y-m-d H:i', $data));
      break;
    case 'image':
      $str = tpl_form_field_image($conf['name'], $data);
      break;
    case 'textarea':
      $str = "<textarea class='span7' type='text' id='{$conf['name']}' name='{$conf['name']}'>{$data}</textarea>";
      break;
    case 'richtext':
      $str = "<textarea class='span7 richtext-clone' type='text' id='{$conf['name']}' name='{$conf['name']}'>{$data}</textarea>";
      break;
    case 'number':
      $str = "<input class='span7' type='number' id='{$conf['name']}' name='{$conf['name']}' value='{$data}' />";
      break;
    case 'text':
    case 'tel':
    case 'option':
    default:
      $str = "<input class='span7' type='text' id='{$conf['name']}' name='{$conf['name']}' value='{$data}' />";
      break;
    }
    return $str;
  }



  function getWebPostTpl($data, $conf) {
    $str = '';
    switch($conf['type']) {
    case 'hidden':
      $str = $data;
      $str .= "<input type='hidden' id='{$conf['name']}' name='{$conf['name']}' value='{$data}' />";
      break;
    case 'time':
      $str = tpl_form_field_date($conf['name'], date('Y-m-d H:i', $data));
      break;
    case 'image':
      $str = tpl_form_field_image($conf['name'], $data);
      break;
    case 'textarea':
      $str = "<textarea rows='4' class='span7' type='text' id='{$conf['name']}' name='{$conf['name']}'>{$data}</textarea>";
      break;
    case 'richtext':
      $str = "<textarea class='span7 richtext-clone' type='text' id='{$conf['name']}' name='{$conf['name']}'>{$data}</textarea>";
      break;
    case 'text':
    case 'tel':
      $str = "<input class='span7' type='text' id='{$conf['name']}' name='{$conf['name']}' value='{$data}' />";
      break;
    case 'number':
      $str = "<input class='span3' type='number' id='{$conf['name']}' step='10000' name='{$conf['name']}' value='{$data}' />";
      break;
    case 'option':
      $options = explode('|', $conf['value']);
      $str = "<select name='{$conf['name']}'>";
      foreach($options as $_o) {
        $checked = ($_o==$data)?'selected':'';
        $str .= "<option value ='{$_o}' {$checked}>{$_o}</option>";
      }
      $str .= "</select>";
      break;
    default:
      break;
    }
    return $str;
  }

  function getWebInput($gpc, $config) {
    $data = array();
    foreach($config as $c) {
      if ($c['required'] == true && (!isset($gpc[$c['name']]) or empty($gpc[$c['name']]))) {
        message($c['title'] . '没有设置', '', 'error');
      }
      if (!isset($gpc[$c['name']]) or empty($gpc[$c['name']])) {
        // just skip
      } else {
        switch($c['type']) {
        case 'time':
          $data[$c['name']] = strtotime($gpc[$c['name']]);
          break;
        default:
          $data[$c['name']] = $gpc[$c['name']];
          break;
        }
      }
    }
    return $data;
  }

  function getMobileInput($gpc, $config) {
    return getWebInput($gpc, $config);
  }


