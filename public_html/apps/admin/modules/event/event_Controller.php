<?php

class event_Controller extends Controller
{

    function __construct()
    {
        parent::__construct('admin', 'event');
        $this->check_login();
        $this->model->goback_url                  = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article  = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;

        session::init();
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if (session::check_permission('QL_DANH_SACH_SU_KIEN') == FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    public function main()
    {
        $this->dsp_all_event();
    }

    public function dsp_all_event()
    {
        $arr_data                   = $this->model->qry_all_event();
        $VIEW_DATA['arr_all_event'] = $arr_data['arr_all_event'];
        $VIEW_DATA['arr_search']    = $arr_data['arr_search'];
        $this->view->render('dsp_all_event', $VIEW_DATA);
    }

    public function dsp_single_position($v_position_id = 0)
    {
        $this->model->db->debug     = 0;
        $VIEW_DATA['arr_all_event'] = $this->model->qry_single_position($v_position_id);

        $this->view->render('dsp_single_position', $VIEW_DATA);
    }

    public function dsp_single_event($v_event_id)
    {
        $arr_data                      = $this->model->qry_single_event($v_event_id);
        $VIEW_DATA['arr_single_event'] = isset($arr_data['arr_single_event']) ? $arr_data['arr_single_event'] : array();
        $VIEW_DATA['arr_all_article'] = isset($arr_data['arr_all_article']) ? $arr_data['arr_all_article'] : array();
        $this->view->render('dsp_single_event', $VIEW_DATA);
    }

    public function swap_order()
    {
        $v_id      = get_post_var('hdn_item_id');
        $v_id_swap = get_post_var('hdn_item_id_swap');
        $this->model->swap_order($v_id, $v_id_swap);
    }

    public function update_event()
    {
        $this->model->update_event();
    }

    public function delete_event()
    {
        $this->model->delete_event();
    }

    public function create_cache()
    {
        get_system_config_value(CFGKEY_CACHE) == 'true' or $this->model->exec_fail($this->model->goback_url, __('cache write error'));
        
        $website_id = session::get('session_website_id');
        
        $cache = new GP_Cache();
        $v_success = $cache->create_event_cache($website_id) && $cache->create_report_cache($website_id);
        
        if ($v_success == TRUE)
        {
            $this->model->exec_done($this->model->goback_url);
        }
        $this->model->exec_fail($this->model->goback_url, __('cache write error'));
    }

}
?>

