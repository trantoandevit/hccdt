<?php
/**
 * Description of custommer_Controller
 *
 * @author Tam Viet
 */
class citizen_account_Controller extends Controller
{
    function __construct() 
    {
        parent::__construct('admin', 'citizen_account');
        $this->check_login();
        $this->model->goback_url                  = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->arr_count_article  = $this->model->gp_qry_count_article();
        //$this->view->template->show_div_website = FALSE;

        session::init();
        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        if (session::check_permission('QL_DANH_SACH_TAI_KHOAN_CONG_DAN',FALSE) == FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
        
    }
    
    public function main()
    {
        $this->dsp_all_account();
    }
    /**
     * man hinh toan bo account
     */
    public function dsp_all_account()
    {
        //Xoa danh sach du lieu luu tru thay doi pass hoa email
        $this->model->do_del_ac_tmp_overdue_confirm();
        $VIEW_DATA['arr_all_account'] = $this->model->dsp_all_account();
        $VIEW_DATA['count_account_overduce_confirm'] = $this->model->qry_count_ac_new_over_confirm();
        $this->view->render('dsp_all_citizen_account',$VIEW_DATA);
    }
    /**
     * man hinh chi tiet 1 account
     */
    public function dsp_single_account()
    {
        $v_account_id = get_post_var('hdn_item_id',0);
        if($v_account_id == 0)
        {
            $this->model->exec_fail($this->view->get_controller_url(),'Tài khoản không hợp lệ');
        }
        
        $VIEW_DATA['arr_single_account'] = $this->model->qry_single_account($v_account_id);
        $this->view->render('dsp_single_citizen_account',$VIEW_DATA);
    }
    /**
     * thuc hien xoa account
     */
    public function delete_account()
    {
        $v_list_delete = trim(get_post_var('hdn_item_id_list',''));
        $this->model->delete_account($v_list_delete);
    }
    /**
     * thuc hien cap nhat thong tin 
     */
    public function update_account()
    {
        $v_id     = trim(get_post_var('hdn_item_id'));
        $v_status = trim(get_post_var('sel_status',''));
        $v_reason = trim(get_post_var('txt_reason',''));
        $this->model->update_account($v_id,$v_status,$v_reason);
                
    }
    //Xoa danh sách các account dang ky qua han kich hoat
    public function do_delete_overdue_confirm()
    {
        if($this->model->do_delete_overdue_confirm() == TRUE)
        {
            echo 'TRUE';
            return;
        }
        echo 'FALSE';
        return;
    }
    
}
