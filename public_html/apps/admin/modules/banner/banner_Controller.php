<?php

class banner_Controller extends Controller {

    function __construct() {
        parent::__construct('admin','banner');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if(session::check_permission('QL_DANH_SACH_BANNER')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }
    public function main() {
        $this->dsp_all_banner();
    }

    public function dsp_all_banner() 
    {
        $VIEW_DATA['arr_all_banner'] = $this->model->qry_all_banner();
        $this->view->render('dsp_all_banner',$VIEW_DATA);
    }

    public function dsp_single_banner($v_id_banner) 
    {
        is_id_number($v_id_banner) OR $v_id_banner = 0;
        $arr_data                               = $this->model->qry_single_banner($v_id_banner);
        $VIEW_DATA['arr_single_banner']         = isset($arr_data['arr_single_banner'])?$arr_data['arr_single_banner']:array();
        $VIEW_DATA['arr_all_category_on_web']   = isset($arr_data['arr_all_category_on_web'])?$arr_data['arr_all_category_on_web']:array();
        $VIEW_DATA['arr_all_cat_to_check']      = isset($arr_data['arr_all_cat_to_check'])?$arr_data['arr_all_cat_to_check']:array();
        $this->view->render('dsp_single_banner',$VIEW_DATA);
    }
    
    public function update_banner() 
    {
        $this->model->update_banner();
    }

    public function delete_banner() 
    {
        $this->model->delete_banner();
    }
    
    public function write_cache()
    {
        get_system_config_value(CFGKEY_CACHE) == 'true' or $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        
        $website_id = session::get('session_website_id');
        $cache = new GP_Cache();
        $balance = $cache->create_banner_cache($website_id);
        
        if ($balance == FALSE)
        {
            $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        }
        
        $this->model->exec_done($this->model->goback_url, $_POST);
    }
}
?>

