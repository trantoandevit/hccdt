<?php

class poll_Controller extends Controller {

    function __construct() {
        parent::__construct('admin','poll');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if(session::check_permission('QL_DANH_SACH_CUOC_THAM_DO_Y_KIEN')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    public function main() {
        $this->dsp_all_poll();
    }

    public function dsp_all_poll() 
    {

        $VIEW_DATA['arr_all_poll']      = $this->model->qry_all_poll();
        $this->view->render('dsp_all_poll',$VIEW_DATA);
    }
    public function dsp_single_poll($v_poll_id)
    {
        $arr_data = $this->model->qry_single_poll($v_poll_id);
        $VIEW_DATA['arr_single_poll']  = isset($arr_data['arr_single_poll'])?$arr_data['arr_single_poll']:array();
        $VIEW_DATA['arr_all_answer']   = isset($arr_data['arr_all_answer'])?$arr_data['arr_all_answer']:array();
        $this->view->render('dsp_single_poll',$VIEW_DATA);
    }
    public function swap_order()
    {
        $v_id       = get_post_var('hdn_item_id');
        $v_id_swap  = get_post_var('hdn_item_id_swap');
        $this->model->swap_order($v_id,$v_id_swap);
    }
    public function update_poll()
    {
        $this->model->update_poll();
    }
    public function delete_poll()
    {
        $this->model->delete_poll();
    }
}
?>

