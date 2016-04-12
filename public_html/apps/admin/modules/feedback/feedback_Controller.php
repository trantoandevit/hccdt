<?php

defined('DS') or die('no direct access');

class feedback_Controller extends Controller
{

    function __construct()
    {
        Session::init();
        //kiem tra dang nhap
        Session::get('user_id') or $this->login_admin();
        parent::__construct('admin', 'feedback');
        
        
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        
        $this->model->goback_url = $this->view->get_controller_url();
        
        if(session::check_permission('QL_GOP_Y') == FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        
    }

    function main()
    {
        $this->dsp_all_feedback();
    }
    /**
     * lay tat ca danh gop y phan hoi
     */
    public function dsp_all_feedback()
    {
        //lay filter
        $v_id_magazine = get_post_var('sel_magazine',-1);
        $v_status      = get_post_var('sel_status',-1);
        
        $VIEW_DATA['arr_all_feedback'] = $this->model->qry_all_feedback();
        
        $this->view->render('dsp_all_feedback', $VIEW_DATA);
    }
    
    /**
     * xoa gop y phan hoi
     */
    public function delete_feedback()
    {
        $this->model->delete_feedback();
    }
    
    /**
     * hien thi chi tiet gop y phan hoi
     */
    public function dsp_single_feedback()
    {
        $v_id = get_post_var('hdn_item_id',0);
        $VIEW_DATA['arr_single_feedback'] = $this->model->qry_single_feedback($v_id);
        
        $this->view->render('dsp_single_feedback',$VIEW_DATA);
    }
    /**
     * cap nhat thong tin gop y phan hoi
     */
    public function update_feedback()
    {
        $this->model->update_feedback();
    }
}

?>
