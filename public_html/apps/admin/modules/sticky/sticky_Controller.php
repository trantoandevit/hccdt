<?php
defined('DS') or die('no direct access');

class sticky_Controller extends Controller
{
    function __construct()
    {
        parent::__construct('admin', 'sticky');
        Session::init();
        Session::get('user_id') or $this->login_admin();
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);

        $this->model->goback_url = $this->view->get_controller_url();
		
		Session::check_permission('QL_DANH_SACH_NOI_BAT') or $this->access_denied();		
    }

    function main()
    {
        $this->view->render('main');
    }

    function dsp_all_sticky($v_default = 0)
    {
     //   Session::check_permission('QL_DANH_SACH_NOI_BAT',0) or $this->access_denied();
        $v_default    = intval($v_default);
        $other_clause = '';
        if ($v_default < 0 or $v_default > 2)
        {
            die(__('invalid request data'));
        }
        if ($v_default == 0)
        {
            $other_clause             = ' And FK_WEBSITE=' . Session::get('session_website_id');
            $data['arr_all_category'] = $this->model->qry_all_category($other_clause);
            $default_category         = isset($data['arr_all_category'][0]) ? $data['arr_all_category'][0]['PK_CATEGORY'] : 0;
            //reset other_clause
            //other_clause cho sticky
            $v_category               = intval(get_request_var('sel_category', $default_category));
            
            $other_clause             = ' And C_DEFAULT = 0 And S.FK_CATEGORY = ' . $v_category;
        }
        //lay tat ca danh sach tin noi bat tren trang chu
        else if($v_default == 1)
        {
            $other_clause = ' And C_DEFAULT = ' . $v_default;
        }
        //lay tat ca tin dang chu y tron ngay
        else if($v_default == 2)
        {
            $other_clause = ' And C_TYPE = ' . $v_default;
        }

        $data['v_default']      = $v_default;
        
        $data['arr_all_sticky'] = $this->model->qry_all_sticky($other_clause);
        $this->view->render('dsp_all_sticky', $data);
    }

    function insert_sticky()
    {
      //  Session::check_permission('THEM_MOI_NOI_BAT') or $this->access_denied();
        $this->model->insert_sticky();
    }

    function delete_sticky()
    {
       // Session::check_permission('XOA_NOI_BAT') or $this->access_denied();
        $this->model->delete_sticky();
    }

    function swap_sticky_order()
    {
     //   Session::check_permission('SUA_NOI_BAT') or $this->access_denied();
        $this->model->swap_sticky_order();
    }

    //cache
    function create_cache()
    {
        //kiem tra cachemode
        if (get_system_config_value(CFGKEY_CACHE) == 'false')
        {
            $this->model->exec_done($this->model->goback_url);
        }
        
        $website_id = session::get('session_website_id');
        
        $cache = new GP_Cache();
        $cache->create_all_article_type_cache($website_id);
        
        $this->model->exec_done($this->model->goback_url, $_POST);
    }
}