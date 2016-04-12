<?php

$time_start = microtime(true);
ini_set('date.timezone', 'Asia/Ho_Chi_Minh');

define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', __DIR__ . DS);

require_once ('config.php');
require_once ('const.php');

//library
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'PEAR.php');
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'Savant3.php');
require_once (SERVER_ROOT . 'libs' . DS . 'adodb5' . DS . 'adodb.inc.php');
require_once (SERVER_ROOT . 'libs' . DS . 'jwdate.class.php');
require_once (SERVER_ROOT . 'libs' . DS . 'session.php');
require_once (SERVER_ROOT . 'libs' . DS . 'lang.php');

//mobile detect
require_once(SERVER_ROOT . DS . 'libs' . DS . 'Mobile_Detect.php');

//TCPDF
require_once (SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'config' . DS . 'lang/vn.php');
require_once (SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'config' . DS . 'tcpdf_config_alt.php');
define("K_TCPDF_EXTERNAL_CONFIG", true);
//require_once (SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'zreport.php');
//MVC
require_once (SERVER_ROOT . 'libs' . DS . 'model.php');
require_once (SERVER_ROOT . 'libs' . DS . 'view.php');
require_once (SERVER_ROOT . 'libs' . DS . 'controller.php');
require_once (SERVER_ROOT . 'libs' . DS . 'bootstrap.php');
require_once (SERVER_ROOT . 'libs' . DS . 'functions.php');

require_once (SERVER_ROOT . 'libs' . DS . 'crypt.php');
Crypt::set_key(CRYPT_KEY);

//Cache
require_once (SERVER_ROOT . 'libs' . DS . 'go_paper_cache.php');

$mvc_xml = new Bootstrap(isset($_GET['url']) ? $_GET['url'] : '');

//Timetrack end
$time_end = microtime(true);
$time = $time_end - $time_start;
//if ($_SERVER['REMOTE_ADDR'] == '192.168.1.28' OR $_SERVER['REMOTE_ADDR'] == '113.190.109.65')
//{
//	echo '<pre>Total Time: ' . $time . '</pre>';
//}