<?php

class citizens_question_Controller extends Controller {

    function __construct() {
        parent::__construct('admin','citizens_question');
        $this->check_login();
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;
        
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if(session::check_permission('QL_CAU_HOI_DAP')==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    public function main() {
        $this->dsp_all_cq();
    }

    public function dsp_all_cq() 
    {
        $arr_data                       = $this->model->qry_all_question();
        $VIEW_DATA['arr_all_question']  = $arr_data['arr_all_question'];
        $VIEW_DATA['arr_search']        = $arr_data['arr_search'];
        
        $arr_data                       = $this->model->qry_all_field();
        $VIEW_DATA['arr_all_field']     = $arr_data['arr_all_field'];
        $VIEW_DATA['arr_search_field']  = $arr_data['arr_search'];
        
        $VIEW_DATA['tab_select']        = get_post_var('hdn_tab_select','question');
        $this->view->render('dsp_all_cq',$VIEW_DATA);
    }
    public function swap_order($type)
    {
        $type = replace_bad_char($type);
        $this->model->swap_order_cq($type);
    }
    public function delete_question()
    {
        $this->model->delete_question();
    }
    public function delete_field()
    {
        $this->model->delete_field();
    }

    public function dsp_single_question($id)
    {
        $VIEW_DATA['arr_single_question'] = $this->model->qry_single_question($id);
        $arr_data                         = $this->model->qry_all_field();
        $VIEW_DATA['arr_all_field']       = $arr_data['arr_all_field'];
        
        $this->view->render('dsp_single_question',$VIEW_DATA);
    }
    
    public function dsp_single_field($id)
    {       
        $VIEW_DATA['arr_single_field'] = $this->model->qry_single_field($id);
        $this->view->render('dsp_single_field',$VIEW_DATA);
    }
    
    public function update_question()
    {
        $this->model->update_question();
    }
    public function update_field()
    {
        $this->model->update_field();
    }
    
}
?>

