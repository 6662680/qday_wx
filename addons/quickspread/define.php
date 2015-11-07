<?php

global $_W;
if (!defined('QUICKSPREAD_INC')) {
  define('QUICKSPREAD_INC', 1);
  define('APP_FONT', IA_ROOT . '/addons/quickfont/');
  define('APP_PHP', IA_ROOT . '/addons/quickspread/');
  define('APP_WEB', IA_ROOT . '/addons/quickspread/template/');
  define('APP_MOB', IA_ROOT . '/addons/quickspread/template/mobile/');
  define('ATTACH_DIR', IA_ROOT . '/attachment/');
  define('RES_CSS', $_W['siteroot'] . '/addons/quickspread/css/');
  define('RES_JS',  $_W['siteroot'] . '/addons/quickspread/js/');
  define('RES_IMG', $_W['siteroot'] . '/addons/quickspread/img/');
}
