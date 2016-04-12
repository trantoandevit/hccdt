<?php

ini_set('date.timezone', 'Asia/Ho_Chi_Minh');

define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', __DIR__ . DS);
define('CLI', 1);

require_once ('config.php');
require_once ('const.php');

//library
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'PEAR.php');
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'Savant3.php');
require_once (SERVER_ROOT . 'libs' . DS . 'adodb5' . DS . 'adodb.inc.php');
require_once (SERVER_ROOT . 'libs' . DS . 'jwdate.class.php');
require_once (SERVER_ROOT . 'libs' . DS . 'session.php');
require_once (SERVER_ROOT . 'libs' . DS . 'lang.php');


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

if (empty($argv[1]))
{
    die('Phai truyen tham so uri, vd: cli.php admin/dashboard');
}
if (php_sapi_name() !== 'cli')
{
    die('CLI only');
}

function log_cli($string)
{
    $date = date_create()->format('d.m.Y H:i');
    file_put_contents(__DIR__ . '/cli_logs.txt', $date . ' | ' . $string . "\n", FILE_APPEND);
}

//handle exception
set_exception_handler(function(Exception $ex)
{
    log_cli('Shutdown due to ' . get_class($ex) . ", trace:\n" . $ex->getTraceAsString() . "\n");
    die;
});

ob_start();
$mvc_xml = new Bootstrap($argv[1]);

$output = trim(ob_get_clean());
if ($output)
{
    log_cli($output);
}

