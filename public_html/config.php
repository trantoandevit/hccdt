<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//File này lưu thông tin kết nối CSDL và hằng số đường dẫn.

/******************** Hang so ket noi CSDL ***********************************/
define('CONST_DB_SERVER_ADDRESS', '172.16.10.90');
define('CONST_DB_DATABASE_NAME', 'hcc-bacgiang');
define('CONST_DB_USER_NAME', 'web_user');
define('CONST_DB_USER_PASSWORD', '123456');
define('DATABASE_TYPE','MYSQL');//Be MSSQL or ORACLE or MYSQL
/******************************************************************************/

//Che do Debug:
//          o   FALSE: Chay that
//          o   TRUE:  Bat che do debug 
$debug = 0;
if(isset($_REQUEST['debug']))
{
    $debug =99;
}
define('DEBUG_MODE',$debug);

//Duong dan dia chi goc
//          o   /virtual-directory/: Chay bang thu muc ao (Virtual Directory)
//          o   /                  : Chay bang domain

define('SITE_ROOT','/hcc-bacgiang/');



//Thu muc luu cache
define('_CONST_SERVER_CACHE_ROOT', SERVER_ROOT . 'cache' . DS . 'html' . DS);

//mã hóa
define('CRYPT_KEY', 'troi may');

//recaptcha
define('RECAPTCHA_PUBLIC_KEY', '6LdtfdoSAAAAACDQFj2SIzHzjOk7SFDyaZO-xo6t');
define('RECAPTCHA_PRIVATE_KEY', '6LdtfdoSAAAAAMN-v6g-5NIZsYF1sgdil2liUWJX');

//Exchange rate svc
define('EXCHANGE_RATE_SVC_URL', 'http://www.vietcombank.com.vn/ExchangeRates/ExrateXML.aspx');
define('EXCHANGE_RATE_SVC_SRC', 'vietcombank.com.vn');

//Gold price svc
define('GOLD_PRICE_SVC_URL', 'http://www.sjc.com.vn/xml/tygiavang.xml');
define('GOLD_PRICE_SVC_SRC', 'sjc.com.vn');

//Weather svc
//lang son
//define('WEATHER_SVC_URL', 'http://weather.yahooapis.com/forecastrss?w=1240015&u=c');
//vinh phuc
define('WEATHER_SVC_URL', 'http://weather.yahooapis.com/forecastrss?w=20070090&u=c');
define('WEATHER_SVC_SRC', 'yahooapis.com');


/******************************************************************************/
//KHONG DIEU CHINH cac tham so duoi day
if (defined('CLI') == false)
{
    if (!preg_match('/^http:/', SITE_ROOT) OR ! preg_match('/^https:/', SITE_ROOT))
    {
        if (empty($_SERVER['HTTPS']))
        {
            define('FULL_SITE_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
        }
        else
        {
            define('FULL_SITE_ROOT', 'https://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
        }
    }
    else
    {
        define('FULL_SITE_ROOT', SITE_ROOT);
    }
}

//Oracle Connect
define('CONST_ORACLE_DSN', 'oci8://mvcxml:mvcxml@172.16.1.252/XE');

//SQL Server Connect
define('CONST_MSSQL_DSN', 'PROVIDER=SQLOLEDB;DRIVER={SQL Server};SERVER=' . CONST_DB_SERVER_ADDRESS . ';DATABASE=' . CONST_DB_DATABASE_NAME . ';UID='.CONST_DB_USER_NAME . ';PWD=' . CONST_DB_USER_PASSWORD . ';');

//MySQL Connect
define('CONST_MYSQL_DSN','mysqli://' . CONST_DB_USER_NAME . ':' . CONST_DB_USER_PASSWORD . '@' .CONST_DB_SERVER_ADDRESS . '/' .  CONST_DB_DATABASE_NAME);

//Pear libs
define('_PATH_TO_PEAR', SERVER_ROOT . 'libs' . DS . 'PEAR' . DS);

/* * *************************************************************************** */
@ini_set('include_path', SERVER_ROOT . 'libs' . DS . 'PEAR' . DS);
@require_once _PATH_TO_PEAR . 'Var_Dump.php';
if (DEBUG_MODE > 0)
{
    error_reporting(E_ALL);
    ini_set('display_errors',1);
}
else
{
    error_reporting(0);
    ini_set('display_errors',0);
}
