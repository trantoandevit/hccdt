<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

class Controller
{

    public $view;
    public $model;
    protected $app_name = '';
    protected $module_name = '';

    function __construct($app, $module)
    {
        $v = $app . '_View';
        $this->view = new View($app, $module);

        $this->app_name = $app;
        $this->module_name = $module;

        $this->_load_model($app, $module);

        //Load app const
        $const_file = SERVER_ROOT . 'apps' . DS . $app . DS . "{$app}_const.php";
        if (file_exists($const_file))
        {
            require_once($const_file);
        }

        //load config
        global $CONFIG;
        $config_data = fixEncoding($this->model->gp_load_options(OPT_SYSCFG));
        try
        {
            $CONFIG = new SimpleXMLElement(xml_add_declaration($config_data), LIBXML_NOCDATA);
        }
        catch (Exception $ex)
        {
            $CONFIG = '<root/>';
        }
        $unit_name = get_system_config_value(CFGKEY_UNIT_NAME);
        if (!defined('_CONST_UNIT_NAME'))
        {
            define('_CONST_UNIT_NAME', $unit_name);
        }
    }

    private function _load_model()
    {
        $path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'modules' . DS . $this->module_name . DS . $this->module_name . '_Model.php';
        if (file_exists($path))
        {
            require $path;
            $model_name = $this->module_name . '_Model';
            $this->model = new $model_name;
        }
        else
        {
            $this->error(1);
        }
    }

    public static function get_post_var($html_object_name, $defaul = '', $is_replace_bad_char = TRUE)
    {
        $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : $defaul;

        if ($is_replace_bad_char)
        {
            return replace_bad_char($var);
        }

        return $var;
    }

    public function error($error_code)
    {
        switch ($error_code) {
            case 1: //Ma loi 1: Không thấy file Model!
                die('Không thấy file Model!');
                break;
            case 2:
                die('xxx');
                break;
        }
    }

    protected function login_admin()
    {
        Session::destroy();
        $url = SITE_ROOT;
        $url .= file_exists('.htaccess') ? '' : 'index.php?url=';
        $url .= 'admin/login/';
        header('location: ' . $url);
        exit;
    }

    protected function access_denied()
    {
        die(__('you dont have access right on this function'));
    }

    public static function check_login()
    {
        session::init();
        $login_name = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            return FALSE;
        }

        return TRUE;
    }

}
