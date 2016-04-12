<?php

class advertising_Controller extends Controller {

    function __construct() {
        parent::__construct('admin','advertising');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if(session::check_permission('QL_DANH_SACH_QUANG_CAO')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    public function main() {
        $this->dsp_all_advertising();
    }

    public function dsp_all_advertising($v_position_id=0) 
    {

        $VIEW_DATA['arr_all_position']      = $this->model->qry_all_advertising();
        $this->view->render('dsp_all_advertising',$VIEW_DATA);
    }
    public function dsp_single_position($v_position_id=0)
    {
        $this->model->db->debug = 0;
        $VIEW_DATA['arr_all_advertising'] = $this->model->qry_single_position($v_position_id);
        
        $this->view->render('dsp_single_position',$VIEW_DATA);
    }
    public function dsp_single_advertising($v_adv_id)
    {
        $VIEW_DATA['arr_single_adv'] = $this->model->qry_single_advertising($v_adv_id);
        $this->view->render('dsp_single_advertising',$VIEW_DATA);
    }
    public function swap_order()
    {
        $v_id       = get_post_var('hdn_item_id');
        $v_id_swap  = get_post_var('hdn_item_id_swap');
        $this->model->swap_order($v_id,$v_id_swap);
    }
    public function update_position()
    {
        $this->model->update_position();
    }
    public function update_advertising()
    {
        $this->model->update_advertising();
    }
    public function delete_position()
    {
        $this->model->delete_position();
    }    
    public function delete_advertising()
    {
        $this->model->delete_advertising();
    }
}
?>

