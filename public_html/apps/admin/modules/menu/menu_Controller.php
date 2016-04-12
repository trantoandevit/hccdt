<?php

class menu_Controller extends Controller {

    function __construct() {
        parent::__construct('admin','menu');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if(session::check_permission('QL_DANH_SACH_MENU')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    public function main() {
        $this->dsp_all_menu();
    }

    public function dsp_all_menu($v_position_id=0) 
    {

        $arr_data                        = $this->model->qry_all_menu();
        $VIEW_DATA['arr_all_position']   = $arr_data['arr_all_position'];
        $VIEW_DATA['arr_theme_position'] = $arr_data['arr_theme_position'];
        $VIEW_DATA['website_menu']       = $arr_data['website_menu'];
        $this->view->render('dsp_all_menu',$VIEW_DATA);
    }
    public function dsp_single_position($v_position_id=0)
    {
        //$this->model->db->debug=0;
        $VIEW_DATA['arr_all_menu'] = $this->model->qry_single_position($v_position_id);
        
        $this->view->render('dsp_single_position',$VIEW_DATA);
    }
    public function dsp_single_menu($v_menu_id)
    {
        $arr_data                     = $this->model->qry_single_menu($v_menu_id);
        $VIEW_DATA['arr_single_menu'] = isset($arr_data['arr_single_menu'])?$arr_data['arr_single_menu']:array();
        $VIEW_DATA['arr_all_menu']    = isset($arr_data['arr_all_menu'])?$arr_data['arr_all_menu']:array();
        $this->view->render('dsp_single_menu',$VIEW_DATA);
    }
    public function dsp_menu_service()
    {
        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category();
        $this->view->render('dsp_menu_service',$VIEW_DATA);
    }
    public function swap_order()
    {
        $v_id           = get_post_var('hdn_item_id');
        $v_id_swap      = get_post_var('hdn_item_id_swap');
        $this->model->swap_order($v_id,$v_id_swap);
    }
    public function update_position()
    {
        $this->model->update_position();
    }
    public function update_menu()
    {
        $this->model->update_menu();
    }
    public function update_theme_position()
    {
        $this->model->update_theme_position();
    }
    public function delete_position()
    {
        $this->model->delete_position();
    }    
    public function delete_menu()
    {
        $this->model->delete_menu();
    }
    public function create_cache()
    {
        
        get_system_config_value(CFGKEY_CACHE) == 'true' or $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        
        $website_id = session::get('session_website_id');
        
        //cache sticky category
        $cache = new GP_Cache();
        $balance = $cache->create_menu_cache($website_id);
        if ($balance == FALSE)
        {
            $this->model->exec_fail($this->view->get_controller_url(), __('cache write error'));
        }
        $this->model->exec_done($this->view->get_controller_url());
    }
}
?>

