<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

class Bootstrap
{

    function __construct($uri)
    {
        //index.php?url=app/module/function/function_args
        $url = explode('/', rtrim($uri, '/'));

        $app = (!empty($url[0])) ? trim($url[0]) : 'frontend';
        if (isset($url[1]))
        {
            $module = trim($url[1]);
        }
        else
        {
            $module = ($app == 'frontend') ? 'frontend' : 'dashboard';
        }
        //Load Controller
        $file = SERVER_ROOT . 'apps' . DS . $app . DS . 'modules' . DS . $module . DS . $module . '_Controller.php';
        if (file_exists($file))
        {
            require $file;
            $controller_class = $module . '_Controller';

            //new instance
            $controller = new $controller_class($app, $module);
            //load ngon ngu
            @session::init();
            if ($app == 'frontend')
            {
                $v_website_id = get_request_var('website_id', $controller->model->qry_default_website_id());
            }
            else
            {
                $v_website_id = isset($_SESSION['session_website_id']) ? session::get('session_website_id') : 0;
            }
            $v_lang = $controller->model->gp_qry_lang_of_website($v_website_id);
            lang::load_lang($v_lang);
        }
        else
        {
            $this->_error(2);
            return false;
        }

        $function_args = (isset($url[3])) ? trim($url[3]) : '';
        $function = (isset($url[2])) ? trim($url[2]) : '';

        //Neu la yeu cau thuc hien method
        if ($function_args != '') //Goi ham co tham so
        {
            //echo '$function_args = ' . $function_args;
            if (method_exists($controller, $function))
            {
                $controller->{$function}($function_args);
            }
            else
            {
                $this->_error(1);
            }
        }
        elseif ($function != '') //Hay goi ham khong co tham so
        {
            if (method_exists($controller, $function))
            {
                $controller->{$function}();
            }
            else
            {
                $this->_error(1);
            }
        }
        else //neu khong co yeu cau gi, thuc hien method mac dinh
        {
            $controller->main();
        }
    }

    private function _error($code)
    {
        switch ($code) {
            case 1:
                require 'error/error.php';
                break;

            case 2:
                require 'error/error.php';
                break;
        }
    }

}
