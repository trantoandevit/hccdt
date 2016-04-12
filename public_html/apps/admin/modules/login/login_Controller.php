<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class login_Controller extends Controller {
     function __construct() {
        parent::__construct('admin', 'login');
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function main()
    {
        
        $this->dsp_login();
    }

    public function dsp_login()
    {
        Session::init();
        //kiem tra dang nhap
        $user_id = Session::get('user_id');        
        if(!isset($user_id))
        {
            $this->view->render('dsp_login');
        }
        else
        {
            //redirect
            $redirct_url = get_request_var('u','');
            if($redirct_url == '')
            {
                $location = $this->view->get_controller_url('','admin');
            }
            else
            {
                $check_permit = get_request_var('c','');
                if($check_permit != '' && session::check_permission($check_permit,FALSE) == FALSE)
                {
                    die('Bạn không có quyền thực hiện chức năng này !!!');
                }
                $location = urldecode($redirct_url);
            }
            header('location:'.$location);
            exit();
        }
    }

    public function do_login()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_login();
    }
    public function do_logout(){
        @session::init();
        session::destroy();
        $this->dsp_login();
    }
}