<?php

defined('DS') or die('no direct access');

class spotlight_Controller extends Controller
{

    function __construct()
    {
        parent::__construct('admin', 'spotlight');
        Session::init();
        Session::get('user_id') or $this->login_admin(); 
        
        Session::check_permission('QL_DANH_SACH_VI_TRI_TIEU_DIEM') or $this->access_denied();
        
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
    }

    function main()
    {
       
        $data['arr_all_position'] = $this->model->qry_all_position();
        $this->view->render('main', $data);
    }

    function dsp_single_position($id = 0)
    {
        $id = intval($id);
        
        $data['arr_single_position'] = $this->model->qry_single_position($id);
        if (!$data['arr_single_position'] && $id != 0)
        {
            die(__('this object is nolonger available!'));
        }
        $data['arr_all_spotlight'] = $this->model->qry_all_spotlight($id);
        $this->view->render('dsp_single_position', $data);
    }

    function update_position()
    {    	
        $v_id = intval(get_post_var('hdn_item_id'));
        $this->model->update_position();
    }

    function delete_position()
    {
        $this->model->delete_position();
    }

    function insert_spotlight()
    {    	
        $this->model->insert_spotlight();
    }

    function swap_spotlight_order()
    {
        $this->model->swap_spotlight_order();
    }

    function delete_spotlight()
    {
        $this->model->delete_spotlight();
    }

    function cache($pos_id)
    {
        get_system_config_value(CFGKEY_CACHE) == 'true' or $this->model->exec_done($url);
        
        $v_website_id = Session::get('session_website_id');
        
        $cache = New GP_Cache();
        $balance = $cache->create_spotlight_cache($v_website_id, $pos_id);
        if ($balance == true)
        {
            $this->model->exec_done($this->view->get_controller_url());
        }
        else
        {
            $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        }
    }

}

?>
