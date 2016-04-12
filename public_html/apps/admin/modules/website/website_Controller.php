<?php
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class website_Controller extends Controller {

    function __construct() {
        parent::__construct('admin', 'website');

        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->show_div_website = FALSE;
        $this->view->template->arr_count_article = $this->model->gp_qry_count_article();

        //Kiem tra dang nhap
        $this->check_login();
        session::init();
        $v_lang_id = session::get('session_lang_id');
        $this->view->template->arr_all_lang = $this->model->qry_all_lang();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        //echo session::check_permission('XEM_DANH_SACH_CHUYEN_TRANG');
        if(session::check_permission('QL_DANH_SACH_CHUYEN_TRANG',FALSE)==FALSE)
        {
            die('Bạn không có quyền thực hiện chức năng này !!!');
        }
    }

    public function main()
    {
        $this->dsp_all_website();
    }

    public function dsp_all_website()
    {
        //echo  $user_function=replace_bad_char($_SESSION['session_website_code'])."::".'XEM_DANH_SACH_CHUYEN_TRANG';
        //var_dump($_SESSION['arr_all_grant_function_code']);
        //echo session::check_permission('XEM_DANH_SACH_CHUYEN_TRANG');

            $VIEW_DATA['arr_all_website'] = $this->model->qry_all_website();

            $this->view->render('dsp_all_website', $VIEW_DATA);
    }

    public function dsp_single_website($website_id)
    {
        is_id_number($website_id) OR $website_id = 0;

        $arr_data                        = $this->model->qry_single_website($website_id);
        $VIEW_DATA['arr_single_website'] = $arr_data['arr_single_website'];
        $VIEW_DATA['arr_all_lang']       = $arr_data['arr_all_lang'];

        $this->view->render('dsp_single_website', $VIEW_DATA);
    }

    public function update_website()
    {
        $this->model->update_website();
    }

    public function delete_website()
    {
        $this->model->delete_website();
    }
    //service theme
    public function dsp_all_theme_to_add()
    {
        $VIEW_DATA['arr_theme'] = $this->model->qry_all_theme();
        $this->view->render('dsp_all_theme_to_add',$VIEW_DATA);
    }
    public function check_code()
    {
        $this->model->check_code($_POST['txt_code']);
    }
    public function check_name()
    {
        $this->model->check_name($_POST['txt_name']);
    }
}